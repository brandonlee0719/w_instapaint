<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright         [PHPFOX_COPYRIGHT]
 * @author            Raymond Benc
 * @package           Phpfox
 * @version           $Id: index.php 7004 2013-12-20 14:23:28Z Raymond_Benc $
 */

if (version_compare(phpversion(), '5.5', '<') === true) {
    exit('phpFox 4 requires PHP 5.5 or newer.');
}

ob_start();

if (!defined('PHPFOX_NO_SESSION')) {
    if (function_exists('ini_set')) {
        ini_set('session.cookie_httponly', true);
    }
}
if (!defined('PHPFOX')) {
    define('PHPFOX', true);
    define('PHPFOX_DS', DIRECTORY_SEPARATOR);
    define('PHPFOX_DIR', dirname(__FILE__) . PHPFOX_DS);
    define('PHPFOX_START_TIME', array_sum(explode(' ', microtime())));
}

defined('PHPFOX_UNIT_TEST') or define('PHPFOX_UNIT_TEST', false);


defined('PHPFOX_PARENT_DIR') or define('PHPFOX_PARENT_DIR', realpath(__DIR__ . '/../') . PHPFOX_DS);

if (!empty($_SERVER['REQUEST_URI'])) {
    $flavor_id = (isset($_COOKIE['_flavor_id']) ? $_COOKIE['_flavor_id'] : 'bootstrap') . PHPFOX_DS;
    $language_id = (isset($_COOKIE['_language_id']) ? $_COOKIE['_language_id'] : 'en') . PHPFOX_DS;
    $content_type = ((isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] == 'application/json') ? 'ajax'
            : 'html') . PHPFOX_DS;
    $the_path = PHPFOX_DS . gmdate('y/m/d/') . $language_id . $flavor_id . $content_type . md5($_SERVER['REQUEST_URI']);
    $the_cache = PHPFOX_DIR . 'file' . PHPFOX_DS . 'http_cache' . $the_path . '.json';
} else {
    $the_path = '';
    $the_cache = '';
}


if (isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) == 'GET' && file_exists($the_cache)) {
    $last_modified_time = filemtime($the_cache);
    $etag = md5_file($the_cache);

    $content = json_decode(file_get_contents($the_cache));

    header("Expires: -1");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s", $last_modified_time) . " GMT");
    header("Etag: $etag");
    header("Cache-Control: must-revalidate");
    header('Pragma: cache');
    header('Content-type: ' . $content->type . '; charset=utf-8');
    header('X_PF_CACHE_SUCCESS: ' . $the_path);

    if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified_time
        || @trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag
    ) {
        header("HTTP/1.1 304 Not Modified");
        exit;
    }

    echo $content->data;
    exit;
} else {
    header('X_PF_CACHE_FAILED: ' . $the_path);
}

if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    exit('Dependencies for phpFox missing. Make sure to run composer first.');
}

if (isset($_SERVER['REQUEST_METHOD'])) {
    if ($_SERVER['REQUEST_METHOD'] == 'PUT' || $_SERVER['REQUEST_METHOD'] == 'DELETE') {
        parse_str(file_get_contents('php://input'), $_REQUEST);
    }
}

include(__DIR__ . '/vendor/autoload.php');
include(__DIR__ . '/include/init.inc.php');
include_once(__DIR__ . '/../PF.Src/Core/Flavor/Flavor.php');

//define('C3_CODECOVERAGE_ERROR_LOG_FILE', __DIR__ .'/file/c3/c3_error.log');
//include __DIR__. '/vendor/codeception/c3/c3.php';


/**
 * @param string $element
 *
 * @return \Core\jQuery
 */
function j($element)
{
    return new Core\jQuery($element);
}

/**
 * @param string $key
 *
 * @return mixed
 */
function param($key)
{
    return Phpfox::getParam($key);
}

/**
 * @param null|string $key
 * @param null|string $default
 *
 * @return \Core\Setting|mixed|null
 */
function setting($key = null, $default = null)
{
    if ($key === null) {
        return Core\Lib::setting();
    }

    $Setting = Core\Lib::setting();

    return $Setting->get($key, $default);
}

/**
 * @param string $key
 *
 * @return mixed
 */
function user_group_setting($key)
{
    return Phpfox::getUserParam($key);
}

/**
 * @param null $key
 * @param null $default
 * @param null $userGroupId
 * @param bool $bRedirect
 *
 * @return \Api\User\Object|\Api\User\Object[]
 * @throws \Exception
 */
function user($key = null, $default = null, $userGroupId = null, $bRedirect = false)
{
    if ($key === null) {
        return Core\Lib::apiUser()->get(\Phpfox::getService('user.auth')->getUserSession());
    }

    $Setting = Core\Lib::userSetting();

    return $Setting->get($key, $default, $userGroupId, $bRedirect);
}

/**
 * @return mixed
 */
