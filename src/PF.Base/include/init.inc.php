<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright         [PHPFOX_COPYRIGHT]
 * @author            Raymond Benc
 * @package           Phpfox
 * @version           $Id: init.inc.php 6619 2013-09-11 12:08:49Z Miguel_Espinoza $
 */

defined('PHPFOX') or exit('NO DICE!');

@ini_set('memory_limit', '-1');
@ini_set('default_charset', "UTF-8");

//@set_time_limit(0);

require(PHPFOX_DIR . 'include' . PHPFOX_DS . 'library' . PHPFOX_DS . 'phpfox' . PHPFOX_DS . 'functions' . PHPFOX_DS . 'fallback.php');

/**
 * compatible php70
 */
defined('MYSQLI_BOTH') or define('MYSQLI_BOTH',3);
defined('MYSQLI_NUM') or define('MYSQLI_NUM',2);
defined('MYSQLI_ASSOC') or define('MYSQLI_ASSOC',1);
defined('MYSQL_BOTH') or define('MYSQL_BOTH',MYSQLI_BOTH);
defined('MYSQL_NUM') or define('MYSQL_NUM',MYSQLI_NUM);
defined('MYSQL_ASSOC') or define('MYSQL_ASSOC',MYSQLI_ASSOC);

/**
 * Config php 5.6
 *
 * @link http://php.net/manual/en/ini.core.php#ini.default-charset
 */
if (version_compare(PHP_VERSION, '5.6', '<')) {
    if (function_exists('mb_internal_encoding')) {
        mb_internal_encoding("UTF-8");
    }

    if (function_exists('iconv_set_encoding')) {
        // Not sure if we want to do all of these
        iconv_set_encoding("input_encoding", "UTF-8");
        iconv_set_encoding("output_encoding", "UTF-8");
        iconv_set_encoding("internal_encoding", "UTF-8");
    }
}

if (!isset($_SERVER['HTTP_USER_AGENT'])) {
    $_SERVER['HTTP_USER_AGENT'] = '';
}

// Start the debug
define('PHPFOX_MEM_START', memory_get_usage());
define('PHPFOX_TIME_START', array_sum(explode(' ', microtime())));

// Fix for foreign characters when server is set to receive other charset (http://www.w3.org/International/O-HTTP-charset)
header('Content-type: text/html; charset=utf-8');

if (file_exists(PHPFOX_DIR . 'file/settings/debug.sett.php') && !defined('PHPFOX_DEBUG')) {
    require(PHPFOX_DIR . 'file/settings/debug.sett.php');
}

require_once(PHPFOX_DIR . 'include' . PHPFOX_DS . 'setting' . PHPFOX_DS . 'constant.sett.php');

$old = PHPFOX_DIR . '../include/setting/server.sett.php';
if (!file_exists(PHPFOX_DIR_SETTINGS . 'license.sett.php')
    || !file_exists(PHPFOX_DIR_SETTINGS . 'version.sett.php')
    || !file_exists(PHPFOX_DIR_SETTINGS . 'server.sett.php')
    || file_exists(PHPFOX_DIR_SETTINGS . 'install.sett.php')
    ) {
    defined('PHPFOX_NO_PLUGINS') or define('PHPFOX_NO_PLUGINS', true);
    defined('PHPFOX_NO_USER_SESSION') or define('PHPFOX_NO_USER_SESSION', true);
    defined('PHPFOX_NO_CSRF') or define('PHPFOX_NO_CSRF', true);
    defined('PHPFOX_INSTALLER') or define('PHPFOX_INSTALLER', true);
    defined('PHPFOX_INSTALLER_NO_TMP') or define('PHPFOX_INSTALLER_NO_TMP', true);
    defined('PHPFOX_NO_RUN') or define('PHPFOX_NO_RUN', true);

    if (file_exists($old)
        && !defined('PHPFOX_IS_UPGRADE')
        && !class_exists('Phpfox_Installer', false)
    ) {
        define('PHPFOX_IS_UPGRADE', true);
        if (!defined('PHPFOX_DEBUG')) {
            define('PHPFOX_DEBUG', false);
        }
    }
} else {
    require(PHPFOX_DIR_SETTINGS . 'license.sett.php');
}

// Set error reporting enviromenta
error_reporting((PHPFOX_DEBUG ? E_ALL | E_STRICT : 0));

