<?php

namespace Apps\PHPfox_Facebook;

use Core\App;
use Core\App\Install\Setting;

/**
 * Class Install
 * @package Apps\PHPfox_Facebook
 */
class Install extends App\App
{
    private $_app_phrases = [

    ];

    public $store_id = 1897;

    protected function setId()
    {
        $this->id = 'PHPfox_Facebook';
    }

    /**
     * Set start and end support version of your App.
     */
    protected function setSupportVersion()
    {
        $this->start_support_version = '4.6.0';
    }

    protected function setAlias()
    {
    }

    protected function setName()
    {
        $this->name = _p('facebook_app');
    }

    protected function setVersion()
    {
        $this->version = '4.6.0';
    }

    protected function setSettings()
    {
        $this->settings = [
            "m9_facebook_enabled" => [
                "var_name" => "m9_facebook_enabled",
                "info" => "Facebook Login Enabled",
                "type" => Setting\Site::TYPE_RADIO,
                "value" => "0",
            ],
            "m9_facebook_app_id" => [
                "var_name" => "m9_facebook_app_id",
                "info" => "Facebook Application ID",
            ],
            "m9_facebook_app_secret" => [
                "var_name" => "m9_facebook_app_secret",
                "info" => "Facebook App Secret",
            ],
        ];
    }

    protected function setUserGroupSettings()
    {
    }

    protected function setComponent()
    {
    }

    protected function setComponentBlock()
    {
    }

    protected function setPhrase()
    {
        $this->phrase = $this->_app_phrases;
    }

    protected function setOthers()
    {
        $this->_publisher = 'phpFox';
        $this->_publisher_url = 'http://store.phpfox.com/';
        $this->_apps_dir = 'core-facebook';
        $this->admincp_route = \Phpfox::getLib('url')->makeUrl('admincp.app.settings', ['id' => 'PHPfox_Facebook']);
    }
}
