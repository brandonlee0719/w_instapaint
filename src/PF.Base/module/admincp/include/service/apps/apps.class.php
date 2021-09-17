<?php
defined('PHPFOX') or exit('NO DICE!');

class Admincp_Service_Apps_Apps extends Phpfox_Service
{
    /**
     * @var array of all default apps
     */
    private $_aDefaultApps = [
            'ad'                     => 'ad',
            'admincp'                => 'admincp',
            'announcement'           => 'announcement',
            'api'                    => 'api',
            'attachment'             => 'attachment',
            'ban'                    => 'ban',
            'blog'                   => 'blog',
            'captcha'                => 'captcha',
            'comment'                => 'comment',
            'contact'                => 'contact',
            'core'                   => 'core',
            'custom'                 => 'custom',
            'egift'                  => 'egift',
            'error'                  => 'error',
            'event'                  => 'event',
            'feed'                   => 'feed',
            'forum'                  => 'forum',
            'friend'                 => 'friend',
            'invite'                 => 'invite',
            'language'               => 'language',
            'like'                   => 'like',
            'link'                   => 'link',
            'log'                    => 'log',
            'mail'                   => 'mail',
            'marketplace'            => 'marketplace',
            'music'                  => 'music',
            'newsletter'             => 'newsletter',
            'notification'           => 'notification',
            'page'                   => 'page',
            'pages'                  => 'pages',
            'photo'                  => 'photo',
            'poke'                   => 'poke',
            'poll'                   => 'poll',
            'privacy'                => 'privacy',
            'profile'                => 'profile',
            'quiz'                   => 'quiz',
            'report'                 => 'report',
            'request'                => 'request',
            'rss'                    => 'rss',
            'search'                 => 'search',
            'share'                  => 'share',
            'subscribe'              => 'subscribe',
            'tag'                    => 'tag',
            'theme'                  => 'theme',
            'track'                  => 'track',
            'user'                   => 'user',
            'PHPfox_CDN_Service'     => 'phpFox CDN Service',
            'PHPfox_Facebook'        => 'Facebook Base',
            'PHPfox_Groups'          => 'Groups',
            'PHPfox_Twemoji_Awesome' => 'Twemoji Awesome',
        ];

    /**
     * Check an App ID/module name is default
     *
     * @param string $sName
     *
     * @return bool
     */
    public function isDefault($sName)
    {
        if (substr($sName, 0, 9) == '__module_') {
            $sName = substr_replace($sName, '', 0, 9);
        }
        if (isset($this->_aDefaultApps[$sName])) {
            return true;
        } else {
            return false;
        }
    }

}