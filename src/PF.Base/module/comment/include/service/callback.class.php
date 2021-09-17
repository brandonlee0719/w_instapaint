<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * Comment Callbacks
 *
 * @copyright       [PHPFOX_COPYRIGHT]
 * @author          phpFox
 * @package         Module_Comment
 */
class Comment_Service_Callback extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('comment');
    }

    /**
     * Get Comment Stats in a period time
     *
     * @param int $iStartTime
     * @param int $iEndTime
     *
     * @return array
     */
    public function getSiteStatsForAdmin($iStartTime, $iEndTime)
    {
        $aCond = [];
        $aCond[] = 'view_id = 0';
        if ($iStartTime > 0) {
            $aCond[] = 'AND time_stamp >= \'' . $this->database()->escape($iStartTime) . '\'';
        }
        if ($iEndTime > 0) {
            $aCond[] = 'AND time_stamp <= \'' . $this->database()->escape($iEndTime) . '\'';
        }

        $iCnt = (int)$this->database()->select('COUNT(*)')
            ->from($this->_sTable)
            ->where($aCond)
            ->execute('getSlaveField');

        return [
            'phrase' => 'comment.comment_on_items',
            'total' => $iCnt
        ];
    }

    /**
     * @param int $iId
     *
     * @return bool|string false or url
     */
    public function getRedirectRequest($iId)
    {
        (($sPlugin = Phpfox_Plugin::get('comment.component_service_callback_getredirectrequest__start')) ? eval($sPlugin) : false);

        $aItem = $this->database()->select('comment_id, type_id, item_id')
            ->from($this->_sTable)
            ->where('comment_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aItem['item_id'])) {
            return false;
        }

        $url = Phpfox::callback($aItem['type_id'] . '.getRedirectComment', $aItem['item_id']);
        if (strpos($url, '?')) {
            $url .= '&';
        } else {
            $url .= '?';
        }
        $url .= 'comment=' . $aItem['comment_id'];

        return $url;
    }

    /**
     * @return array
     */
    public function getNotificationSettings()
    {
        (($sPlugin = Phpfox_Plugin::get('comment.component_service_callback_getnotificationsettings__start')) ? eval($sPlugin) : false);

        return [
            'comment.add_new_comment' => [
                'phrase' => _p('new_comments'),
                'default' => 1
            ],
            'comment.approve_new_comment' => [
                'phrase' => _p('comments_for_approval'),
                'default' => 1
            ]
        ];
    }

    /**
     * @param int $iId
     *
     * @return bool|string
     */
    public function getReportRedirect($iId)
    {
        return $this->getRedirectRequest($iId);
    }

    /**
     * Action to take when user cancelled their account
     *
     * @param int $iUser
     */
    public function onDeleteUser($iUser)
    {
        (($sPlugin = Phpfox_Plugin::get('comment.component_service_callback_ondeleteuser__start')) ? eval($sPlugin) : false);

        $aComments = $this->database()
            ->select('comment_id')
            ->from($this->_sTable)
            ->where('user_id = ' . (int)$iUser)
            ->execute('getSlaveRows');
        foreach ($aComments as $aComment) {
            Phpfox::getService('comment.process')->delete($aComment['comment_id']);
        }
        $this->database()->delete(Phpfox::getT('comment_rating'), 'user_id = ' . (int)$iUser);
    }

    /**
     * @return array
     */
    public function spamCheck()
    {
        return [
            'phrase' => _p('comment_title'),
            'value' => Phpfox::getService('comment')->getSpamTotal(),
            'link' => Phpfox_Url::instance()->makeUrl('admincp.comment', ['view' => 'spam'])
        ];
    }

    /**
     * @return array
     */
    public function reparserList()
    {
        return [
            'name' => _p('comments_text'),
            'table' => 'comment_text',
            'original' => 'text',
            'parsed' => 'text_parsed',
            'item_field' => 'comment_id'
        ];
    }

    /**
     * @return array
     */
    public function getDashboardActivity()
    {
        $aUser = Phpfox::getService('user')->get(Phpfox::getUserId(), true);

        return [
            _p('comments_activity') => $aUser['activity_comment']
        ];
    }

    /**
     * @return array
     */
    public function getSiteStatsForAdmins()
    {
        $iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

        return [
            'phrase' => _p('new_comments_stats'),
            'value' => $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('comment'))
                ->where('view_id = 0 AND time_stamp >= ' . $iToday)
                ->execute('getSlaveField')
        ];
    }

    /**
     * @return array
     */
    public function updateCounterList()
    {
        (($sPlugin = Phpfox_Plugin::get('comment.component_service_callback_updatecounterlist__start')) ? eval($sPlugin) : false);
        $aList = array();

        $aList[] = [
            'name' => _p('update_owner_id_for_comments_only_for_those_that_upgraded_from_v1_6_21'),
            'id' => 'comment-order-id'
        ];

        return $aList;
    }

    /**
     * @param int $iId
     * @param int $iPage
     * @param int $iPageLimit
     *
     * @return string|int
     */
    public function updateCounter($iId, $iPage, $iPageLimit)
    {
        (($sPlugin = Phpfox_Plugin::get('comment.component_service_callback_updatecounter__start')) ? eval($sPlugin) : false);

        if (!file_exists(PHPFOX_DIR . 'include' . PHPFOX_DS . 'settings' . PHPFOX_DS . 'server.sett.php')) {
            return Phpfox_Error::set(_p('your_old_v1_6_21_setting_file_must_exist',
                array('file' => 'include' . PHPFOX_DS . 'settings' . PHPFOX_DS . 'server.sett.php')));
        }

        require(PHPFOX_DIR . 'include' . PHPFOX_DS . 'settings' . PHPFOX_DS . 'server.sett.php');

        $sTable = (isset($_CONF['db']['prefix']) ? $_CONF['db']['prefix'] : '') . 'comments';

        if (!$this->database()->tableExists($sTable)) {
            return Phpfox_Error::set(_p('the_database_table_table_does_not_exist', array('table' => $sTable)));
        }

        $iCnt = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('comment'))
            ->where('type_id = \'profile\'')
            ->execute('getSlaveField');

        $aRows = $this->database()->select('m.comment_id, i.user_id AS owner_user_id')
            ->from(Phpfox::getT('comment'), 'm')
            ->join($sTable, 'oc', 'oc.cid = m.upgrade_item_id')
            ->join(Phpfox::getT('user'), 'i', 'i.upgrade_user_id = oc.itemid')
            ->where('type_id = \'profile\'')
            ->limit($iPage, $iPageLimit, $iCnt)
            ->execute('getSlaveRows');

        foreach ($aRows as $aRow) {
            $this->database()->update(Phpfox::getT('comment'), array('owner_user_id' => $aRow['owner_user_id']),
                'comment_id = ' . (int)$aRow['comment_id']);
        }

        (($sPlugin = Phpfox_Plugin::get('comment.component_service_callback_updatecounter__end')) ? eval($sPlugin) : false);

        return $iCnt;
    }

    /**
     * @return array
     */
    public function getActivityPointField()
    {
        return [
            _p('comments_activity') => 'activity_comment'
        ];
    }

    /**
     * @return array
     */
    public function pendingApproval()
    {
        return [
            'phrase' => _p('comments_approve'),
            'value' => $this->getPendingTotal(),
            'link' => Phpfox_Url::instance()->makeUrl('admincp.comment')
        ];
    }

    public function getPendingTotal()
    {
        return $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('comment'))->where('view_id = 1')
            ->execute('getSlaveField');
    }

    public function getAdmincpAlertItems()
    {
        $iTotalPending = $this->getPendingTotal();
        return [
            'message'=> _p('you_have_total_pending_comments', ['total'=>$iTotalPending]),
            'value' => $iTotalPending,
            'link' => Phpfox_Url::instance()->makeUrl('admincp.comment', array('view' => 'pending'))
        ];
    }

    /**
     * @return string to parse to url
     */
    public function getAjaxProfileController()
    {
        return 'comment.profile';
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     *
     * @return mixed
     */
    public function __call($sMethod, $aArguments)
    {
        if (preg_match("/^getNewsFeed(.*?)$/i", $sMethod, $aMatches)) {
            $sModuleId = (isset($aMatches[1])) ? strtolower(explode('_', $aMatches[1])[0]) : false;

            //Check module is enable or not
            if ($sMethod === false || !Phpfox::isModule($sModuleId)) {
                return false;
            }

            return Phpfox::callback(strtolower($aMatches[1]) . '.getCommentNewsFeed', $aArguments[0],
                (isset($aArguments[1]) ? $aArguments[1] : null));
        } elseif (preg_match("/^getFeedRedirect(.*?)$/i", $sMethod, $aMatches)) {
            return Phpfox::callback(strtolower($aMatches[1]) . '.getFeedRedirect', $aArguments[0], $aArguments[1]);
        } elseif (preg_match("/^getNotificationFeed(.*?)$/i", $sMethod, $aMatches)) {
            if (empty($aMatches[1])) {
                $aMatches[1] = 'feed';
            }
            $aMatches[1] = trim($aMatches['1'], '_');

            return Phpfox::callback(strtolower($aMatches[1]) . '.getCommentNotificationFeed', $aArguments[0]);
        } elseif (preg_match("/^getNotificationDeny_Comment_(.+)$/i", $sMethod, $aMatches)) {
            if (count($aMatches) < 1) {
                return false;
            }

            $sModuleId = strtolower($aMatches[1]);

            if (Phpfox::hasCallback($sModuleId, "getRedirectComment")) {
                $sLink = Phpfox::callback("$sModuleId.getRedirectComment", $aArguments[0]['item_id']);
            } else {
                $sLink = '#';
            }

            return [
                'link' => $sLink,
                'message' => _p('your_comment_has_been_denied'),
                'icon' => Phpfox::getLib('template')->getStyle('image', 'activity.png', 'blog'),
                'no_profile_image' => true
            ];
        } elseif (preg_match("/^getNotification(.*?)$/i", $sMethod, $aMatches)) {
            $sModuleId = (isset($aMatches[1])) ? strtolower(explode('_', $aMatches[1])[0]) : false;

            // Check module is enable or not
            if ($sMethod === false || !Phpfox::isModule($sModuleId)) {
                return false;
            }

            return Phpfox::callback(strtolower($aMatches[1]) . '.getCommentNotification', $aArguments[0]);
        } elseif (preg_match("/^getAjaxCommentVar(.*?)$/i", $sMethod, $aMatches)) {
            return Phpfox::callback(strtolower($aMatches[1]) . '.getAjaxCommentVar');
        } elseif (preg_match("/^getCommentItem(.*?)$/i", $sMethod, $aMatches)) {
            return Phpfox::callback(strtolower($aMatches[1]) . '.getCommentItem', $aArguments[0]);
        } elseif (preg_match("/^addComment(.*?)$/i", $sMethod, $aMatches)) {
            return Phpfox::callback(strtolower($aMatches[1]) . '.addComment', $aArguments[0],
                (isset($aArguments[1]) ? $aArguments[1] : null), (isset($aArguments[2]) ? $aArguments[2] : null));
        }

        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('comment.service_callback__call')) {
            return eval($sPlugin);
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);

        return null;
    }

    /**
     * @param $iUserId
     * @return array|bool
     */
    public function getUserStatsForAdmin($iUserId)
    {
        if (!$iUserId) {
            return false;
        }

        $iTotal = db()->select('COUNT(*)')
            ->from(':comment')
            ->where('user_id =' . (int)$iUserId)
            ->execute('getField');

        return [
            'total_name' => _p('comments'),
            'total_value' => $iTotal,
            'type' => 'item'
        ];
    }
}