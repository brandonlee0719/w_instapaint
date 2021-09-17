<?php

namespace Apps\Core_eGifts;

use Core\App;

class Install extends App\App
{
    public $store_id = 1878;
    private $_app_phrases = [

    ];

    protected function setId()
    {
        $this->id = 'Core_eGifts';
    }

    protected function setAlias()
    {
        $this->alias = 'egift';
    }

    protected function setName()
    {
        $this->name = _p('Egifts');
    }

    protected function setVersion()
    {
        $this->version = '4.6.0';
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
        $this->start_support_version = '4.6.0';
        $this->end_support_version = '';
    }

    protected function setSettings()
    {
    }

    protected function setUserGroupSettings()
    {
        $this->user_group_settings = [
            'pf_can_send_gift_other' => [
                'var_name' => 'pf_can_send_gift_other',
                'info' => 'Can send gift to other members ?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
            ]
        ];
    }

    protected function setComponent()
    {
        $this->component = [
            'block' => [
                'received-gifts' => '',
            ]
        ];
    }

    protected function setComponentBlock()
    {
        $this->component_block = [
            _p('Gifts') => [
                'type_id' => '0',
                'm_connection' => 'profile.index',
                'component' => 'received-gifts',
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
        $this->admincp_route = "/egift/admincp";
        $this->admincp_menu = [
            'Manage Categories' => '#',
            'Manage Gifts' => 'egift.manage-gifts',
            'Invoices' => 'egift.invoices',
        ];
        $this->admincp_help = "admincp_help";
        $this->admincp_action_menu = [
            'admincp.egift.add-category' => _p('New Category'),
            'admincp.egift.add-gift' => _p('New eGift')
        ];
        $this->_publisher = 'phpFox';
        $this->_publisher_url = 'http://store.phpfox.com/';
        $this->_apps_dir = "core-egift";

        $this->_writable_dirs = [
            'PF.Base/file/pic/egift/'
        ];
        $this->database = [
            'Egift',
            'EgiftCategory',
            'EgiftInvoice'
        ];
        $this->_admin_cp_menu_ajax = false;
    }
}
