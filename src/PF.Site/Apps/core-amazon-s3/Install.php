<?php

namespace Apps\PHPfox_AmazonS3;

use Core\App;
use Core\App\Install\Setting;

/**
 * Class Install
 * @package Apps\PHPfox_AmazonS3
 */
class Install extends App\App
{
    private $_app_phrases = [

    ];

    public $store_id = 1860;

    public function __construct()
    {
        parent::__construct();
    }

    protected function setId()
    {
        $this->id = 'PHPfox_AmazonS3';
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
        $this->alias = 'amazons3';
    }

    protected function setName()
    {
        $this->name = _p('Amazon CDN');
    }

    protected function setPhrase()
    {

    }

    protected function setVersion()
    {
        $this->version = '4.5.4';
    }

    protected function setSettings()
    {
        $this->settings = [
            'cdn_enabled' => [
                'var_name' => 'cdn_enabled',
                'info' => 'Enable CDN',
                'type' => Setting\Site::TYPE_RADIO,
                'value' => '0'
            ],
            'cdn_amazon_id' => [
                'var_name' => 'cdn_amazon_id',
                'info' => 'Amazon Key ID',
            ],
            'cdn_amazon_secret' => [
                'var_name' => 'cdn_amazon_secret',
                'info' => 'Amazon Secret Key',
                'type' => Setting\Site::TYPE_PASSWORD
            ],
            'cdn_region' => [
                'var_name' => 'cdn_region',
                'info' => 'Bucket region',
                'description' => 'This setting is updated from managed page. Do not change this value.'
            ],
            'cdn_bucket' => [
                'var_name' => 'cdn_bucket',
                'info' => 'Bucket Name',
                'description' => 'This setting is updated from managed page. Do not change this value.'
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

    protected function setOthers()
    {
        $this->_publisher = 'phpFox';
        $this->_publisher_url = 'http://store.phpfox.com/';
        $this->_apps_dir = "core-amazon-s3";
        $this->admincp_menu = [
            _p('Manage') => '/amazons3/manage',
        ];
        $this->admincp_route = \Phpfox::getLib('url')->makeUrl('admincp.setting.edit',['module-id'=>'amazons3']);
        $this->_admin_cp_menu_ajax = false;
    }
}