//check trial
if ((function_exists('ioncube_file_info') && is_array(ioncube_file_info()))) {
    if ((defined('PHPFOX_LICENSE_ID') && PHPFOX_LICENSE_ID != '')) {
    } else {
        define('PHPFOX_TRIAL_MODE', true);
        $i = ioncube_file_info();
        $date = $i['FILE_EXPIRY'];
        // $date = strtotime('+2 days', time());
        $datetime1 = new DateTime();
        $datetime2 = new DateTime(date('Y-m-d', $date));
        $interval = $datetime1->diff($datetime2);
        define('PHPFOX_TRIAL_EXPIRES', $interval->format("%r%a"));
        if (PHPFOX_TRIAL_EXPIRES < 0) {
            exit('phpFox trial has expired. Make a purchase for a license <a href="https://www.phpfox.com/">here</a>.');
        }
    }
}

spl_autoload_register(function ($class){
    static $class_map;

    if(!$class_map){
        $class_map =  include __DIR__ .'/class_map.config.php';
    }

    if(substr($class, 0,7) != 'Phpfox_')
        return false;

    $key = str_replace('_', '.', strtolower($class));

    if(!isset($class_map[$key]))
        return false;

    include __DIR__ .'/' .  $class_map[$key];

    return true;

});

spl_autoload_register(function ($class){

    if (strpos($class, '_')){
        $parts = explode('_', strtolower($class));
        $module =  array_shift($parts);
        $file =  PHPFOX_DS . implode(PHPFOX_DS, $parts) . '.class.php';

        if (file_exists($filename =PHPFOX_DIR_MODULE . $module . PHPFOX_DS . 'include' . $file)) {
            require($filename);
            return true;
        }

        if(file_exists($filename =PHPFOX_DIR_LIB_CORE . $module . PHPFOX_DS . $file)){
            require($filename);
            return true;
        }
    }

    $name = str_replace("\\", '/', strtolower($class));

    if (substr($name, 0, 5) == 'core/'
        || substr($name, 0, 12) == 'controllers/'
        || substr($name, 0, 4) == 'api/'
    ) {
        $class = str_replace("\\", '/', $class);
        $dir = PHPFOX_DIR_SRC;

        $path = $dir . $class . '.php';

        if(file_exists($path)){
            require($path);
            return true;
        }
        return false;
    }

    if (preg_match('/([a-zA-Z0-9]+)_service_([a-zA-Z0-9_]+)/', $name, $matches)) {
        $parts = explode('_', $matches[2]);
        if (count($parts) > 1) {
            if ($parts[0] == $parts[1]) {
                unset($parts[1]);
            }
        }
        $className = $matches[1] . '.' . implode('.', $parts);

        Phpfox::getService($className);
    }
});

require(PHPFOX_DIR_LIB_CORE . 'phpfox' . PHPFOX_DS . 'phpfox.class.php');
require(PHPFOX_DIR_LIB_CORE . 'error' . PHPFOX_DS . 'error.class.php');
require(PHPFOX_DIR_LIB_CORE . 'module' . PHPFOX_DS . 'service.class.php');
require(PHPFOX_DIR_LIB_CORE . 'module' . PHPFOX_DS . 'component.class.php');

// No need to load the debug class if the debug is disabled
if ((!defined('PHPFOX_DEBUG_ON_SCREEN') || PHPFOX_DEBUG_ON_SCREEN == false)) {

    (new Core\ErrorHandler(E_ERROR | E_COMPILE_ERROR |
        E_COMPILE_WARNING | E_CORE_ERROR | E_CORE_WARNING | E_ERROR |
        E_PARSE | E_RECOVERABLE_ERROR | E_USER_ERROR))->register();
}

date_default_timezone_set('GMT');

define('PHPFOX_TIME', time());

$version = PHPFOX_DIR_SETTINGS . 'version.sett.php';
if (file_exists($version)) {
    $version = require($version);
    
    // remove these lines on 4.6.0 official.
    if($version['version'] == '4.6.0' and Phpfox::VERSION == '4.6.0-rc1'){ // rc-only.
        $version['version'] =  '4.6.0-beta1';
    }
    if (version_compare(Phpfox::VERSION, $version['version'], '>')) {
        defined('PHPFOX_NO_PLUGINS') or define('PHPFOX_NO_PLUGINS', true);
        defined('PHPFOX_NO_USER_SESSION') or define('PHPFOX_NO_USER_SESSION', true);
        defined('PHPFOX_NO_CSRF') or define('PHPFOX_NO_CSRF', true);
        defined('PHPFOX_INSTALLER') or define('PHPFOX_INSTALLER', true);
        defined('PHPFOX_INSTALLER_NO_TMP') or define('PHPFOX_INSTALLER_NO_TMP', true);
        defined('PHPFOX_NO_RUN') or define('PHPFOX_NO_RUN', true);
        defined('PHPFOX_IS_UPGRADE') or define('PHPFOX_IS_UPGRADE', true);
    }
}

