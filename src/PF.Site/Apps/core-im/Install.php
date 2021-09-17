<?php

namespace Apps\PHPfox_IM;

use Core\App;

class Install extends App\App
{
    protected function setId()
    {
        $this->id = 'PHPfox_IM';
    }

    public $store_id = 1837;

    /**
     * Set start and end support version of your App.
     */
    protected function setSupportVersion()
    {
        $this->start_support_version = '4.6.0';
    }

    protected function setAlias()
    {
        $this->alias = 'im';
    }

    protected function setName()
    {
        $this->name = _p('instant_messaging');
    }

    protected function setVersion()
    {
        $this->version = '4.6.0';
    }

    protected function setSettings()
    {
        $this->settings = [
            'pf_im_node_server' => [
                'var_name' => "pf_im_node_server",
                'info' => "Provide your Node JS server"
            ],
            'pf_im_node_server_key' => [
                'var_name' => 'pf_im_node_server_key',
                'info' => 'Provide your Node JS server key (Ignore this setting if you are using phpFox IM hosting service)'
            ],
            'pf_total_conversations' => [
                'var_name' => "pf_total_conversations",
                'info' => "Total Latest Conversations in IM List",
                'description' => 'For unlimited add "0" without quotes.',
                "js_variable" => true,
                'value' => 20
            ],
            'pf_time_to_delete_message' => [
                'var_name' => "pf_time_to_delete_message",
                'info' => "How long user can still delete their own message? (days)",
                'description' => 'Define how long a message can be deleted. After this time, message cannot be deleted by owner. Put 0 means owner can delete their messages all time.',
                'value' => 0
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
    }

    protected function setOthers()
    {
        $this->admincp_menu = [
            'Instant Messaging' => '#',
            'Manage Notification Sound' => 'im.manage-sound',
            'Import Data From v3' => 'im.import-data-v3'
        ];
        $this->admincp_route = "/im/admincp";
        $this->_publisher = 'phpFox';
        $this->_publisher_url = 'http://store.phpfox.com/';
        $this->_apps_dir = 'core-im';
        $this->_admin_cp_menu_ajax = false;
    }
}
