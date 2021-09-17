<?php
namespace Apps\PHPfox_Core;

use Core\App;
use Phpfox;
use Core\App\Install\Setting;

/**
 * Class Install
 * @author  Neil
 * @version 4.5.0
 * @package Apps\PHPfox_Core
 */
class Install extends App\App
{

    /**
     * @var array
     */
    private $_app_phrases = [];

    /**
     *
     */
    protected function setId()
    {
        $this->id = 'PHPfox_Core';
    }

    /**
     * Set start and end support version of your App.
     * @example   $this->start_support_version = 4.2.0
     * @example   $this->end_support_version = 4.5.0
     * @see       list of our verson at PF.Base/install/include/installer.class.php ($_aVersions)
     * @important You DO NOT ALLOW to set current version of phpFox for start_support_version and end_support_version. We will reject of app if you use current version of phpFox for these variable. These variables help clients know their sites is work with your app or not.
     */
    protected function setSupportVersion()
    {
        $this->start_support_version = Phpfox::getVersion();
        $this->end_support_version = Phpfox::getVersion();
    }


    /**
     *
     */
    protected function setAlias()
    {
    }

    /**
     *
     */
    protected function setName()
    {
        $this->name = 'phpFox Core';
    }

    /**
     *
     */
    protected function setVersion()
    {
        $this->version = Phpfox::getVersion();
    }

    /**
     *
     */
    protected function setSettings()
    {
        $this->settings = [
            "pf_core_cache_driver" => [
                "var_name" => "pf_core_cache_driver",
                "info" => "Cache Driver",
                "description" => "pf_core_cache_driver_description",
                "type" => "select",
                "value" => "file",
                "options" => [
                    "file" => "File System",
                    "redis" => "Redis",
                    "memcached" => "Memcache"
                ],
                "group_class" => "core_cache_driver"
            ],
            "pf_core_cache_redis_host" => [
                "var_name" => "pf_core_cache_redis_host",
                "info" => "Redis Host",
                "group_class" => "core_cache_driver",
                "option_class" => "pf_core_cache_driver=redis"
            ],
            "pf_core_cache_redis_port" => [
                "var_name" => "pf_core_cache_redis_port",
                "info" => "Redis Port",
                "group_class" => "core_cache_driver",
                "option_class" => "pf_core_cache_driver=redis"
            ],
            "pf_core_cache_memcached_host" => [
                "var_name" => "pf_core_cache_memcached_host",
                "info" => "Memcache Host",
                "group_class" => "core_cache_driver",
                "option_class" => "pf_core_cache_driver=memcached"
            ],
            "pf_core_cache_memcached_port" => [
                "var_name" => "pf_core_cache_memcached_port",
                "info" => "Memcache Port",
                "group_class" => "core_cache_driver",
                "option_class" => "pf_core_cache_driver=memcached"
            ],

            "pf_core_bundle_js_css" => [
                "var_name" => "pf_core_bundle_js_css",
                "info" => "Bundle JavaScript & CSS",
                "type" => Setting\Site::TYPE_RADIO,
                "value" => 0,
                "group_class" => "core_redis"
            ]
//            "pf_core_redis_host" => [
//                "var_name" => "pf_core_redis_host",
//                "info" => "Redis URI",
//                "group_class" => "core_redis"
//            ],
//            "pf_core_redis" => [
//                "var_name" => "pf_core_redis",
//                "info" => "Enable Redis",
//                "type" => Setting\Site::TYPE_RADIO,
//                "value" => 0,
//                "group_class" => "core_redis",
//                "requires" => "pf_core_redis_host"
//            ]
        ];
    }

    /**
     *
     */
    protected function setUserGroupSettings()
    {
    }

    /**
     *
     */
    protected function setComponent()
    {
    }

    /**
     *
     */
    protected function setComponentBlock()
    {
    }

    /**
     *
     */
    protected function setPhrase()
    {
        $this->phrase = $this->_app_phrases;
    }

    /**
     *
     */
    protected function setOthers()
    {
        $this->_publisher = 'phpFox';
        $this->_publisher_url = 'http://store.phpfox.com/';
        $this->_apps_dir = 'core';
        $this->database = [
            'Feed_Tag_Data',
            'Tag',
            'Currency',
            'Temp_File'
        ];
    }
}