Phpfox::getLib('setting')->set();

if (!defined('PHPFOX_INSTALLER')
    && file_exists(PHPFOX_DIR_SETTINGS . 'redirection.sett.php')
    && isset($_SERVER['HTTP_HOST'])
    && $_SERVER['HTTP_HOST'] != Phpfox::getParam('core.host')
) {
    $page_url = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $page_url .= Phpfox::getParam('core.host') . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $page_url .= Phpfox::getParam('core.host') . $_SERVER["REQUEST_URI"];
    }
    header('Location: ' . $page_url);
    exit;
}

if (defined('PHPFOX_INSTALLER')) {
    if (isset($_GET['phpfox-upgrade']) || !defined('PHPFOX_IS_UPGRADE')) {
        $autoloader = include PHPFOX_DIR . 'vendor' . PHPFOX_DS . 'autoload.php';
        $allNamespaces = [
            'Apps\\PHPfox_AmazonS3\\' => 'PF.Site' . PHPFOX_DS . 'Apps' . PHPFOX_DS . 'core-amazon-s3',
            'Apps\\PHPfox_CDN\\' => 'PF.Site' . PHPFOX_DS . 'Apps' . PHPFOX_DS . 'core-cdn',
            'Apps\\PHPfox_CDN_Service\\' => 'PF.Site' . PHPFOX_DS . 'Apps' . PHPFOX_DS . 'core-cdn-service',
            'Apps\\PHPfox_Core\\' => 'PF.Site' . PHPFOX_DS . 'Apps' . PHPFOX_DS . 'core',
            'Apps\\PHPfox_Facebook\\' => 'PF.Site' . PHPFOX_DS . 'Apps' . PHPFOX_DS . 'core-facebook',
            'Apps\\PHPfox_Flavors\\' => 'PF.Site' . PHPFOX_DS . 'Apps' . PHPFOX_DS . 'core-flavors',
            'Apps\\PHPfox_Groups\\' => 'PF.Site' . PHPFOX_DS . 'Apps' . PHPFOX_DS . 'core-groups',
            'Apps\\PHPfox_IM\\' => 'PF.Site' . PHPFOX_DS . 'Apps' . PHPFOX_DS . 'core-im',
            'Apps\\PHPfox_Twemoji_Awesome\\' => 'PF.Site' . PHPFOX_DS . 'Apps' . PHPFOX_DS . 'core-twemoji-awesome',
            'Apps\\PHPfox_Videos\\' => 'PF.Site' . PHPFOX_DS . 'Apps' . PHPFOX_DS . 'core-videos',
        ];
        foreach ($allNamespaces as $namespace => $path) {
            $autoloader->setPsr4($namespace, PHPFOX_PARENT_DIR . $path);
        }

        require(PHPFOX_DIR . 'install/include/installer.class.php');
        (new Phpfox_Installer())->run();
        exit;
    }

    $sMessage = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
    $sMessage .= '<html xmlns="http://www.w3.org/1999/xhtml" lang="en">';
    $sMessage .= '<head><title>Upgrade Taking Place</title><meta http-equiv="Content-Type" content="text/html;charset=utf-8" /><style type="text/css">body{font-family:verdana; color:#000; font-size:9pt; margin:5px; background:#fff;} img{border:0px;}</style></head><body>';
    $sMessage .= file_get_contents(PHPFOX_DIR . 'static' . PHPFOX_DS . 'upgrade.html');
    $sMessage .= '</body></html>';
    echo $sMessage;
    exit;
}

if (!defined('PHPFOX_NO_PLUGINS')) {
    Phpfox_Plugin::set();
}

if (!defined('PHPFOX_INSTALLER')) {
    new Core\App();
}

if (Phpfox_Request::instance()->get('ping-no-session')) {
    define('PHPFOX_NO_SESSION', true);
    define('PHPFOX_NO_APPS', true);
}

// Start a session if needed
if (!defined('PHPFOX_NO_SESSION')) {
    Phpfox_Session_Handler::instance()->init();
}

if (!defined('PHPFOX_NO_USER_SESSION')) {
    Phpfox::getService('log.session')->setUserSession();
}

// check if user already verified their email
if (!defined('PHPFOX_CLI') || PHPFOX_CLI != true) {
    Phpfox::getService('user.auth')->handleStatus();
}

(($sPlugin = Phpfox_Plugin::get('init')) ? eval($sPlugin) : false);

(PHPFOX_DEBUG ? Phpfox_Debug::end('init') : false);
