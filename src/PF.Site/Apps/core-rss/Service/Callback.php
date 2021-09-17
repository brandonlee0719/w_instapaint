<?php

namespace Apps\Core_RSS\Service;

use Phpfox;
use Phpfox_Plugin;
use Phpfox_Error;
use Phpfox_Service;

class Callback extends Phpfox_Service
{
    public function getProfileLink()
    {
        return 'rss';
    }

    public function getProfileSettings()
    {
        return array(
            'rss.display_on_profile' => array(
                'phrase' => _p('display_rss_subscribers_count')
            ),
            'rss.can_subscribe_profile' => array(
                'phrase' => _p('subscribe_to_your_rss_feed'),
                'default' => '1',
                'no_user' => true
            )
        );
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     * @return null
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('rss.service_callback__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}
