<?php
namespace Apps\Core_Events\Service;

use Phpfox;
use Phpfox_Database;
use Phpfox_Error;
use Phpfox_Module;
use Phpfox_Plugin;
use Phpfox_Template;
use Phpfox_Url;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Callback
 * @package Apps\Core_Events\Service
 */
class Callback extends \Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('event');
    }

    /**
     * @param int $iStartTime
     * @param int $iEndTime
     *
     * @return array
     */
    public function getSiteStatsForAdmin($iStartTime, $iEndTime)
    {
        $aCond = array();
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
            'phrase' => 'event.events',
            'total' => $iCnt
        ];
    }

    /**
     * @return array
     */
    public function getDashboardActivity()
    {
        $aUser = Phpfox::getService('user')->get(Phpfox::getUserId(), true);

        return [
            _p('events') => $aUser['activity_event']
        ];
    }

    /**
     * @param int $iId
     *
     * @return array|int|string
     */
    public function getCommentItem($iId)
    {
        $aRow = $this->database()->select('feed_comment_id AS comment_item_id, privacy_comment, user_id AS comment_user_id')
            ->from(Phpfox::getT('event_feed_comment'))
            ->where('feed_comment_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        $aRow['comment_view_id'] = '0';

        if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'], $aRow['privacy_comment'])) {
            Phpfox_Error::set(_p('unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));
            unset($aRow['comment_item_id']);
        }

        $aRow['parent_module_id'] = 'event';

        return $aRow;
    }

    /**
     * @param int $iItemId
     *
     * @return array
     */
    public function getFeedDetails($iItemId)
    {
        return [
            'module' => 'event',
            'table_prefix' => 'event_',
            'item_id' => $iItemId
        ];
    }

    /**
     * @param array $aVals
     * @param null $iUserId, deprecated, remove in 4.7.0
     * @param null $sUserName, deprecated, remove in 4.7.0
     */
    public function addComment($aVals, $iUserId = null, $sUserName = null)
    {
        $aRow = $this->database()->select('fc.feed_comment_id, fc.user_id, e.event_id, e.title, u.full_name, u.gender')
            ->from(Phpfox::getT('event_feed_comment'), 'fc')
            ->join(Phpfox::getT('event'), 'e', 'e.event_id = fc.parent_user_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
            ->where('fc.feed_comment_id = ' . (int)$aVals['item_id'])
            ->execute('getSlaveRow');

        // Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
        if (empty($aVals['parent_id'])) {
            $this->database()->updateCounter('event_feed_comment', 'total_comment', 'feed_comment_id',
                $aRow['feed_comment_id']);
        }

        // Send the user an email
        $sLink = Phpfox_Url::instance()->permalink(array('event', 'comment-id' => $aRow['feed_comment_id']),
            $aRow['event_id'], $aRow['title']);
        $sItemLink = Phpfox_Url::instance()->permalink('event', $aRow['event_id'], $aRow['title']);

        Phpfox::getService('comment.process')->notify(array(
                'user_id' => $aRow['user_id'],
                'item_id' => $aRow['feed_comment_id'],
                'owner_subject' => _p('full_name_commented_on_a_comment_posted_on_the_event_title',
                    array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['title'])),
                'owner_message' => _p('full_name_commented_on_one_of_your_comments_you_posted_on_the_event', array(
                    'full_name' => Phpfox::getUserBy('full_name'),
                    'item_link' => $sItemLink,
                    'title' => $aRow['title'],
                    'link' => $sLink
                )),
                'owner_notification' => 'comment.add_new_comment',
                'notify_id' => 'event_comment_feed',
                'mass_id' => 'event',
                'mass_subject' => (Phpfox::getUserId() == $aRow['user_id'] ? _p('full_name_commented_on_one_of_gender_event_comments',
                    array(
                        'full_name' => Phpfox::getUserBy('full_name'),
                        'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1)
                    )) : _p('full_name_commented_on_one_of_row_full_name_s_event_comments',
                    array('full_name' => Phpfox::getUserBy('full_name'), 'row_full_name' => $aRow['full_name']))),
                'mass_message' => (Phpfox::getUserId() == $aRow['user_id'] ? _p('full_name_commented_on_one_of_gender_own_comments_on_the_event',
                    array(
                        'full_name' => Phpfox::getUserBy('full_name'),
                        'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1),
                        'item_link' => $sItemLink,
                        'title' => $aRow['title'],
                        'link' => $sLink
                    )) : _p('full_name_commented_on_one_of_row_full_name_s', array(
                    'full_name' => Phpfox::getUserBy('full_name'),
                    'row_full_name' => $aRow['full_name'],
                    'item_link' => $sItemLink,
                    'title' => $aRow['title'],
                    'link' => $sLink
                )))
            )
        );
    }

    /**
     * @param array $aNotification
     *
     * @return array
     */
    public function getNotificationComment_Feed($aNotification)
    {
        return $this->getCommentNotification($aNotification);
    }

    /**
     * @param array $aNotification
     *
     * @return array|bool
     */
    public function getCommentNotification($aNotification)
    {
        $aRow = $this->database()
            ->select('fc.feed_comment_id, u.user_id, u.gender, u.user_name, u.full_name, e.event_id, e.title')
            ->from(Phpfox::getT('event_feed_comment'), 'fc')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
            ->join(Phpfox::getT('event'), 'e', 'e.event_id = fc.parent_user_id')
            ->where('fc.feed_comment_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['feed_comment_id'])) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'],
            Phpfox::getParam('notification.total_notification_title_length'), '...');

        if ($aNotification['user_id'] == $aRow['user_id']) {
            if (isset($aNotification['extra_users']) && count($aNotification['extra_users'])) {
                $sPhrase = _p('users_commented_on_span_class_drop_data_user_row_full_name_s_span_comment_on_the_event_title',
                    array(
                        'users' => Phpfox::getService('notification')->getUsers($aNotification, true),
                        'row_full_name' => $aRow['full_name'],
                        'title' => $sTitle
                    ));
            } else {
                $sPhrase = _p('users_commented_on_gender_own_comment_on_the_event_title', array(
                    'users' => $sUsers,
                    'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1),
                    'title' => $sTitle
                ));
            }
        } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
            $sPhrase = _p('users_commented_on_one_of_your_comments_on_the_event_title',
                array('users' => $sUsers, 'title' => $sTitle));
        } else {
            $sPhrase = _p('users_commented_on_one_of_span_class_drop_data_user_row_full_name_s_span_comments_on_the_event_title',
                array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
        }

        return [
            'link' => Phpfox_Url::instance()->permalink([
                'event',
                'comment-id' => $aRow['feed_comment_id']
            ], $aRow['event_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        ];
    }

    /**
     * @param array $aVals
     *
     * @return array
     */
    public function uploadVideo($aVals)
    {
        return [
            'module' => 'event',
            'item_id' => (is_array($aVals) && isset($aVals['callback_item_id']) ? $aVals['callback_item_id'] : (int)$aVals)
        ];
    }

    /**
     * @param array $aNotification
     *
     * @return array|bool
     */
    public function getNotificationComment($aNotification)
    {
        $aRow = $this->database()
            ->select('fc.feed_comment_id, u.user_id, u.gender, u.user_name, u.full_name, e.event_id, e.title')
            ->from(Phpfox::getT('event_feed_comment'), 'fc')
            ->join(Phpfox::getT('event'), 'e', 'e.event_id = fc.parent_user_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('fc.feed_comment_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (empty($aRow)) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'],
            Phpfox::getParam('notification.total_notification_title_length'), '...');

        if ($aNotification['user_id'] == $aRow['user_id']) {
            if (isset($aNotification['extra_users']) && count($aNotification['extra_users'])) {
                $sPhrase = _p('users_commented_on_span_class_drop_data_user_row_full_name_s_span_event_title', array(
                    'users' => Phpfox::getService('notification')->getUsers($aNotification, true),
                    'row_full_name' => $aRow['full_name'],
                    'title' => $sTitle
                ));
            } else {
                $sPhrase = _p('users_commented_on_gender_own_event_title', array(
                    'users' => $sUsers,
                    'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1),
                    'title' => $sTitle
                ));
            }
        } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
            $sPhrase = _p('users_commented_on_your_event_title', array('users' => $sUsers, 'title' => $sTitle));
        } else {
            $sPhrase = _p('users_commented_on_span_class_drop_data_user_row_full_name_s_span_event_title',
                array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
        }

        return array(
            'link' => Phpfox_Url::instance()->permalink(array('event', 'comment-id' => $aRow['feed_comment_id']),
                $aRow['event_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        );
    }

    /**
     * @param array $aNotification
     *
     * @return array
     */
    public function getNotificationComment_Like($aNotification)
    {
        $aRow = $this->database()->select('fc.feed_comment_id, u.user_id, u.gender, u.user_name, u.full_name, e.event_id, e.title')
            ->from(Phpfox::getT('event_feed_comment'), 'fc')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
            ->join(Phpfox::getT('event'), 'e', 'e.event_id = fc.parent_user_id')
            ->where('fc.feed_comment_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'],
            Phpfox::getParam('notification.total_notification_title_length'), '...');

        if ($aNotification['user_id'] == $aRow['user_id']) {
            if (isset($aNotification['extra_users']) && count($aNotification['extra_users'])) {
                $sPhrase = _p('users_liked_span_class_drop_data_user_row_full_name_s_span_comment_on_the_event_title',
                    array(
                        'users' => Phpfox::getService('notification')->getUsers($aNotification, true),
                        'row_full_name' => $aRow['full_name'],
                        'title' => $sTitle
                    ));
            } else {
                $sPhrase = _p('users_liked_gender_own_comment_on_the_event_title', array(
                    'users' => $sUsers,
                    'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1),
                    'title' => $sTitle
                ));
            }
        } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
            $sPhrase = _p('users_liked_one_of_your_comments_on_the_event_title',
                array('users' => $sUsers, 'title' => $sTitle));
        } else {
            $sPhrase = _p('users_liked_one_on_span_class_drop_data_user_row_full_name_s_span_comments_on_the_event_title',
                array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
        }

        return array(
            'link' => Phpfox_Url::instance()->permalink(array('event', 'comment-id' => $aRow['feed_comment_id']),
                $aRow['event_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        );
    }

    /**
     * Enables a sponsor after being paid for or admin approved
     *
     * @param array $aParams
     *
     * @return mixed
     */
    public function enableSponsor($aParams)
    {
        return Phpfox::getService('event.process')->sponsor((int)$aParams['item_id'], 1);
    }

    /**
     * @param array $aVals
     * @param string $sText
     *
     * @return void
     */
    public function updateCommentText($aVals, $sText)
    {
        $aEvent = $this->database()->select('m.event_id, m.title, m.title_url, u.full_name, u.user_id, u.user_name')
            ->from($this->_sTable, 'm')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = m.user_id')
            ->where('m.event_id = ' . (int)$aVals['item_id'])
            ->execute('getSlaveRow');

        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->update('comment_event', $aVals['item_id'],
            serialize(array('content' => $sText, 'title' => $aEvent['title'])), $aVals['comment_id']) : null);
    }

    /**
     * @param int $iId
     * @param string $sName
     *
     * @return string
     */
    public function getItemName($iId, $sName)
    {
        return _p('a_href_link_on_name_s_event_a',
            array('link' => Phpfox_Url::instance()->makeUrl('comment.view', array('id' => $iId)), 'name' => $sName));
    }

    /**
     * @param array $aParams
     *
     * @return bool|string
     */
    public function getLink($aParams)
    {
        // get the owner of this song
        $aEvent = $this->database()
            ->select('e.event_id, e.title')
            ->from(Phpfox::getT('event'), 'e')
            ->where('e.event_id = ' . (int)$aParams['item_id'])
            ->execute('getSlaveRow');
        if (empty($aEvent)) {
            return false;
        }
        return Phpfox::permalink('event', $aEvent['event_id'], $aEvent['title']);
    }

    /**
     * @param array $aRow
     *
     * @return bool|array
     */
    public function getCommentNewsFeed($aRow)
    {
        $oUrl = Phpfox_Url::instance();

        if (!Phpfox::getLib('parse.format')->isSerialized($aRow['content'])) {
            return false;
        }

        $aParts = unserialize($aRow['content']);
        $aRow['text'] = _p('a_href_user_link_user_name_a_added_a_comment_on_the_event_a_href_title_link_title_a', array(
                'user_name' => $aRow['owner_full_name'],
                'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                'title_link' => $aRow['link'],
                'title' => Phpfox::getService('feed')->shortenTitle($aParts['title'])
            )
        );

        $aRow['text'] .= Phpfox::getService('feed')->quote($aParts['content']);

        return $aRow;
    }

    /**
     * @param int $iId, deprecated, remove in 4.7.0
     * @param int $iChild
     *
     * @return bool|string
     */
    public function getFeedRedirectFeedLike($iId, $iChild)
    {
        return $this->getFeedRedirect($iChild);
    }

    /**
     * @param int $iId
     * @param int $iChild, deprecated, remove in 4.7.0
     *
     * @return bool|string
     */
    public function getFeedRedirect($iId, $iChild = 0)
    {
        $aEvent = $this->database()->select('e.event_id, e.title')
            ->from($this->_sTable, 'e')
            ->where('e.event_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aEvent['event_id'])) {
            return false;
        }

        return Phpfox::permalink('event', $aEvent['event_id'], $aEvent['title']);
    }

    /**
     * @param int $iId
     */
    public function deleteComment($iId)
    {
        $this->database()->updateCounter('event', 'total_comment', 'event_id', $iId, true);
    }

    /**
     * @param int $iId
     *
     * @return bool|string
     */
    public function getReportRedirect($iId)
    {
        return $this->getFeedRedirect($iId);
    }

    /**
     * @param array $aRow
     *
     * @return array
     */
    public function getNewsFeed($aRow)
    {
        if ($sPlugin = Phpfox_Plugin::get('event.service_callback_getnewsfeed_start')) {
            eval($sPlugin);
        }
        $oUrl = Phpfox_Url::instance();

        $aRow['text'] = _p('owner_full_name_added_a_new_event_title', array(
                'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                'owner_full_name' => $aRow['owner_full_name'],
                'title_link' => $aRow['link'],
                'title' => Phpfox::getService('feed')->shortenTitle($aRow['content'])
            )
        );

        $aRow['icon'] = 'module/event.png';
        $aRow['enable_like'] = true;

        return $aRow;
    }

    /**
     * @param string $sGroupUrl
     * @param int $iGroupId
     *
     * @return array|bool
     */
    public function groupMenu($sGroupUrl, $iGroupId)
    {
        //Todo on group server do not have method hasAccess
        if (!Phpfox::getService('groups')->hasAccess($iGroupId, 'can_use_event')) {
            return false;
        }

        return [
            _p('events') => [
                'active' => 'event',
                'url' => Phpfox_Url::instance()->makeUrl('group', [$sGroupUrl, 'event'])
            ]
        ];
    }

    /**
     * @param int $iId
     *
     * @return bool
     */
    public function deleteGroup($iId)
    {
        $aEvents = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('module_id = \'group\' AND item_id = ' . (int)$iId)
            ->execute('getSlaveRows');

        foreach ($aEvents as $aEvent) {
            Phpfox::getService('event.process')->delete($aEvent['event_id'], $aEvent);
        }

        return true;
    }

    /**
     * @return array
     */
    public function getDashboardLinks()
    {
        return [
            'submit' => [
                'phrase' => _p('create_an_event'),
                'link' => 'event.add',
                'image' => 'misc/calendar_add.png'
            ],
            'edit' => [
                'phrase' => _p('manage_events'),
                'link' => 'event.view_my',
                'image' => 'misc/calendar_edit.png'
            ]
        ];
    }

    /**
     * @return array
     */
    public function getBlockDetailsProfile()
    {
        return array(
            'title' => _p('events')
        );
    }

    /**
     * Action to take when user cancelled their account
     *
     * @param int $iUser
     *
     * @return void
     */
    public function onDeleteUser($iUser)
    {
        $aEvents = $this->database()
            ->select('event_id')
            ->from($this->_sTable)
            ->where('user_id = ' . (int)$iUser)
            ->execute('getSlaveRows');

        $null = null;
        foreach ($aEvents as $aEvent) {
            Phpfox::getService('event.process')->delete($aEvent['event_id'], $null, true);
        }
    }

    /**
     * @return array
     */
    public function getGroupPosting()
    {
        return [
            _p('can_create_event') => 'can_create_event'
        ];
    }

    /**
     * @return array
     */
    public function getGroupAccess()
    {
        return [
            _p('view_events') => 'can_use_event'
        ];
    }

    /**
     * @param array $aRow
     *
     * @return array
     */
    public function getNotificationFeedApproved($aRow)
    {
        return array(
            'message' => _p('your_event_title_has_been_approved',
                array('title' => Phpfox::getLib('parse.output')->shorten($aRow['item_title'], 20, '...'))),
            'link' => Phpfox_Url::instance()->makeUrl('event', array('redirect' => $aRow['item_id'])),
            'path' => 'event.url_image',
            'suffix' => '_120'
        );
    }

    /**
     * @return array
     */
    public function getGlobalPrivacySettings()
    {
        return [
            'event.display_on_profile' => [
                'phrase' => _p('events'),
                'default' => '0'
            ]
        ];
    }

    /**
     * @return array
     */
    public function pendingApproval()
    {
        return [
            'phrase' => _p('events'),
            'value' => Phpfox::getService('event')->getPendingTotal(),
            'link' => Phpfox_Url::instance()->makeUrl('event', ['view' => 'pending'])
        ];
    }

    public function getAdmincpAlertItems()
    {
        $iTotalPending = Phpfox::getService('event')->getPendingTotal();
        return [
            'message'=> _p('you_have_total_pending_events', ['total'=>$iTotalPending]),
            'value' => $iTotalPending,
            'link' => Phpfox::getLib('url')->makeUrl('event', ['view' => 'pending'])
        ];
    }

    /**
     * @return string
     */
    public function getUserCountFieldInvite()
    {
        return 'event_invite';
    }

    /**
     * @param array $aRow
     *
     * @return array
     */
    public function getNotificationFeedInvite($aRow)
    {
        return [
            'message' => _p('full_name_invited_you_to_an_event', [
                'user_link' => Phpfox_Url::instance()->makeUrl($aRow['user_name']),
                'full_name' => $aRow['full_name']
            ]),
            'link' => Phpfox_Url::instance()->makeUrl('event', ['redirect' => $aRow['item_id']])
        ];
    }

    /**
     * @return array
     */
    public function reparserList()
    {
        return [
            'name' => _p('event_text'),
            'table' => 'event_text',
            'original' => 'description',
            'parsed' => 'description_parsed',
            'item_field' => 'event_id'
        ];
    }

    /**
     * @return array
     */
    public function getSiteStatsForAdmins()
    {
        $iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

        return [
            'phrase' => _p('events'),
            'value' => $this->database()
                ->select('COUNT(*)')
                ->from(Phpfox::getT('event'))
                ->where('view_id = 0 AND time_stamp >= ' . $iToday)
                ->execute('getSlaveField')
        ];
    }

    /**
     * @param int $iId video_id
     *
     * @return array in the format:
     * array(
     *    'title' => 'item title',            <-- required
     *  'link'  => 'makeUrl()'ed link',            <-- required
     *  'paypal_msg' => 'message for paypal'        <-- required
     *  'item_id' => int                <-- required
     *  'user_id;   => owner's user id            <-- required
     *    'error' => 'phrase if item doesnt exit'        <-- optional
     *    'extra' => 'description'            <-- optional
     *    'image' => 'path to an image',            <-- optional
     *    'image_dir' => 'photo.url_photo|...        <-- optional (required if image)
     * )
     */
    public function getToSponsorInfo($iId)
    {
        // check that this user has access to this group
        $aEvent = $this->database()->select('e.user_id, e.event_id as item_id, e.title, e.privacy, e.location, e.start_time, e.end_time, e.image_path as image, e.server_id,e.user_id')
            ->from($this->_sTable, 'e')
            ->where('e.event_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (empty($aEvent)) {
            return array('error' => _p('sponsor_error_not_found'));
        }

        if ($aEvent['privacy'] > 0) {
            return array('error' => _p('sponsor_error_privacy'));
        }

        $aEvent['title'] = _p('sponsor_title', array('sEventTitle' => $aEvent['title']));
        $aEvent['paypal_msg'] = _p('sponsor_paypal_message', array('sEventTitle' => $aEvent['title']));
        $aEvent['link'] = Phpfox::permalink('event', $aEvent['item_id'], $aEvent['title']);
        $aEvent['extra'] = '<b>' . _p('date') . '</b> ' . Phpfox::getTime('l, F j, Y g:i a',
                $aEvent['start_time']) . ' - ';

        if (date('dmy', $aEvent['start_time']) === date('dmy', $aEvent['end_time'])) {
            $aEvent['extra'] .= Phpfox::getTime('g:i a', $aEvent['end_time']);
        } else {
            $aEvent['extra'] .= Phpfox::getTime('l, F j, Y g:i a', $aEvent['end_time']);
        }

        if (isset($aEvent['image']) && $aEvent['image'] != '') {
            $aEvent['image_dir'] = 'event.url_image';
            $aEvent['image'] = sprintf($aEvent['image'], '');
        }
        $aEvent = array_merge($aEvent, [
            'redirect_completed' => 'event',
            'message_completed' => _p('purchase_event_sponsor_completed'),
            'redirect_pending_approval' => 'event',
            'message_pending_approval' => _p('purchase_event_sponsor_pending_approval')
        ]);
        return $aEvent;
    }

    /**
     * @param $aRow
     *
     * @return mixed
     */
    public function getNewsFeedFeedLike($aRow)
    {
        if ($aRow['owner_user_id'] == $aRow['viewer_user_id']) {
            $aRow['text'] = _p('a_href_user_link_full_name_a_liked_their_own_a_href_link_event_a', array(
                    'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
                    'user_link' => Phpfox_Url::instance()->makeUrl($aRow['owner_user_name']),
                    'link' => $aRow['link']
                )
            );
        } else {
            $aRow['text'] = _p('a_href_user_link_full_name_a_liked_a_href_view_user_link_view_full_name_a_s_a_href_link_event_a',
                array(
                    'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
                    'user_link' => Phpfox_Url::instance()->makeUrl($aRow['owner_user_name']),
                    'view_full_name' => Phpfox::getLib('parse.output')->clean($aRow['viewer_full_name']),
                    'view_user_link' => Phpfox_Url::instance()->makeUrl($aRow['viewer_user_name']),
                    'link' => $aRow['link']
                )
            );
        }

        $aRow['icon'] = 'misc/thumb_up.png';

        return $aRow;
    }

    /**
     * @param array $aRow
     *
     * @return array
     */
    public function getNotificationFeedNotifyLike($aRow)
    {
        return [
            'message' => _p('a_href_user_link_full_name_a_liked_your_a_href_link_event_a', [
                'full_name' => Phpfox::getLib('parse.output')->clean($aRow['full_name']),
                'user_link' => Phpfox_Url::instance()->makeUrl($aRow['user_name']),
                'link' => Phpfox_Url::instance()->makeUrl('event', ['redirect' => $aRow['item_id']])
            ]),
            'link' => Phpfox_Url::instance()->makeUrl('event', ['redirect' => $aRow['item_id']]),
            'path' => 'event.url_image',
            'suffix' => '_120'
        ];
    }

    /**
     * @param int $iItemId
     *
     * @return string
     */
    public function sendLikeEmail($iItemId)
    {
        return _p('a_href_user_link_full_name_a_liked_your_a_href_link_event_a', [
            'full_name' => Phpfox::getLib('parse.output')->clean(Phpfox::getUserBy('full_name')),
            'user_link' => Phpfox_Url::instance()->makeUrl(Phpfox::getUserBy('user_name')),
            'link' => Phpfox_Url::instance()->makeUrl('event', ['redirect' => $iItemId])
        ]);
    }

    /**
     * @param int $iId
     *
     * @return bool|string
     */
    public function getRedirectComment($iId)
    {
        return $this->getFeedRedirect($iId);
    }

    /**
     * @return array
     */
    public function getSqlTitleField()
    {
        return [
            'table' => 'event',
            'field' => 'title',
            'has_index' => 'title'
        ];
    }

    /**
     * @return array
     */
    public function updateCounterList()
    {
        $aList = [];

        $aList[] = [
            'name' => _p('event_invite_count'),
            'id' => 'event-invite-count'
        ];

        return $aList;
    }

    /**
     * @param int $iId
     * @param int $iPage
     * @param int $iPageLimit
     *
     * @return int|null
     */
    public function updateCounter($iId, $iPage, $iPageLimit)
    {
        if ($iId == 'event-invite-count') {
            $iCnt = $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('user'))
                ->execute('getSlaveField');

            $aRows = $this->database()->select('u.user_id, COUNT(gi.invite_id) AS total_invites')
                ->from(Phpfox::getT('user'), 'u')
                ->leftJoin(Phpfox::getT('event_invite'), 'gi', 'gi.invited_user_id = u.user_id')
                ->group('u.user_id')
                ->limit($iPage, $iPageLimit, $iCnt)
                ->execute('getSlaveRows');

            foreach ($aRows as $aRow) {
                $this->database()->update(Phpfox::getT('user_count'), array('event_invite' => $aRow['total_invites']),
                    'user_id = ' . (int)$aRow['user_id']);
            }

            return $iCnt;
        }
        return null;
    }

    /**
     * @param array $aItem
     *
     * @return array|bool
     */
    public function getActivityFeedComment($aItem)
    {
        if (Phpfox::isModule('like')) {
            $this->database()->select('l.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'l',
                    'l.type_id = \'event_comment\' AND l.item_id = fc.feed_comment_id AND l.user_id = ' . Phpfox::getUserId());
        }

        $aRow = $this->database()->select('fc.*, e.event_id, e.title')
            ->from(Phpfox::getT('event_feed_comment'), 'fc')
            ->join(Phpfox::getT('event'), 'e', 'e.event_id = fc.parent_user_id')
            ->where('fc.feed_comment_id = ' . (int)$aItem['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['event_id'])) {
            return false;
        }

        $sLink = Phpfox_Url::instance()->permalink(array('event', 'comment-id' => $aRow['feed_comment_id']),
            $aRow['event_id'], $aRow['title']);

        $aReturn = array(
            'no_share' => true,
            'feed_status' => $aRow['content'],
            'feed_link' => $sLink,
            'total_comment' => $aRow['total_comment'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => (isset($aRow['is_liked']) ? $aRow['is_liked'] : false),
            'feed_icon' => Phpfox::getLib('image.helper')->display(array(
                'theme' => 'misc/comment.png',
                'return_url' => true
            )),
            'time_stamp' => $aRow['time_stamp'],
            'enable_like' => true,
            'comment_type_id' => 'event',
            'like_type_id' => 'event_comment',
            'parent_user_id' => $aRow['parent_user_id']
        );

        return $aReturn;
    }

    /**
     * @param int $iItemId
     * @param bool $bDoNotSendEmail
     *
     * @return bool|null
     */
    public function addLikeComment($iItemId, $bDoNotSendEmail = false)
    {
        $aRow = $this->database()->select('fc.feed_comment_id, fc.content, fc.user_id, e.event_id, e.title')
            ->from(Phpfox::getT('event_feed_comment'), 'fc')
            ->join(Phpfox::getT('event'), 'e', 'e.event_id = fc.parent_user_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
            ->where('fc.feed_comment_id = ' . (int)$iItemId)
            ->execute('getSlaveRow');

        if (!isset($aRow['feed_comment_id'])) {
            return false;
        }

        $this->database()->updateCount('like', 'type_id = \'event_comment\' AND item_id = ' . (int)$iItemId . '',
            'total_like', 'event_feed_comment', 'feed_comment_id = ' . (int)$iItemId);

        if (!$bDoNotSendEmail) {
            $sLink = Phpfox_Url::instance()->permalink(array('event', 'comment-id' => $aRow['feed_comment_id']),
                $aRow['event_id'], $aRow['title']);
            $sItemLink = Phpfox_Url::instance()->permalink('event', $aRow['event_id'], $aRow['title']);

            Phpfox::getLib('mail')->to($aRow['user_id'])
                ->subject(array(
                    'event.full_name_liked_a_comment_you_posted_on_the_event_title',
                    array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['title'])
                ))
                ->message(array(
                    'event.full_name_liked_your_comment_message_event',
                    array(
                        'full_name' => Phpfox::getUserBy('full_name'),
                        'link' => $sLink,
                        'content' => Phpfox::getLib('parse.output')->shorten($aRow['content'], 50, '...'),
                        'item_link' => $sItemLink,
                        'title' => $aRow['title']
                    )
                ))
                ->notification('like.new_like')
                ->send();

            Phpfox::getService('notification.process')->add('event_comment_like', $aRow['feed_comment_id'],
                $aRow['user_id']);
        }
        return null;
    }

    /**
     * @param int $iItemId
     *
     * @return void
     */
    public function deleteLikeComment($iItemId)
    {
        $this->database()->updateCount('like', 'type_id = \'event_comment\' AND item_id = ' . (int)$iItemId . '',
            'total_like', 'event_feed_comment', 'feed_comment_id = ' . (int)$iItemId);
    }

    /**
     * @param int $iId
     *
     * @return array
     */
    public function addPhoto($iId)
    {
        return [
            'module' => 'event',
            'item_id' => $iId,
            'table_prefix' => 'event_'
        ];
    }

    /**
     * @param array $aVals
     *
     * @return array
     */
    public function addLink($aVals)
    {
        return [
            'module' => 'event',
            'item_id' => $aVals['callback_item_id'],
            'table_prefix' => 'event_'
        ];
    }

    /**
     * @param int $iEvent
     *
     * @return array
     */
    public function getFeedDisplay($iEvent)
    {
        return [
            'module' => 'event',
            'table_prefix' => 'event_',
            'ajax_request' => 'event.addFeedComment',
            'item_id' => $iEvent
        ];
    }

    /**
     * @param int $iItemId
     * @param bool $bDoNotSendEmail
     *
     * @return bool|null
     */
    public function addLike($iItemId, $bDoNotSendEmail = false)
    {
        $aRow = $this->database()->select('event_id, title, user_id')
            ->from(Phpfox::getT('event'))
            ->where('event_id = ' . (int)$iItemId)
            ->execute('getSlaveRow');

        if (!isset($aRow['event_id'])) {
            return false;
        }

        $this->database()->updateCount('like', 'type_id = \'event\' AND item_id = ' . (int)$iItemId . '', 'total_like',
            'event', 'event_id = ' . (int)$iItemId);

        if (!$bDoNotSendEmail) {
            $sLink = Phpfox::permalink('event', $aRow['event_id'], $aRow['title']);

            Phpfox::getLib('mail')->to($aRow['user_id'])
                ->subject(array(
                    'event.full_name_liked_your_event_title',
                    array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['title'])
                ))
                ->message(array(
                    'event.full_name_liked_your_event_message',
                    array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aRow['title'])
                ))
                ->notification('like.new_like')
                ->send();

            Phpfox::getService('notification.process')->add('event_like', $aRow['event_id'], $aRow['user_id']);
        }
        return null;
    }

    /**
     * @param int $iItemId
     */
    public function deleteLike($iItemId)
    {
        $this->database()->updateCount('like', 'type_id = \'event\' AND item_id = ' . (int)$iItemId . '', 'total_like',
            'event', 'event_id = ' . (int)$iItemId);
    }

    /**
     * @param array $aNotification
     *
     * @return array|bool
     */
    public function getNotificationLike($aNotification)
    {
        $aRow = $this->database()->select('e.event_id, e.title, e.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('event'), 'e')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('e.event_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['event_id'])) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'],
            Phpfox::getParam('notification.total_notification_title_length'), '...');

        if ($aNotification['user_id'] == $aRow['user_id']) {
            $sPhrase = _p('users_liked_gender_own_event_title', array(
                'users' => $sUsers,
                'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1),
                'title' => $sTitle
            ));
        } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
            $sPhrase = _p('users_liked_your_event_title', array('users' => $sUsers, 'title' => $sTitle));
        } else {
            $sPhrase = _p('users_liked_span_class_drop_data_user_row_full_name_s_span_event_title',
                array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
        }

        return [
            'link' => Phpfox_Url::instance()->permalink('event', $aRow['event_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        ];
    }

    /**
     * @return void
     */
    public function canShareItemOnFeed()
    {
    }

    /**
     * @param array $aRow
     *
     * @return bool|array
     */
    public function getActivityFeedCustomChecks($aRow)
    {
        if ((defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE') && !Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->hasPerm(null,
                    'event.view_browse_events'))
            || (!defined('PHPFOX_IS_PAGES_VIEW') && $aRow['custom_data_cache']['module_id'] == 'pages' && !Phpfox::getService('pages')->hasPerm($aRow['custom_data_cache']['item_id'],
                    'event.view_browse_events'))
        ) {
            return false;
        }

        return $aRow;
    }

    /**
     * @param array $aItem
     * @param null $aCallback, deprecated, remove in 4.7.0
     * @param bool $bIsChildItem
     *
     * @return array|bool
     */
    public function getActivityFeed($aItem, $aCallback = null, $bIsChildItem = false)
    {
        $aEvent = Phpfox::getService('event')->getEvent($aItem['item_id']);
        /**
         * Check active parent module
         */
        if (isset($aEvent['module_id']) && $aEvent['module_id']) {
            try {
                Phpfox_Module::instance()->get($aEvent['module_id']);
            } catch (Exception $e) {
                return false;
            }
        }

        $this->database()->select(Phpfox::getUserField('u2') . ', ')->join(Phpfox::getT('user'), 'u2',
            'u2.user_id = e.user_id');
        $sSelect = 'e.start_time, e.end_time, e.user_id, e.event_id, e.module_id, e.item_id, e.title, e.time_stamp, e.image_path, e.server_id, e.total_like, e.total_comment, e.location, e.privacy, e.privacy_comment, e.start_time, e.view_id, e.is_sponsor, et.description_parsed';
        if (Phpfox::isModule('like')) {
            $sSelect .= ', l.like_id AS is_liked';
            $this->database()->leftJoin(Phpfox::getT('like'), 'l',
                'l.type_id = \'event\' AND l.item_id = e.event_id AND l.user_id = ' . Phpfox::getUserId());
        }

        $aRow = $this->database()->select($sSelect)
            ->from(Phpfox::getT('event'), 'e')
            ->leftJoin(Phpfox::getT('event_text'), 'et', 'et.event_id = e.event_id')
            ->where('e.event_id = ' . (int)$aItem['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['event_id'])) {
            return false;
        }

        if ($bIsChildItem) {
            $aItem = $aRow;
        }

        if (((defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE') && !Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->hasPerm(null,
                        'event.view_browse_events'))
                || (!defined('PHPFOX_IS_PAGES_VIEW') && $aRow['module_id'] == 'pages' && Phpfox::isModule('pages') && !Phpfox::getService('pages')->hasPerm($aRow['item_id'],
                        'event.view_browse_events')))
            || ($aRow['module_id'] && Phpfox::isModule($aRow['module_id']) && Phpfox::hasCallback($aRow['module_id'],
                    'canShareOnMainFeed') && !Phpfox::callback($aRow['module_id'] . '.canShareOnMainFeed',
                    $aRow['item_id'], 'event.view_browse_events', $bIsChildItem))
        ) {
            return false;
        }
        $aRow['is_on_feed'] = true;
        $aRows = [$aRow];
        Phpfox::getService('event.browse')->processRows($aRows);
        $sContent = Phpfox_Template::instance()->assign(['aEvent' => array_values($aRows)[0][0]])->getTemplate('event.block.feed-rows',
            true);

        $aReturn = [
            'feed_title' => '',
            'feed_info' => _p('created_an_event'),
            'feed_link' => Phpfox::permalink('event', $aRow['event_id'], $aRow['title']),
            'feed_icon' => Phpfox::getLib('image.helper')->display([
                'theme' => 'module/event.png',
                'return_url' => true
            ]),
            'time_stamp' => $aRow['time_stamp'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => isset($aRow['is_liked']) ? $aRow['is_liked'] : false,
            'enable_like' => true,
            'like_type_id' => 'event',
            'total_comment' => $aRow['total_comment'],
            'custom_data_cache' => $aRow,
            'feed_custom_html' => $sContent
        ];

        if ($bIsChildItem) {
            $aReturn = array_merge($aReturn, $aItem);
        }

        if (!defined('PHPFOX_IS_PAGES_VIEW') && (($aRow['module_id'] == 'groups' && Phpfox::isModule('groups')) || ($aRow['module_id'] == 'pages' && Phpfox::isModule('pages')))) {
            $aPage = $this->database()->select('p.*, pu.vanity_url, ' . Phpfox::getUserField('u', 'parent_'))
                ->from(':pages', 'p')
                ->join(':user', 'u', 'p.page_id=u.profile_page_id')
                ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
                ->where('p.page_id=' . (int)$aRow['item_id'])
                ->execute('getSlaveRow');

            if (empty($aPage)) {
                return false;
            }
            $aReturn['parent_user_name'] = Phpfox::getService($aRow['module_id'])->getUrl($aPage['page_id'],
                $aPage['title'], $aPage['vanity_url']);
            $aReturn['feed_table_prefix'] = 'pages_';
            if ($aRow['user_id'] != $aPage['parent_user_id']) {
                $aReturn['parent_user'] = Phpfox::getService('user')->getUserFields(true, $aPage, 'parent_');
                unset($aReturn['feed_info']);
            }
        }

        (($sPlugin = Phpfox_Plugin::get('event.component_service_callback_getactivityfeed__1')) ? eval($sPlugin) : false);
        return $aReturn;
    }

    /**
     * @param array $aNotification
     *
     * @return array|bool
     */
    public function getNotificationApproved($aNotification)
    {
        $aRow = $this->database()->select('e.event_id, e.title, e.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('event'), 'e')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('e.event_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['event_id'])) {
            return false;
        }

        $sPhrase = _p('your_event_title_has_been_approved', array(
            'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'],
                Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));

        return array(
            'link' => Phpfox_Url::instance()->permalink('event', $aRow['event_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog'),
            'no_profile_image' => true
        );
    }

    /**
     * @param array $aNotification
     *
     * @return array|bool
     */
    public function getNotificationInvite($aNotification)
    {
        $aRow = $this->database()->select('e.event_id, e.title, e.user_id, u.full_name')
            ->from(Phpfox::getT('event'), 'e')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = e.user_id')
            ->where('e.event_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['event_id'])) {
            return false;
        }

        $sPhrase = _p('users_invited_you_to_the_event_title', array(
            'users' => Phpfox::getService('notification')->getUsers($aNotification),
            'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'],
                Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));

        return array(
            'link' => Phpfox_Url::instance()->permalink('event', $aRow['event_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        );
    }

    /**
     * @param array $aUser
     *
     * @return array|bool
     */
    public function getProfileMenu($aUser)
    {
        if (!Phpfox::getParam('profile.show_empty_tabs')) {
            if (!isset($aUser['total_event'])) {
                return false;
            }

            if (isset($aUser['total_event']) && (int)$aUser['total_event'] === 0) {
                return false;
            }
        }
        $aTotal = $this->getTotalItemCount($aUser['user_id']);
        $aMenus[] = array(
            'phrase' => _p('events'),
            'url' => 'profile.event',
            'total' => $aTotal['total'],
            'icon' => 'module/event.png'
        );

        return $aMenus;
    }

    /**
     * @param int $iUserId
     *
     * @return array
     */
    public function getTotalItemCount($iUserId)
    {
        $iTotal = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('event'))
            ->where('view_id = 0 AND item_id = 0 AND user_id = ' . (int)$iUserId)
            ->execute('getSlaveField');
        return array(
            'field' => 'total_event',
            'total' => $iTotal
        );
    }

    /**
     * @return string
     */
    public function getProfileLink()
    {
        return 'profile.event';
    }

    /**
     * @param array $aPhoto
     *
     * @return array|bool
     */
    public function getPhotoDetails($aPhoto)
    {
        $aRow = $this->database()->select('event_id, title')
            ->from(Phpfox::getT('event'))
            ->where('event_id = ' . (int)$aPhoto['group_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['event_id'])) {
            return false;
        }

        $sLink = Phpfox::permalink('event', $aRow['event_id'], $aRow['title']);

        return array(
            'breadcrumb_title' => _p('events'),
            'breadcrumb_home' => Phpfox_Url::instance()->makeUrl('event'),
            'module_id' => 'event',
            'item_id' => $aRow['event_id'],
            'title' => $aRow['title'],
            'url_home' => $sLink,
            'url_home_photo' => Phpfox::permalink(array('event', 'photo'), $aRow['event_id'], $aRow['title']),
            'theater_mode' => _p('in_the_event_a_href_link_title_a', array('link' => $sLink, 'title' => $aRow['title'])),
            'feed_table_prefix' => 'event_'
        );
    }

    /**
     * @param string $sSearch
     */
    public function globalUnionSearch($sSearch)
    {
        $sConds = Phpfox::getService('event')->getConditionsForSettingPageGroup('item');
        $this->database()->select('item.event_id AS item_id, item.title AS item_title, item.time_stamp AS item_time_stamp, item.user_id AS item_user_id, \'event\' AS item_type_id, item.image_path AS item_photo, item.server_id AS item_photo_server')
            ->from(Phpfox::getT('event'), 'item')
            ->where('item.view_id = 0 AND item.privacy = 0 AND ' . $this->database()->searchKeywords('item.title',
                    $sSearch). $sConds)
            ->union();
    }

    /**
     * @param array $aRow
     *
     * @return array
     */
    public function getSearchInfo($aRow)
    {
        $aInfo = array();
        $aInfo['item_link'] = Phpfox_Url::instance()->permalink('event', $aRow['item_id'], $aRow['item_title']);
        $aInfo['item_name'] = _p('events');

        if (!empty($aRow['item_photo'])) {
            $aInfo['item_display_photo'] = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aRow['item_photo_server'],
                    'file' => $aRow['item_photo'],
                    'path' => 'event.url_image',
                    'suffix' => '',
                    'max_width' => '320',
                    'max_height' => '320'
                )
            );
        }
        else {
            $aInfo['item_display_photo'] = '<img src="'.Phpfox::getParam('event.event_default_photo').'"/>';
        }
        return $aInfo;
    }

    /**
     * @return array
     */
    public function getSearchTitleInfo()
    {
        return [
            'name' => _p('events')
        ];
    }

    /**
     * @param array $aPage
     *
     * @return array|null
     */
    public function getPageMenu($aPage)
    {
        if (!Phpfox::getService('pages')->hasPerm($aPage['page_id'],
                'event.view_browse_events') || !Phpfox::getUserParam('event.can_access_event')
        ) {
            return null;
        }

        $aMenus[] = [
            'phrase' => _p('events'),
            'url' => Phpfox::getService('pages')
                    ->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']) . 'event/',
            'icon' => 'module/event.png',
            'landing' => 'event'
        ];

        return $aMenus;
    }

    /**
     * @param array $aPage
     *
     * @return array|null
     */
    public function getGroupMenu($aPage)
    {
        if (!Phpfox::getService('groups')->hasPerm($aPage['page_id'],
                'event.view_browse_events') || !Phpfox::getUserParam('event.can_access_event')
        ) {
            return null;
        }

        $aMenus[] = array(
            'phrase' => _p('Events'),
            'url' => Phpfox::getService('groups')->getUrl($aPage['page_id'], $aPage['title'],
                    $aPage['vanity_url']) . 'event/',
            'icon' => 'module/event.png',
            'landing' => 'event'
        );

        return $aMenus;
    }

    /**
     * @param array $aPage
     *
     * @return array|null
     */
    public function getPageSubMenu($aPage)
    {
        if (!Phpfox::getService('pages')->hasPerm($aPage['page_id'],
                'event.share_events') || !Phpfox::getUserParam('event.can_create_event')
        ) {
            return null;
        }

        return [
            [
                'phrase' => _p('add_new_event'),
                'url' => Phpfox_Url::instance()->makeUrl('event.add', [
                    'module' => 'pages',
                    'item' => $aPage['page_id']
                ])
            ]
        ];
    }

    /**
     * @param array $aPage
     *
     * @return array|null
     */
    public function getGroupSubMenu($aPage)
    {
        if (!Phpfox::getService('groups')->hasPerm($aPage['page_id'],
                'event.share_events') || !Phpfox::getUserParam('event.can_create_event')
        ) {
            return null;
        }

        return array(
            [
                'phrase' => _p('add_new_event'),
                'url' => Phpfox_Url::instance()->makeUrl('event.add', [
                    'module' => 'groups',
                    'item' => $aPage['page_id']
                ])
            ]
        );
    }

    /**
     * @return array
     */
    public function getPagePerms()
    {
        $aPerms = [];

        $aPerms['event.share_events'] = _p('who_can_share_events');
        $aPerms['event.view_browse_events'] = _p('who_can_view_browse_events');

        return $aPerms;
    }

    /**
     * @return array
     */
    public function getGroupPerms()
    {
        $aPerms = [
            'event.share_events' => _p('who_can_share_events')
        ];
        return $aPerms;
    }

    /**
     * @param int $iPage
     *
     * @return bool
     */
    public function canViewPageSection($iPage)
    {
        if (!Phpfox::getService('pages')->hasPerm($iPage, 'event.view_browse_events')) {
            return false;
        }

        return true;
    }

    /**
     * @param array $aItem
     *
     * @return array|bool
     */
    public function getVideoDetails($aItem)
    {
        $aRow = $this->database()->select('event_id, title')
            ->from(Phpfox::getT('event'))
            ->where('event_id = ' . (int)$aItem['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['event_id'])) {
            return false;
        }

        $sLink = Phpfox::permalink(array('event', 'video'), $aRow['event_id'], $aRow['title']);

        return [
            'breadcrumb_title' => _p('event'),
            'breadcrumb_home' => Phpfox_Url::instance()->makeUrl('event'),
            'module_id' => 'event',
            'item_id' => $aRow['event_id'],
            'title' => $aRow['title'],
            'url_home' => $sLink,
            'url_home_photo' => $sLink,
        ];
    }

    /**
     * @param array $aNotification
     *
     * @return array
     */
    public function getCommentNotificationTag($aNotification)
    {
        $aRow = $this->database()->select('e.event_id, e.title, u.user_name, u.full_name')
            ->from(Phpfox::getT('comment'), 'c')
            ->join(Phpfox::getT('event_feed_comment'), 'fc', 'fc.feed_comment_id = c.item_id')
            ->join(Phpfox::getT('event'), 'e', 'e.event_id = fc.parent_user_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
            ->where('c.comment_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        $sPhrase = _p('user_name_tagged_you_in_a_comment_in_an_event', array('user_name' => $aRow['full_name']));

        return [
            'link' => Phpfox_Url::instance()
                    ->permalink('event', $aRow['event_id'], $aRow['title']) . 'comment_' . $aNotification['item_id'],
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        ];
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     *
     * @return null
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('event.service_callback__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

    /**
     * @param array $aNotification
     *
     * @return array|bool
     */
    public function getNotificationNewItem_Groups($aNotification)
    {
        if (!Phpfox::isModule('groups')) {
            return false;
        }
        $aItem = Phpfox::getService('event')->getEvent($aNotification['item_id']);
        if (empty($aItem) || empty($aItem['item_id']) || ($aItem['module_id'] != 'groups')) {
            return false;
        }

        $aRow = Phpfox::getService('groups')->getPage($aItem['item_id']);

        if (!isset($aRow['page_id'])) {
            return false;
        }

        $sPhrase = _p('{{ users }} add a new event in the group "{{ title }}"', array(
            'users' => Phpfox::getService('notification')->getUsers($aNotification),
            'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'],
                Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));

        return [
            'link' => Phpfox_Url::instance()->permalink('event', $aItem['event_id'], $aItem['title']),
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'event')
        ];
    }

    /**
     * @return string
     */
    public function getAjaxCommentVar()
    {
        return 'event.can_post_comment_on_event';
    }

    /**
     * @description: return callback param for adding feed comment on event
     *
     * @param $iId
     * @param $aVals
     *
     * @return array|bool
     */
    public function getFeedComment($iId, $aVals)
    {
        Phpfox::isUser(true);

        //validate lata
        if (Phpfox::getLib('parse.format')->isEmpty($aVals['user_status'])) {
            Phpfox_Error::set(_p('add_some_text_to_share'));
            return false;
        }

        $aEvent = Phpfox::getService('event')->getForEdit($iId, true);

        //check event is exists
        if (!isset($aEvent['event_id'])) {
            Phpfox_Error::set(_p('unable_to_find_the_event_you_are_trying_to_comment_on'));
            return false;
        }

        $sLink = Phpfox::permalink('event', $aEvent['event_id'], $aEvent['title']);
        $aCallback = array(
            'module' => 'event',
            'table_prefix' => 'event_',
            'link' => $sLink,
            'email_user_id' => $aEvent['user_id'],
            'subject' => _p('full_name_wrote_a_comment_on_your_event_title',
                array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aEvent['title'])),
            'message' => _p('full_name_wrote_a_comment_on_your_event_message',
                array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aEvent['title'])),
            'notification' => 'event_comment',
            'feed_id' => 'event_comment',
            'item_id' => $aEvent['event_id']
        );

        return $aCallback;
    }

    /**
     * @description: callback after a comment feed added on event
     *
     * @param $iId
     */
    public function onAddFeedCommentAfter($iId)
    {
        Phpfox_Database::instance()->updateCounter('event', 'total_comment', 'event_id', $iId);
    }

    /**
     * @description: callback to check permission to view an event
     *
     * @param $iId
     *
     * @return array|bool
     */
    public function canViewItem($iId)
    {
        return Phpfox::getService('event')->canViewItem($iId);
    }

    /**
     * This callback will be called when a page or group be deleted
     * @param $iId
     * @param $sType
     */
    public function onDeletePage($iId, $sType)
    {
        $aEvents = db()->select('event_id')->from(':event')->where([
            'module_id' => $sType,
            'item_id' => $iId
        ])->executeRows();
        foreach ($aEvents as $aEvent) {
            Phpfox::getService('event.process')->delete($aEvent['event_id'], null, true);
        }
    }

    /**
     * @return array
     */
    public function getAttachmentField()
    {
        return [
            'event',
            'event_id'
        ];
    }

    /**
     * @param $iId
     * @param null $iUserId
     */
    public function addTrack($iId, $iUserId = null)
    {
        if ($iUserId == null) {
            $iUserId = Phpfox::getUserBy('user_id');
        }
        db()->insert(Phpfox::getT('track'), [
            'type_id' => 'event',
            'item_id' => (int)$iId,
            'ip_address' => Phpfox::getIp(),
            'user_id' => $iUserId,
            'time_stamp' => PHPFOX_TIME
        ]);
    }

    /**
     * This callback will be called when admin delete a sponsor in admincp
     * @param $aParams
     */
    public function deleteSponsorItem($aParams)
    {
        db()->update(':event', ['is_sponsor' => 0], ['event_id' => $aParams['item_id']]);
        \Phpfox_Cache::instance()->remove();
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
            ->from(':event')
            ->where('user_id ='.(int)$iUserId)
            ->execute('getField');
        return [
            'total_name' => _p('events'),
            'total_value' => $iTotal,
            'type' => 'item'
        ];
    }

    /**
     * @return mixed
     */
    public function getUploadParams() {
        return Phpfox::getService('event')->getUploadParams();
    }

    /**
     * @param $aParams
     */
    public function addItemNotification($aParams)
    {
        $aEvent = Phpfox::getService('event')->getEvent($aParams['page_id']);
        if (!$aEvent) {
            return false;
        }
        if ($aParams['item_type'] == 'photo' && Phpfox::isModule('photo')) {
            $aEventFeed = db()->select('*')->from(':event_feed')->where('type_id = \'photo\' AND item_id ='.(int)$aParams['item_id'])->execute('getRow');
            $sLink = Phpfox::getLib('url')->permalink('photo', $aParams['item_id']).(isset($aEventFeed['feed_id']) ? 'feed_'.$aEventFeed['feed_id'] : '');
            $sSubject = _p('full_name_added_photo_s_on_your_event_title',
                array(
                    'full_name' => Phpfox::getUserBy('full_name'),
                    'title' => $aEvent['title']
                ));
            $sMessage = _p('full_name_added_photo_s_on_your_event_message',
                array(
                    'full_name' => Phpfox::getUserBy('full_name'),
                    'link1' => Phpfox::getLib('url')->permalink('event', $aEvent['event_id'], $aEvent['title']),
                    'title' => $aEvent['title'],
                    'link2' => $sLink,
                ));
            Phpfox::getLib('mail')->to($aEvent['user_id'])
                ->subject($sSubject)
                ->message($sMessage)
                ->notification('event_add_photo')
                ->send();
            if (Phpfox::isModule('notification')) {
                Phpfox::getService('notification.process')->add('event_add_photo', $aParams['item_id'],
                    $aEvent['user_id']);
            }
        }
    }

    /**
     * @param array $aNotification
     *
     * @return array|bool
     */
    public function getNotificationAdd_Photo($aNotification)
    {
        if (!Phpfox::isModule('photo')) {
            return false;
        }
        $aRow = $this->database()
            ->select('p.photo_id, e.event_id, e.title as event_title, e.user_id as event_owner_id, p.title as photo_title, '. Phpfox::getUserField())
            ->from(':photo','p')
            ->join(':event','e','e.event_id = p.group_id')
            ->join(':user','u','p.user_id = u.user_id')
            ->where('p.photo_id = '.$aNotification['item_id'])
            ->execute('getRow');

        if (empty($aRow)) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['event_title'],
            Phpfox::getParam('notification.total_notification_title_length'), '...');
        $aEventFeed = db()->select('*')->from(':event_feed')->where('type_id = \'photo\' AND item_id ='.(int)$aNotification['item_id'])->execute('getRow');
        $sLink = Phpfox::getLib('url')->permalink('photo', $aNotification['item_id']).(isset($aEventFeed['feed_id']) ? 'feed_'.$aEventFeed['feed_id'] : '');
        $iTotalExtra = 0;
        if (isset($aEventFeed['feed_id'])) {
            $iTotalExtra = db()->select('COUNT(*)')->from(':photo_feed')->where('feed_table = \'event_feed\' AND feed_id = '.$aEventFeed['feed_id'])->execute('getField');
        }
        $sPhrase = _p(($iTotalExtra ? 'users_added_some_photos_on_your_event_title' : 'users_added_a_photo_on_your_event_title'), array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => $sLink,
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        );
    }
    public function processInstallRss()
    {
        (new \Apps\Core_Events\Installation\Version\v460())->importToRssFeed();
    }
}
