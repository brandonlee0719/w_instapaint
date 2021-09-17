<?php

namespace Apps\Core_Newsletter\Service;

use Phpfox_Service;
use Phpfox_Plugin;
use Phpfox;
use Phpfox_Error;

class Callback extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {

    }

    public function getNotificationSettings()
    {
        if (Phpfox::getUserParam('newsletter.show_privacy')) {
            return array(
                'newsletter.can_receive_notification' => array(
                    'phrase' => _p('receive_newsletter'),
                    'default' => 1
                )
            );
        }
        return null;
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     * @return mixed
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('notification.service_callback__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        return Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}
