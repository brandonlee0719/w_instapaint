<?php

namespace Core\Route;

/**
 * Class RouteUrl
 *
 * @property  $uri
 * @property  $expression
 * @property  $accept
 * @property  $auth
 * @property  $run
 * @property  $where
 * @property  $args
 * @property  $filter
 *
 * @package Core\Route
 */
class RouteUrl implements \ArrayAccess
{
    /**
     * @var array|string
     */
    private $params = [];

    /**
     * RouteUrl constructor.
     *
     * @param string $uri
     * @param mixed  $callback Closure or controller name
     * @param array  $params
     */
    public function __construct($uri, $callback, $params = [])
    {
        if (is_string($callback)) {
            $callback = function () use ($callback) {
                \Phpfox_Module::instance()->dispatch($callback);

                return 'controller';
            };
        }

        if (is_string($params)) {
            $params = [
                'call' => $params,
            ];
        }
        $this->params = $params;

        $uri = trim($uri, '/');
        $this->uri = $uri;
        $this->expression = $this->compile($uri);
        if ($callback instanceof \Closure) {
            $this->run = $callback;
        }
    }

    /**
     */
    private function update_where()
    {
        if (empty($this->params['where'])) {
            return;
        }

        $search = $replace = [];
        foreach ($this->params['where'] as $key => $value) {
            if (substr($key, 0, 1) == ':') {
                $key = substr($key, 1);
            }
            $search[] = "<$key>" . '[^/]++';
            $replace[] = "<$key>$value";
        }
        unset($this->params['where']);
        $this->expression = str_replace($search, $replace, $this->expression);
    }


    /**
     * @param       $uri
     * @param array $regex
     *
     * @return string
     */
    private function compile($uri, $regex = [])
    {

        $parts = explode('/', $uri);
        foreach ($parts as $index => $part) {
            if (substr($part, 0, 1) == ':') {
                $parts[$index] = '<' . substr($part, 1) . '>';
            }
            if ($part == '*') {
                $parts[$index] = '_ANY_';
            }
        }

        $uri = implode('/', $parts);
        // The URI should be considered literal except for keys and optional parts
        // Escape everything preg_quote would escape except for : ( ) < >
        $expression = preg_replace('#' . '[.\\+*?[^\\]${}=!|]' . '#', '\\\\$0', $uri);

        if (strpos($expression, '(') !== false) {
            // Make optional parts of the URI non-capturing and optional
            $expression = str_replace(['(', ')',], ['(?:', ')?',], $expression);
        }

        // Insert default regex for keys
        $expression = str_replace(['<', '>',], ['(?P<', '>' . '[^/]++' . ')',], $expression);

        $expression = str_replace('/_ANY_', '(.*)', $expression);

        return '#^' . $expression . '$#u';
    }

    /**
     * @param      $uri
     * @param null $method
     *
     * @return bool
     */
    public function match($uri, $method = null)
    {
        $this->update_where();

        if ($method and $this->accept and !in_array(strtoupper($method), $this->accept)) {
            return false;
        }

        if (!preg_match($this->expression, $uri, $matches)) {
            return false;
        }

        $args = [];
        foreach ($matches as $name => $value) {
            if (is_int($name)) {
                continue;
            }

            $args[$name] = $value;
        }

        $this->args = $args;

        if ($this->filter instanceof \Closure) {
            return call_user_func($this->filter, $this);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    function __get($name)
    {
        return isset($this->params[$name]) ? $this->params[$name] : null;
    }

    /**
     * @inheritdoc
     */
    function __set($name, $value)
    {
        return $this->params[$name] = $value;
    }

    public function offsetExists($offset)
    {
        return isset($this->params[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->params[$offset]) ? $this->params[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        $this->params[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->params[$offset]);
    }

}