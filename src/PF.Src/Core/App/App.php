<?php

namespace Core\App;

use Core\App\Install\Database;
use Core\App\Install\Phrase;
use Core\App\Install\Setting;
use Phpfox;
use Phpfox_File;
use Phpfox_Installer;

/**
 * Class App
 *
 * @author  Neil
 * @version 4.5.0
 * @package Core\App
 */
abstract class App
{
    /** an app CAN'T change this ID, if change, it will become another app
     *
     * @var string
     */
    public $id;

    /**
     * @var string is alias name of this App. This value shouldn't change
     */
    public $alias;

    /**
     * @var string is name of this App. You can change this value, but we don't recommend
     */
    public $name;

    /**
     * @var string is version of this App. We recommend you use 4.x or 4.x.x
     */
    public $version;

    /**
     * @var string is icon for this app. It can a link of image, a font-awesome class. If it empty or not set, we will
     *      use file "icon.png" is root path of your app. If file "icon.png" doesn't exist, we auto generate two
     *      characters for your icon.
     */
    public $icon;

    /**
     * @var string
     */
    public $admincp_route;

    /**
     * @var string
     */
    public $admincp_menu;

    /**
     * @var string
     */
    public $admincp_help;

    /**
     * @var string
     */
    public $admincp_action_menu;

    /**
     * @var string
     */
    public $map;

    /**
     * @var string
     */
    public $map_search;

    /**
     * @var array
     */
    public $menu = [];

    /**
     * @var array
     */
    public $settings;

    /**
     * @var Setting\Site
     */
    protected $_settings;

    /**
     * @var Setting\Groups
     */
    protected $_user_group_settings;

    /**
     * @var array
     */
    public $user_group_settings;

    /**
     * @var
     */
    public $notifications;

    /**
     * @var array
     */
    public $database;

    /**
     * @var array
     */
    public $component;

    /**
     * @var array
     */
    public $component_block;

    /**
     * @var array
     */
    public $component_block_remove;

    /**
     * @var
     */
    public $routes;

    /**
     * @var string
     */
    public $path;

    /**
     * @var string
     */
    public $start_support_version = '';

    /**
     * @var string
     */
    public $end_support_version = '';

    /**
     * @var array
     */
    public $phrase = [];

    /**
     * @var Phrase\Phrase
     */
    private $_phrase;

    private $_aErrorMessages = [];

    /**
     * Name of publisher
     *
     * @var string
     */
    public $_publisher = 'n/a';

    /**
     * Home page of publisher
     *
     * @var string
     */
    public $_publisher_url = '';

    /**
     * @var string
     */
    public $_apps_dir = '';

    /**
     * External directories of app
     *
     * Example:
     * $external_paths = [
     *      [
     *          'dir' => 'PF.Base/static/example.html'
     *          'removable' => true // this directory will be removed when uninstall app
     *      ]
     * ]
     *
     * @var array
     */
    public $external_paths = [];

    /**
     * @var array store errors from this app
     */
    protected $_errors;

    /**
     * @var array defined list of dirs that must have write permissions
     */
    protected $_writable_dirs = [];

    /**
     * @var bool old settings will be remmoved from database when installing new app
     */
    protected $_remove_old_settings = true;

    /**
     * @var bool allow user to remove database when uninstall app
     */
    public $allow_remove_database = true;

    /**
     * @var array of core apps
     */
    private $_aCores
        = [
            'PHPfox_Core',
            'PHPfox_Flavors',
        ];

    /**
     * @var bool
     */
    public $_admin_cp_menu_ajax = true;

    private $_aOfficial
        = [
            'PHPfox_Core',
            'PHPfox_Flavors',
        ];

    /**
     * App constructor.
     */
    public function __construct()
    {
        $this->_phrase = new Phrase\Phrase();
        $this->setId();
        $this->setAlias();
        $this->setName();
        $this->setSupportVersion();
        $this->setVersion();
        $this->setSettings();
        $this->setUserGroupSettings();
        $this->setComponent();
        $this->setComponentBlock();
        $this->setOthers();

        if (!is_array($this->admincp_menu) && !is_array($this->settings) && !is_array($this->user_group_settings)) {
            $this->admincp_menu = [
                $this->name => '#',
            ];
        }

        // if don't set apps_dir, we use app_id
        if (empty($this->_apps_dir)) {
            $this->_apps_dir = $this->id;
        }
        $this->path = PHPFOX_DIR_SITE . 'Apps' . PHPFOX_DS . $this->_apps_dir . PHPFOX_DS;

        $this->initData();
        $attributes = get_object_vars($this);
        foreach ($attributes as $iKey => $attribute) {
            if (is_array($attribute) && substr($iKey, 0, 1) != '_') {
                $this->{$iKey} = json_decode(json_encode($attribute));
            }
        }
    }

