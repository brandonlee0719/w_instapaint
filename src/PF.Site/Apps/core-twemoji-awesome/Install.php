<?php

namespace Apps\PHPfox_Twemoji_Awesome;

use Core\App;

/**
 * Class Install
 * @package Apps\PHPfox_Twemoji_Awesome
 */
class Install extends App\App
{
    private $_app_phrases = [

    ];

    public $js_phrases = [
        'emoji' => 'emoji'
    ];

    public $credits = [
        "Elle Kasai" => "https://github.com/ellekasai/twemoji-awesome",
        "Twitter" => "http://twitter.github.io/twemoji/",
        "linyows" => "https://github.com/linyows/jquery-emoji"
    ];

    public $store_id = 1879;

    protected function setId()
    {
        $this->id = 'PHPfox_Twemoji_Awesome';
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
        $this->name = _p('twemoji_awesome_app');
    }

    protected function setVersion()
    {
        $this->version = '4.6.0';
    }

    protected function setSettings()
    {
        $this->settings = [
            'twemoji_selectors' => [
                'var_name' => "twemoji_selectors",
                'info' => "CSS Selectors",
                "value" => ".panel_rows_preview, .mail_text, .activity_feed_content_status, .comment_mini_text, .item_content, .item_view_content, .activity_feed_content_display, .forum_mini_post ._c",
                "js_variable" => true
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
        $this->addPhrases($this->_app_phrases);
    }

    protected function setOthers()
    {
        $this->admincp_route = \Phpfox::getLib('url')->makeUrl('admincp.app.settings', ['id' => 'PHPfox_Twemoji_Awesome']);
        $this->_publisher = 'phpFox';
        $this->_publisher_url = 'http://store.phpfox.com/';
        $this->_apps_dir = 'core-twemoji-awesome';
    }
}