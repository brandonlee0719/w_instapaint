<?php

namespace Core;

/**
 * Class Block
 *
 * @package Core
 *
 * @method Block title($title)
 * @method Block content($content)
 */
class Block
{
    public $db;
    public $request;
    public $url;
    public $active;

    private $_arg = [];

    public function __construct($controller = null, $location = null, $callback = null)
    {
        if ($controller === null) {
            $controller = 'route_' . \Core\Route\Controller::$name['route'];
            $controller = str_replace('/', '_', $controller);
        }

        $this->request = new Request();
        $this->url = new Url();
        $this->active = (new \Api\User())->get(\Phpfox::getService('user.auth')->getUserSession());
        $this->db = new Db();

        \Phpfox_Module::instance()->block($controller, $location, $callback, $this);
    }

    public function get($arg)
    {
        if (!isset($this->_arg[$arg])) {
            return '';
        }
        return $this->_arg[$arg];
    }

    public function __call($method, $args)
    {
        if ($method == 'content' && PHPFOX_IS_AJAX_PAGE) {
            echo $args[0];
            return;
        }

        $this->_arg[$method] = $args[0];
    }
}