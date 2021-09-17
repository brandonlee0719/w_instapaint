<?php

namespace Core;

use Core\App\Object;
use Core\Route\Controller as RouteController;
use Phpfox_Module;

/**
 * Class App
 *
 * @package Core
 */
class App
{
    static private $refreshed =  false;
    /**
     * @var array
     */
    public static $routes = [];

    /**
     * @var App\App[]
     */
    private static $_apps = [];

    /**
     * List of core apps/modules. It doesn't display anywhere on backend.
     *
     * @var array
     */
    private $_aCoreApps
        = [
            'like',
            //        'notification',
            'admincp',
            'api',
            'ban',
            'core',
            'custom',
            'error',
            'language',
            'link',
            'log',
            'page',
            'privacy',
            'profile',
            'report',
            'request',
            'search',
            'share',
            'theme',
            'user',
        ];

    private $_NotAllowDisable
        = [
            'photo',
            'Core_Photos',
        ];

    /**
     * @param bool $refresh
     *
     * @return \array[]
     * @todo improve cache
     */
    public function getAllAppFromDatabase($refresh = false)
    {
        if ($refresh) {
            return $aRows = \Phpfox::getLib('database')
                ->select('*')
                ->from(':apps')
                ->execute('getSlaveRows');
        }
        return get_from_cache(['lib', 'all_apps'], function () {
            return $aRows = \Phpfox::getLib('database')
                ->select('*')
                ->from(':apps')
                ->execute('getSlaveRows');
        });
    }

    public function getAllPsr4Namespace($aRows)
    {
        $allNamespaces = [];
        foreach ($aRows as $row) {
            $sAppId = $row['apps_id'];
            $sAppDir = $row['apps_dir'];

            if (!$sAppDir) {
                $sAppDir = $sAppId;
            }
            $namespace = 'Apps\\' . $sAppId . '\\';
            $allNamespaces[$namespace] = 'PF.Site' . PHPFOX_DS . 'Apps'
                . PHPFOX_DS . $sAppDir;
        }

        return $allNamespaces;
    }

    /**
     * @param $aAppNamespaces
     *
     */
    public function initAutoload($aAppNamespaces)
    {
        $autoloader = include PHPFOX_DIR . 'vendor' . PHPFOX_DS
            . 'autoload.php';

        if (isset($_REQUEST['rename_on_upgrade']) && !empty($_REQUEST['apps_dir']) && !empty($_REQUEST['apps_id'])) {
            $aAppNamespaces[sprintf('Apps\\%s\\', $_REQUEST['apps_id'])] = 'PF.Site' . PHPFOX_DS . 'Apps' . PHPFOX_DS . $_REQUEST['apps_dir'];
        }

        foreach ($aAppNamespaces as $namespace => $path) {
            $autoloader->addPsr4($namespace, PHPFOX_PARENT_DIR . $path);
        }
    }

