<?php

namespace Core\App;

use Core\Installation\FileHelper;

class Object extends \Core\Objectify
{

    /**
     * App ID. Can only be alphanumeric and may contain the underscore
     *
     * @var int
     */
    public $id;

    public $apps_dir;

    public $is_phpfox_default = false;

    public $admin_cp_menu_ajax = false;

    private $default_phpfox_apps
        = [
            '__module_ad',
            '__module_admincp',
            '__module_announcement',
            '__module_api',
            '__module_attachment',
            '__module_ban',
            '__module_blog',
            '__module_captcha',
            '__module_comment',
            '__module_contact',
            '__module_core',
            '__module_custom',
            '__module_egift',
            '__module_error',
            '__module_event',
            '__module_feed',
            '__module_forum',
            '__module_friend',
            '__module_invite',
            '__module_language',
            '__module_like',
            '__module_link',
            '__module_log',
            '__module_mail',
            '__module_marketplace',
            '__module_music',
            '__module_newsletter',
            '__module_notification',
            '__module_page',
            '__module_pages',
            '__module_photo',
            '__module_poke',
            '__module_poll',
            '__module_privacy',
            '__module_profile',
            '__module_quiz',
            '__module_report',
            '__module_request',
            '__module_rss',
            '__module_search',
            '__module_share',
            '__module_subscribe',
            '__module_tag',
            '__module_theme',
            '__module_track',
            '__module_user',
            'PHPfox_Core',
            'PHPfox_Flavors',
        ];

    /**
     * Name of the app
     *
     * @var string
     */
    public $name;

    /**
     * Full path to the app. This is built by the system
     *
     * @var string
     */
    public $path;

    public $module_id;

    public $is_active;

    /**
     * Define if this is an old school module or app
     *
     * @var bool
     */
    public $is_module = false;

    /**
     * Full path to icon anywhere on the internet(s)
     *
     * @var string
     */
    public $icon;

    /**
     * Version of the app
     *
     * @var string
     */
    public $version = '4.0.0';

    /**
     * Actual version of the app. This is created by the system
     *
     * @var string
     */
    public $currentVersion;

    /**
     * If an app is external, these are the auth ID/key
     *
     * @var object
     */
    public $auth
        = [
            'id' => '',
            'key' => '',
        ];

    /**
     * Attach a menu to the AdminCP for your app
     *
     * @var array
     */
    public $admincp_menu = [];

    public $admincp_action_menu = [];

    public $external_paths = [];

    /**
     * When admins view your app connect to a custom route
     *
     * @var string
     */
    public $admincp_route;

    /**
     * Global settings for your app and for admins to edit
     *
     * @var array
     */
    public $settings = [];

    /**
     * Webhooks you wish to attach an event to
     *
     * @var array
     */
    public $webhooks = [];

    /**
     * List of external routes, only needed if route is using API
     *
     * @var array
     */
    public $routes = [];

    /**
     * Attach anything to each page of the sites <head></head>
     *
     * @var array
     */
    public $head = [];

    public $js_phrases = [];

    /**
     * Attach JavaScript files. Full links to the JS file itself
     *
     * @var array
     */
    public $js = [];


    public $map = [];

    public $map_search = [];

    public $footer = [];

    /**
     * Internal phpFox app id. Created by the system. Move along.
     *
     * @var int
     */
    public $internal_id;

    public $store_id;

    /**
     * Company that created the app
     *
     * @var string
     */
    public $vendor;

    /**
     * User group settings your app may need and for admins to edit
     *
     * @var array
     */
    public $user_group_settings = [];

    /**
     * Define if your app requires another app or a specific PHP version or PHP lib
     *
     * @var array
     */
    public $requires = [];

    /**
     * Using open source code? Sharing is caring.
     *
     * @var array
     */
    public $credits = [];

    /**
     * @var array
     */
    public $menu = [];

    /**
     * @var bool
     */
    public $is_core = false;

    /**
     * @var array
     */
    public $blocks = [];

    /**
     * @var string
     */
    public $admincp_help = '';