    /**
     * Set ID
     */
    abstract protected function setId();

    /**
     * Set start and end support version of your App.
     *
     * @example   $this->start_support_version = 4.2.0
     * @example   $this->end_support_version = 4.5.0
     * @see       list of our verson at PF.Base/install/include/installer.class.php ($_aVersions)
     * @important You DO NOT ALLOW to set current version of phpFox for start_support_version and end_support_version. We will reject of app if you use current version of phpFox for these variable.
     *            These variables help clients know their sites is work with your app or not.
     */
    abstract protected function setSupportVersion();

    /**
     * Set Alias
     */
    abstract protected function setAlias();

    /**
     * Set name
     */
    abstract protected function setName();

    /**
     * Set version
     */
    abstract protected function setVersion();

    /**
     * Set phrases
     */
    abstract protected function setPhrase();

    /**
     * Set settings
     */
    abstract protected function setSettings();

    /**
     * Set user group settings
     */
    abstract protected function setUserGroupSettings();

    /**
     * Set component
     */
    abstract protected function setComponent();

    /**
     * Set component block
     */
    abstract protected function setComponentBlock();

    /*
     * Set other attributes for this app
     */
    abstract protected function setOthers();

    /*
     * This function will be called in the start of processInstall function
     */
    protected function preInstall()
    {
    }

    /**
     * Check this App is valid
     *
     * @return bool
     */
    public function isValid()
    {
        if ($this->isCore()) {
            return true;
        }

        if (empty($this->id)) {
            $this->_aErrorMessages[] = "Missing app_id";
            return false;
        }

        if (empty($this->name)) {
            $this->_aErrorMessages[] = "Missing App name";
            return false;
        }

        if (empty($this->version)) {
            $this->_aErrorMessages[] = "Missing Version";
            return false;
        }

        //Check min version
        if (!empty($this->start_support_version) && (version_compare($this->start_support_version, Phpfox::VERSION, ">") and  strpos(Phpfox::VERSION, $this->start_support_version) !== 0)) {
            $this->_aErrorMessages[] = "This app requires phpFox " . $this->start_support_version . " or newer";
            return false;
        }

        //We have to have alias for remove/disable menu when uninstall/disable
        if (count($this->menu) && empty($this->alias)) {
            return false;
        }

        return true;
    }

    /**
     * Init data for this app
     */
    private function initData()
    {
        //Setting
        if (is_array($this->settings)) {
            if (count($this->settings)) {
                foreach ($this->settings as $key => $setting) {
                    if (!isset($setting['var_name'])) {
                        $setting['var_name'] = $key;
                    }
                    $oSetting = new Setting\Site($setting);
                    if ($oSetting->isValid()) {
                        $this->_phrase->addPhrase($oSetting->getPhraseVarName(), $oSetting->getPhraseValue());
                        $this->_settings[] = $oSetting;
                    } else {
                        $this->_errors[] = $oSetting->getError();
                    }
                }
            }
        }

        //User groups setting
        if (count($this->user_group_settings)) {
            foreach ($this->user_group_settings as $key => $group_setting) {
                if (!isset($group_setting['var_name'])) {
                    $group_setting['var_name'] = $key;
                }
                $oGroupSetting = new Setting\Groups($group_setting);
                if ($oGroupSetting->isValid()) {
                    $this->_phrase->addPhrase($oGroupSetting->getPhraseVarName(), $oGroupSetting->getPhraseValue());
                    $this->_user_group_settings[] = $oGroupSetting;
                } else {
                    $this->_errors[] = $oGroupSetting->getError();
                }
            }
        }
        //Icon
        if (!isset($this->icon) || empty($this->icon)) {
            if (file_exists($this->path . 'icon.png')) {
                $this->icon = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/' . $this->_apps_dir . '/icon.png';
            }
        } elseif (filter_var($this->icon, FILTER_VALIDATE_URL) === false && strpos($this->icon, 'fa') !== false) {
            $name = $this->name[0];
            $parts = explode(' ', $this->name);
            if (isset($parts[1])) {
                $name .= trim($parts[1])[0];
            } elseif (isset($this->name[1])) {
                $name .= $this->name[1];
            }
            $class_color = '_' . $name;
            $name = '<i class="fa ' . $this->icon . '" aria-hidden="true"></i></i>';
            $this->icon = '<b class="app_icons"><i class="app_icon ' . strtolower($class_color) . '">' . $name . '</i></b>';
        }
        //phrase
        $this->setPhrase();
    }

