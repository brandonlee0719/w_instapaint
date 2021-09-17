<?php

namespace Apps\Core_Newsletter;

use Core\App;

class Install extends App\App
{
    public $store_id = 1902;

    private $_app_phrases = [

    ];

    protected function setId()
    {
        $this->id = 'Core_Newsletter';
    }

    protected function setAlias()
    {
        $this->alias = 'newsletter';
    }

    protected function setName()
    {
        $this->name = _p('newsletter_app');
    }

    protected function setVersion()
    {
        $this->version = '4.6.0';
    }

    protected function setSupportVersion()
    {
        $this->start_support_version = '4.6.0';
    }

    protected function setSettings()
    {
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
        $this->admincp_route = "/newsletter/admincp";
        $this->admincp_menu = [
            'Manage Newsletter' => '#',
            'Create Newsletter' => 'newsletter.add',
        ];

        $this->_publisher = 'phpFox';
        $this->_publisher_url = 'http://store.phpfox.com/';
        $this->_apps_dir = "core-newsletter";

        $this->database = [
            'Newsletter',
            'Newsletter_Text',
        ];
        $this->_admin_cp_menu_ajax = false;
    }
}