    public function __construct($refresh = false)
    {
        if(self::$refreshed)
            $refresh = false;

        if(!self::$refreshed and $refresh == false){
            $cache = new Cache();
            $settings = $cache->get('app_settings');
            if (is_bool($settings)) {
                self::$refreshed =  $refresh = true;
            }
        }

        $base = PHPFOX_DIR_SITE . 'Apps' . PHPFOX_DS;
        if (!is_dir($base)) {
            self::$_apps = [];
            return;
        }

        if (!empty(self::$_apps) and !$refresh) {
            return;
        }

        self::$_apps = [];

        $allApps = $this->getAllAppFromDatabase($refresh);
        $aAppNamespaces = $this->getAllPsr4Namespace($allApps);
        $this->initAutoload($aAppNamespaces);
        foreach ($allApps as $aApp) {
            $app = $aApp['apps_id'];
            $dir = (isset($aApp['apps_dir']) && !empty($aApp['apps_dir'])) ? $aApp['apps_dir'] : $aApp['apps_id'];

            if (in_array($app,
                ['phpFox_Single_Device_Login',
                    'phpFox_Shoutbox',
                    'phpFox_CKEditor',
                    'phpFox_RESTful_API',
                    'phpFox_Backup_Restore',
                    'Core_BetterAds',
                    'PHPfox_Videos',
                    'PHPfox_IM',
                    'PHPfox_CDN_Service'
                ])) {
                if (!\Phpfox::isPackage(3) && (!defined('PHPFOX_TRIAL_MODE') || !PHPFOX_TRIAL_MODE)) {
                    if (\Phpfox::isUser()
                        && \Phpfox::getUserParam('admincp.has_admin_access')
                    ) {
                        \Phpfox::getService('admincp.module.process')
                            ->updateActivity($app, 0, false);
                    }

                    continue;
                }
            }

            $appInfo = Lib::appInit($app);

            if (!$appInfo) {
                continue;
            }

            if (!$appInfo->isActive()) {
                continue;
            }

            if (file_exists($vendor_file = $appInfo->path . 'vendor/autoload.php')) {
                require_once($vendor_file);
            }

            RouteController::$active = $appInfo->path;
            RouteController::$activeId = $appInfo->id;
            self::$_apps[$appInfo->id] = $appInfo;

            if (file_exists($start_filename = $appInfo->path . 'start.php')) {
                $callback = require_once($start_filename);
                if (is_callable($callback)) {
                    $View = new View();
                    $viewEnv = null;
                    if (is_dir($appInfo->path . 'views/')) {
                        $View->loader()->addPath($appInfo->path . 'views/', $appInfo->id);
                        $viewEnv = $View->env();
                    }
                    call_user_func($callback, $this->get($appInfo->id),
                        $viewEnv);
                }
            }

            if (isset($appInfo->routes)) {
                foreach ((array)$appInfo->routes as $key => $route) {
                    $orig = $route;
                    $route = (array)$route;
                    $route['id'] = $appInfo->id;
                    if (is_string($orig)) {
                        $route['url'] = $orig;
                    }
                    Route::$routes = array_merge(Route::$routes,
                        [$key => $route]);
                }
            }


        }

        $settings = [];
        $cache = new Cache();

        foreach ($this->all() as $app) {
            if ($app->blocks) {
                $blocks = [];
                foreach ($app->blocks as $block) {
                    $blocks[$block->route][$block->location][]
                        = $block->callback;
                }
                \Core\Block\Group::make($blocks);
            }


            if ($refresh && $app->settings) {
                foreach (json_decode(json_encode($app->settings), true) as $key => $value) {
                    $thisValue = (isset($value['value']) ? $value['value'] : null);
                    $value = (new \Core\Db())->select('*')->from(':setting')
                        ->where(['var_name' => $key])->get();
                    if (isset($value['value_actual'])) {
                        $thisValue = $value['value_actual'];
                    }
                    $settings[$key] = $thisValue;
                }
            }
        }

        if ($refresh and $settings) {
            $cache->set('app_settings', $settings);

            new Setting($cache->get('app_settings'));
        }

        if (function_exists('flavor')) {
            $forceFlavor = \Phpfox_Request::instance()->get('force-flavor');
            if ($forceFlavor) {
                flavor()->set_active($forceFlavor);
            }
            if (flavor()->active) {
                $start = flavor()->active->path . 'start.php';
                if (file_exists($start)) {
                    require_once($start);
                }
            }
        }
    }

    public function vendor()
    {

    }

    public function add($id)
    {
        if (is_string($id)) {
            $app = Lib::appInit($id);
        } elseif ($id instanceof Object) {
            $app = $id;
        }

        if (!$app) {
            exit("Apps not found  [$id]");
        }

        return self::$_apps[$app->id] = $app;
    }

    public function make($name)
    {
        ignore_user_abort(true);

        $base = PHPFOX_DIR_SITE . 'Apps/';
        $gitFile = null;
        $git = '';
        $url = '';

        if (!preg_match('/^[a-zA-Z\_][a-zA-Z\_0-9]+$/', $name)) {
            throw new \Exception(_p('app_name_validation_message'));
        }


        $appBase = $base . $name . '/';
        if (is_dir($appBase)) {
            throw new \Exception('App already exists.');
        }

        $dirs = [
            'Block',
            'Controller',
            'Service',
            'assets',
            'hooks',
            'views',
        ];
        foreach ($dirs as $dir) {
            $path = $appBase . $dir;
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }
        }

        \Core\App\Migrate::migrate($name, true);

        file_put_contents($appBase . 'assets/autoload.js',
            "\n\$Ready(function() {\n\n});");
        file_put_contents($appBase . 'assets/autoload.css', "\n");
        file_put_contents($appBase . 'start.php', "<?php\n");

        $lockPath = $appBase . 'app.lock';
        $lock = json_encode(['installed' => PHPFOX_TIME, 'version' => 0],
            JSON_PRETTY_PRINT);
        file_put_contents($lockPath, $lock);

        (new \Core\Cache())->purge();

        $App = new App(true);

        $Object = $App->get($name);

        $this->makeKey($Object, md5(uniqid()), md5(uniqid() . rand(0, 10000)));

