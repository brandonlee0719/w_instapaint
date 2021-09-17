<?php
/**
 * Created by PhpStorm.
 * User: namnv
 * Date: 10/14/16
 * Time: 2:17 PM
 */

namespace Core\Api;


use Core\Route\RouteUrl;
use Phpfox_Plugin;

/**
 * Class Route
 *
 * @package Core\Api
 *
 * @method ApiManager auth($authenticate)
 * @method ApiManager run($callback)
 * @method ApiManager accept($methods)
 * @method ApiManager call($class)
 * @method ApiManager where($array)
 * @method ApiManager url($url)
 * @method ApiManager filter($callback)
 *
 */
class ApiManager
{
    /**
     * @var RouteUrl[]
     */
    public static $routes = [];

    /**
     * @var array
     */
    public static $group;

    /**
     * @var string
     */
    private static $_active;

    private static $_loaded = false;

    /**
     * @param        $uri
     * @param string $method
     *
     * @return bool|\Core\Route\RouteUrl
     */
    public static function match($uri, $method)
    {
        if (false == self::$_loaded) {
            (($sPlugin = Phpfox_Plugin::get('route_start')) ? eval($sPlugin) : false);
            self::$_loaded = true;
        }
        foreach (self::$routes as $name => $route) {
            if (is_array($route)) {
                continue;
            }
            if ($route->match($uri, $method)) {
                return $route;
            }
        }

        return false;
    }

    public static function register($routes)
    {
        foreach ($routes as $route => $params) {
            $route = trim($route, '/');
            if (empty($params['api_service'])) {
                return error(_p('Missing API Service to handle request.'));
            }

            $params['api'] = true;
            self::$routes[$route] = new RouteUrl($route, null, $params);
            self::$_active = $route;
        }
    }

    public static function add($route, $callback, $params)
    {
        $route = trim($route, '/');

        self::$routes[$route] = new RouteUrl($route, $callback, $params);

        self::$_active = $route;
    }

    public function __call($method, $args)
    {
        if (count($args) === 1) {
            $args = $args[0];
        }

        self::$routes[self::$_active][$method] = $args;
        return $this;
    }

    /**
     * @param string $uri
     * @param array  $params
     *
     * @return array
     */
    public static function post($uri, $params = [])
    {
        return self::process('post', $uri, $params);
    }

    /**
     * @param string $uri
     * @param array  $params
     *
     * @return array
     */
    public static function get($uri, $params = [])
    {
        return self::process('get', $uri, $params);
    }

    /**
     * @param string $uri
     * @param array  $params
     *
     * @return array
     */
    public static function put($uri, $params = [])
    {
        return self::process('put', $uri, $params);
    }

    /**
     * @param string $uri
     * @param array  $params
     *
     * @return array
     */
    public static function delete($uri, $params = [])
    {
        return self::process('delete', $uri, $params);
    }

    /**
     * @param string $method available values: get, post, put, delete, update
     * @param string $uri
     * @param array  $params
     *
     * @return array
     */
    public static function process($method, $uri, $params = [])
    {
        $r = self::match($uri, $method);

        if ($r) {
            $r->args = array_merge($r->args, $params);
        }

        $api = \Phpfox::getService($r['api_service']);

        $transport = new ApiTransportLocal();


        if (!$api || !($api instanceof \Core\Api\ApiServiceBase)) {
            $content = ['status' => 'failed', 'errors' => [_p('Cannot find API Service for this request.')],];
        } else {
            $content = $api->process($r, $transport, $method);
        }

        return $content;
    }
}