    /**
     * Add a database table for this app
     *
     * @param $database Database\Table
     */
    protected function addDatabase($database)
    {
        if ($database - $this->isValid()) {
            $this->database[] = $database;
        }
    }

    /**
     * Add new phrases
     *
     * @param array $aParams
     */
    protected function addPhrases($aParams)
    {
        if (is_array($aParams)) {
            foreach ($aParams as $var_name => $value) {
                $this->_phrase->addPhrase($var_name, $value);
            }
        }
    }

    /**
     * Get all phrases of this app
     *
     * @return array
     */
    public function getPhrases()
    {
        return $this->_phrase->all();
    }

    /**
     * @param bool $bRemoveDb
     */
    public function uninstall($bRemoveDb = true)
    {
        if ($bRemoveDb && is_array($this->database) && count($this->database)) {
            foreach ($this->database as $database) {
                $sNamespace = "\\Apps\\" . $this->id . "\\Installation\\Database\\" . $database;
                if (class_exists($sNamespace)) {
                    /**
                     * @var $oDatabase \Core\App\Install\Database\Table
                     */
                    $oDatabase = new $sNamespace();
                    $oDatabase->drop();
                }
            }
        }
        if (isset($this->alias) && !empty($this->alias)) {
            //Delete block, component
            db()->delete(':block', "module_id='" . $this->alias . "'");
            db()->delete(':component', "module_id='" . $this->alias . "'");
            //delete alias
            db()->delete(':module', "module_id='" . $this->alias . "'");
        }
        db()->delete(':apps', 'apps_id="' . $this->id . '"');
    }

    /**
     * Process install/upgrade for this app
     *
     * @return bool
     */
    public function processInstall()
    {
        if (!$this->isValid()) {
            return false;
        }
        // prepare for installation
        $this->preInstall();

        if (is_array($this->database) && count($this->database)) {
            foreach ($this->database as $database) {
                $sNamespace = "\\Apps\\" . $this->id . "\\Installation\\Database\\" . $database;
                if (class_exists($sNamespace)) {
                    /**
                     * @var $oDatabase \Core\App\Install\Database\Table
                     */
                    $oDatabase = new $sNamespace();
                    $oDatabase->install();
                }
            }
        }
        //add phrase from json
        $sPath = PHPFOX_DIR_SITE . 'Apps' . PHPFOX_DS . $this->_apps_dir . PHPFOX_DS . 'phrase.json';
        if (file_exists($sPath)) {
            $aJsonPhrases = json_decode(file_get_contents($sPath), true);
            $this->addPhrases($aJsonPhrases);
        }
        //Add Phrase
        \Core\Lib::phrase()->addPhrase($this->_phrase->all());

        //Add Component
        Phpfox::getService('admincp.component.process')->importFromApp($this);

        // add block
        Phpfox::getService('admincp.block.process')->importFromApp($this);

        //Add setting
        if (isset($this->settings)) {
            Phpfox::getService('admincp.setting.process')->importFromApp($this);
        }
        //Add user group setting
        Phpfox::getService('user.group.setting.process')
            ->importFromApp($this);

        //Add menu
        Phpfox::getService('admincp.menu.process')->importFromApp($this);

        $iCnt = db()->select('COUNT(*)')
            ->from(':apps')
            ->where('apps_id="' . $this->id . '"')
            ->execute('getSlaveField');

        if (!$iCnt) {
            db()->insert(':apps', [
                'apps_id'    => $this->id,
                'apps_dir'   => $this->_apps_dir,
                'apps_name'  => $this->name,
                'version'    => $this->version,
                'apps_alias' => isset($this->alias) ? $this->alias : '',
                'author'     => $this->_publisher,
                'vendor'     => $this->_publisher_url,
                'apps_icon'  => $this->icon,
                'type'       => ($this->isCore()) ? 1 : 2,
                'is_active'  => 1,
            ]);
        } else {
            //upgrade case
            $aUpdate = [
                'apps_name'  => $this->name,
                'version'    => $this->version,
                'apps_dir'   => $this->_apps_dir,
                'apps_alias' => isset($this->alias) ? $this->alias : '',
                'author'     => $this->_publisher,
                'vendor'     => $this->_publisher_url,
                'apps_icon'  => $this->icon,
            ];
            db()->update(':apps', $aUpdate, 'apps_id="' . $this->id . '"');
        }

        //Add Alias to table :module
        if (isset($this->alias)) {
            //Check Alias is exist
            $iCnt = db()->select('COUNT(*)')
                ->from(':module')
                ->where('module_id = "' . $this->alias . '"')
                ->execute('getSlaveField');
            if (!$iCnt) {
                $aInsert = [
                    'module_id'       => $this->alias,
                    'product_id'      => 'phpfox',
                    'is_core'         => '0',
                    'is_active'       => '1',
                    'is_menu'         => '0',
                    'menu'            => '',
                    'phrase_var_name' => 'module_apps',
                ];
                db()->insert(':module', $aInsert);
            }
        }

        if (is_array($this->_writable_dirs)) {
            foreach ($this->_writable_dirs as $dir) {
                $dir = str_replace('/', PHPFOX_DS, $dir);
                if (!is_dir(PHPFOX_PARENT_DIR . $dir)) {
                    Phpfox_File::instance()->mkdir(PHPFOX_PARENT_DIR . $dir, true, 0777);
                }
            }
        }

        if (file_exists($this->path . 'installer.php')) {
            Installer::$method = 'onInstall';
            Installer::$basePath = $this->path;
            require_once($this->path . 'installer.php');
        }
        return true;
    }

