<?php

namespace Core;

use Api as FoxApi;

class Lib
{
    /**
     * @var Phrase
     */
    private static $_oPhrase;

    private static $_aAppDirs;

    /**
     * @return Phrase
     */
    public static function phrase()
    {
        if (self::$_oPhrase === null) {
            self::$_oPhrase = new Phrase();
        }
        return self::$_oPhrase;
    }

    /**
     * @var App
     */
    private static $_oApp;

    /**
     * @return App
     */
    public static function app()
    {
        if (self::$_oApp === null) {
            self::$_oApp = new App();
        }
        return self::$_oApp;
    }


    /**
     * @var Storage
     */
    private static $_oStorage;

    /**
     * @return Storage
     */
    public static function storage()
    {
        if (self::$_oStorage === null) {
            self::$_oStorage = new Storage();
        }
        return self::$_oStorage;
    }

    /**
     * @var Request
     */
    private static $_oRequest;

    /**
     * @return Request
     */
    public static function request()
    {
        if (self::$_oRequest === null) {
            self::$_oRequest = new Request();
        }
        return self::$_oRequest;
    }

    /**
     * @return mixed
     * @deprecated 4.7.0
     */
    public static function appsGroup()
    {
        return \Phpfox::getService('groups');
    }

    /**
     * @return mixed
     * @deprecated 4.7.0
     */
    public static function appsPages()
    {
        return \Phpfox::getService('pages');
    }

    /**
     * @var Setting
     */
    private static $oSetting;

    /**
     * @return Setting
     */
    public static function setting()
    {
        if (self::$oSetting == null) {
            self::$oSetting = new Setting();
        }
        return self::$oSetting;
    }

    /**
     * @var HTTP\Cache
     */
    private static $oHttpCache;

    /**
     * @return HTTP\Cache
     */
    public static function httpCache()
    {
        if (self::$oHttpCache == null) {
            self::$oHttpCache = new HTTP\Cache();
        }
        return self::$oHttpCache;
    }

    /**
     * @var Search
     */
    private static $oSearch;

    /**
     * @return Search
     */
    public static function search()
    {
        if (self::$oSearch == null) {
            self::$oSearch = new Search();
        }
        return self::$oSearch;
    }

    /**
     * @var Form
     */
    private static $oForm;

    /**
     * @return Form
     */
    public static function form()
    {
        if (self::$oForm == null) {
            self::$oForm = new Form();
        }
        return self::$oForm;
    }

    /**
     * @var Validator
     */
    private static $oValidator;

    /**
     * @return Validator
     */
    public static function validator()
    {
        if (self::$oValidator == null) {
            self::$oValidator = new Validator();
        }
        return self::$oValidator;
    }

    /**
     * @var Auth\User
     */
    private static $oAuthUser;

    /**
     * @return Auth\User
     */
    public static function authUser()
    {
        if (self::$oAuthUser == null) {
            self::$oAuthUser = new Auth\User();
        }
        return self::$oAuthUser;
    }

    /**
     * @var FoxApi\Notification
     */
    private static $oApiNotification;

    /**
     * @return FoxApi\Notification
     */
    public static function apiNotification()
    {
        if (self::$oApiNotification == null) {
            self::$oApiNotification = new FoxApi\Notification();
        }
        return self::$oApiNotification;
    }

    /**
     * @var Moment
     */
    private static $oMoment;

    /**
     * @return Moment
     */
    public static function moment()
    {
        if (self::$oMoment == null) {
            self::$oMoment = new Moment();
        }
        return self::$oMoment;
    }

    /**
     * @var Is
     */
    private static $oIs;

    /**
     * @return Is
     */
    public static function is()
    {
        if (self::$oIs == null) {
            self::$oIs = new Is();
        }
        return self::$oIs;
    }

    /**
     * @var Redis
     */
    private static $oRedis;

    /**
     * @return Redis
     */
    public static function redis()
    {
        if (self::$oRedis == null) {
            self::$oRedis = new Redis();
        }
        return self::$oRedis;
    }

    /**
     * @var FoxApi\User
     */
    private static $oApiUser;

    /**
     * @return FoxApi\User
     */
    public static function apiUser()
    {
        if (self::$oApiUser == null) {
            self::$oApiUser = new FoxApi\User();
        }
        return self::$oApiUser;
    }

    /**
     * @var User\Setting
     */
    private static $oUserSetting;

    /**
     * @return User\Setting
     */
    public static function userSetting()
    {
        if (self::$oUserSetting == null) {
            self::$oUserSetting = new User\Setting();
        }
        return self::$oUserSetting;
    }

    /**
     * @var Text
     */
    private static $oText;

    /**
     * @return Text
     */
    public static function text()
    {
        if (self::$oText == null) {
            self::$oText = new Text();
        }
        return self::$oText;
    }

    /**
     * @param string  $app_id
     * @param  string $sAppDir
     *
     * @return bool|\Core\App\App
     */
    public static function appInit($app_id, $sAppDir = '')
    {
        if (empty($sAppDir)) {
            $sAppDir = $app_id;
            if (!defined('PHPFOX_INSTALLER') || !PHPFOX_INSTALLER) {
                if (empty(self::$_aAppDirs)) {
                    self::$_aAppDirs = get_from_cache(['lib', 'aAppDirs'], function () {
                        $aAppDirs = [];
                        $dataRows = \Phpfox::getLib('database')->select('apps_id, apps_dir')
                            ->from(':apps')
                            ->executeRows();

                        foreach ($dataRows as $row) {
                            $aAppDirs[$row['apps_id']] = $row['apps_dir'];
                        }
                        return $aAppDirs;
                    });
                }

                if (in_array($app_id, self::$_aAppDirs)) {
                    $sAppDir = self::$_aAppDirs[$app_id];
                }
            }
        }

        $className = '\Apps\\' . $app_id . '\Install';

        if (class_exists($className)) {
            return new $className();
        } elseif (file_exists(PHPFOX_DIR_SITE . 'Apps' . PHPFOX_DS . $sAppDir . PHPFOX_DS . 'app.json')) {
            App\Migrate::migrate($sAppDir);
            if (class_exists($className)) {
                return new $className();
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}