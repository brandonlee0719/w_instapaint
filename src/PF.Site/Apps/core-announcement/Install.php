<?php

namespace Apps\Core_Announcement;

use Core\App;

class Install extends App\App
{
    private $_app_phrases = [

    ];

    public $store_id = 1930;

    protected function setId()
    {
        $this->id = 'Core_Announcement';
    }

    protected function setAlias()
    {
        $this->alias = 'announcement';
    }

    protected function setName()
    {
        $this->name = _p('announcement_app');
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
        $this->user_group_settings = [
            'can_close_announcement' => [
                'var_name' => 'can_close_announcement',
                'info' => 'Are members of this user group allowed to close the announcements block in the dashboard?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
            ],
            'can_view_announcements' => [
                'var_name' => 'can_view_announcements',
                'info' => 'Can browse and view announcements?',
                'description' => '',
                'type' => 'boolean',
                'value' => 1,
            ]
        ];
    }

    protected function setComponent()
    {
        $this->component = [
            'block' => [
                'index' => '',
                'more' => ''
            ],
            'controller' => [
                'index' => 'announcement.index',
            ]
        ];
    }

    protected function setComponentBlock()
    {
        $this->component_block = [
            'Announcement' => [
                'type_id' => '0',
                'm_connection' => 'core.index-member',
                'component' => 'index',
                'location' => '7',
                'is_active' => '1',
                'ordering' => '10',
            ],
            'More Announcements' => [
                'type_id' => '0',
                'm_connection' => 'announcement.view',
                'component' => 'more',
                'location' => '3',
                'is_active' => '1',
                'ordering' => '1',
            ]
        ];
    }

    protected function setPhrase()
    {
        $this->phrase = $this->_app_phrases;
    }

    protected function setOthers()
    {
        $this->admincp_route =  \Phpfox::getLib('url')->makeUrl('admincp.app',['id'=>'Core_Announcement']);
        $this->_publisher = 'phpFox';
        $this->_publisher_url = 'http://store.phpfox.com/';
        $this->_apps_dir = "core-announcement";
        $this->database = [
            'Announcement',
            'Announcement_Hide',
        ];

        $this->admincp_route = "/announcement/admincp";
        $this->admincp_menu = [
            'Manage Announcement' => '#',
            'New Announcement' => 'announcement.add'
        ];
        $this->_admin_cp_menu_ajax = false;
    }
}