    /**
     * Check this app is compatible with current phpFox version
     *
     * @return bool
     */
    public function isCompatible()
    {
        $aVersions = (new Phpfox_Installer())->getVersionList();
        $iStart = array_search($this->start_support_version, $aVersions);
        $iCurrent = array_search(Phpfox::getVersion(), $aVersions);
        $iEnd = array_search($this->end_support_version, $aVersions);
        if (($iStart <= $iCurrent) && ($iCurrent <= $iEnd)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Disable this app
     */
    public function disable()
    {
        db()->update(Phpfox::getT('apps'), [
            'is_active' => 0,
        ], 'apps_id="' . $this->id . '"');
    }

    /**
     * Enable this app
     */
    public function enable()
    {
        db()->update(Phpfox::getT('apps'), [
            'is_active' => 1,
        ], 'apps_id="' . $this->id . '"');
    }

    /**
     * Check this app is a core app or not
     *
     * @return bool
     */
    public function isCore()
    {
        return in_array($this->id, $this->_aOfficial);
    }

    /**
     * Check this app is active
     *
     * @return bool
     */
    public function isActive()
    {
        if (in_array($this->id, $this->_aCores)) {
            return true;
        }
        if (defined('PHPFOX_APP_INSTALLING') && PHPFOX_APP_INSTALLING) {
            return true;//installing app
        }
        $id = $this->id;
        return $id == get_from_cache(['app', 'is_active', $this->id], function () use ($id) {
            return db()->select('apps_id')
                ->from(':apps')
                ->where('apps_id=\'' . $id . '\' AND is_active=1')
                ->execute('getSlaveField');
        });
    }

    /**
     * @param $settings
     *
     * @return bool
     * @deprecated 4.6.0
     */
    public function saveSettings($settings)
    {
        foreach ($settings as $key => $value) {
            if (isset($this->settings->{$key}->requires) && $value == '1') {
                $set_value = (isset($settings[$this->settings->{$key}->requires]) ? $settings[$this->settings->{$key}->requires] : false);
                if (!$set_value) {
                    error(_p('"{{ name }}" requires setting "{{ requires }}".', [
                        'name'     => $this->settings->{$key}->info,
                        'requires' => $this->settings->{$this->settings->{$key}->requires}->info,
                    ]));
                }
            }
        }

        foreach ($settings as $key => $value) {
            if (is_array($value)) {
                $value = serialize($value);
            }
            //Check the setting is exist
            $iCnt = Phpfox::getLib('database')->select('COUNT(*)')
                ->from(':setting')
                ->where(['var_name' => $key])
                ->executeField();

            if ($iCnt) {
                //Update value of this setting
                $aUpdate = [
                    'value_actual' => $value,
                ];
                Phpfox::getLib('database')->update(':setting', $aUpdate, [
                    'var_name' => $key,
                ]);
            } else {
                //Add new setting it not found on database. This case support for upgrade version < 4.5.0
                Phpfox::getLib('database')->insert(':setting', [
                    'module_id'       => $this->alias,
                    'product_id'      => $this->id,
                    'is_hidden'       => 1,
                    'version_id'      => $this->version,
                    'type_id'         => 'string',
                    'var_name'        => $key,
                    'phrase_var_name' => $key,
                    'value_actual'    => $value,
                    'value_default'   => $value,
                ]);
            }
        }

        (new \Core\Cache())->del('app_settings');

        return true;
    }

    public function getErrorMessages()
    {
        return $this->_aErrorMessages;
    }

    public function isRemoveOldSettings()
    {
        return $this->_remove_old_settings;
    }
}