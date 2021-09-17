<?php

namespace Core\App;

class Installer
{
    public static $method = 'onInstall';

    public static $basePath;

    public $db;
    public $path;

    public function __construct()
    {
        $this->db = new \Core\Db();
        $this->path = self::$basePath;
    }

    public function onInstall($callback)
    {
        if (self::$method != 'onInstall') {
            return;
        }
        call_user_func($callback);
    }

    public function OnUninstall($callback)
    {
        if (self::$method != 'OnUninstall') {
            return;
        }
        call_user_func($callback);
    }
}