        return $Object;
    }

    public function makeKey(App\Object $App, $id, $key, $internalId = 0)
    {
        $file = PHPFOX_DIR_SETTINGS . md5($App->id
                . \Phpfox::getParam('core.salt')) . '.php';

        $response = [
            'id'          => $id,
            'key'         => $key,
            'version'     => $App->version,
            'internal_id' => $internalId,
        ];
        $paste = "<?php\nreturn " . var_export((array)$response, true) . ';';

        file_put_contents($file, $paste);
    }

    /**
     * @param null $zip
     *
     * @param bool $download
     * @param bool $isUpgrade
     *
     * @return Object
     * @throws \Exception
     */
    public function import($zip = null, $download = false, $isUpgrade = false)
    {
        if ($zip === null || empty($zip)) {
            $zip = PHPFOX_DIR_FILE . 'static' . PHPFOX_DS . 'import-' . uniqid()
                . '.zip';
            register_shutdown_function(function () use ($zip) {
                unlink($zip);
            });

            if (isset($_FILES['ajax_upload'])) {
                file_put_contents($zip,
                    file_get_contents($_FILES['ajax_upload']['tmp_name']));
            } else {
                file_put_contents($zip, file_get_contents('php://input'));
            }
        }

        if ($download) {
            $zipUrl = $zip;
            $zip = PHPFOX_DIR_FILE . 'static' . PHPFOX_DS . 'import-' . uniqid()
                . '.zip';
            register_shutdown_function(function () use ($zip) {
//				unlink($zip);
            });

            file_put_contents($zip, fox_get_contents($zipUrl));
        }

        $fromWindows = false;
        $archive = new \ZipArchive();
        $archive->open($zip);
        $json = $archive->getFromName('/Install.php');

        if (!$json) {
            $json = $archive->getFromName('Install.php');
        }

        if (!$json) {
            $json = $archive->getFromName('\\Install.php');
            $fromWindows = true;
        }

        $json = json_decode($json);
        if (!isset($json->id)) {
            throw error(_p('Not a valid App to install.'));
        }

        $base = PHPFOX_DIR_SITE . 'Apps/' . $json->id . '/';
        if (!is_dir($base)) {
            mkdir($base, 0777, true);
        }

        $archive->close();
        $appPath = $base . 'import-' . uniqid() . '.zip';
        copy($zip, $appPath);

        $newZip = new \ZipArchive();
        $newZip->open($appPath);
        $newZip->extractTo($base);
        $newZip->close();

        register_shutdown_function(function () use ($appPath) {
            unlink($appPath);
        });

        $check = $base . 'app.json';
        if (!file_exists($check)) {
            throw new \Exception('App was unable to install.');
        }

        $lockPath = $base . 'app.lock';
        if (!$isUpgrade && file_exists($lockPath)) {
            unlink($lockPath);
        }

        $isNew = false;
        if (file_exists($lockPath)) {
            $lock = json_decode(file_get_contents($lockPath));
            $lock->updated = PHPFOX_TIME;
            file_put_contents($lockPath, json_encode($lock, JSON_PRETTY_PRINT));
        } else {
            $isNew = true;

            $this->processJson($json, $base);

            $lock = json_encode([
                'installed' => PHPFOX_TIME,
                'version'   => $json->version,
            ], JSON_PRETTY_PRINT);
            file_put_contents($lockPath, $lock);
        }

        $CoreApp = new \Core\App(true);
        $Object = $CoreApp->get($json->id);

        if ($isNew) {
            $Request = \Phpfox_Request::instance();
            $internalId = 0;
            if ($Request->get('product')) {
                $product = json_decode($Request->get('product'));
                $internalId = $product->id;
            }
            $this->makeKey($Object, $Request->get('auth_id'),
                $Request->get('auth_key'), $internalId);
        }

        return $Object;
    }

    public function processUpgrade($json, $base)
    {
        if (file_exists($base . 'installer.php')) {
            \Core\App\Installer::$method = 'onInstall';
            \Core\App\Installer::$basePath = $base;

            require_once($base . 'installer.php');
        }
    }

    /**
     * @deprecated
     *
     * @param $json
     * @param $base
     *
     * @return bool
     */
    public function processJson($json, $base)
    {
        return false;
    }

    /**
     * @param $id
     *
     * @return App\Object|null
     */
    public function getByInternalId($id)
    {
        foreach ($this->all() as $app) {
            if ($app->internal_id == $id) {
                return $app;
            }
        }
        return null;
    }

    /**
     * @param string $id
     *
     * @return App\Object
     * @throws \Exception
     */
    public function get($id)
    {
        if (substr($id, 0, 9) == '__module_') {
            $id = substr_replace($id, '', 0, 9);
            $db = new \Core\Db();
            $module = $db->select('m.*')
                ->from(':module', 'm')
                ->where(['m.module_id' => $id])
                ->get();

            if ($module['product_id'] == 'phpfox') {
                $module['version'] = \Phpfox::getVersion();
            }

            $app = [
                'id'        => '__module_' . $id,
                'name'      => ($module['phrase_var_name']
                    && ($module['product_id'] != 'phpfox'))
                    ? _p($module['phrase_var_name'])
                    : \Phpfox_Locale::instance()->translate($id, 'module'),
                'path'      => null,
                'is_active' => $module['is_active'],
                'module_id' => $id,
                'is_module' => true,
                'version'   => $module['version'],
                'icon'      => (!empty($module['apps_icon']))
                    ? $module['apps_icon'] : null,
                'vendor'    => (!empty($module['vendor'])) ? $module['vendor']
                    : null,
            ];
        } elseif (!isset(self::$_apps[$id])) {
            throw new \Exception(sprintf('App not found "%s"', $id));
        } else {
            $app = self::$_apps[$id];
        }
        return new App\Object($app);
    }

    /**
     * @param bool|string $includeModules
     *
     * @return App\Object[]
     */
    public function all($includeModules = false)
    {
        $apps = [];
        if ($includeModules) {
            $modules = Phpfox_Module::instance()->all();
            $skip = $this->_aCoreApps;
            foreach ($modules as $module_id) {
                if (in_array($module_id, $skip)) {
                    continue;
                }

                $coreFile = PHPFOX_DIR_MODULE . $module_id
                    . '/install/version/v3.phpfox';
                if ($includeModules == '__core') {
                    if (!file_exists($coreFile)) {
                        continue;
                    }
                } else {
                    if ($includeModules == '__not_core'
                        || $includeModules == '__remove_core'
                    ) {
                        if (file_exists($coreFile)) {
                            continue;
                        }
                    }
                }

                $aModule = \Phpfox::getService('admincp.module')
                    ->getForEdit($module_id);
                if ($aModule['phrase_var_name'] == 'module_apps') {
                    continue;
                }
                $aProduct = ($aModule && !empty($aModule['product_id']))
                    ? \Phpfox::getService('admincp.product')
                        ->getForEdit($aModule['product_id']) : [];
                $app = [
                    'id'        => '__module_' . $module_id,
                    'name'      => ($aProduct
                        && ($aModule['product_id'] != 'phpfox')
                        && $aProduct['title'])
                        ? $aProduct['title']
                        : \Phpfox_Locale::instance()
                            ->translate($module_id, 'module'),
                    'path'      => null,
                    'is_module' => true,
                    'icon'      => (!empty($aProduct['icon']))
                        ? $aProduct['icon'] : null,
                    'vendor'    => (!empty($aProduct['vendor']))
                        ? $aProduct['vendor'] : null,
                ];

                $apps[] = new App\Object($app);
            }

            if ($includeModules == '__core'
                || $includeModules == '__not_core'
            ) {
                return $apps;
            }
        }

        foreach (self::$_apps as $app) {
            $apps[] = new App\Object($app);
        }

        return $apps;
    }

    public function processRow($app)
    {
        if ($app['type'] == 'module') {
            $oAppDetail = [
                'id'            => '__module_' . $app['id'],
                'name'          => _p($app['name']),
                'path'          => null,
                'is_module'     => true,
                'is_active'     => $app['is_active'],
                'icon'          => (!empty($app['icon'])) ? $app['icon'] : null,
                'vendor'        => (!empty($app['vendor'])) ? $app['vendor']
                    : null,
                'publisher'     => $app['publisher'],
                'allow_disable' => (in_array($app['id'], $this->_NotAllowDisable)) ? false : true,
                'version'       => $app['version'],
            ];
        } else {
            $oAppDetail = Lib::appInit($app['id']);
        }

        $oAppObject = new App\Object($oAppDetail);
        $oAppObject->version = $app['version'];
        $oAppObject->allow_disable = (in_array($app['id'], $this->_NotAllowDisable)) ? false : true;
        if (!empty($app['publisher'])) {
            $oAppObject->publisher = $app['publisher'];
        }
        $oAppObject->publisher_url = $app['vendor'];
        return $oAppObject;
    }

    /**
     * Get all modules and apps (included disabled)
     *
     * @return array
     */
    public function getForManage()
    {
        $sCoreApps = implode($this->_aCoreApps, "','");
        $oDb = db();
        $oDb->select('apps_icon as icon, module_id AS id, version, author as publisher, vendor, phrase_var_name as name, is_active, \'module\' AS type')
            ->from(":module")
            ->where("module_id NOT IN ('" . $sCoreApps
                . "') AND phrase_var_name!='module_apps'")
            ->union();
        $oDb->select('apps_icon as icon, apps_id as id, version, author as publisher, vendor,  apps_name as name, is_active, \'app\' AS type')
            ->from(':apps')
            ->union();

        $rows = array_map(function ($item) {
            return $this->processRow($item);
        }, $oDb->executeRows());

        uasort($rows, function ($a, $b) {
            return strtolower($a->name) > strtolower($b->name);
        });

        return $rows;
    }

    public function exists($id, $bReturnId = false)
    {
        return (isset(self::$_apps[$id]) ? ($bReturnId ? $id : true) : false);
    }
}