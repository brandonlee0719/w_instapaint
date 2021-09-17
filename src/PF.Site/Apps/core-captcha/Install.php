<?php

namespace Apps\Core_Captcha;

use Core\App;

class Install extends App\App
{
    private $_app_phrases = [

    ];

    public $store_id = 1933;

    protected function setId()
    {
        $this->id = 'Core_Captcha';
    }

    protected function setAlias()
    {
        $this->alias = 'captcha';
    }

    protected function setName()
    {
        $this->name = _p('captcha_app');
    }

    protected function setVersion()
    {
        $this->version = '4.6.0';
    }

    protected function setSupportVersion()
    {
        $this->start_support_version = '4.6.0';
        $this->end_support_version = '';
    }

    protected function setSettings()
    {
        $this->settings = [
            'captcha_code' => [
                'var_name' => 'captcha_code',
                'info' => 'Captcha String',
                'description' => 'Alphanumeric characters that will be part of the Captcha routine.',
                'type' => 'input:text',
                'value' => '23456789bcdfghjkmnpqrstvwxyzABCDEFGHJKLMNPQRSTUVWXYZ',
                'ordering' => 1
            ],
            'captcha_type' => [
                'var_name' => 'captcha_type',
                'info' => 'Captcha Type',
                'description' => 'Select captcha type<br>If you choose Recaptcha, visit google.com/recaptcha and get free key',
                'type' => 'drop',
                'value' => 'default',
                'options' => [
                    'default',
                    'recaptcha',
                    'qrcode',
                ],
                'ordering' => 2
            ],
            'captcha_limit' => [
                'var_name' => 'captcha_limit',
                'info' => 'Captcha Limit',
                'description' => 'Limit how many characters will be displayed in the Captcha image.',
                'type' => 'integer',
                'value' => 5,
                'ordering' => 3
            ],
            'recaptcha_public_key' => [
                'var_name' => 'recaptcha_public_key',
                'info' => 'reCAPTCHA Public Key',
                'description' => 'Enter your reCAPTCHA public key here.',
                'type' => 'input:text',
                'value' => '',
                'ordering' => 4
            ],
            'recaptcha_private_key' => [
                'var_name' => 'recaptcha_private_key',
                'info' => 'reCAPTCHA Private Key',
                'description' => 'Enter your reCAPTCHA private key here.',
                'type' => 'input:text',
                'value' => '',
                'ordering' => 5
            ],
            'captcha_use_font' => [
                'var_name' => 'captcha_use_font',
                'info' => 'Use Font (TTF)',
                'description' => 'If enabled and if your server supports the PHP function <a href="http://se.php.net/imagettftext" target="_blank" rel="nofollow">imagettftext</a> the Captcha routine will use a TTF (True Type Font) to create the text instead of using the default image processing string function.<br><br>The font that will be used is controlled by the setting <a href="' . \Phpfox::getLib('url')->makeUrl('admincp.setting.search') . '?var=captcha_font">Captcha Font</a>:',
                'type' => 'boolean',
                'value' => 0,
                'ordering' => 6
            ],
            'captcha_font' => [
                'var_name' => 'captcha_font',
                'info' => 'Captcha Font',
                'description' => 'Select which TTF (True Type Font) you would like to use for your Captcha image.<br><br>Note the setting <a href="' . \Phpfox::getLib('url')->makeUrl('admincp.setting.search') . '?var=captcha_font">Captcha Font</a> must be enabled in order to use this option.',
                'type' => 'select',
                'value' => 'HECK.TTF',
                'options' => [
                    'HECK.TTF' => 'HECK.TTF'
                ],
                'ordering' => 7
            ],
        ];
    }

    protected function setUserGroupSettings()
    {
        $this->user_group_settings = [
            'captcha_on_comment' => [
                'var_name' => 'captcha_on_comment',
                'info' => 'Enable CAPTHCA challenge when a user adds a comment?',
                'description' => '',
                'type' => 'boolean',
                'value' => 0,
            ],
            'captcha_on_blog_add' => [
                'var_name' => 'captcha_on_blog_add',
                'info' => 'Enable CAPTCHA challenge when adding a new blog?',
                'description' => '',
                'type' => 'boolean',
                'value' => 0,
            ],
        ];
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
        $this->_apps_dir = "core-captcha";
        $this->admincp_route = \Phpfox::getLib('url')->makeUrl('admincp.app.settings', ['id' => 'Core_Captcha']);
    }
}