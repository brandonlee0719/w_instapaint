<?php

namespace Core;

class Controller
{
    public $request;
    public $url;
    public $active;
    public $route;
    public $auth;

    private $_view;
    private $_template;

    public static $__view;
    public static $__self;

    public function __construct($path = null, $route = null)
    {
        $this->request = new Request();
        $this->url = new Url();
        $this->active = (new \Api\User())->get(\Phpfox::getService('user.auth')->getUserSession());
        $this->route = $route;
        $this->auth = new Auth\User();

        $this->_template = \Phpfox_Template::instance();
        $this->_view = self::$__view?:self::$__view = new View();
        if ($path !== null && is_dir($path)) {
            $this->_view->loader()->addPath($path);
        }

        self::$__view = $this->_view;
        self::$__self = $this;
    }

    public function block($location, \Closure $callback)
    {
        new Block('route_' . $this->route, $location, $callback);

        return $this;
    }

    public function h1($name, $url)
    {
        $this->_template->setBreadCrumb($name, ($url ? $this->url->make($url) : ''), true);

        return $this;
    }

    public function menu(array $menu = [])
    {
        if (!$menu) {
            return $this->_template->getSubMenu();
        }

        $this->_template->setSubMenu($menu);

        return $this;
    }

    public function sectionMenu($title, $url, $extra = '')
    {
        $this->_template->menu($title, $url, $extra);

        return $this;
    }

    public function subMenu($section, $menu)
    {
        $this->_template->buildSectionMenu($section, $menu, true);

        return $this;
    }

    public function section($name, $url)
    {
        $this->_template->setBreadCrumb($name, $this->url->make($url));

        return $this;
    }

    public function asset($asset)
    {
        new Asset($asset);

        return $this;
    }

    public function title($title)
    {
        $this->_template->setTitle($title);

        return $this;
    }

    public function render($name, array $params = [])
    {
        return $this->_view->render($name, $params);
    }

    public function setHeader($mHeaders, $mValue = null)
    {
        return $this->_template->setHeader($mHeaders, $mValue);
    }
}