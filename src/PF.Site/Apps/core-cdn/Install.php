<?php

namespace Apps\PHPfox_CDN;

use Core\App;
use Core\App\Install\Setting;

/**
 * Class Install
 * @package Apps\PHPfox_CDN
 */
class Install extends App\App
{
    private $_app_phrases = [

    ];

    public $store_id = 1859;

    public function __construct()
    {
        parent::__construct();
    }

    protected function setId()
    {
        $this->id = 'PHPfox_CDN';
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
        $this->name = _p('phpFox CDN');
    }

    protected function setVersion()
    {
        $this->version = '4.6.0';
    }

    protected function setSettings()
    {
        $this->settings = [
            'pf_cdn_enabled' => [
                'var_name' => 'pf_cdn_enabled',
                'info' => 'Enable CDN',
                'type' => Setting\Site::TYPE_RADIO,
                'value' => '0',
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
        $this->admincp_route = "/pfcdn/acp";
        $this->_admin_cp_menu_ajax = false;
        $this->admincp_menu = [
            _p("Servers") => "#"
        ];
        $this->admincp_action_menu = [
            "/pfcdn/acp/server" => _p("New Server")
        ];
        $this->_publisher = 'phpFox';
        $this->_publisher_url = 'http://store.phpfox.com/';
        $this->_apps_dir = "core-cdn";
        $this->admincp_help = 'https://docs.phpfox.com/display/FOX4MAN/Setting+Up+phpFox+CDN';
    }
}