function phrase()
{
    $Reflect = (new ReflectionClass('Phpfox_Locale'))->newInstanceWithoutConstructor();

    return call_user_func_array([$Reflect, 'phrase'], func_get_args());
}

/**
 * @param string  $sVarName
 * @param array   $aParam
 * @param  string $sLanguageId
 *
 * @return string
 */
function _p($sVarName = '', $aParam = [], $sLanguageId = '')
{
    return Core\Lib::phrase()->get($sVarName, $aParam, $sLanguageId);
}

function error()
{
    $Reflect = (new ReflectionClass('Core\Exception'))->newInstanceWithoutConstructor();

    return call_user_func_array([$Reflect, 'toss'], func_get_args());
}

/**
 * @return \Core\Text
 */
function text()
{
    return Core\Lib::text();
}

/**
 * @param string  $name
 * @param Closure $callback
 *
 * @return \Core\Route\Group
 */
function group($name, Closure $callback)
{
    return new Core\Route\Group($name, $callback);
}

function register_api($adapter, $array)
{
    \Core\Route::registerApi($adapter, $array);
}

/**
 * @param string         $route
 * @param Closure|string $callback
 *
 * @return \Core\Route
 */
function route($route, $callback)
{
    return new Core\Route($route, $callback, true);
}

/**
 * @param string|array $asset
 *
 * @return \Core\Asset
 */
function asset($asset)
{
    return new Core\Asset($asset);
}

/**
 * @param string $str
 *
 * @return \Core\Text\Parse
 */
function parse($str)
{
    return text()->parse($str);
}

/**
 * @param string $route
 * @param int    $id
 * @param string $title
 *
 * @return string
 */
function permalink($route, $id, $title)
{
    return \Phpfox_Url::instance()->permalink($route, $id, $title);
}

/**
 * @return \Core\Redis
 */
function redis()
{
    return Core\Lib::redis();
}

/**
 * @param int         $location
 * @param Closure     $callback
 * @param null|string $controller
 *
 * @return bool|\Core\Block
 */
function block($location, $callback, $controller = null)
{
    if ($controller !== null && is_callable($controller)) {
        return new Core\Block($callback, $location, $controller);
    }

    if (!is_numeric($location)) {
        Core\Block\Group::$blocks[$location] = $callback;
        return true;
    }

    return new Core\Block(null, $location, $callback);
}

/**
 * @param string  $name
 * @param Closure $callback
 *
 * @return \Core\Event
 */
function event($name, $callback)
{
    return new Core\Event($name, $callback);
}

/**
 * @return \Core\Storage
 */
function storage()
{
    return Core\Lib::storage();
}

/**
 * @param string $name
 * @param array  $params
 *
 * @return \Core\View
 */
function render($name, $params = [])
{
    return Core\Controller::$__view->render($name, $params);
}

/**
 * @param string $name
 * @param array  $params
 *
 * @return \Core\View|string
 */
function view($name, $params = [])
{
    return Core\Controller::$__view->view($name, $params);
}

/**
 * @param null|int $id
 *
 * @return \Core\App|\Core\App\Object
 */
function app($id = null)
{
    $app = Core\Lib::app();

    if ($id != null) {
        return $app->get($id);
    }

    return $app;
}

/**
 * @param null|int $app_id
 *
 * @return string
 */
function home($app_id = null)
{
    if ($app_id !== null) {
        $path = str_replace(PHPFOX_DIR_SITE, home() . 'PF.Site/', app($app_id)->path);
        return $path;
    }

    return setting('core.path_actual');
}

/**
 * @return \Core\Is
 */
function is()
{
    return Core\Lib::is();
}

/**
 * @param null|int $seconds
 *
 * @return \Core\Moment|string
 */
function moment($seconds = null)
{
    $object = Core\Lib::moment();
    if ($seconds !== null) {
        return $object->toString($seconds);
    }

    return $object;
}

/**
 * @return Phpfox_Database_Driver_Mysql
 */
function db()
{
    return \Phpfox_Database::instance();
}

/**
 * @return \Core\Request
 */
function request()
{
    return Core\Lib::request();
}

/**
 * @param string $app_id
 * @param string $key_name
 * @param int    $feed_id
 * @param int    $user_id
 * @param bool   $force
 *
 * @return bool
 */
function notify($app_id, $key_name, $feed_id, $user_id, $force = true)
{
    return Core\Lib::apiNotification()->post($app_id . '/' . $key_name, $feed_id, $user_id, $force);
}

/**
 * @param string $name
 * @param string $url
 *
 * @return \Core\Controller
 */
function section($name, $url)
{
    return Core\Controller::$__self->section($name, $url);
}

/**
 * @param string $title
 * @param string $url
 * @param string $extra
 *
 * @return \Core\Controller
 */
function sectionMenu($title, $url, $extra = '')
{
    return Core\Controller::$__self->sectionMenu($title, $url, $extra);
}

