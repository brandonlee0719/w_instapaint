<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Feed_Component_Block_Mini_Feed_Action extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $aFeed = $this->getParam('aFeed');
        if (!isset($aFeed['feed_id'])) {
            $aFeed['feed_id'] = $aFeed['item_id'];
        }
        $aFeed['is_view_item'] = true;
        $sFeedType = (isset($aFeed['feed_display']) ? $aFeed['feed_display'] : null);

        $bCanPostComment = true;

        // check user group setting of module/app -- Can add comment?
        if (!empty($aFeed['comment_type_id']) && $aFeed['comment_type_id'] != 'app' && Phpfox::hasCallback($aFeed['comment_type_id'],
                'getAjaxCommentVar')) {
            $sVar = Phpfox::callback($aFeed['comment_type_id'] . '.getAjaxCommentVar');
            if ($sVar !== null) {
                $bCanPostComment = Phpfox::getUserParam($sVar);
            }
        }

        if (isset($aFeed['comment_privacy']) && $aFeed['user_id'] != Phpfox::getUserId() && (Phpfox::isModule('privacy') && !Phpfox::getUserParam('privacy.can_comment_on_all_items'))) {
            switch ($aFeed['comment_privacy']) {
                case 1:
                    if ((int)$aFeed['feed_is_friend'] <= 0) {
                        $bCanPostComment = false;
                    }
                    break;
                case 2:
                    if ((int)$aFeed['feed_is_friend'] > 0) {
                        $bCanPostComment = true;
                    } else {
                        if (Phpfox::isModule('friend') && !Phpfox::getService('friend')->isFriendOfFriend($aFeed['user_id'])) {
                            $bCanPostComment = false;
                        }
                    }
                    break;
                case 3:
                    $bCanPostComment = false;
                    break;
            }
        }
        $aFeed['can_post_comment'] = $bCanPostComment;

        if (isset($aFeed['total_like']) && (int)$aFeed['total_like'] > 0 && Phpfox::isModule('like')) {
            $aFeed['likes'] = Phpfox::getService('like')->getLikesForFeed($aFeed['like_type_id'], $aFeed['item_id'],
                ((int)$aFeed['feed_is_liked'] > 0 ? true : false), Phpfox::getParam('feed.total_likes_to_display'),
                false, (isset($aFeed['feed_table_prefix']) ? $aFeed['feed_table_prefix'] : ''));
        }

        $aFeed['type_id'] = (!empty($aFeed['type_id']) ? $aFeed['type_id'] : (isset($aFeed['report_module']) ? $aFeed['report_module'] : ''));

        if ($aFeed['type_id'] == 'forum_reply') {
            $aFeed['type_id'] = 'forum_post';
        }

        if (Phpfox::isModule('share') && !isset($aFeed['total_share'])) {
            $aFeed['total_share'] = Phpfox::getService('feed')->getShareCount($aFeed['type_id'], $aFeed['item_id']);
        }

        $aFeedActions = Phpfox::getService('feed')->getFeedActions($aFeed);
        $aFeed = array_merge($aFeed, $aFeedActions);
        $this->template()->assign(array(
                'aFeed' => $aFeed,
                'sFeedType' => $sFeedType,
                'feedJson' => json_encode($aFeed)
            )
        );

        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('comment.component_block_comment_clean')) ? eval($sPlugin) : false);
    }
}