    /**
     * @var array
     */
    public $notifications = [];

    /**
     * Name of publisher
     *
     * @var string
     */
    public $publisher = 'n/a';

    /**
     * Home page of publisher
     *
     * @var string
     */
    public $publisher_url = '';

    public $allow_disable = true;

    /**
     * @var bool allow user to remove app's database when uninstall
     */
    public $allow_remove_database = true;

    /**
     * Object constructor.
     *
     * @param string $keys
     */
    public function __construct($keys)
    {
        parent::__construct($keys);

        if (in_array($this->id, $this->default_phpfox_apps)) {
            $this->is_phpfox_default = true;
        }
        if (!$this->icon) {
            $name = isset($this->name[0]) ? $this->name[0] : 'pf';
            $parts = explode(' ', $this->name);
            if (isset($parts[1])) {
                $parts[1] = trim($parts[1]);
            }
            if (isset($parts[1]) && !empty($parts[1])) {
                $name .= $parts[1][0];
            } elseif (isset($this->name[1])) {
                $name .= $this->name[1];
            }
            // If app name is not alphabet, use app id to determine class name instead of app name
            preg_match('/^[a-z][a-z]$/', $name, $appColorClass);
            if (!count($appColorClass)) {
                $name = substr(str_replace('__module_', '', strtolower($this->id)), 0, 2);
            }
            $class_color = '_' . $name;
            switch (str_replace('__module_', '', strtolower($this->id))) {
                case 'ad':
                    $name = '<i class="fa fa-bullhorn" aria-hidden="true"></i></i>';
                    break;
                case 'announcement':
                    $name = '<i class="fa fa-paper-plane" aria-hidden="true"></i></i>';
                    break;
                case 'attachment':
                    $name = '<i class="fa fa-paperclip" aria-hidden="true"></i></i>';
                    break;
                case 'blog':
                    $name = '<i class="fa fa-pencil-square-o" aria-hidden="true"></i></i>';
                    break;
                case 'captcha':
                    $name = '<i class="fa fa-shield" aria-hidden="true"></i></i>';
                    break;
                case 'comment':
                    $name = '<i class="fa fa-commenting-o" aria-hidden="true"></i></i>';
                    break;
                case 'contact':
                    $name = '<i class="fa fa-phone" aria-hidden="true"></i></i>';
                    break;
                case 'event':
                    $name = '<i class="fa fa-calendar" aria-hidden="true"></i></i>';
                    break;
                case 'egift':
                    $name = '<i class="fa fa-gift" aria-hidden="true"></i>';
                    break;
                case 'feed':
                    $name = '<i class="fa fa-bars" aria-hidden="true"></i>';
                    break;
                case 'forum':
                    $name = '<i class="fa fa-comments-o" aria-hidden="true"></i></i>';
                    break;
                case 'friend':
                    $name = '<i class="fa fa-users" aria-hidden="true"></i></i>';
                    break;
                case 'invite':
                    $name = '<i class="fa fa-envelope-open-o" aria-hidden="true"></i></i>';
                    break;
                case 'mail':
                    $name = '<i class="fa fa-envelope-o" aria-hidden="true"></i></i>';
                    break;
                case 'marketplace':
                    $name = '<i class="fa fa-shopping-bag" aria-hidden="true"></i></i>';
                    break;
                case 'music':
                    $name = '<i class="fa fa-music" aria-hidden="true"></i></i>';
                    break;
                case 'newsletter':
                    $name = '<i class="fa fa-newspaper-o" aria-hidden="true"></i></i>';
                    break;
                case 'notification':
                    $name = '<i class="fa fa-bell-o" aria-hidden="true"></i></i>';
                    break;
                case 'pages':
                    $name = '<i class="fa fa-flag" aria-hidden="true"></i></i>';
                    break;
                case 'photo':
                    $name = '<i class="fa fa-picture-o" aria-hidden="true"></i></i>';
                    break;
                case 'poke':
                    $name = '<i class="fa fa-hand-o-right" aria-hidden="true"></i></i>';
                    break;
                case 'poll':
                    $name = '<i class="fa fa-bar-chart" aria-hidden="true"></i></i>';
                    break;
                case 'quiz':
                    $name = '<i class="fa fa-question-circle" aria-hidden="true"></i></i>';
                    break;
                case 'rss':
                    $name = '<i class="fa fa-rss" aria-hidden="true"></i>';
                    break;
                case 'subscribe':
                    $name = '<i class="fa fa-hand-o-up" aria-hidden="true"></i></i>';
                    break;
                case 'tag':
                    $name = '<i class="fa fa-tags" aria-hidden="true"></i>';
                    break;
                case 'track':
                    $name = '<i class="fa fa-eye" aria-hidden="true"></i>';
                    break;
                default:
                    break;
            }

            $this->icon = '<b class="app_icons"><i class="app_icon ' . strtolower($class_color) . '">' . $name . '</i></b>';
        } else {
            $this->icon = '<div class="app_icons image_load" data-src="' . $this->icon . '"></div>';
        }

        if (!$this->is_module) {
            $file = PHPFOX_DIR_SETTINGS . md5($this->id . \Phpfox::getParam('core.salt')) . '.php';
            if (file_exists($file)) {
                $this->auth = (object)require($file);
                $this->internal_id = (isset($this->auth->internal_id) ? $this->auth->internal_id : 0);
            }
        }

        if (is_array($this->auth)) {
            $this->auth = (object)$this->auth;
        }

        if (file_exists($this->path . 'core.json')) {
            $this->is_core = true;
        }

        if ($this->admincp_help) {
            $this->admincp_help = \Phpfox_Url::instance()->makeUrl($this->admincp_help);
        }
        if (isset($keys->_admin_cp_menu_ajax)) {
            $this->admin_cp_menu_ajax = ($keys->_admin_cp_menu_ajax) ? true : false;
        }

        if (isset($keys->external_paths)) {
            $this->external_paths = $keys->external_paths;
        }

        if (isset($keys->allow_remove_database)) {
            $this->allow_remove_database = $keys->allow_remove_database;
        }

        if (is_array($keys)) {
            $this->is_active = (isset($keys['is_active'])) ? $keys['is_active'] : true;
            $this->publisher = (isset($keys['publisher'])) ? $keys['publisher'] : 'n/a';
            if ($this->publisher == 'phpfox') {
                $this->publisher = 'phpFox';
            }
            if (isset($keys['publisher']) && strtolower($keys['publisher']) == 'phpfox') {
                $this->publisher_url = 'https://store.phpfox.com/';
            }
            if (isset($keys['allow_disable'])) {
                $this->allow_disable = $keys['allow_disable'];
            }
        } elseif (is_object($keys)) {
            /**
             * @var $keys App/App
             */
            $this->publisher = $keys->_publisher;
            $this->publisher_url = $keys->_publisher_url;
            $this->is_active = $keys->isActive();
            $this->apps_dir = $keys->_apps_dir;
        }
    }

