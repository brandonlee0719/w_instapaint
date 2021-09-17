<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright       [PHPFOX_COPYRIGHT]
 * @author          phpFox
 * @package         Module_Comment
 * @version         $Id: process.class.php 7165 2014-03-03 15:23:19Z Fern $
 */
class Comment_Service_Process extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('comment');
    }

    /**
     * @param array $aVals
     * @param null|int $iUserId
     * @param null|string $sUserName
     *
     * @return int
     */
    public function add($aVals, $iUserId = null, $sUserName = null)
    {
        $iUserId = ($iUserId === null ? Phpfox::getUserId() : (int)$iUserId);
        $sUserName = ($sUserName === null ? Phpfox::getUserBy('full_name') : $sUserName);

        $prefix = isset($aVals['table_prefix']) ? $aVals['table_prefix'] : '';
        // START: Check if user can comment on this item
        if (Phpfox::isModule('feed') && ((isset($aVals['is_via_feed']) && !empty($aVals['is_via_feed'])) || !empty($aVals['is_api']))) {
            $aFeed = $this->database()->select('feed_id, privacy, privacy_comment, user_id')
                ->from(Phpfox::getT($prefix . 'feed'))
                ->where('item_id = ' . (int)$aVals['item_id'] . ' AND type_id = \'' . (!empty($aVals['app_object']) ? Phpfox::getLib('parse.input')->clean($aVals['app_object']) : Phpfox::getLib('parse.input')->clean($aVals['type'])) . '\'')
                ->execute('getSlaveRow');

            if (!empty($aVals['is_api']) && !empty($aFeed['feed_id']) && empty($aVals['is_via_feed'])) {
                $aVals['is_via_feed'] = $aFeed['feed_id'];
            }

            if (!empty($aFeed) && !Phpfox::getUserParam('privacy.can_comment_on_all_items')) {
                if (isset($aFeed['privacy_comment']) && !empty($aFeed['privacy']) && !empty($aFeed['user_id']) && $aFeed['user_id'] != $iUserId) {
                    if ($aFeed['privacy_comment'] == 1 && Phpfox::isModule('friend') && Phpfox::getService('friend')->isFriend($iUserId,
                            $aFeed['user_id']) != true) {
                        return Phpfox_Error::display(_p('unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));
                    } else {
                        if ($aFeed['privacy_comment'] == 2 && Phpfox::isModule('friend') && Phpfox::getService('friend')->isFriendOfFriend($iUserId) != true) {
                            return Phpfox_Error::display(_p('unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));
                        } else {
                            if ($aFeed['privacy_comment'] == 3 && ($aFeed['user_id'] != Phpfox::getUserId())) {
                                return Phpfox_Error::display(_p('unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));
                            } else {
                                if ($aFeed['privacy_comment'] == 4 && ($bCheck = Phpfox::getService('privacy')->check($aVals['type'],
                                        $aVals['item_id'], $aFeed['user_id'], $aFeed['privacy_comment'], null,
                                        true)) != true) {
                                    return Phpfox_Error::display(_p('unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));
                                }
                            }
                        }
                    }
                }

                // Fallback: if the item is private and it cannot be accessed by the one trying to comment, then, the user should not be able to.
                if (isset($aFeed['privacy']) && !empty($aFeed['privacy']) && !empty($aFeed['user_id']) && $aFeed['user_id'] != $iUserId) {
                    if ($aFeed['privacy'] == 1 && Phpfox::isModule('friend') && Phpfox::getService('friend')->isFriend($iUserId,
                            $aFeed['user_id']) != true) {
                        return Phpfox_Error::display(_p('unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));
                    } else {
                        if ($aFeed['privacy'] == 2 && Phpfox::isModule('friend') && Phpfox::getService('friend')->isFriendOfFriend($iUserId) != true) {
                            return Phpfox_Error::display(_p('unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));
                        } else {
                            if ($aFeed['privacy'] == 3 && ($aFeed['user_id'] != Phpfox::getUserId())) {
                                return Phpfox_Error::display(_p('unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));
                            } else {
                                if ($aFeed['privacy'] == 4 && ($bCheck = Phpfox::getService('privacy')->check($aVals['type'],
                                        $aVals['item_id'], $aFeed['user_id'], $aFeed['privacy'], null, true)) != true) {
                                    return Phpfox_Error::display(_p('unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));
                                }
                            }
                        }
                    }
                }
            }
        }
        // END: Check if user can comment on this item

        if (isset($aVals['parent_group_id']) && isset($aVals['group_view_id']) && $aVals['group_view_id'] > 0) {
            define('PHPFOX_SKIP_FEED', true);
        }

        if (Phpfox::getParam('comment.comment_hash_check')) {
            if (Phpfox::getLib('spam.hash')->setParams([
                'table' => 'comment_hash',
                'total' => Phpfox::getParam('comment.comments_to_check'),
                'time' => Phpfox::getParam('comment.total_minutes_to_wait_for_comments'),
                'content' => $aVals['text']
            ])->isSpam()) {
                return false;
            }
        }

        if ($aVals['type'] != 'app') {
            $aItem = Phpfox::callback($aVals['type'] . '.getCommentItem', $aVals['item_id']);
            if (!isset($aItem['comment_item_id'])) {
                return false;
            }
        } else {
            $feed = $this->database()->select('*')->from(Phpfox::getT($prefix . 'feed'))->where(['feed_id' => $aVals['item_id']])->executeRow();
            $aItem['comment_user_id'] = $feed['user_id'];
            $aItem['comment_view_id'] = 0;
        }
        $bIsBlocked = Phpfox::getService('user.block')->isBlocked($aItem['comment_user_id'], Phpfox::getUserId());
        if ($bIsBlocked) {
            Phpfox_Error::set(_p('unable_to_leave_a_comment_at_this_time_dot'));

            return false;
        }

        $aVals = array_merge($aItem, $aVals);

        $bCheck = Phpfox::getService('ban')->checkAutomaticBan($aVals['text']);
        if ($bCheck == false) {
            return false;
        }
        $aInsert = array(
            'parent_id' => $aVals['parent_id'],
            'type_id' => $aVals['type'],
            'item_id' => $aVals['item_id'],
            'user_id' => $iUserId,
            'owner_user_id' => $aItem['comment_user_id'],
            'time_stamp' => PHPFOX_TIME,
            'ip_address' => Phpfox_Request::instance()->getServer('REMOTE_ADDR'),
            'view_id' => (($aItem['comment_view_id'] == 2 && $aItem['comment_user_id'] != $iUserId) ? '1' : '0'),
            'author' => (!empty($aVals['is_via_feed']) ? (int)$aVals['is_via_feed'] : ''),
            'feed_table' => $prefix . 'feed'
        );

        if (!$iUserId) {
            $aInsert['author'] = substr($aVals['author'], 0, 255);
            $aInsert['author_email'] = $aVals['author_email'];
            if (!empty($aVals['author_url']) && Phpfox_Validator::instance()->verify('url', $aVals['author_url'])) {
                $aInsert['author_url'] = $aVals['author_url'];
            }
        }

        $bIsSpam = false;
        if (Phpfox::getParam('comment.spam_check_comments')) {
            if (Phpfox::getLib('spam')->check(array(
                    'action' => 'isSpam',
                    'params' => array(
                        'module' => 'comment',
                        'content' => Phpfox::getLib('parse.input')->prepare($aVals['text'])
                    )
                )
            )
            ) {
                $aInsert['view_id'] = '9';
                $bIsSpam = true;
                Phpfox_Error::set(_p('your_comment_has_been_marked_as_spam_it_will_have_to_be_approved_by_an_admin'));
            }
        }

        if (Phpfox::getUserParam('comment.approve_all_comments')) {
            $aInsert['view_id'] = '1';
            $bIsSpam = true;
            Phpfox_Ajax::instance()->sPopupMessage = _p('notice');
            Phpfox_Error::set(_p('your_comment_has_successfully_been_added_however_it_is_pending_an_admins_approval'));
        }

        (($sPlugin = Phpfox_Plugin::get('comment.service_process_add')) ? eval($sPlugin) : false);

        $iId = $this->database()->insert($this->_sTable, $aInsert);

        Phpfox::getLib('parse.bbcode')->useVideoImage(($aVals['type'] == 'feed' ? true : false));

        $aVals['text_parsed'] = Phpfox::getLib('parse.input')->prepare($aVals['text'], false, [
            'comment' => $iId
        ]);

        $this->database()->insert(Phpfox::getT('comment_text'), array(
                'comment_id' => $iId,
                'text' => Phpfox::getLib('parse.input')->clean($aVals['text']),
                'text_parsed' => $aVals['text_parsed']
            )
        );

        $aVals['comment_id'] = $iId;

        if (!empty($aVals['parent_id'])) {
            $this->database()->updateCounter('comment', 'child_total', 'comment_id', (int)$aVals['parent_id']);
        }

        if ($bIsSpam === true) {
            return false;
        }
        Phpfox::getService('user.process')->notifyTagged($aVals['text'], $iId, $aVals['type']);

        if (isset($aVals['app_object']) && app()->exists($aVals['app_object'])) {
            $app = app($aVals['app_object']);
            //Update time_update feed
            $this->database()->update(Phpfox::getT($prefix . 'feed'),
                [
                    'time_update' => PHPFOX_TIME
                ],
                'feed_id = ' . (int)$aVals['item_id']
            );
            if (isset($app->notifications) && isset($app->notifications->{'__comment'})) {
                notify($app->id, '__comment', $aVals['item_id'], $feed['user_id'], false);
            }
        } else {
            // Callback this action to other modules
            Phpfox::callback($aVals['type'] . '.addComment', $aVals, $iUserId, $sUserName);
        }

        if (($aItem['comment_view_id'] == 2 && $aItem['comment_user_id'] != $iUserId)) {
            (Phpfox::isModule('request') ? Phpfox::getService('request.process')->add('comment_pending', $iId,
                $aItem['comment_user_id']) : false);

            return 'pending_moderation';
        }

        // Update user activity
        Phpfox::getService('user.activity')->update(Phpfox::getUserId(), 'comment');

        if (Phpfox::getParam('feed.top_stories_update') != 'like') {
            $sFeedPrefix = '';
            $sNewTypeId = $aVals['type'];
            if (!empty($aItem['parent_module_id']) && ($aItem['parent_module_id'] == 'pages' ||
                    $aItem['parent_module_id'] == 'event' || $aItem['parent_module_id'] == 'groups')
            ) {
                $sFeedPrefix = $aItem['parent_module_id'] . '_';
                if ($sNewTypeId == 'pages') {
                    $sNewTypeId = 'pages_comment';
                }

                if ($sNewTypeId == 'event') {
                    $sNewTypeId = 'event_comment';
                }

                if ($sNewTypeId == 'groups') {
                    $sNewTypeId = 'groups_comment';
                    $sFeedPrefix = 'pages_';
                }

                if ($aItem['parent_module_id'] == 'groups') {
                    $sFeedPrefix = 'pages_';
                }
            }

            Phpfox::getService('feed.process')->clearCache($aVals['type'], $aVals['item_id']);
            $this->database()->update(Phpfox::getT($sFeedPrefix . 'feed'), array('time_update' => PHPFOX_TIME),
                'type_id = \'' . $this->database()->escape($sNewTypeId) . '\' AND item_id = ' . (int)$aVals['item_id']);

            if (!empty($sFeedPrefix)) {
                $this->database()->update(Phpfox::getT('feed'), array('time_update' => PHPFOX_TIME),
                    'type_id = \'' . $this->database()->escape($sNewTypeId) . '\' AND item_id = ' . (int)$aVals['item_id']);
            }
        }

        if (redis()->enabled()) {
            redis()->set('comment/' . $iId, [
                'comment_id' => $iId,
                'parent_id' => $aVals['parent_id'],
                'type_id' => $aVals['type'],
                'item_id' => $aVals['item_id'],
                'user_id' => $iUserId,
                'owner_user_id' => $aItem['comment_user_id'],
                'time_stamp' => PHPFOX_TIME,
                'text' => $aVals['text_parsed']
            ]);

            redis()->lpush('comments/' . $aVals['type'] . '/' . $aVals['item_id'], $iId);
            redis()->incr('total/comments/' . $aVals['type'] . '/' . $aVals['item_id']);
        }

        (($sPlugin = Phpfox_Plugin::get('comment.service_process_add_end')) ? eval($sPlugin) : false);

        return $iId;
    }

    /**
     * @param int $iId
     * @param string $sText
     *
     * @return bool
     */
    public function updateText($iId, $sText)
    {
        if (Phpfox::getService('comment')->hasAccess($iId, 'edit_own_comment', 'edit_user_comment')) {
            $oFilter = Phpfox::getLib('parse.input');
            Phpfox::getService('ban')->checkAutomaticBan($sText);
            if (Phpfox::getParam('comment.spam_check_comments')) {
                if (Phpfox::getLib('spam')->check([
                    'action' => 'isSpam',
                    'params' => [
                        'module' => 'comment',
                        'content' => Phpfox::getLib('parse.input')->prepare($sText)
                    ]
                ])
                ) {
                    $this->database()->update(Phpfox::getT('comment'), ['view_id' => '9'], "comment_id = " . (int)$iId);

                    Phpfox_Error::set(_p('your_comment_has_been_marked_as_spam_it_will_have_to_be_approved_by_an_admin'));
                }
            }

            $aVals = $this->database()->select('cmt.*')
                ->from($this->_sTable, 'cmt')
                ->where('cmt.comment_id = ' . (int)$iId)
                ->execute('getSlaveRow');

            Phpfox::getLib('parse.bbcode')->useVideoImage(($aVals['type_id'] == 'feed' ? true : false));

            $this->database()->update(Phpfox::getT('comment'),
                array('update_time' => PHPFOX_TIME, "update_user" => Phpfox::getUserBy("full_name")),
                "comment_id = " . (int)$iId);
            $aVals['text_parsed'] = $oFilter->prepare($sText, false, [
                'comment' => $iId
            ]);
            $this->database()->update(Phpfox::getT('comment_text'),
                array('text' => $oFilter->clean($sText), "text_parsed" => $aVals['text_parsed']),
                "comment_id = " . (int)$iId);

            if (Phpfox::hasCallback($aVals['type_id'], 'updateCommentText')) {
                Phpfox::callback($aVals['type_id'] . '.updateCommentText', $aVals, $aVals['text_parsed']);
            }

            Phpfox::getService('user.process')->notifyTagged($sText, $iId, $aVals['type_id']);

            if (redis()->enabled()) {
                $comment = redis()->get('comment/' . $iId);
                $comment->text = $aVals['text_parsed'];
                $comment->update_time = PHPFOX_TIME;
                redis()->set('comment/' . $iId, $comment);
            }

            return true;
        }

        return false;
    }

    /**
     * @todo Fix this, not working anymore
     *
     * @param int $iId
     * @param int $iTypeId
     * @param bool $bForce
     *
     * @return bool
     */
    public function deleteInline($iId, $iTypeId, $bForce = false)
    {
        $bCanDeleteOnProfile = false;
        $aCore = Phpfox_Request::instance()->get('core');
        if (isset($aCore['is_user_profile']) && $aCore['is_user_profile']) {
            if ($iTypeId == 'feed') {
                $this->database()->join(Phpfox::getT('feed_comment'), 'fc', 'fc.feed_comment_id = c1.item_id');
            } else {
                $this->database()->join(Phpfox::getT('feed'), 'fc',
                    'c1.type_id = fc.type_id AND c1.item_id = fc.item_id');
            }
            $aParent = $this->database()->select('fc.parent_user_id, c1.owner_user_id')
                ->from(Phpfox::getT('comment'), 'c1')
                ->where('c1.comment_id = ' . (int)$iId)
                ->execute('getSlaveRow');

            $bCanDeleteComment = false;
            if (isset($aParent['parent_user_id']) && $aParent['parent_user_id'] == Phpfox::getUserId()) {
                $bCanDeleteComment = true;
            } elseif (isset($aParent['owner_user_id']) && $aParent['owner_user_id'] == Phpfox::getUserId()) {
                $bCanDeleteComment = true;
            }

            $bCanDeleteOnProfile = ($bCanDeleteComment && Phpfox::getUserParam('comment.can_delete_comments_posted_on_own_profile'));
        }


        if (Phpfox::isModule('pages') && Phpfox_Request::instance()->get('type_id') == 'pages') {
            $aPagesParent = $this->database()->select('c1.*, pf.parent_user_id')
                ->from(Phpfox::getT('comment'), 'c1')
                ->join(Phpfox::getT('pages_feed'), 'pf', 'pf.item_id = c1.item_id')
                ->where('c1.comment_id = ' . (int)$iId)
                ->execute('getSlaveRow');

            if (isset($aPagesParent['comment_id']) && Phpfox::getService('pages')->isAdmin($aPagesParent['parent_user_id'])) {
                $bCanDeleteOnProfile = true;
            }
        }

        if ($bForce === true) {
            $bCanDeleteOnProfile = true;
        }

        if ((($iUserId = Phpfox::getService('comment')->hasAccess($iId, 'delete_own_comment',
                    'delete_user_comment')) !== false) || $bCanDeleteOnProfile == true) {
            $aCommentRow = $this->database()->select('*')
                ->from($this->_sTable)
                ->where('comment_id = ' . (int)$iId)
                ->execute('getSlaveRow');

            $this->delete($iId);

            if (empty($aCommentRow['parent_id'])) {
                Phpfox::callback($iTypeId . '.deleteComment', $aCommentRow['item_id']);
            }

            // Update user activity
            Phpfox::getService('user.activity')->update($iUserId, 'comment', '-');

            (($sPlugin = Phpfox_Plugin::get('comment.service_process_deleteinline')) ? eval($sPlugin) : false);

            if (Phpfox::getParam('feed.cache_each_feed_entry')) {
                Phpfox::getService('feed.process')->clearCache($iTypeId, $iId);
            }

            return true;
        }

        return false;
    }

    /**
     * Deletes a comment given its comment id
     *
     * @param int $iId
     *
     * @return bool
     */
    public function delete($iId)
    {
        // delete the feed as well
        $comment = $this->database()->select('*')
            ->from(Phpfox::getT('comment'))
            ->where('comment_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete($comment['type_id'], (int)$iId) : null);

        $this->database()->delete(Phpfox::getT('comment'), "comment_id = " . (int)$iId);
        $this->database()->delete(Phpfox::getT('comment_text'), "comment_id = " . (int)$iId);
        $this->database()->delete(Phpfox::getT('comment_rating'), 'comment_id = ' . (int)$iId);

        if (redis()->enabled()) {
            redis()->del('comment/' . $iId);
            redis()->lrem('comments/' . $comment['type_id'] . '/' . $comment['item_id'], 0, $iId);
            redis()->decr('total/comments/' . $comment['type_id'] . '/' . $comment['item_id']);
        }

        (($sPlugin = Phpfox_Plugin::get('comment.service_process_delete')) ? eval($sPlugin) : false);

        return true;
    }

    /**
     * @todo $iUserId is not use anymore. Remove it and update all place call this function
     *
     * @param int $iUserId
     * @param int $iItemId
     * @param string $sCategory
     *
     * @return bool|null
     */
    public function deleteForItem($iUserId, $iItemId, $sCategory)
    {
        $aRows = $this->database()->select('user_id, comment_id')
            ->from($this->_sTable)
            ->where("item_id = " . $iItemId . " AND type_id = '" . $this->database()->escape($sCategory) . "'")
            ->execute('getSlaveRows');

        if (!count($aRows)) {
            return false;
        }

        $this->database()->delete($this->_sTable,
            "item_id = " . (int)$iItemId . " AND type_id = '" . $this->database()->escape($sCategory) . "'");

        foreach ($aRows as $aRow) {
            (Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->deleteAllOfItem([
                'comment_'.$sCategory,
                'comment_'.$sCategory.'_tag',
                'comment_'.$sCategory.'tag',
            ], (int)$aRow['comment_id']) : null);
            // Update user activity
            Phpfox::getService('user.activity')->update($aRow['user_id'], 'comment', '-');
        }

        return null;
    }

    /**
     * @param int $iId
     * @param string $sAction
     * @param bool $bIsAdmin
     *
     * @return bool
     */
    public function moderate($iId, $sAction, $bIsAdmin = false)
    {
        $aComment = $this->database()->select('c.comment_id, c.user_id, c.type_id, c.item_id, c.parent_id, ct.text, ct.text_parsed, c.type_id AS type, u.full_name')
            ->from($this->_sTable, 'c')
            ->join(Phpfox::getT('comment_text'), 'ct', 'ct.comment_id = c.comment_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
            ->where('c.comment_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aComment['comment_id'])) {
            return Phpfox_Error::set(_p('not_a_valid_comment'));
        }

        $aItem = Phpfox::callback($aComment['type_id'] . '.getCommentItem', $aComment['item_id']);

        $aVals = array_merge($aItem, $aComment);

        if (!Phpfox::getUserParam('comment.can_moderate_comments')) {
            // Make sure this user can actually moderate this comment
            if ($aItem['comment_user_id'] != Phpfox::getUserId()) {
                return Phpfox_Error::set(_p('unable_to_moderate_this_comment'));
            }
        }

        if ($sAction == 'approve') {
            $this->database()->update($this->_sTable,
                array('view_id' => (($bIsAdmin && $aVals['comment_view_id'] == 2) ? '1' : '0')),
                'comment_id = ' . $aComment['comment_id']);

            // Update user activity
            if (($bIsAdmin && $aVals['comment_view_id'] == 2)) {

            } else {
                Phpfox::getService('user.activity')->update($aComment['user_id'], 'comment');
                Phpfox::getLib('mail')->to($aComment['user_id'])
                    ->subject(array(
                        'comment.comment_approved_on_site_title',
                        array('site_title' => Phpfox::getParam('core.site_title'))
                    ))
                    ->message(array(
                            'comment.one_of_your_comments_on_site_title',
                            array(
                                'site_title' => Phpfox::getParam('core.site_title'),
                                'link' => Phpfox_Url::instance()->makeUrl('comment.view',
                                    array('id' => $aComment['comment_id']))
                            )
                        )
                    )
                    ->notification('comment.approve_new_comment')
                    ->send();

                Phpfox::getService('notification.process')->add('comment_user_approve', $iId, $aComment['user_id'], $aComment['user_id'], true);
            }

            if ($bIsAdmin) {
                $this->database()->updateCounter('user', 'total_spam', 'user_id', $aComment['user_id'], true);

                define('FEED_FORCE_USER_ID', $aComment['user_id']);

                // Callback this action to other modules
                Phpfox::callback($aVals['type'] . '.addComment', $aVals, $aComment['user_id'], $aComment['full_name']);
            }

            // notify tagged users
            $aMatches = Phpfox::getLib('parse.output')->mentionsRegex($aComment['text']);
            if (Phpfox::isModule('notification')) {
                $sName = 'comment_';
                if ($aVals['type'] == 'photo_album' || (strpos($aVals['type'],
                            'music') !== false) || ($aVals['type'] == 'user_status')) {
                    $sName .= $aVals['type'] . 'tag';
                } else {
                    $sName .= $aVals['type'] . '_tag';
                }

                foreach ($aMatches as $oUser) {
                    Phpfox::getService('notification.process')->add($sName, $aComment['comment_id'], $oUser->id,
                        $aComment['user_id'], true);
                }
            }
        } else {
            // send notification to owner of comment
            Phpfox::getService('notification.process')->add("comment_deny_comment_$aComment[type_id]", $aComment['item_id'], $aComment['user_id'], $aComment['user_id'], true);

            $this->delete($aComment['comment_id']);
        }

        if (isset($aVals['comment_view_id']) && $aVals['comment_view_id'] == 2) {
            if ($bIsAdmin) {
                (Phpfox::isModule('request') ? Phpfox::getService('request.process')->add('comment_pending',
                    $aComment['comment_id'], $aItem['comment_user_id']) : false);
            } else {
                // Remove the initial request
                (Phpfox::isModule('request') ? Phpfox::getService('request.process')->delete('comment_pending',
                    $aComment['comment_id'], $aItem['comment_user_id']) : false);

                // Process this action with other modules
                Phpfox::callback($aComment['type_id'] . '.processCommentModeration', $sAction, $aComment['item_id']);
            }
        }

        return true;
    }

    /**
     * @param $aParams
     * @param bool $bSendToOwner
     */
    public function notify($aParams, $bSendToOwner = true)
    {
        $iSenderUserId = isset($aParams['sender_id']) ? $aParams['sender_id'] : null;
        $aExcludeUsers = isset($aParams['exclude_users']) ? $aParams['exclude_users'] : null;

        // to owner of item
        if ($bSendToOwner) {
            Phpfox_Mail::instance()->to($aParams['user_id'])
                ->subject($aParams['owner_subject'])
                ->message($aParams['owner_message'])
                ->notification($aParams['owner_notification'])
                ->send();
            if (Phpfox::isModule('notification')) {
                Phpfox::getService('notification.process')->add($aParams['notify_id'], $aParams['item_id'],
                    $aParams['user_id'], $iSenderUserId);
            }
        }

        // to users that commented in an item
        Phpfox::getService('comment')->massMail($aParams['mass_id'], $aParams['item_id'], $aParams['user_id'], [
            'subject' => $aParams['mass_subject'],
            'message' => $aParams['mass_message']
        ], $iSenderUserId, $aExcludeUsers);

        if ($sPlugin = Phpfox_Plugin::get('comment.service_process_notify_1')) {
            eval($sPlugin);
        }
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
        if ($sPlugin = Phpfox_Plugin::get('comment.service_process___call')) {
            eval($sPlugin);

            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}