/**
 * @param string $section
 * @param array  $menu
 *
 * @return \Core\Controller
 */
function subMenu($section, $menu)
{
    return Core\Controller::$__self->subMenu($section, $menu);
}

/**
 * @param string $title
 * @param string $url
 * @param string $extra
 *
 * @see sectionMenu()
 *
 * @return \Core\Controller
 */
function button($title, $url, $extra = '')
{
    return sectionMenu($title, $url, $extra);
}

/**
 * @param string $section
 * @param array  $menu
 *
 * @see subMenu()
 *
 * @return \Core\Controller
 */
function menu($section, $menu)
{
    return subMenu($section, $menu);
}


function flavor()
{
    return new Core\Flavor\Flavor();
}

;

/**
 * @param null|string $name
 *
 * @return \Core\Cache
 */
function cache($name = null)
{
    return new Core\Cache($name);
}

/**
 * @param null  $route
 * @param array $params
 *
 * @return \Core\Url
 */
function url($route = null, $params = [])
{
    $object = new \Core\Url();
    if ($route !== null && $object != null) {
        return $object->make($route, $params);
    }

    return $object;
}

/**
 * @param string $name
 * @param string $url
 *
 * @return \Core\Controller
 */
function h1($name, $url)
{
    return Core\Controller::$__self->h1($name, $url);
}

/**
 * @param string $title
 *
 * @return \Core\Controller
 */
function title($title)
{
    return Core\Controller::$__self->title($title);
}

/**
 * @return \Core\Auth\User
 */
function auth()
{
    return Core\Lib::authUser();
}

/**
 * @return \Core\Validator
 */
function validator()
{
    return Core\Lib::validator();
}

function resolve_path($file){
    return strtr($file, ['\\'=> PHPFOX_DS, '//'=>PHPFOX_DS]);
}

/**
 * @return \Core\Form
 */
function form()
{
    return Core\Lib::form();
}

/**
 * @return \Core\Search
 */
function search()
{
    return Core\Lib::search();
}

/**
 * @return \Core\HTTP\Cache
 */
function http_cache()
{
    return Core\Lib::httpCache();
}


/**
 * @param string|array $name
 * @param \Closure     $callback
 * @param int          $lifetime default is "0"
 *
 * @return mixed
 */
function get_from_cache($name, \Closure $callback, $lifetime = 0)
{
    $cache = Phpfox_Cache::instance();

    $key = $cache->set(is_array($name) ? implode('_', $name) : $name);

    $data = $cache->get($key, $lifetime);

    if (!empty($data) or !$callback) {
        return $data;
    }

    $data = $callback();

    $cache->save($key, $data);

    return $data;
}

/**
 * @param string $sPath Url or path to content
 *
 * @return string
 */

function fox_get_contents($sPath)
{
    //TODO check file_get_contents parameters
    if (filter_var($sPath, FILTER_VALIDATE_URL) === false) {
        return file_get_contents($sPath);
    } else {
        //use CURL to get
        $ch = curl_init($sPath);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 3,
            CURLOPT_TIMEOUT        => 30,
        ]);

        $content = curl_exec($ch);

        if ($error = curl_errno($ch)) {
            exit(curl_error($ch));
        }
        curl_close($ch);
        return $content;
    }
}

/**
 * This method is for debug only
 * <code>
 * _dump($var1, $var2, ...)
 * </code>
 */
function _dump()
{
    echo '<pre>', var_export(func_get_args(), 1), '</pre>';
    exit;
}

if (!defined('PHPFOX_NO_RUN')) {
    try {
        Core\Lib::app();
        Phpfox::run();
    } catch (\Exception $e) {

        if (\Core\Route\Controller::$isApi) {
            http_response_code(400);
            $content = [
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ];
            header('Content-type: application/json');
            echo json_encode($content, JSON_PRETTY_PRINT);
            exit;
        }

        if (PHPFOX_IS_AJAX_PAGE || Phpfox_Request::instance()->get('is_ajax_post')) {
            header('Content-type: application/json');

            $msg = $e->getMessage();
            if (Phpfox_Request::instance()->get('is_ajax_post')) {
                $msg = '<div class="error_message">' . $msg . '</div>';
            }
            echo json_encode([
                'error' => $msg,
            ]);
            exit;
        }
        header('Content-type: text/html');

        if (!PHPFOX_DEBUG) {
            new Core\Route('*', function (Core\Controller $controller) {
                http_response_code(400);

                return $controller->render('@Base/layout.html', [
                    'content' => '<div class="error_message">Something went wrong here. We have notified the village elders about the issue.</div>',
                ]);
            });

            if (($View = (new Core\Route\Controller())->get())) {
                echo $View->getContent();
            }

            exit;
        }

        throw new Exception($e->getMessage(), $e->getCode(), $e);
    }
}