    public function delete($val = null)
    {
        if ($this->menu && isset($this->menu->url)) {
            \Phpfox_Database::instance()->delete(':menu', ['m_connection' => 'main', 'url_value' => $this->menu->url]);
        }

        (new \Core\Home(PHPFOX_LICENSE_ID, PHPFOX_LICENSE_KEY))->uninstall([
            'product_id' => $this->internal_id,
        ]);

        $path = $this->path;
        /*https://github.com/moxi9/phpfox/issues/523*/
        $json_path = $path . 'app.json';
        if (file_exists($json_path)) {
            $json = json_decode(file_get_contents($json_path));
            //remove menu if exist
            if (isset($json->menu)) {
                \Phpfox::getService('admincp.menu.process')->delete($json->menu->url, true);
            }

            if (isset($json->alias)) {
                db()->delete(':module', ['module_id' => $json->alias]);
            }
        }

        \Phpfox_Cache::instance()->remove();
        //un-installation Code
        if (file_exists($path . 'uninstall.php')) {
            \Core\App\Installer::$method = 'OnUninstall';
            \Core\App\Installer::$basePath = $path;
            require_once($path . 'uninstall.php');
        }

        if ($this->blocks) {
            foreach ($this->blocks as $block) {
                db()->delete(':block', ['component' => $block->callback]);
                db()->delete(':cache', ['file_name' => '_apps_block_' . $block->callback]);
            }
        }

        if (is_dir($path)) {
            \Phpfox_File::instance()->delete_directory($path);
        }

        // check and remove external directories
        if (!empty($this->external_paths)) {
            foreach ($this->external_paths as $aDir) {
                if (isset($aDir->removable) && $aDir->removable) {
                    $sDir = PHPFOX_ROOT . $aDir->dir;
                    if (is_dir($sDir)) {
                        \Phpfox_File::instance()->delete_directory($sDir);
                    } else {
                        \Phpfox_File::instance()->unlink($sDir);
                    }
                }
            }
        }
    }

