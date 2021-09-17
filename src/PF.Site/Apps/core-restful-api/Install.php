<?php
namespace Apps\phpFox_RESTful_API;

use Core\App;

class Install extends App\App
{
    private $_app_phrases = [

    ];

    public $store_id = 1664;

    protected function setId()
    {
        $this->id = 'phpFox_RESTful_API';
    }

    protected function setAlias()
    {
    }

    protected function setName()
    {
        $this->name = 'RESTful API';
    }

    protected function setVersion()
    {
        $this->version = '4.1.3';
    }

    protected function setSupportVersion()
    {
        $this->start_support_version = '4.6.0';
    }

    protected function setSettings()
    {
        $this->settings = [
            'pf_restful_api_access_lifetime' => [
                'info' => 'Expired Time for An Access Token (in seconds)',
                'type' => 'input:text',
                'value' => '86400',
            ]
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
        $this->admincp_route = '/restful_api/admincp';

        $this->admincp_menu = ['Clients' => '#'];

        $this->admincp_action_menu = ['/restful_api/admincp/client' => 'New Client'];

        $this->_publisher = 'phpFox';
        $this->_publisher_url = 'http://store.phpfox.com/';
        $this->_apps_dir = 'core-restful-api';
    }
}