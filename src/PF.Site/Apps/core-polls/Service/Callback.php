<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Polls\Service;

use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Template;
use Phpfox_Url;

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author        phpFox
 * @package        Poll
 *
 */
class Callback extends \Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('poll');
    }

    /**
     *
     * @return bool
     */
    public function checkFeedShareLink()
    {
        if (!Phpfox::getUserParam('poll.can_create_poll')) {
            return false;
        }
        return true;
    }

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

        return array(
            'phrase' => 'poll.polls',
            'total' => $iCnt
        );
    }

    public function getProfileLink()
    {
        (($sPlugin = Phpfox_Plugin::get('poll.component_service_callback_getprofilelink_start')) ? eval($sPlugin) : false);
        return 'profile.poll';
    }

    public function getAjaxCommentVar()
    {
        (($sPlugin = Phpfox_Plugin::get('poll.component_service_callback_getajaxcommentvar_start')) ? eval($sPlugin) : false);
        return 'poll.can_post_comment_on_poll';
    }

    public function getCommentItem($iId)
    {
        (($sPlugin = Phpfox_Plugin::get('poll.component_service_callback_getcommentitem_start')) ? eval($sPlugin) : false);

        $aRow = $this->database()->select('poll_id AS comment_item_id, privacy_comment, user_id AS comment_user_id')
            ->from($this->_sTable)
            ->where('poll_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        $aRow['comment_view_id'] = '0';

        if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'], $aRow['privacy_comment'])) {
            Phpfox_Error::set(_p('unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));

            unset($aRow['comment_item_id']);
        }

        return $aRow;

    }

    public function getActivityFeedComment($aRow)
    {
        if (Phpfox::isUser() && Phpfox::isModule('like')) {
            $this->database()->select('l.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'l',
                    'l.type_id = \'feed_mini\' AND l.item_id = c.comment_id AND l.user_id = ' . Phpfox::getUserId());
        }

        $aItem = $this->database()->select('b.poll_id, b.privacy, b.question, b.time_stamp, b.total_comment, b.total_like, c.total_like, ct.text_parsed AS text, f.friend_id AS is_friend, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('comment'), 'c')
            ->join(Phpfox::getT('comment_text'), 'ct', 'ct.comment_id = c.comment_id')
            ->join(Phpfox::getT('poll'), 'b', 'c.type_id = \'poll\' AND c.item_id = b.poll_id AND c.view_id = 0')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = b.user_id')
            ->leftJoin(Phpfox::getT('friend'), 'f',
                "f.user_id = b.user_id AND f.friend_user_id = " . Phpfox::getUserId())
            ->where('c.comment_id = ' . (int)$aRow['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aItem['poll_id'])) {
            return false;
        }

        $bCanViewItem = true;
        if (Phpfox::isModule('privacy') && $aItem['privacy'] > 0) {
            $bCanViewItem = \Phpfox::getService('privacy')->check('poll', $aItem['poll_id'], $aItem['user_id'],
                $aItem['privacy'], $aItem['is_friend'], true);
        }

        if (!$bCanViewItem) {
            return false;
        }

        $sLink = Phpfox::permalink('poll', $aItem['poll_id'], $aItem['question']);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aItem['question'],
            (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : 50));
        $sUser = '<a href="' . Phpfox_Url::instance()->makeUrl($aItem['user_name']) . '">' . $aItem['full_name'] . '</a>';
        $sGender = \Phpfox::getService('user')->gender($aItem['gender'], 1);

        if ($aRow['user_id'] == $aItem['user_id']) {
            $sMessage = _p('posted_a_comment_on_gender_poll_a_href_link_title_a',
                array('gender' => $sGender, 'link' => $sLink, 'title' => $sTitle));
        } else {
            $sMessage = _p('posted_a_comment_on_user_name_s_poll_a_href_link_title_a',
                array('user_name' => $sUser, 'link' => $sLink, 'title' => $sTitle));
        }

        return array(
            'no_share' => true,
            'feed_info' => $sMessage,
            'feed_link' => $sLink,
            'feed_status' => $aItem['text'],
            'feed_total_like' => $aItem['total_like'],
            'feed_is_liked' => isset($aItem['is_liked']) ? $aItem['is_liked'] : false,
            'feed_icon' => Phpfox::getLib('image.helper')->display(array(
                'theme' => 'module/poll.png',
                'return_url' => true
            )),
            'time_stamp' => $aRow['time_stamp'],
            'like_type_id' => 'feed_mini'
        );
    }

    /**
     * @param $aVals
     * @param null $iUserId , deprecated, remove in 4.7
     * @param null $sUserName , deprecated, remove in 4.7
     */
    public function addComment($aVals, $iUserId = null, $sUserName = null)
    {
        (($sPlugin = Phpfox_Plugin::get('poll.component_service_callback_addcomment_start')) ? eval($sPlugin) : false);

        $aPoll = $this->database()->select('u.full_name, u.user_id, u.gender, u.user_name, p.poll_id, p.question, p.privacy, p.privacy_comment')
            ->from($this->_sTable, 'p')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
            ->where('p.poll_id = ' . (int)$aVals['item_id'])
            ->execute('getSlaveRow');

        (Phpfox::isModule('feed') ? \Phpfox::getService('feed.process')->add($aVals['type'] . '_comment',
            $aVals['comment_id'], $aPoll['privacy'], $aPoll['privacy_comment']) : null);

        if (empty($aVals['parent_id'])) {
            $this->database()->updateCounter('poll', 'total_comment', 'poll_id', $aVals['item_id']);
        }

        // Send the user an email
        $sLink = Phpfox::permalink('poll', $aPoll['poll_id'], $aPoll['question']);

        Phpfox::getService('comment.process')->notify(array(
                'user_id' => $aPoll['user_id'],
                'item_id' => $aPoll['poll_id'],
                'owner_subject' => _p('full_name_commented_on_one_of_your_polls_title',
                    array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aPoll['question'])),
                'owner_message' => _p('full_name_commented_on_your_poll_a_href_link_title_a_to_see_the_comment_thread_follow_the_link_below_a_href_link_link_a',
                    array(
                        'full_name' => Phpfox::getUserBy('full_name'),
                        'link' => $sLink,
                        'title' => $aPoll['question']
                    )),
                'owner_notification' => 'comment.add_new_comment',
                'notify_id' => 'comment_poll',
                'mass_id' => 'poll',
                'mass_subject' => (Phpfox::getUserId() == $aPoll['user_id'] ? _p('full_name_commented_on_gender_poll',
                    array(
                        'full_name' => Phpfox::getUserBy('full_name'),
                        'gender' => \Phpfox::getService('user')->gender($aPoll['gender'], 1)
                    ))
                    : _p('full_name_commented_on_other_full_name_s_poll', array(
                        'full_name' => Phpfox::getUserBy('full_name'),
                        'other_full_name' => $aPoll['full_name']
                    ))),
                'mass_message' => (Phpfox::getUserId() == $aPoll['user_id'] ?
                    _p('full_name_commented_on_gender_poll_a_href_link_title_a_to_see_the_comment_thread_follow_the_link_below_a_href_link_link_a',
                        array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'gender' => \Phpfox::getService('user')->gender($aPoll['gender'], 1),
                            'link' => $sLink,
                            'title' => $aPoll['question']
                        ))
                    :
                    _p('full_name_commented_on_other_full_name_s_poll_a_href_link_title_a_to_see_the_comment_thread_follow_the_link_below_a_href_link_link_a',
                        array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'other_full_name' => $aPoll['full_name'],
                            'link' => $sLink,
                            'title' => $aPoll['question']
                        )))
            )
        );

        (($sPlugin = Phpfox_Plugin::get('poll.component_service_callback_addcomment_end')) ? eval($sPlugin) : false);
    }

    public function getCommentNotification($aNotification)
    {
        $aRow = $this->database()->select('p.poll_id, p.question, p.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('poll'), 'p')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
            ->where('p.poll_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (empty($aRow)) {
            return false;
        }

        if ($aNotification['user_id'] == $aRow['user_id'] && !isset($aNotification['extra_users'])) {
            $sPhrase = _p('user_name_commented_on_gender_poll_title', array(
                'user_name' => \Phpfox::getService('notification')->getUsers($aNotification),
                'gender' => \Phpfox::getService('user')->gender($aRow['gender'], 1),
                'title' => Phpfox::getLib('parse.output')->shorten($aRow['question'],
                    Phpfox::getParam('notification.total_notification_title_length'), '...')
            ));
        } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
            $sPhrase = _p('user_name_commented_on_your_poll_title', array(
                'user_name' => \Phpfox::getService('notification')->getUsers($aNotification),
                'title' => Phpfox::getLib('parse.output')->shorten($aRow['question'],
                    Phpfox::getParam('notification.total_notification_title_length'), '...')
            ));
        } else {
            $sPhrase = _p('user_name_commented_on_span_class_drop_data_user_full_name_s_span_poll_title', array(
                'user_name' => \Phpfox::getService('notification')->getUsers($aNotification),
                'full_name' => $aRow['full_name'],
                'title' => Phpfox::getLib('parse.output')->shorten($aRow['question'],
                    Phpfox::getParam('notification.total_notification_title_length'), '...')
            ));
        }

        return array(
            'link' => \Phpfox_Url::instance()->permalink('poll', $aRow['poll_id'], $aRow['question']),
            'message' => $sPhrase,
            'icon' => \Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        );
    }

    public function updateCommentText($aVals, $sText)
    {
    }

    public function getItemName($iId, $sName)
    {
        return '<a href="' . Phpfox_Url::instance()->makeUrl('comment.view',
                array('id' => $iId)) . '">' . _p('on_name_s_poll', array('name' => $sName)) . '</a>';
    }

    public function deleteComment($iPoll)
    {
        (($sPlugin = Phpfox_Plugin::get('poll.component_service_callback_deletecomment_start')) ? eval($sPlugin) : false);

        $this->database()->update($this->_sTable, array('total_comment' => array('= total_comment -', 1)),
            'poll_id = ' . (int)$iPoll);

        (($sPlugin = Phpfox_Plugin::get('poll.component_service_callback_deletecomment_end')) ? eval($sPlugin) : false);
    }

    public function processCommentModeration($sAction, $iId)
    {
        (($sPlugin = Phpfox_Plugin::get('poll.component_service_callback_processcommentmoderation_start')) ? eval($sPlugin) : false);
        // Is this comment approved?
        if ($sAction == 'approve') {
            // Update the poll count
            \Phpfox::getService('poll.process')->updateCounter($iId);

            // Get the polls details so we can add it to our news feed
            $aPoll = $this->database()->select('p.poll_id, p.user_id, p.question_url, ct.text_parsed, c.user_id AS comment_user_id, c.comment_id')
                ->from($this->_sTable, 'p')
                ->join(Phpfox::getT('comment'), 'c', 'c.type_id = \'poll\' AND c.item_id = p.poll_id')
                ->join(Phpfox::getT('comment_text'), 'ct', 'ct.comment_id = c.comment_id')
                ->where('p.poll_id = ' . (int)$iId)
                ->execute('getSlaveRow');

            // Add to news feed
            (Phpfox::isModule('feed') ? \Phpfox::getService('feed.process')->add('comment_poll', $aPoll['poll_id'],
                $aPoll['text_parsed'], $aPoll['comment_user_id'], $aPoll['user_id'], $aPoll['comment_id']) : null);

            // Send the user an email
            if (Phpfox::getParam('core.is_personal_site')) {
                $sLink = Phpfox_Url::instance()->makeUrl('poll', $aPoll['title_url']);
            } else {
                $sLink = \Phpfox::getService('user')->getLink(Phpfox::getUserId(), Phpfox::getUserBy('user_name'),
                    array('poll', $aPoll['question_url']));
            }
            Phpfox::getLib('mail')->to($aPoll['comment_user_id'])
                ->subject(array(
                    'poll.full_name_approved_your_comment_on_site_title',
                    array(
                        'full_name' => Phpfox::getUserBy('full_name'),
                        'site_title' => Phpfox::getParam('core.site_title')
                    )
                ))
                ->message(array(
                        'poll.full_name_approved_your_comment_on_site_title_message',
                        array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'site_title' => Phpfox::getParam('core.site_title'),
                            'link' => $sLink
                        )
                    )
                )
                ->notification('comment.approve_new_comment')
                ->send();
        }
        (($sPlugin = Phpfox_Plugin::get('poll.component_service_callback_processcommentmoderation_end')) ? eval($sPlugin) : false);
    }

    public function getCommentNewsFeed($aRow)
    {
        $oUrl = Phpfox_Url::instance();

        if ($aRow['owner_user_id'] == $aRow['item_user_id']) {
            $aRow['text'] = _p('a_href_user_link_full_name_a_added_a_new_comment_on_their_own_a_href_title_link_poll_a',
                array(
                    'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
                    'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                    'title_link' => $aRow['link']
                )
            );
        } else {
            if ($aRow['item_user_id'] == Phpfox::getUserBy('user_id')) {
                $aRow['text'] = _p('a_href_user_link_full_name_a_added_a_new_comment_on_your_a_href_title_link_poll_a',
                    array(
                        'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
                        'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                        'title_link' => $aRow['link']
                    )
                );
            } else {
                $aRow['text'] = _p('a_href_user_link_full_name_a_added_a_new_comment_on_a_href_item_user_link_item_user_name_s_a_a_href_title_link_poll_a',
                    array(
                        'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
                        'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                        'title_link' => $aRow['link'],
                        'item_user_name' => $this->preParse()->clean($aRow['viewer_full_name']),
                        'item_user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['viewer_user_id']))
                    )
                );
            }
        }

        $aRow['text'] .= Phpfox::getService('feed')->quote($aRow['content']);

        return $aRow;
    }

    public function getNewsFeed($aRow)
    {
        if ($sPlugin = Phpfox_Plugin::get('poll.service_callback_getnewsfeed_start')) {
            eval($sPlugin);
        }
        $oUrl = Phpfox_Url::instance();

        $aRow['text'] = _p('a_href_user_link_full_name_a_added_a_new_poll_a_href_question_url_question_a', array(
                'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['owner_user_id'])),
                'full_name' => $this->preParse()->clean($aRow['owner_full_name']),
                'question_url' => $aRow['link'],
                'question' => Phpfox::getService('feed')->shortenTitle($aRow['content'])
            )
        );

        $aRow['icon'] = 'module/poll.png';
        $aRow['enable_like'] = true;

        return $aRow;
    }

    public function getRedirectComment($iId)
    {
        return $this->getFeedRedirect($iId);
    }

    /**
     * @param $iId
     * @param int $iChild , deprecated, remove in 4.7
     * @return bool|string
     */
    public function getFeedRedirect($iId, $iChild = 0)
    {
        (($sPlugin = Phpfox_Plugin::get('poll.component_service_callback_getcommentnewsfeed_start')) ? eval($sPlugin) : false);

        $aQuestion = $this->database()->select('p.poll_id, p.question')
            ->from($this->_sTable, 'p')
            ->where('p.poll_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aQuestion['poll_id'])) {
            return false;
        }

        (($sPlugin = Phpfox_Plugin::get('poll.component_service_callback_getcommentnewsfeed_end')) ? eval($sPlugin) : false);

        return Phpfox::permalink('poll', $aQuestion['poll_id'], $aQuestion['question']);
    }

    public function getReportRedirect($iId)
    {
        return $this->getFeedRedirect($iId);
    }

    public function addTrack($iId, $iUserId = null)
    {
        (($sPlugin = Phpfox_Plugin::get('poll.component_service_callback_addtrack_start')) ? eval($sPlugin) : false);
        if ($iUserId == null) {
            $iUserId = Phpfox::getUserBy('user_id');
        }
        $this->database()->insert(Phpfox::getT('track'), [
            'type_id' => 'poll',
            'item_id' => (int)$iId,
            'ip_address' => Phpfox::getIp(),
            'user_id' => $iUserId,
            'time_stamp' => PHPFOX_TIME
        ]);
        (($sPlugin = Phpfox_Plugin::get('poll.component_service_callback_addtrack_end')) ? eval($sPlugin) : false);
    }

    public function getLatestTrackUsers($iId, $iUserId)
    {
        (($sPlugin = Phpfox_Plugin::get('poll.component_service_callback_getlatesttrackusers_start')) ? eval($sPlugin) : false);
        if ($iId === false) {
            // user is viewing the general area: poll
            $this->database()->where('track.user_id != ' . (int)$iUserId);
        } else {
            // user is viewing one specific poll: profile.poll.pollName
            $this->database()->where('track.user_id != ' . (int)$iUserId . ' AND track.item_id = ' . (int)$iId);
        }

        $aRows = $this->database()->select('DISTINCT ' . Phpfox::getUserField())
            ->from(Phpfox::getT('track'), 'track')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = track.user_id')
            ->where('track.type_id="poll"')
            ->order('track.time_stamp DESC')
            ->limit(0, 7)
            ->execute('getSlaveRows');

        (($sPlugin = Phpfox_Plugin::get('poll.component_service_callback_getlatesttrackusers_end')) ? eval($sPlugin) : false);
        return (count($aRows) ? $aRows : false);
    }

    public function getCommentItemName()
    {
        (($sPlugin = Phpfox_Plugin::get('poll.component_service_callback_getcommentitemname_start')) ? eval($sPlugin) : false);
        return 'poll';
    }

    public function getWhatsNew()
    {
        return array(
            'poll.polls' => array(
                'ajax' => '#poll.getNew?id=js_new_item_holder',
                'id' => 'poll',
                'block' => 'poll.new'
            )
        );
    }

    public function getDashboardLinks()
    {
        return array(
            'submit' => array(
                'phrase' => _p('create_a_poll'),
                'link' => 'poll.add',
                'image' => 'misc/chart_bar_add.png'
            ),
            'edit' => array(
                'phrase' => _p('manage_polls'),
                'link' => 'profile.poll',
                'image' => 'misc/chart_bar_edit.png'
            )
        );
    }

    /**
     * Action to take when user cancelled their account
     * @param int $iUser
     * @return boolean
     */
    public function onDeleteUser($iUser)
    {
        // get all the items from this user
        $aPolls = $this->database()
            ->select('poll_id')
            ->from($this->_sTable)
            ->where('user_id = ' . (int)$iUser)
            ->execute('getSlaveRows');

        foreach ($aPolls as $aPoll) {
            \Phpfox::getService('poll.process')->moderatePoll($aPoll['poll_id'], 2);
        }
        $aAnswersVoted = db()->select('answer_id')
                        ->from(':poll_result')
                        ->where('user_id ='.(int)$iUser)
                        ->execute('getRows');

        if (count($aAnswersVoted)) {
            $sAnswerIds = implode(',', array_map(function ($item) {
                return $item['answer_id'];
            }, $aAnswersVoted));
        }
        if (!empty($sAnswerIds)) {
            db()->update(':poll_answer', ['total_votes' => 'total_votes - 1'], 'answer_id IN (' . $sAnswerIds . ')',
                false);
        }
        db()->delete(':poll_result','user_id ='.(int)$iUser);
        return true;
    }

    public function getItemView()
    {
        if (Phpfox_Request::instance()->get('req3') != '') {
            return true;
        }
        return null;
    }

    /**
     * Used primarily in the site stats this function returns how many polls are pending
     * approval
     * @return array
     */
    public function pendingApproval()
    {
        return array(
            'phrase' => _p('polls'),
            'value' => \Phpfox::getService('poll')->getPendingTotal(),
            'link' => Phpfox_Url::instance()->makeUrl('poll', array('view' => 'pending'))
        );
    }

    public function getAdmincpAlertItems()
    {
        $iTotalPending = Phpfox::getService('poll')->getPendingTotal();
        return [
            'target'=> '_blank',
            'message'=> _p('you_have_total_pending_polls', ['total'=>$iTotalPending]),
            'value' => $iTotalPending,
            'link' => Phpfox_Url::instance()->makeUrl('poll', array('view' => 'pending'))
        ];
    }

    public function getDashboardActivity()
    {
        $aUser = \Phpfox::getService('user')->get(Phpfox::getUserId(), true);

        return array(
            _p('polls_activity') => $aUser['activity_poll']
        );
    }

    public function getSiteStatsForAdmins()
    {
        $iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

        return array(
            'phrase' => _p('polls'),
            'value' => $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('poll'))
                ->where('view_id = 0 AND time_stamp >= ' . $iToday)
                ->execute('getSlaveField')
        );
    }

    /**
     * @param $iId , deprecated, removed in 4.7
     * @param int $iChildId
     * @return bool|string
     */
    public function getFeedRedirectFeedLike($iId, $iChildId = 0)
    {
        return $this->getFeedRedirect($iChildId);
    }

    public function getNewsFeedFeedLike($aRow)
    {
        if ($aRow['owner_user_id'] == $aRow['viewer_user_id']) {
            $aRow['text'] = _p('a_href_user_link_full_name_a_likes_their_own_a_href_link_poll_a', array(
                    'full_name' => Phpfox::getLib('parse.output')->clean($aRow['owner_full_name']),
                    'user_link' => Phpfox_Url::instance()->makeUrl($aRow['owner_user_name']),
                    'gender' => \Phpfox::getService('user')->gender($aRow['owner_gender'], 1),
                    'link' => $aRow['link']
                )
            );
        } else {
            $aRow['text'] = _p('a_href_user_link_full_name_a_likes_a_href_view_user_link_view_full_name_a_s_a_href_link_poll_a',
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

    public function getNotificationFeedNotifyLike($aRow)
    {
        return array(
            'message' => _p('a_href_user_link_full_name_a_likes_your_a_href_link_poll_a', array(
                    'full_name' => Phpfox::getLib('parse.output')->clean($aRow['full_name']),
                    'user_link' => Phpfox_Url::instance()->makeUrl($aRow['user_name']),
                    'link' => Phpfox_Url::instance()->makeUrl('poll', array('redirect' => $aRow['item_id']))
                )
            ),
            'link' => Phpfox_Url::instance()->makeUrl('poll', array('redirect' => $aRow['item_id']))
        );
    }

    public function sendLikeEmail($iItemId)
    {
        return _p('a_href_user_link_full_name_a_likes_your_a_href_link_poll_a', array(
                'full_name' => Phpfox::getLib('parse.output')->clean(Phpfox::getUserBy('full_name')),
                'user_link' => Phpfox_Url::instance()->makeUrl(Phpfox::getUserBy('user_name')),
                'link' => Phpfox_Url::instance()->makeUrl('poll', array('redirect' => $iItemId))
            )
        );
    }

    public function getActivityPointField()
    {
        return array(
            _p('polls_activity') => 'activity_poll'
        );
    }

    public function getSqlTitleField()
    {
        return array(
            array(
                'table' => 'poll',
                'field' => 'question',
                'has_index' => 'question'
            ),
            array(
                'table' => 'poll_answer',
                'field' => 'answer'
            )
        );
    }

    public function canShareItemOnFeed()
    {
    }

    /**
     * @param $aItem
     * @param null $aCallback , deprecated, remove in 4.7.0
     * @param bool $bIsChildItem
     * @return array
     */
    public function getActivityFeed($aItem, $aCallback = null, $bIsChildItem = false)
    {
        if ($bIsChildItem) {
            $this->database()->select(Phpfox::getUserField('u2') . ', ')->join(Phpfox::getT('user'), 'u2',
                'u2.user_id = p.user_id');
        }

        $aRow = \Phpfox::getService('poll')->getPollByUrl($aItem['item_id']);

        \Phpfox_Template::instance()->assign('aPoll', $aRow);
        \Phpfox_Component::setPublicParam('custom_param_' . $aItem['feed_id'], $aRow);
        if ($bIsChildItem) {
            $aItem = array_merge($aRow, $aItem);
        }
        $aReturn = array(
            'feed_title' => $aRow['question'],
            'feed_info' => _p('created_a_poll'),
            'feed_link' => Phpfox::permalink('poll', $aRow['poll_id'], $aRow['question']),
            'total_comment' => $aRow['total_comment'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => $aRow['is_liked'],
            'feed_icon' => Phpfox::getLib('image.helper')->display(array(
                'theme' => 'module/poll.png',
                'return_url' => true
            )),
            'time_stamp' => $aRow['time_stamp'],
            'enable_like' => true,
            'comment_type_id' => 'poll',
            'can_post_comment' => Phpfox::getUserParam('poll.can_post_comment_on_poll'),
            'like_type_id' => 'poll',
            'load_block' => 'poll.feed-rows'
        );
        if ($bIsChildItem) {
            $aReturn = array_merge($aReturn, $aItem);
        }

        if ($bIsChildItem) {
            $aReturn = array_merge($aReturn, $aRow);
        }

        (($sPlugin = Phpfox_Plugin::get('poll.component_service_callback_getactivityfeed__1')) ? eval($sPlugin) : false);

        return $aReturn;
    }

    public function getNotification($aNotification)
    {
        $aRow = $this->database()->select('p.poll_id, p.question, p.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('poll'), 'p')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
            ->where('p.poll_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');
        if (empty($aRow)) {
            return false;
        }
        if ($aNotification['user_id'] == $aRow['user_id']) {
            $sPhrase = _p('user_name_voted_on_gender_poll_title', array(
                'user_name' => \Phpfox::getService('notification')->getUsers($aNotification),
                'gender' => \Phpfox::getService('user')->gender($aRow['gender'], 1),
                'title' => Phpfox::getLib('parse.output')->shorten($aRow['question'],
                    Phpfox::getParam('notification.total_notification_title_length'), '...')
            ));
        } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
            $sPhrase = _p('user_name_voted_on_your_poll_title', array(
                'user_name' => \Phpfox::getService('notification')->getUsers($aNotification),
                'title' => Phpfox::getLib('parse.output')->shorten($aRow['question'],
                    Phpfox::getParam('notification.total_notification_title_length'), '...')
            ));
        } else {
            $sPhrase = _p('user_name_voted_on_span_class_drop_data_user_full_name_s_span_poll_title', array(
                'user_name' => \Phpfox::getService('notification')->getUsers($aNotification),
                'full_name' => $aRow['full_name'],
                'title' => Phpfox::getLib('parse.output')->shorten($aRow['question'],
                    Phpfox::getParam('notification.total_notification_title_length'), '...')
            ));
        }

        return array(
            'link' => Phpfox_Url::instance()->permalink('poll', $aRow['poll_id'], $aRow['question']),
            'message' => $sPhrase
        );
    }

    public function getNotificationLike($aNotification)
    {
        $aRow = $this->database()->select('p.poll_id, p.question, p.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('poll'), 'p')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
            ->where('p.poll_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if ($aNotification['user_id'] == $aRow['user_id']) {
            $sPhrase = _p('user_name_liked_gender_own_poll_title', array(
                'user_name' => \Phpfox::getService('notification')->getUsers($aNotification),
                'gender' => \Phpfox::getService('user')->gender($aRow['gender'], 1),
                'title' => Phpfox::getLib('parse.output')->shorten($aRow['question'],
                    Phpfox::getParam('notification.total_notification_title_length'), '...')
            ));
        } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
            $sPhrase = _p('user_name_liked_your_poll_title', array(
                'user_name' => \Phpfox::getService('notification')->getUsers($aNotification),
                'title' => Phpfox::getLib('parse.output')->shorten($aRow['question'],
                    Phpfox::getParam('notification.total_notification_title_length'), '...')
            ));
        } else {
            $sPhrase = _p('user_name_liked_span_class_drop_data_user_full_name_span_poll_title', array(
                'user_name' => \Phpfox::getService('notification')->getUsers($aNotification),
                'full_name' => $aRow['full_name'],
                'title' => Phpfox::getLib('parse.output')->shorten($aRow['question'],
                    Phpfox::getParam('notification.total_notification_title_length'), '...')
            ));
        }

        return array(
            'link' => Phpfox_Url::instance()->permalink('poll', $aRow['poll_id'], $aRow['question']),
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        );
    }

    public function addLike($iItemId, $bDoNotSendEmail = false)
    {
        $aRow = $this->database()->select('poll_id, question, user_id')
            ->from(Phpfox::getT('poll'))
            ->where('poll_id = ' . (int)$iItemId)
            ->execute('getSlaveRow');

        if (!isset($aRow['poll_id'])) {
            return false;
        }

        $this->database()->updateCount('like', 'type_id = \'poll\' AND item_id = ' . (int)$iItemId . '', 'total_like',
            'poll', 'poll_id = ' . (int)$iItemId);

        if (!$bDoNotSendEmail) {
            $sLink = Phpfox::permalink('poll', $aRow['poll_id'], $aRow['question']);

            Phpfox::getLib('mail')->to($aRow['user_id'])
                ->subject(array(
                    'poll.full_name_liked_your_poll_question',
                    array('full_name' => Phpfox::getUserBy('full_name'), 'question' => $aRow['question'])
                ))
                ->message(array(
                    'poll.full_name_liked_your_poll_question_message',
                    array(
                        'full_name' => Phpfox::getUserBy('full_name'),
                        'link' => $sLink,
                        'question' => $aRow['question']
                    )
                ))
                ->notification('like.new_like')
                ->send();

            \Phpfox::getService('notification.process')->add('poll_like', $aRow['poll_id'], $aRow['user_id']);
        }
    }

    public function deleteLike($iItemId)
    {
        $this->database()->updateCount('like', 'type_id = \'poll\' AND item_id = ' . (int)$iItemId . '', 'total_like',
            'poll', 'poll_id = ' . (int)$iItemId);
    }

    public function getNotificationApproved($aNotification)
    {
        $aRow = $this->database()->select('p.poll_id, p.question, p.user_id, u.gender, u.full_name')
            ->from(Phpfox::getT('poll'), 'p')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
            ->where('p.poll_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['poll_id'])) {
            return false;
        }

        $sPhrase = _p('your_poll_title_has_been_approved', array(
            'title' => Phpfox::getLib('parse.output')->shorten($aRow['question'],
                Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));

        return array(
            'link' => Phpfox_Url::instance()->permalink('poll', $aRow['poll_id'], $aRow['question']),
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog'),
            'no_profile_image' => true
        );
    }

    public function getAjaxProfileController()
    {
        return 'poll.index';
    }

    public function getProfileMenu($aUser)
    {
        if (!Phpfox::getUserParam('poll.can_access_polls')) {
            return false;
        }
        if (!Phpfox::getParam('profile.show_empty_tabs')) {
            if (!isset($aUser['total_poll'])) {
                return false;
            }

            if (isset($aUser['total_poll']) && (int)$aUser['total_poll'] === 0) {
                return false;
            }
        }

        $aMenus[] = array(
            'phrase' => _p('polls'),
            'url' => 'profile.poll',
            'total' => (int)(isset($aUser['total_poll']) ? $aUser['total_poll'] : 0),
            'icon' => 'feed/poll.png'
        );

        return $aMenus;
    }

    public function getTotalItemCount($iUserId)
    {
        return array(
            'field' => 'total_poll',
            'total' => $this->database()->select('COUNT(*)')->from(Phpfox::getT('poll'))->where('item_id = 0 AND view_id = 0 AND user_id = ' . (int)$iUserId)->execute('getSlaveField')
        );
    }

    public function globalUnionSearch($sSearch)
    {
        $this->database()->select('item.poll_id AS item_id, item.question AS item_title, item.time_stamp AS item_time_stamp, item.user_id AS item_user_id, \'poll\' AS item_type_id, item.image_path AS item_photo, item.server_id AS item_photo_server')
            ->from(Phpfox::getT('poll'), 'item')
            ->where('item.item_id = 0 AND item.view_id = 0 AND ' . $this->database()->searchKeywords('item.question',
                    $sSearch) . ' AND item.privacy = 0')
            ->union();
    }

    public function getSearchInfo($aRow)
    {
        $aInfo = array();
        $aInfo['item_link'] = Phpfox_Url::instance()->permalink('poll', $aRow['item_id'], $aRow['item_title']);
        $aInfo['item_name'] = _p('poll');
        if (!empty($aRow['item_photo'])) {
            $aInfo['item_display_photo'] = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aRow['item_photo_server'],
                    'file' => $aRow['item_photo'],
                    'path' => 'poll.url_image',
                    'suffix' => '_150',
                    'max_width' => '320',
                    'max_height' => '320'
                )
            );
        }
        return $aInfo;
    }

    public function getSearchTitleInfo()
    {
        return array(
            'name' => _p('polls')
        );
    }

    public function getGlobalPrivacySettings()
    {
        return array(
            'poll.default_privacy_setting' => array(
                'phrase' => _p('polls')
            )
        );
    }

    public function getParentItemCommentUrl($aComment)
    {
        $aRow = $this->database()->select('p.poll_id, p.question')
            ->from(Phpfox::getT('poll'), 'p')
            ->where('p.poll_id = ' . (int)$aComment['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['poll_id'])) {
            return false;
        }

        return Phpfox_Url::instance()->permalink('poll', $aRow['poll_id'], $aRow['question']);
    }

    public function getCommentNotificationTag($aNotification)
    {
        $aRow = $this->database()->select('p.poll_id, p.question, u.user_name, u.full_name')
            ->from(Phpfox::getT('comment'), 'c')
            ->join(Phpfox::getT('poll'), 'p', 'p.poll_id = c.item_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
            ->where('c.comment_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');
        if (empty($aRow)) {
            return false;
        }


        $sPhrase = _p('user_name_tagged_you_in_a_comment_in_a_poll', array('user_name' => $aRow['full_name']));

        return array(
            'link' => Phpfox_Url::instance()->permalink('poll', $aRow['poll_id'],
                    $aRow['question']) . 'comment_' . $aNotification['item_id'],
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        );
    }

    /**
     * @return bool
     */
    public function ignoreDeleteLikesAndTagsWithFeed()
    {
        return true;
    }

    /**
     * @return array
     */
    public function getAttachmentField()
    {
        return [
            'poll',
            'poll_id'
        ];
    }

    /**
     * @param $aParams
     * @return mixed
     */
    public function enableSponsor($aParams)
    {
        return Phpfox::getService('poll.process')->sponsor($aParams['item_id'], 1);
    }

    /**
     * @param $iId
     * @return array|int|string
     */
    public function getToSponsorInfo($iId)
    {
        $aPoll = $this->database()->select('p.question, p.image_path as image, p.server_id, p.poll_id as item_id, p.user_id')
            ->from(Phpfox::getT('poll'), 'p')
            ->where('p.poll_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (empty($aPoll)) {
            return array('error' => _p('sponsor_error_poll_not_found'));
        }

        $aPoll['title'] = _p('poll_sponsor_title', array('sPollTitle' => $aPoll['question']));
        $aPoll['paypal_msg'] = _p('poll_sponsor_paypal_message', array('sPollTitle' => $aPoll['question']));
        $aPoll['link'] = Phpfox::permalink('poll', $aPoll['item_id'], $aPoll['question']);
        $aPoll['image_dir'] = 'poll.url_image';
        $aPoll['image'] = sprintf($aPoll['image'], '_150');
        $aPoll = array_merge($aPoll, [
            'redirect_completed' => 'poll',
            'message_completed' => _p('purchase_poll_sponsor_completed'),
            'redirect_pending_approval' => 'poll',
            'message_pending_approval' => _p('purchase_poll_sponsor_pending_approval')
        ]);
        return $aPoll;
    }

    public function getLink($aParams)
    {

        $aUser = $this->database()->select('u.user_name, p.question, p.poll_id')
            ->from(Phpfox::getT('poll'), 'p')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = p.user_id')
            ->where('p.poll_id = ' . (int)$aParams['item_id'])
            ->execute('getSlaveRow');
        if (empty($aUser)) {
            return false;
        }
        return \Phpfox::permalink('poll', $aUser['poll_id'], $aUser['question']);
    }
    /**
     * @param $iUserId int, user id of selected user
     * @return array|bool
     */
    public function getUserStatsForAdmin($iUserId)
    {
        if (!$iUserId) {
            return false;
        }

        $iTotalPoll = db()->select('COUNT(*)')
            ->from(':poll')
            ->where('user_id =' . (int)$iUserId)
            ->execute('getField');
        return [
            'total_name' => _p('polls'),
            'total_value' => $iTotalPoll,
            'type' => 'item'
        ];
    }

    public function getUploadParams() {
        return Phpfox::getService('poll')->getUploadParams();
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
        if ($sPlugin = Phpfox_Plugin::get('poll.service_callback__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}