    /**
     * @param $object
     * @param $feed
     *
     * @return Map
     */
    public function map($object, $feed)
    {
        if (substr($object, 0, 1) == '{') {
            $object = json_decode($object);
        }

        return new Map($this, (object)$object, $this->map, $feed);
    }

    /**
     * Export application
     *
     * @return string
     */
    public function export()
    {
        $zipFile = PHPFOX_DIR_FILE . 'static' . PHPFOX_DS . 'package' . PHPFOX_DS . 'phpfox-app-' . $this->id . '.zip';
        $paths = [$this->path];
        $helper = new FileHelper();

        $externalDirs = array_map(function ($dir) {
            return PHPFOX_ROOT . $dir->dir;
        }, $this->external_paths);
        // create checksum
        $helper->createChecksum($this->path, array_merge($paths, $externalDirs));

        $packageInformation = [
            'id' => $this->id,
            'apps_dir' => $this->apps_dir,
            'internal_id' => $this->internal_id,
            'version' => $this->version,
            'current_version' => $this->currentVersion,
            'name' => $this->name,
            'module' => $this->is_module,
            'type' => 'app',
            'icon' => $this->icon,
            'is_core' => $this->is_core,
        ];

        if (!empty($this->external_paths)) {
            foreach ($this->external_paths as $aDir) {
                $paths[] = PHPFOX_ROOT . trim($aDir->dir, PHPFOX_DS);
            }
            $packageInformation = array_merge($packageInformation, ['external_paths' => $this->external_paths]);
        }

        $helper->export($zipFile, $paths, [], $packageInformation, $this->id, $this->apps_dir);
        $name = strtolower(basename(trim($this->path, '/')));

        \Phpfox_File::instance()->forceDownload($zipFile, 'phpfox-app-' . $name . '.zip');

        return $zipFile;
    }

    /**
     * Build checksum for app
     */
//    private function _updateChecksum()
//    {
//        $buildDir = $this->path;
//        // app dir
//        $content = $this->_createChecksum($buildDir);
//        // external paths
//        foreach ($this->external_paths as $dir) {
//            $content .= $this->_createChecksum(PHPFOX_ROOT . $dir->dir);
//        }
//        // update checksum file
//        file_put_contents($buildDir . 'checksum', $content);
//    }

    public function getAdmincpRoute()
    {
        if ($this->admincp_route) {
            return $this->admincp_route;
        } elseif ($this->settings) {
            return \Phpfox::getLib('url')->makeUrl('admincp.app.settings', ['id' => $this->id]);
        } elseif ($this->user_group_settings) {
            $oAppInit = \Core\Lib::appInit($this->id);

            $sAppAlias = $oAppInit->alias ? $oAppInit->alias : $oAppInit->id;

            return \Phpfox::getLib('url')->makeUrl('admincp.user.group.add',
                ['group_id' => 2, 'module' => $sAppAlias, 'setting' => 1, 'hide_app' => 1]);
        }
    }

    public function __toArray()
    {

    }
}