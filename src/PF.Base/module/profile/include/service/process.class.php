<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Profile_Service_Process
 */
class Profile_Service_Process extends Phpfox_Service
{
    public function clearProfileCache($mUser)
    {
        if (Phpfox::getParam('core.super_cache_system')) {
            $this->cache()->remove(array('profile', 'user_id_' . (int)$mUser));
        }
    }

    /**
     * @param $sMethod
     * @param $aArguments
     * @return null
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('profile.service_process__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}
