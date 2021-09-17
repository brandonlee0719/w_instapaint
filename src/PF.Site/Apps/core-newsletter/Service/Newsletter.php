<?php

namespace Apps\Core_Newsletter\Service;

use Phpfox_Service;
use Phpfox_Plugin;
use Phpfox_Error;
use Phpfox;

class Newsletter extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('newsletter');
    }

    /**
     * Sanity check, this function checks for users pending their newsletter and newsletters still incomplete (in process)
     * sets Phpfox_Error
     */
    public function checkPending()
    {
        $aUsers = db()->select('user_id')
            ->from(Phpfox::getT('user_field'))
            ->where('newsletter_state != ' . CORE_NEWSLETTER_STATUS_DRAFT)
            ->execute('getSlaveRows');

        if (!empty($aUsers)) {
            Phpfox_Error::set(_p('there_are_users_still_missing_their_newsletter_total',
                array('total' => count($aUsers))));
            return Phpfox::getLib('url')->makeUrl('admincp.newsletter.manage', array('task' => 'pending-users'));
        }

        $aNewsletters = db()->select('newsletter_id')
            ->from($this->_sTable)
            ->where('state = ' . CORE_NEWSLETTER_STATUS_IN_PROGRESS)
            ->execute('getSlaveRows');
        if (!empty($aNewsletters)) {
            Phpfox_Error::set(_p('there_are_newsletters_in_process_total', array('total' => count($aNewsletters))));
            return Phpfox::getLib('url')->makeUrl('admincp.newsletter.manage', array('task' => 'pending-tasks'));
        }
        return null;
    }


    public function get($iId = null)
    {
        if (is_int($iId)) {
            db()->where('n.newsletter_id = ' . (int)$iId);
        }

        $aNewsletters = db()->select('n.*,n.user_group_id as news_user_group_id, nt.*, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'n')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = n.user_id')
            ->join(Phpfox::getT('newsletter_text'), 'nt', 'nt.newsletter_id = n.newsletter_id')
            ->order('time_stamp DESC')
            ->execute('getSlaveRows');
        if ($iId !== null && !empty($aNewsletters)) {
            return reset($aNewsletters);
        }
        return $aNewsletters;
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
        if ($sPlugin = Phpfox_Plugin::get('notification.service_notification__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        return Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

    /**
     * filter all bbcode tag type
     * @param $sText , string
     * @return string
     */
    public function filterBbcodeTags($sText)
    {
        return preg_replace('/\[[^\]]*\][^\/]*\[\/[^\]]*\]/', '', $sText);
    }
}
