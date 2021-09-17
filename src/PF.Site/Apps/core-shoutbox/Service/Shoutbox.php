<?php
namespace Apps\phpFox_Shoutbox\Service;

use Apps\phpFox_Shoutbox\Installation\Database\Shoutbox as dbShoutbox;

class Shoutbox
{
    private static $_process;
    
    public static function process()
    {
        if (self::$_process == null) {
            self::$_process = new Process();
        }
        return self::$_process;
    }
    
    private static $_dbShoutbox;
    
    public static function sbTable()
    {
        if (self::$_dbShoutbox == null) {
            self::$_dbShoutbox = new dbShoutbox();
        }
        return self::$_dbShoutbox->getTableName();
    }
    
    private static $_get;
    
    public static function get()
    {
        if (self::$_get == null) {
            self::$_get = new Get();
        }
        return self::$_get;
    }
}