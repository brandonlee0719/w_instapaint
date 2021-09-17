<?php

namespace Apps\Core_Poke;

use Core\App;
use Core\App\Install\Setting;

class Install extends App\App
{
    private $_app_phrases = [

    ];

    public $store_id = 1898;

    protected function setId()
    {
        $this->id = 'Core_Poke';
    }

    protected function setAlias()
    {
        $this->alias = 'poke';
    }

    protected function setName()
    {
        $this->name = _p('poke_app');
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
            'add_to_feed' => [
                'var_name' => 'add_to_feed',
                'info' => 'Add pokes to activity feed',
                'type' => Setting\Site::TYPE_RADIO,
                'description' => 'If enabled every poke sent by a user will be added to the activity feed',
                'value' => '0',
            ]
        ];
    }

    protected function setUserGroupSettings()
    {
        $this->user_group_settings = [
            'can_poke' => [
                'var_name' => 'can_poke',
                'info' => 'Can members of this user group poke other members?',
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
            'can_only_poke_friends' => [
                'var_name' => 'can_only_poke_friends',
                'info' => 'Can members of this user group send a poke only to  people in their friends list?<br><br>(If you disable it, members of this user group will be able to poke people also not in their friends list)',
                'description' => '',
                'type' => 'boolean',
                'value' => 0,
            ]
        ];
    }

    protected function setComponent()
    {
        $this->component = [
            'block' => [
                'display' => '',
            ]
        ];
    }

    protected function setComponentBlock()
    {
        $this->component_block = [
            'Pokes' => [
                'type_id' => '0',
                'm_connection' => 'core.index-member',
                'component' => 'display',
                'location' => '3',
                'is_active' => '1',
                'ordering' => '3',
            ]
        ];
    }

    protected function setPhrase()
    {
        $this->phrase = $this->_app_phrases;
    }

    protected function setOthers()
    {
        $this->_publisher = 'phpFox';
        $this->_publisher_url = 'http://store.phpfox.com/';
        $this->admincp_route = \Phpfox::getLib('url')->makeUrl('admincp.app.settings', ['id' => 'Core_Poke']);
        $this->_apps_dir = "core-poke";
        $this->database = [
            'Poke_Data',
        ];
    }
}
