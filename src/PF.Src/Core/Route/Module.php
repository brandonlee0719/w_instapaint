<?php

namespace Core\Route;

class Module
{
    public $module;
    public $controller;

    public function __construct($module, $controller)
    {
        $this->module = strtolower($module);
        $this->controller = strtolower($controller);
    }
}