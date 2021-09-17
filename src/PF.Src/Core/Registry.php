<?php

namespace Core;

/**
 * Class Registry
 *
 * @package Core
 */
class Registry
{
    /**
     * @var array
     */
    private static $vars = [];

    /**
     * @param string $key
     * @param mixed  $def
     *
     * @return mixed
     */
    public static function get($key, $def = null)
    {
        return isset(self::$vars[$key]) ? self::$vars[$key] : $def;
    }

    /**
     * @param string $key
     * @param mixed  $val
     */
    public static function set($key, $val)
    {
        self::$vars[$key] = $val;
    }

    /**
     * Put value with key if associate key name does not set.
     *
     * @param $key
     * @param $val
     */
    public static function put($key, $val)
    {
        if (!isset(self::$vars[$key])) {
            self::$vars[$key] = $val;
        }
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public static function has($key)
    {
        return isset(self::$vars[$key]);
    }
}