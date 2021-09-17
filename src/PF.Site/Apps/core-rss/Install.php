<?php

namespace Apps\Core_RSS;

use Core\App;

class Install extends App\App
{
    public $store_id = 1899;

    private $_app_phrases = [

    ];

    protected function setId()
    {
        $this->id = 'Core_RSS';
    }

    protected function setAlias()
    {
        $this->alias = 'rss';
    }

    protected function setName()
    {
        $this->name = _p('rss_app');
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
        $this->settings = [
            'total_rss_display' => [
                'var_name' => 'total_rss_display',
                'info' => 'Total Items',
                'description' => 'Define how many items can be displayed within a RSS feed.',
                'type' => 'integer',
                'value' => 15,
                'ordering' => 1,
            ],
            'display_rss_count_on_profile' => [
                'var_name' => 'display_rss_count_on_profile',
                'info' => 'RSS Subscriber Count on Profile',
                'description' => 'Set to <b>True<b/> to display the RSS subscriber count on a users profile.<br/><b>Notice:</b> If enabled the user will have the ability to disable this via their privacy settings.',
                'type' => 'boolean',
                'value' => 1,
                'ordering' => 2,
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
        $this->admincp_route = '/rss/admincp';
        $this->admincp_menu = [
            'Manage Feeds' => 'rss',
            'Manage Groups' => 'rss.group',
            'Add New Group' => 'rss.add-group',
            'Add New Feed' => 'rss.add'
        ];
        $this->map = [];
        $this->database = [
            'Rss',
            'Rss_Group',
            'Rss_Log',
            'Rss_Log_User'
        ];
        $this->_admin_cp_menu_ajax = false;
        $this->_publisher = 'phpFox';
        $this->_publisher_url = 'http://store.phpfox.com/';
        $this->_apps_dir = 'core-rss';
    }
}
