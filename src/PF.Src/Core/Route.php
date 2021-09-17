<?php

namespace Core;

use Core\Api\ApiManager;
use Core\Route\Controller;
use Core\Route\RouteUrl;

/**
 * Class Route
 *
 * @package Core
 *
 * @method Route auth($authenticate)
 * @method Route run($callback)
 * @method Route accept($methods)
 * @method Route call($class)
 * @method Route where($array)
 * @method Route url($url)
 * @method Route filter($callback)
 *
 */
class Route
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

    /**
     * @var bool
     */
    private static $_buildApi = false;


    public function __construct($route, $callback = null, $short = false)
    {
        if (!self::$_buildApi) {
            self::$_buildApi = true;
            $routes = require(PHPFOX_DIR_SETTING . 'routes.sett.php');
            foreach ($routes as $key => $value) {
                self::add($key, null, $value);
            }
        }

        if (is_array($route)) {
            foreach ($route as $key => $value) {

                if (is_string($value)) {
                    $value = [
                        'call' => $value,
                    ];
                }

                $value['path'] = Controller::$active;
                self::add($key, null, $value);
            }
        } else {
            if (self::$group) {
                $route = self::$group . $route;
            }

            self::add($route, $callback, [
                'path'  => Controller::$active,
                'id'    => Controller::$activeId,
                'short' => $short,
            ]);
        }
    }

    /**
     * @param $uri
     *
     * @return bool|\Core\Route\RouteUrl
     */
    public static function match($uri)
    {

        $apiPrefix = Registry::get('PHPFOX_EXTERNAL_API_PREFIX', 'restful_api');
        $method = request()->method();

        if (substr($uri, 0, strlen($apiPrefix)) == $apiPrefix) {
            $internal_url = substr($uri, strlen($apiPrefix) + 1);
            if (($r = ApiManager::match($internal_url, $method)) != false) {
                return $r;
            }
        }

        foreach (self::$routes as $name => $route) {
            if (is_array($route)) {
                continue;
            }
            if ($route->match($uri)) {
                return $route;
            }
        }

        return false;
    }

    public static function registerApi($adapter, $routes)
    {
        if (!\Phpfox::getService($adapter['class'])) {
            return error(_p('Cannot find the API Adapter.'));
        }

        $prefix = $adapter['route'] . '/';

        foreach ($routes as $route => $params) {
            $route = $prefix . trim($route, '/');

            if (empty($params['api_service'])) {
                return error(_p('Missing API Service to handle request.'));
            }

            $params['api'] = true;
            $params['api_adapter'] = $adapter['class'];

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

}