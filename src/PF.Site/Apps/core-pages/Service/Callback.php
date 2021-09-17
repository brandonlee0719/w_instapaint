<?php

namespace Apps\Core_Pages\Service;

use Phpfox;
use Phpfox_Ajax;
use Phpfox_Database;
use Phpfox_Error;
use Phpfox_Pages_Callback;
use Phpfox_Plugin;
use Phpfox_Request;
use Phpfox_Template;
use Phpfox_Url;

defined('PHPFOX') or exit('NO DICE!');

class Callback extends Phpfox_Pages_Callback
{
    public function __construct()
    {
        parent::__construct();
        Phpfox::getService('pages')->setIsInPage();
    }

    /**
     * @return Facade|object
     */
    public function getFacade()
    {
        return Phpfox::getService('pages.facade');
    }

    public function getProfileLink()
    {
        return 'profile.pages';
    }

    public function getProfileMenu($aUser)
    {
        if (!Phpfox::getUserParam('pages.can_view_browse_pages')) {
            return false;
        }

        if (Phpfox::getParam('profile.show_empty_tabs') == false) {
            if (!isset($aUser['total_pages'])) {
                return false;
            }

            if (isset($aUser['total_pages']) && (int)$aUser['total_pages'] === 0) {
                return false;
            }
        }

        $aMenus[] = array(
            'phrase' => _p('pages'),
            'url' => 'profile.pages',
            'total' => (int)(isset($aUser['total_pages']) ? $aUser['total_pages'] : 0),
            'icon' => 'feed/blog.png'
        );

        return $aMenus;
    }

    public function canShareItemOnFeed()
    {
    }

    public function getActivityFeed($aItem, $aCallback = null, $bIsChildItem = false)
    {
        if ($bIsChildItem) {
            $this->database()->select(Phpfox::getUserField('u2') . ', ')->join(Phpfox::getT('user'), 'u2',
                'u2.user_id = p.user_id');
        }

        $aRow = $this->database()->select('p.*, pc.page_type, pu.vanity_url')
            ->from(Phpfox::getT('pages'), 'p')
            ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
            ->leftJoin(Phpfox::getT('pages_category'), 'pc', 'pc.category_id = p.category_id')
            ->where('p.page_id = ' . (int)$aItem['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['page_id'])) {
            return false;
        }

        if ($bIsChildItem) {
            $aItem = $aRow;
        }

        $aReturn = array(
            'feed_title' => $aRow['title'],
            'no_user_show' => true,
            'feed_content' => ($aRow['page_type'] == '1' ? ($aRow['total_like'] == '1' ? _p('1_member') : _p('total_like_members',
                array('total_like' => $aRow['total_like']))) : ($aRow['total_like'] == '1' ? _p('1_like') : _p('total_like_likes',
                array('total_like' => $aRow['total_like'])))),
            'feed_link' => Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'],
                $aRow['vanity_url']),
            'feed_icon' => Phpfox::getLib('image.helper')->display(array(
                'theme' => 'module/marketplace.png',
                'return_url' => true
            )),
            'time_stamp' => $aRow['time_stamp'],
            'enable_like' => false,
        );

        if (!empty($aRow['image_path'])) {
            $sImage = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aRow['server_id'],
                    'path' => (($aRow['app_id'] != 0) ? 'app.url_image' : 'pages.url_image'),
                    'file' => $aRow['image_path'],
                    'suffix' => '_120',
                    'max_width' => 120,
                    'max_height' => 120
                )
            );

            $aReturn['feed_image'] = $sImage;
        }

        if ($bIsChildItem) {
            $aReturn = array_merge($aReturn, $aItem);
        }

        return $aReturn;
    }

    /**
     * A tag B in a comment in a page, B will receive this notification
     * @param $aNotification
     * @return array
     */
    public function getCommentNotificationTag($aNotification)
    {
        $aRow = $this->database()->select('b.page_id, b.title, pu.vanity_url, u.full_name, fc.feed_comment_id')
            ->from(Phpfox::getT('comment'), 'c')
            ->join(Phpfox::getT('pages_feed_comment'), 'fc', 'fc.feed_comment_id = c.item_id')
            ->join(Phpfox::getT('pages'), 'b', 'b.page_id = fc.parent_user_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
            ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = b.page_id')
            ->where('c.comment_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        $sPhrase = _p('full_name_tagged_you_in_a_comment_in_page_title', [
            'full_name' => $aRow['full_name'],
            'title' => $aRow['title']
        ]);

        return array(
            'link' => Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'],
                    $aRow['vanity_url']) . 'comment-id_' . $aRow['feed_comment_id'] . '/',
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        );
    }

    /**
     * A post to a page and tag B, B will receive this notification
     * @param $aNotification
     * @return array
     */
    public function getNotificationPost_Tag($aNotification)
    {
        $aRow = $this->database()->select('b.page_id, b.title, pu.vanity_url, u.full_name, fc.feed_comment_id')
            ->from(Phpfox::getT('pages_feed_comment'), 'fc')
            ->join(Phpfox::getT('pages'), 'b', 'b.page_id = fc.parent_user_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
            ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = b.page_id')
            ->where('fc.feed_comment_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        $sPhrase = _p('full_name_tagged_you_in_a_post_in_page_title', [
            'full_name' => $aRow['full_name'],
            'title' => $aRow['title']
        ]);

        return array(
            'link' => Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'],
                    $aRow['vanity_url']) . 'comment-id_' . $aRow['feed_comment_id'] . '/',
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        );
    }

    public function getSiteStatsForAdmin($iStartTime, $iEndTime)
    {
        $aCond = array();
        $aCond[] = 'app_id = 0 AND view_id = 0 AND item_type = 0';
        if ($iStartTime > 0) {
            $aCond[] = 'AND time_stamp >= \'' . $this->database()->escape($iStartTime) . '\'';
        }
        if ($iEndTime > 0) {
            $aCond[] = 'AND time_stamp <= \'' . $this->database()->escape($iEndTime) . '\'';
        }

        $iCnt = (int)$this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('pages'))
            ->where($aCond)
            ->execute('getSlaveField');

        return array(
            'phrase' => 'pages.pages',
            'total' => $iCnt
        );
    }

    public function getSiteStatsForAdmins()
    {
        $iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

        return array(
            'phrase' => _p('pages'),
            'value' => $this->database()->select('COUNT(*)')
                ->from(':pages')
                ->where('view_id = 0 AND item_type = 0 AND time_stamp >= ' . $iToday)
                ->executeField()
        );
    }

    public function addPhoto($iId)
    {
        Phpfox::getService('pages')->setIsInPage();

        return array(
            'module' => 'pages',
            'item_id' => $iId,
            'table_prefix' => 'pages_'
        );
    }

    public function getDashboardActivity()
    {
        $aUser = Phpfox::getService('user')->get(Phpfox::getUserId(), true);

        return array(
            _p('pages') => $aUser['activity_pages']
        );
    }

    public function getCommentNotification($aNotification)
    {
        $aRow = $this->database()->select('fc.feed_comment_id, u.user_id, u.gender, u.user_name, u.full_name, e.page_id, e.title, pu.vanity_url')
            ->from(Phpfox::getT('pages_feed_comment'), 'fc')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
            ->join(Phpfox::getT('pages'), 'e', 'e.page_id = fc.parent_user_id')
            ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = e.page_id')
            ->where('fc.feed_comment_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['feed_comment_id'])) {
            return false;
        }

        if ($aNotification['user_id'] == $aRow['user_id'] && isset($aNotification['extra_users']) && count($aNotification['extra_users'])) {
            $sUsers = Phpfox::getService('notification')->getUsers($aNotification, true);
        } else {
            $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        }
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'],
            Phpfox::getParam('notification.total_notification_title_length'), '...');

        if ($aNotification['user_id'] == $aRow['user_id']) {
            if (isset($aNotification['extra_users']) && count($aNotification['extra_users'])) {
                $sPhrase = _p('users_commented_on_full_name_comment',
                    array('users' => $sUsers, 'full_name' => $aRow['full_name'], 'title' => $sTitle));
            } else {
                $sPhrase = _p('users_commented_on_gender_own_comment', array(
                    'users' => $sUsers,
                    'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1),
                    'title' => $sTitle
                ));
            }
        } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
            $sPhrase = _p('users_commented_on_one_of_your_comments', array('users' => $sUsers, 'title' => $sTitle));
        } else {
            $sPhrase = _p('users_commented_on_one_of_full_name_comments',
                array('users' => $sUsers, 'full_name' => $aRow['full_name'], 'title' => $sTitle));
        }

        $sLink = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);

        return array(
            'link' => $sLink . 'wall/comment-id_' . $aRow['feed_comment_id'],
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        );
    }

    public function getPhotoDetails($aPhoto)
    {
        Phpfox::getService('pages')->setIsInPage();

        $aRow = Phpfox::getService('pages')->getPage($aPhoto['group_id']);

        if (!isset($aRow['page_id'])) {
            return false;
        }

        Phpfox::getService('pages')->setMode();

        $sLink = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);

        return array(
            'breadcrumb_title' => _p('pages'),
            'breadcrumb_home' => Phpfox_Url::instance()->makeUrl('pages'),
            'module_id' => 'pages',
            'item_id' => $aRow['page_id'],
            'title' => $aRow['title'],
            'url_home' => $sLink,
            'url_home_photo' => $sLink . 'photo/',
            'theater_mode' => _p('in_the_page_link_title', array('link' => $sLink, 'title' => $aRow['title'])),
            'feed_table_prefix' => 'pages_'
        );
    }

    public function getPhotoCount($iPageId)
    {
        $iCnt = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('photo'))
            ->where("module_id = 'pages' AND group_id = " . $iPageId)
            ->execute('getSlaveField');

        return ($iCnt > 0) ? $iCnt : 0;
    }

    public function getAlbumCount($iPageId)
    {
        $iCnt = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('photo_album'))
            ->where("module_id = 'pages' AND group_id = " . $iPageId)
            ->execute('getSlaveField');

        return ($iCnt > 0) ? $iCnt : 0;
    }

    public function uploadVideo($aVals)
    {
        Phpfox::getService('pages')->setIsInPage();

        return array(
            'module' => 'pages',
            'item_id' => (is_array($aVals) && isset($aVals['callback_item_id']) ? $aVals['callback_item_id'] : (int)$aVals)
        );
    }

    public function addLink($aVals)
    {
        return array(
            'module' => 'pages',
            'item_id' => $aVals['callback_item_id'],
            'table_prefix' => 'pages_'
        );
    }

    public function getFeedDisplay($iEvent)
    {
        return array(
            'module' => 'pages',
            'table_prefix' => 'pages_',
            'ajax_request' => 'event.addFeedComment',
            'item_id' => $iEvent,
            'disable_share' => Phpfox::getService('pages')->hasPerm($iEvent, 'pages.share_updates')
        );
    }

    public function getActivityFeedCustomChecksComment($aRow)
    {
        if ((defined('PHPFOX_IS_PAGES_VIEW') && !Phpfox::getService('pages')->hasPerm(null,
                    'pages.view_browse_updates'))
            || (!defined('PHPFOX_IS_PAGES_VIEW') && !Phpfox::getService('pages')->hasPerm($aRow['custom_data_cache']['page_id'],
                    'pages.view_browse_updates'))
            || (defined('PHPFOX_IS_PAGES_VIEW') && !Phpfox::getService('pages')->hasPerm(null, 'pages.share_updates'))
            || (!defined('PHPFOX_IS_PAGES_VIEW') && !Phpfox::getService('pages')->hasPerm($aRow['custom_data_cache']['page_id'],
                    'pages.share_updates'))
        ) {
            return false;
        }

        if ($aRow['custom_data_cache']['reg_method'] == 2 &&
            (
                !Phpfox::getService('pages')->isMember($aRow['custom_data_cache']['page_id']) &&
                !Phpfox::getService('pages')->isAdmin($aRow['custom_data_cache']['page_id']) &&
                Phpfox::getService('user')->isAdminUser(Phpfox::getUserId())
            )
        ) {
            return false;
        }

        return $aRow;
    }

    public function getActivityFeedComment($aItem)
    {
        $aRow = $this->database()->select('fc.*, l.like_id AS is_liked, e.reg_method, e.page_id, e.title, e.app_id AS is_app, pu.vanity_url, ' . Phpfox::getUserField('u',
                'parent_'))
            ->from(Phpfox::getT('pages_feed_comment'), 'fc')
            ->join(Phpfox::getT('pages'), 'e', 'e.page_id = fc.parent_user_id')
            ->join(Phpfox::getT('user'), 'u', 'u.profile_page_id = e.page_id')
            ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = e.page_id')
            ->leftJoin(Phpfox::getT('like'), 'l',
                'l.type_id = \'pages_comment\' AND l.item_id = fc.feed_comment_id AND l.user_id = ' . Phpfox::getUserId())
            ->where('fc.feed_comment_id = ' . (int)$aItem['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['page_id'])) {
            return false;
        }

        if ((defined('PHPFOX_IS_PAGES_VIEW') && !Phpfox::getService('pages')->hasPerm(null,
                    'pages.view_browse_updates'))
            || (!defined('PHPFOX_IS_PAGES_VIEW') && !Phpfox::getService('pages')->hasPerm($aRow['page_id'],
                    'pages.view_browse_updates'))
        ) {
            return false;
        }

        if ($aRow['reg_method'] == 2 &&
            (
                !Phpfox::getService('pages')->isMember($aRow['page_id']) &&
                !Phpfox::getService('pages')->isAdmin($aRow['page_id']) &&
                Phpfox::getService('user')->isAdminUser(Phpfox::getUserId())
            )
        ) {
            return false;
        }

        $sLink = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'],
                $aRow['vanity_url']) . 'wall/comment-id_' . $aItem['item_id'] . '/';
        $aUser = Phpfox::getService('user')->getUser($aRow['user_id']);

        $aReturn = array_merge($aUser, [
            'feed_status' => $aRow['content'],
            'feed_link' => $sLink,
            'total_comment' => $aRow['total_comment'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => $aRow['is_liked'],
            'feed_icon' => Phpfox::getLib('image.helper')->display(array(
                'theme' => 'misc/comment.png',
                'return_url' => true
            )),
            'feed_title' => '',
            'time_stamp' => $aRow['time_stamp'],
            'enable_like' => true,
            'comment_type_id' => 'pages',
            'like_type_id' => 'pages_comment',
            'is_custom_app' => $aRow['is_app'],
            'custom_data_cache' => $aRow
        ]);

        $aReturn['parent_user_name'] = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'],
            $aRow['vanity_url']);

        if ($aRow['user_id'] != $aRow['parent_user_id']) {
            if (!defined('PHPFOX_IS_PAGES_VIEW') && !defined('PHPFOX_PAGES_ADD_COMMENT')) {
                $aReturn['parent_user'] = Phpfox::getService('user')->getUserFields(true, $aRow, 'parent_');
            }
        }

        return $aReturn;
    }

    public function getActivityFeedItemLiked($aItem)
    {
        $aRow = $this->database()->select('p.page_id, p.title, p.total_like, pu.vanity_url, l.like_id AS is_liked, p.image_path, p.image_server_id')
            ->from(Phpfox::getT('pages'), 'p')
            ->where('p.page_id = ' . (int)$aItem['item_id'])
            ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
            ->leftJoin(Phpfox::getT('like'), 'l',
                'l.type_id = \'pages\' AND l.item_id = p.page_id AND l.user_id = ' . Phpfox::getUserId())
            ->execute('getSlaveRow');

        $sLink = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);

        $aReturn = array(
            'feed_title' => '',
            'feed_info' => _p('liked_the_page_link_title_title', array(
                'link' => $sLink,
                'link_title' => Phpfox::getLib('parse.output')->clean($aRow['title']),
                'title' => Phpfox::getLib('parse.output')->clean(Phpfox::getLib('parse.output')->shorten($aRow['title'],
                    50, '...'))
            )),
            'feed_link' => $sLink,
            'no_target_blank' => true,
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => $aRow['is_liked'],
            'feed_icon' => Phpfox::getLib('image.helper')->display(array(
                'theme' => 'misc/comment.png',
                'return_url' => true
            )),
            'time_stamp' => $aItem['time_stamp'],
            'like_type_id' => 'pages'
        );

        if (!empty($aRow['image_path'])) {
            $sImage = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aRow['image_server_id'],
                    'path' => 'pages.url_image',
                    'file' => $aRow['image_path'],
                    'suffix' => '_120',
                    'max_width' => 120,
                    'max_height' => 120
                )
            );

            $aReturn['feed_image'] = $sImage;
        }

        return $aReturn;
    }

    public function addEvent($iItem)
    {
        Phpfox::getService('pages')->setIsInPage();

        $aRow = Phpfox::getService('pages')->getPage($iItem);

        if (!isset($aRow['page_id'])) {
            return false;
        }

        return $aRow;
    }

    public function viewEvent($iItem)
    {
        $aRow = $this->addEvent($iItem);

        if (!$aRow) {
            return false;
        }

        $sLink = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);

        return array(
            'breadcrumb_title' => _p('pages'),
            'breadcrumb_home' => Phpfox_Url::instance()->makeUrl('pages'),
            'module_id' => 'pages',
            'item_id' => $aRow['page_id'],
            'title' => $aRow['title'],
            'url_home' => $sLink,
            'url_home_pages' => $sLink . 'event/'
        );
    }

    public function getFeedDetails($iItemId)
    {
        return array(
            'module' => 'pages',
            'table_prefix' => 'pages_',
            'item_id' => $iItemId
        );
    }

    public function deleteFeedItem($iItemId)
    {
        $aFeedComment = $this->database()->select('*')
            ->from(Phpfox::getT('pages_feed_comment'))
            ->where('feed_comment_id = ' . (int)$iItemId)
            ->execute('getSlaveRow');

        if (empty($aFeedComment) || empty($aFeedComment['parent_user_id'])) {
            return true;
        }

        $iTotalComments = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('pages_feed'))
            ->where('type_id = \'pages_comment\' AND parent_user_id = ' . $aFeedComment['parent_user_id'])
            ->execute('getSlaveField');

        $this->database()->update(Phpfox::getT('pages'), array('total_comment' => $iTotalComments),
            'page_id = ' . (int)$aFeedComment['parent_user_id']);

        return true;
    }

    public function getNotificationInvite($aNotification)
    {
        $aRow = Phpfox::getService('pages')->getPage($aNotification['item_id']);

        if (!isset($aRow['page_id'])) {
            return false;
        }

        $sPhrase = _p('users_invited_you_to_check_out_the_page_title', array(
            'users' => Phpfox::getService('notification')->getUsers($aNotification),
            'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'],
                Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));

        return array(
            'link' => Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']),
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        );
    }

    public function deleteLike($iItemId, $iUserId = 0)
    {
        // Get the threads from this page
        if (db()->tableExists(\Phpfox::getT('forum_thread'))) {
            $aRows = $this->database()->select('thread_id')
                ->from(Phpfox::getT('forum_thread'))
                ->where('group_id = ' . (int)$iItemId)
                ->execute('getSlaveRows');

            $aThreads = array();
            foreach ($aRows as $sKey => $aRow) {
                $aThreads[] = $aRow['thread_id'];
            }
            if (!empty($aThreads)) {
                $this->database()->delete(Phpfox::getT('forum_subscribe'),
                    'user_id = ' . Phpfox::getUserId() . ' AND thread_id IN (' . implode($aThreads, ',') . ')');
            }
        }

        $aRow = Phpfox::getService('pages')->getPage($iItemId);
        if (!isset($aRow['page_id'])) {
            return false;
        }

        $this->database()->updateCount('like', 'type_id = \'pages\' AND item_id = ' . (int)$iItemId . '',
            'total_like',
            'pages', 'page_id = ' . (int)$iItemId);
        $iFriendId = (int)$this->database()->select('user_id')
            ->from(Phpfox::getT('user'))
            ->where('profile_page_id = ' . (int)$aRow['page_id'])
            ->execute('getSlaveField');

        $this->database()->delete(Phpfox::getT('friend'),
            'user_id = ' . (int)$iFriendId . ' AND friend_user_id = ' . ($iUserId > 0 ? $iUserId : Phpfox::getUserId()));
        $this->database()->delete(Phpfox::getT('friend'),
            'friend_user_id = ' . (int)$iFriendId . ' AND user_id = ' . ($iUserId > 0 ? $iUserId : Phpfox::getUserId()));


        if (!$iUserId) {
            $sLink = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);
            if (!defined('PHPFOX_CANCEL_ACCOUNT') || PHPFOX_CANCEL_ACCOUNT != true) {
                Phpfox_Ajax::instance()->call('window.location.href = \'' . $sLink . '\';');
            }
        }

        /* Remove invites */
        if ($iUserId != Phpfox::getUserId()) // Its not the user willingly leaving the page
        {
            $this->database()->delete(Phpfox::getT('pages_invite'),
                'page_id = ' . (int)$iItemId . ' AND invited_user_id =' . (int)$iUserId);
        }

        return true;
    }

    public function addLike($iItemId, $bDoNotSendEmail = false, $iUserId = null)
    {
        $aRow = Phpfox::getService('pages')->getPage($iItemId);

        if (!isset($aRow['page_id'])) {
            return false;
        }

        $this->database()->updateCount('like', 'type_id = \'pages\' AND item_id = ' . (int)$iItemId . '', 'total_like',
            'pages', 'page_id = ' . (int)$iItemId);
        $sLink = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);

        if ($iUserId === null) {
            if (!$aRow['page_type']) {
                Phpfox::getLib('mail')->to($aRow['user_id'])
                    ->subject(array(
                        'pages.full_name_liked_your_page_title',
                        array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['title'])
                    ))
                    ->message(array(
                        'pages.full_name_liked_your_page',
                        array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'link' => $sLink,
                            'title' => $aRow['title']
                        )
                    ))
                    ->notification('like.new_like')
                    ->send();

                Phpfox::getService('notification.process')->add('pages_like', $aRow['page_id'], $aRow['user_id']);

                (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('pages_itemLiked',
                    $aRow['page_id']) : null);
            }
        } else {
            if ($iUserId != $aRow['user_id']) {
                Phpfox::getLib('mail')->to($iUserId)
                    ->subject(array('pages.membership_accepted_to_title', array('title' => $aRow['title'])))
                    ->message(array(
                        'pages.your_membership_to_the_page_link',
                        array('link' => $sLink, 'title' => $aRow['title'])
                    ))
                    ->send();

                Phpfox::getService('notification.process')->add('pages_like', $aRow['page_id'], $aRow['user_id'],
                    $iUserId, true);
            }
        }

        $iFriendId = (int)$this->database()->select('user_id')
            ->from(Phpfox::getT('user'))
            ->where('profile_page_id = ' . (int)$aRow['page_id'])
            ->execute('getSlaveField');

        $bIsApprove = true;
        if ($iUserId === null) {
            $iUserId = Phpfox::getUserId();
            $bIsApprove = false;
        }

        $this->database()->insert(Phpfox::getT('friend'), array(
                'is_page' => 1,
                'list_id' => 0,
                'user_id' => $iUserId,
                'friend_user_id' => $iFriendId,
                'time_stamp' => PHPFOX_TIME
            )
        );

        $this->database()->insert(Phpfox::getT('friend'), array(
                'is_page' => 1,
                'list_id' => 0,
                'user_id' => $iFriendId,
                'friend_user_id' => $iUserId,
                'time_stamp' => PHPFOX_TIME
            )
        );

        if (!$bIsApprove) {
            Phpfox_Ajax::instance()->call('window.location.href = \'' . $sLink . '\';');
        }

        return null;
    }

    public function getVideoDetails($aItem)
    {
        Phpfox::getService('pages')->setIsInPage();

        $aRow = Phpfox::getService('pages')->getPage($aItem['item_id']);

        if (!isset($aRow['page_id'])) {
            return false;
        }

        Phpfox::getService('pages')->setMode();

        $sLink = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);

        return array(
            'breadcrumb_title' => _p('pages'),
            'breadcrumb_home' => Phpfox_Url::instance()->makeUrl('pages'),
            'module_id' => 'pages',
            'item_id' => $aRow['page_id'],
            'title' => $aRow['title'],
            'url_home' => $sLink,
            'url_home_photo' => $sLink . 'video/',
            'theater_mode' => _p('in_the_page_link_title', array('link' => $sLink, 'title' => $aRow['title']))
        );
    }

    public function getMusicDetails($aItem)
    {
        Phpfox::getService('pages')->setIsInPage();

        $aRow = Phpfox::getService('pages')->getPage($aItem['item_id']);

        if (!isset($aRow['page_id'])) {
            return false;
        }

        Phpfox::getService('pages')->setMode();

        $sLink = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);

        return array(
            'breadcrumb_title' => _p('pages'),
            'breadcrumb_home' => Phpfox_Url::instance()->makeUrl('pages'),
            'module_id' => 'pages',
            'item_id' => $aRow['page_id'],
            'title' => $aRow['title'],
            'url_home' => $sLink,
            'url_home_photo' => $sLink . 'music/',
            'theater_mode' => _p('in_the_page_link_title', array('link' => $sLink, 'title' => $aRow['title']))
        );
    }

    public function getBlogDetails($aItem)
    {
        Phpfox::getService('pages')->setIsInPage();
        $aRow = Phpfox::getService('pages')->getPage($aItem['item_id']);
        if (!isset($aRow['page_id'])) {
            return false;
        }
        $sLink = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);

        return array(
            'breadcrumb_title' => _p('pages'),
            'breadcrumb_home' => Phpfox_Url::instance()->makeUrl('pages'),
            'module_id' => 'pages',
            'item_id' => $aRow['page_id'],
            'title' => $aRow['title'],
            'url_home' => $sLink,
            'url_home_photo' => $sLink . 'blog/',
            'theater_mode' => _p('in_the_page_link_title', array('link' => $sLink, 'title' => $aRow['title']))
        );
    }

    public function uploadSong($iItemId)
    {
        Phpfox::getService('pages')->setIsInPage();

        return array(
            'module' => 'pages',
            'item_id' => $iItemId,
            'table_prefix' => 'pages_'
        );
    }

    public function getNotificationLike($aNotification)
    {
        $aRow = Phpfox::getService('pages')->getPage($aNotification['item_id']);

        if (!isset($aRow['page_id'])) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        if (!isset($aRow['gender'])) {
            $sGender = 'their';
        } else {
            $sGender = Phpfox::getService('user')->gender($aRow['gender'], 1);
        }
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'],
            Phpfox::getParam('notification.total_notification_title_length'), '...');

        if ($aRow['page_type'] == '1') {
            if ($aNotification['user_id'] == $aRow['user_id']) {
                $sPhrase = _p('users_joined_gender_own_page_title',
                    array('users' => $sUsers, 'gender' => $sGender, 'title' => $sTitle));
            } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
                $sPhrase = _p('users_joined_your_page_title', array('users' => $sUsers, 'title' => $sTitle));
            } else {
                $sPhrase = _p('users_joined_full_names_page_title', array(
                    'users' => $sUsers,
                    'full_name' => Phpfox::getLib('parse.output')->shorten($aRow['full_name'], 0),
                    'title' => $sTitle
                ));
            }
        } else {
            if ($aNotification['user_id'] == $aRow['user_id']) {
                $sPhrase = _p('users_liked_gender_own_page_title',
                    array('users' => $sUsers, 'gender' => $sGender, 'title' => $sTitle));
            } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
                $sPhrase = _p('users_liked_your_page_title', array('users' => $sUsers, 'title' => $sTitle));
            } else {
                $sPhrase = _p('users_liked_full_names_page_title', array(
                    'users' => $sUsers,
                    'full_name' => Phpfox::getLib('parse.output')->shorten($aRow['full_name'], 0),
                    'title' => $sTitle
                ));
            }
        }

        return array(
            'link' => Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']),
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        );
    }

    public function addForum($iId)
    {
        Phpfox::getService('pages')->setIsInPage();

        $aRow = Phpfox::getService('pages')->getPage($iId);

        if (!isset($aRow['page_id'])) {
            return false;
        }

        $sLink = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);

        return array(
            'module' => 'pages',
            'item' => $aRow['page_id'],
            'group_id' => $aRow['page_id'],
            'url_home' => $sLink,
            'title' => $aRow['title'],
            'table_prefix' => 'pages_',
            'item_id' => $aRow['page_id'],
            'breadcrumb_title' => _p('pages'),
            'breadcrumb_home' => Phpfox_Url::instance()->makeUrl('pages')
        );
    }

    public function getPagePerms()
    {
        $aPerms = [
            'pages.share_updates' => _p('who_can_post_a_comment'),
            'pages.view_browse_updates' => _p('who_can_view_browse_comments'),
            'pages.view_browse_widgets' => _p('can_view_widgets'),
            'pages.view_admins' => _p('who_can_view_admins')
        ];

        return $aPerms;
    }

    public function checkFeedShareLink()
    {
        return false;
    }

    public function getAjaxCommentVar()
    {
        return null;
    }

    public function getRedirectComment($iId)
    {
        $aListing = $this->database()->select('pfc.feed_comment_id AS comment_item_id, pfc.privacy_comment, pfc.user_id AS comment_user_id, m.*, pu.vanity_url, pfc.parent_user_id AS item_id')
            ->from(Phpfox::getT('pages_feed_comment'), 'pfc')
            ->join(Phpfox::getT('pages'), 'm', 'm.page_id = pfc.parent_user_id')
            ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = m.page_id')
            ->where('pfc.feed_comment_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aListing['page_id'])) {
            return false;
        }

        return Phpfox::getService('pages')->getUrl($aListing['page_id'], $aListing['title'],
                $aListing['vanity_url']) . 'comment-id_' . $aListing['comment_item_id'] . '/';
    }

    public function getFeedRedirect($iId, $iChild = 0)
    {
        $aListing = $this->database()->select('m.page_id, m.title, pu.vanity_url, pf.item_id')
            ->from(Phpfox::getT('pages_feed'), 'pf')
            ->join(Phpfox::getT('pages'), 'm', 'm.page_id = pf.parent_user_id')
            ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = m.page_id')
            ->where('pf.feed_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (!isset($aListing['page_id'])) {
            return false;
        }

        return Phpfox::getService('pages')->getUrl($aListing['page_id'], $aListing['title'],
                $aListing['vanity_url']) . 'comment-id_' . $aListing['item_id'] . '/';
    }

    public function getItemName($iId, $sName)
    {
        return '<a href="' . Phpfox_Url::instance()->makeUrl('comment.view',
                array('id' => $iId)) . '">' . _p('page_group_name', array('name' => $sName)) . '</a>';
    }

    public function getCommentItem($iId)
    {
        $aRow = $this->database()->select('feed_comment_id AS comment_item_id, privacy_comment, user_id AS comment_user_id')
            ->from(Phpfox::getT('pages_feed_comment'))
            ->where('feed_comment_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        $aRow['comment_view_id'] = '0';

        if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'], $aRow['privacy_comment'])) {
            Phpfox_Error::set(_p('unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));

            unset($aRow['comment_item_id']);
        }

        $aRow['parent_module_id'] = 'pages';

        return $aRow;
    }

    public function addComment($aVals, $iUserId = null, $sUserName = null)
    {
        $aRow = $this->database()->select('fc.feed_comment_id, fc.user_id, e.page_id, e.title, u.full_name, u.gender, pu.vanity_url, e.user_id as owner')
            ->from(Phpfox::getT('pages_feed_comment'), 'fc')
            ->join(Phpfox::getT('pages'), 'e', 'e.page_id = fc.parent_user_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
            ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = e.page_id')
            ->where('fc.feed_comment_id = ' . (int)$aVals['item_id'])
            ->execute('getSlaveRow');

        // Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
        if (empty($aVals['parent_id'])) {
            $this->database()->updateCounter('pages_feed_comment', 'total_comment', 'feed_comment_id',
                $aRow['feed_comment_id']);
        }

        // Send the user an email
        $sLink = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'],
                $aRow['vanity_url']) . 'wall/comment-id_' . $aRow['feed_comment_id'] . '/';
        $sItemLink = Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);

        Phpfox::getService('comment.process')->notify(array(
                'user_id' => $aRow['owner'],
                'item_id' => $aRow['feed_comment_id'],
                'owner_subject' => _p('full_name_commented_on_a_comment_posted_on_the_page_title',
                    array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['title'])),
                'owner_message' => _p('full_name_commented_on_one_of_your_comments', array(
                    'full_name' => Phpfox::getUserBy('full_name'),
                    'item_link' => $sItemLink,
                    'title' => $aRow['title'],
                    'link' => $sLink
                )),
                'owner_notification' => 'comment.add_new_comment',
                'notify_id' => 'pages_comment_feed',
                'mass_id' => 'pages',
                'mass_subject' => (Phpfox::getUserId() == $aRow['user_id'] ? _p('full_name_commented_on_one_of_gender_page_comments',
                    array(
                        'full_name' => Phpfox::getUserBy('full_name'),
                        'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1)
                    )) : _p('full_name_commented_on_one_of_other_full_name_s_page_comments',
                    array('full_name' => Phpfox::getUserBy('full_name'), 'other_full_name' => $aRow['full_name']))),
                'mass_message' => (Phpfox::getUserId() == $aRow['user_id'] ? _p('full_name_comment_on_one_of_gender',
                    array(
                        'full_name' => Phpfox::getUserBy('full_name'),
                        'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1),
                        'item_link' => $sItemLink,
                        'title' => $aRow['title'],
                        'link' => $sLink
                    )) : _p('full_name_commented_on_one_of_other_full_name', array(
                    'full_name' => Phpfox::getUserBy('full_name'),
                    'other_full_name' => $aRow['full_name'],
                    'item_link' => $sItemLink,
                    'title' => $aRow['title'],
                    'link' => $sLink
                ))),
                'exclude_users' => [$aRow['owner']]
            )
        );
    }

    public function getNotificationComment($aNotification)
    {
        $aRow = $this->database()->select('fc.feed_comment_id, u.user_id, u.gender, u.user_name, u.full_name, e.page_id, e.title, pu.vanity_url')
            ->from(Phpfox::getT('pages_feed_comment'), 'fc')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
            ->join(Phpfox::getT('pages'), 'e', 'e.page_id = fc.parent_user_id')
            ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = e.page_id')
            ->where('fc.feed_comment_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['feed_comment_id'])) {
            return false;
        }

        if ($aNotification['item_user_id'] == $aRow['user_id'] && isset($aNotification['extra_users']) && count($aNotification['extra_users'])) {
            $sUsers = Phpfox::getService('notification')->getUsers($aNotification, true);
        } else {
            $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        }
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'],
            Phpfox::getParam('notification.total_notification_title_length'), '...');

        $sPhrase = _p('users_commented_on_the_page_title', array('users' => $sUsers, 'title' => $sTitle));

        return array(
            'link' => Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'],
                    $aRow['vanity_url']) . 'wall/comment-id_' . $aRow['feed_comment_id'] . '/',
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        );
    }

    public function getNotificationComment_Feed($aNotification)
    {
        $aRow = $this->database()->select('fc.feed_comment_id, u.user_id, u.gender, u.user_name, u.full_name, e.page_id, e.title, pu.vanity_url')
            ->from(Phpfox::getT('pages_feed_comment'), 'fc')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
            ->join(Phpfox::getT('pages'), 'e', 'e.page_id = fc.parent_user_id')
            ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = e.page_id')
            ->where('fc.feed_comment_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['feed_comment_id'])) {
            return false;
        }

        if ($aNotification['user_id'] == $aRow['user_id'] && isset($aNotification['extra_users']) && count($aNotification['extra_users'])) {
            $sUsers = Phpfox::getService('notification')->getUsers($aNotification, true);
        } else {
            $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        }
        $sGender = Phpfox::getService('user')->gender($aRow['gender'], 1);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'],
            Phpfox::getParam('notification.total_notification_title_length'), '...');

        if ($aNotification['user_id'] == $aRow['user_id']) {
            if (isset($aNotification['extra_users']) && count($aNotification['extra_users'])) {
                $sPhrase = _p('users_commented_on_span_class_drop_data_user_full_name_s_span_comment_on_the_page_title',
                    array('users' => $sUsers, 'full_name' => $aRow['full_name'], 'title' => $sTitle));
            } else {
                $sPhrase = _p('users_commented_on_gender_own_comment_on_the_page_title',
                    array('users' => $sUsers, 'gender' => $sGender, 'title' => $sTitle));
            }
        } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
            $sPhrase = _p('users_commented_on_one_of_your_comments_on_the_page_title',
                array('users' => $sUsers, 'title' => $sTitle));
        } else {
            $sPhrase = _p('users_commented_on_one_of_full_name',
                array('users' => $sUsers, 'full_name' => $aRow['full_name'], 'title' => $sTitle));
        }

        return array(
            'link' => Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'],
                    $aRow['vanity_url']) . 'wall/comment-id_' . $aRow['feed_comment_id'] . '/',
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        );
    }

    public function getNotificationComment_Link($aNotification)
    {
        $iPageId = Phpfox::getService('user')->getUser($aNotification['user_id'], 'u.profile_page_id');
        if ($aNotification['item_user_id'] == Phpfox::getService('pages')->getPageOwnerId($iPageId['profile_page_id'])) {
            return false;
        }

        $sPageTitle = db()->select('p.title')
            ->from(':link', 'l')
            ->leftJoin(':pages', 'p', 'p.page_id = l.item_id')
            ->leftJoin(':pages_url', 'pu', 'pu.page_id = l.item_id')
            ->where([
                'l.module_id' => 'pages',
                'l.link_id' => $aNotification['item_id']
            ])
            ->executeField();

        if (!$sPageTitle) {
            return false;
        }

        $sPhrase = _p('full_name_posted_a_link_in_page_title', [
            'full_name' => $aNotification['full_name'],
            'title' => $sPageTitle
        ]);

        return [
            'link' => Phpfox::getService('link')->getUrl($aNotification['item_id']),
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        ];
    }

    public function getTotalItemCount($iUserId)
    {
        return array(
            'field' => 'total_pages',
            'total' => $this->database()->select('COUNT(*)')
                ->from(Phpfox::getT('pages'), 'p')
                ->where('p.view_id = 0 AND p.user_id = ' . (int)$iUserId . ' AND p.app_id = 0 AND p.item_type = 0')
                ->execute('getSlaveField')
        );
    }

    public function globalUnionSearch($sSearch)
    {
        $this->database()->select('item.page_id AS item_id, item.title AS item_title, item.time_stamp AS item_time_stamp, item.user_id AS item_user_id, \'pages\' AS item_type_id, item.image_path AS item_photo, item.image_server_id 	 AS item_photo_server')
            ->from(Phpfox::getT('pages'), 'item')
            ->where('item.view_id = 0 AND ' . $this->database()->searchKeywords('item.title',
                    $sSearch) . ' AND item.privacy = 0 AND item.item_type = 0')
            ->union();
    }

    public function getSearchInfo($aRow)
    {
        $aPage = $this->database()->select('p.page_id, p.title, pu.vanity_url, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('pages'), 'p')
            ->join(Phpfox::getT('user'), 'u', 'u.profile_page_id = p.page_id')
            ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
            ->where('p.page_id = ' . (int)$aRow['item_id'])
            ->execute('getSlaveRow');

        $aInfo = array();
        $aInfo['item_link'] = Phpfox::getService('pages')->getUrl($aPage['page_id'], $aPage['title'],
            $aPage['vanity_url']);
        $aInfo['item_name'] = _p('page');
        $aInfo['profile_image'] = $aPage;

        return $aInfo;
    }

    public function getSearchTitleInfo()
    {
        return array(
            'name' => _p('pages')
        );
    }

    public function getNotificationApproved($aNotification)
    {
        $aRow = $this->database()->select('v.page_id, v.title, v.user_id, u.gender, u.full_name, pu.vanity_url')
            ->from(Phpfox::getT('pages'), 'v')
            ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = v.page_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.page_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['page_id'])) {
            return false;
        }

        $sPhrase = _p('your_page_has_been_approved', array(
            'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'],
                Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));

        return array(
            'link' => Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'],
                $aRow['vanity_url']),
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog'),
            'no_profile_image' => true
        );
    }

    public function addLikeComment($iItemId, $bDoNotSendEmail = false)
    {
        $aRow = $this->database()->select('fc.feed_comment_id, fc.content, fc.user_id, e.page_id, e.title')
            ->from(Phpfox::getT('pages_feed_comment'), 'fc')
            ->join(Phpfox::getT('pages'), 'e', 'e.page_id = fc.parent_user_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
            ->where('fc.feed_comment_id = ' . (int)$iItemId)
            ->execute('getSlaveRow');

        if (!isset($aRow['feed_comment_id'])) {
            return false;
        }

        $this->database()->updateCount('like', 'type_id = \'pages_comment\' AND item_id = ' . (int)$iItemId . '',
            'total_like', 'pages_feed_comment', 'feed_comment_id = ' . (int)$iItemId);

        if (!$bDoNotSendEmail) {
            $sLink = Phpfox_Url::instance()->permalink(array('pages', 'comment-id' => $aRow['feed_comment_id']),
                $aRow['page_id'], $aRow['title']);
            $sItemLink = Phpfox_Url::instance()->permalink('pages', $aRow['page_id'], $aRow['title']);

            Phpfox::getLib('mail')->to($aRow['user_id'])
                ->subject(array(
                    'pages.full_name_liked_a_comment_you_made_on_the_page_title',
                    array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['title'])
                ))
                ->message(array(
                    'pages.full_name_liked_a_comment_you_made_on_the_page_title_to_view_the_comment_thread_follow_the_link_below_a_href_link_link_a',
                    array(
                        'full_name' => Phpfox::getUserBy('full_name'),
                        'link' => $sLink,
                        'item_link' => $sItemLink,
                        'title' => $aRow['title']
                    )
                ))
                ->notification('like.new_like')
                ->send();

            Phpfox::getService('notification.process')->add('pages_comment_like', $aRow['feed_comment_id'],
                $aRow['user_id']);
        }

        return true;
    }

    //It is posting feeds for comments made in a Page of type group set to registration method "invide only", this should not happen.
    public function deleteLikeComment($iItemId)
    {
        $this->database()->updateCount('like', 'type_id = \'pages_comment\' AND item_id = ' . (int)$iItemId . '',
            'total_like', 'pages_feed_comment', 'feed_comment_id = ' . (int)$iItemId);
    }

    public function deleteComment($iId)
    {
        $this->database()->update(Phpfox::getT('pages_feed_comment'),
            array('total_comment' => array('= total_comment -', 1)), 'feed_comment_id = ' . (int)$iId);
    }

    public function updateCounterList()
    {
        $aList = array();

        $aList[] = array(
            'name' => _p('users_pages_groups_count'),
            'id' => 'pages-total'
        );

        return $aList;
    }

    public function updateCounter($iId, $iPage, $iPageLimit)
    {
        $iCnt = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('user'))
            ->execute('getSlaveField');

        $aRows = $this->database()->select('u.user_id, u.user_name, u.full_name, COUNT(b.page_id) AS total_items')
            ->from(Phpfox::getT('user'), 'u')
            ->leftJoin(Phpfox::getT('pages'), 'b', 'b.user_id = u.user_id AND b.view_id = 0 AND b.app_id = 0')
            ->limit($iPage, $iPageLimit, $iCnt)
            ->group('u.user_id')
            ->execute('getSlaveRows');

        foreach ($aRows as $aRow) {
            $this->database()->update(Phpfox::getT('user_field'), array('total_pages' => $aRow['total_items']),
                'user_id = ' . $aRow['user_id']);
        }

        return $iCnt;
    }

    public function getNotificationComment_Like($aNotification)
    {
        $aRow = $this->database()->select('fc.feed_comment_id, u.user_id, u.gender, u.user_name, u.full_name, e.page_id, e.title')
            ->from(Phpfox::getT('pages_feed_comment'), 'fc')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = fc.user_id')
            ->join(Phpfox::getT('pages'), 'e', 'e.page_id = fc.parent_user_id')
            ->where('fc.feed_comment_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'],
            Phpfox::getParam('notification.total_notification_title_length'), '...');

        if ($aNotification['user_id'] == $aRow['user_id']) {
            if (isset($aNotification['extra_users']) && count($aNotification['extra_users'])) {
                $sPhrase = _p('users_liked_span_class_drop_data_user_row_full_name_s_span_comment_on_the_page_title',
                    array(
                        'users' => Phpfox::getService('notification')->getUsers($aNotification, true),
                        'row_full_name' => $aRow['full_name'],
                        'title' => $sTitle
                    ));
            } else {
                $sPhrase = _p('users_liked_gender_own_comment_on_the_page_title', array(
                    'users' => $sUsers,
                    'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1),
                    'title' => $sTitle
                ));
            }
        } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
            $sPhrase = _p('users_liked_one_of_your_comments_on_the_page_title',
                array('users' => $sUsers, 'title' => $sTitle));
        } else {
            $sPhrase = _p('users_liked_one_on_span_class_drop_data_user_row_full_name_s_span_comments_on_the_page_title',
                array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
        }

        return array(
            'link' => Phpfox_Url::instance()->permalink(array('pages', 'comment-id' => $aRow['feed_comment_id']),
                $aRow['page_id'], $aRow['title']),
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        );
    }

    public function getNotificationInvite_Admin($aNoti)
    {
        $aRow = $this->database()->select('v.page_id, v.title, v.user_id, u.gender, u.full_name, pu.vanity_url')
            ->from(Phpfox::getT('pages'), 'v')
            ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = v.page_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.page_id = ' . (int)$aNoti['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['page_id'])) {
            return false;
        }

        $sPhrase = _p('you_have_been_invited_to_become_an_admin_of_page', ['page_name' => $aRow['title']]);

        return [
            'link' => Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']),
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        ];
    }

    public function getDetailOnThemeUpdate($iGroup)
    {
        if (!$iGroup) {
            return false;
        }

        $aGroup = $this->database()->select('*')
            ->from(Phpfox::getT('pages'))
            ->where('page_id = ' . (int)$iGroup . '')
            ->execute('getSlaveRow');

        if (!isset($aGroup['page_id'])) {
            return false;
        }

        if (Phpfox::getService('pages')->isAdmin($aGroup)) {
            return array(
                'table' => 'pages',
                'field' => 'designer_style_id',
                'action' => 'page_id',
                'value' => $aGroup['page_id'],
                'javascript' => '$(\'.style_submit_box_theme\').hide(); $(\'.style_box\').removeClass(\'style_box_active\'); $(\'.style_box\').each(function(){ if($(this).hasClass(\'style_box_test\')) $(this).removeClass(\'style_box_test\').addClass(\'style_box_active\');  {} });'
            );
        }

        return false;
    }

    public function getDetailOnOrderUpdate($aVals)
    {
        if (!isset($aVals['param']['item_id'])) {
            return false;
        }

        $aGroup = $this->database()->select('*')
            ->from(Phpfox::getT('pages'))
            ->where('page_id = ' . (int)$aVals['param']['item_id'] . '')
            ->execute('getSlaveRow');

        if (!isset($aGroup['page_id'])) {
            return false;
        }

        return false;
    }

    public function getDetailOnBlockUpdate($aVals)
    {
        if (!isset($aVals['item_id'])) {
            return false;
        }

        $aGroup = $this->database()->select('*')
            ->from(Phpfox::getT('pages'))
            ->where('page_id = ' . (int)$aVals['item_id'] . '')
            ->execute('getSlaveRow');

        if (!isset($aGroup['page_id'])) {
            return false;
        }

        return false;
    }

    /* Used to get a page when there is no certainty of the module */
    public function getItem($iId)
    {
        Phpfox::getService('pages')->setIsInPage();
        $aItem = $this->database()->select('*')->from(Phpfox::getT('pages'))->where('item_type = 0 AND page_id = ' . (int)$iId)->execute('getSlaveRow');
        if (empty($aItem)) {
            return false;
        }
        $aItem['module'] = 'pages';
        $aItem['module_title'] = _p('pages');
        $aItem['item_id'] = $iId;

        return $aItem;
    }

    /**
     * @param $iUser
     * @throws \Exception
     */
    public function onDeleteUser($iUser)
    {
        $aRows = $this->database()->select('*')
            ->from(Phpfox::getT('pages'))
            ->where('user_id = ' . (int)$iUser . ' AND item_type = 0')
            ->execute('getSlaveRows');

        foreach ($aRows as $aRow) {
            Phpfox::getService('pages.process')->delete($aRow['page_id'], true, true);
        }
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('pages.service_callback__call')) {
            eval($sPlugin);

            return;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

    public function checkPermission($iId, $sName)
    {
        return Phpfox::getService('pages')->hasPerm($iId, $sName);
    }

    public function getReportRedirect($iId)
    {
        return Phpfox::getService('pages')->getUrl($iId);
    }

    /**
     * @discussion: callback to check permission to get feeds of a page
     * @param $iId
     *
     * @return bool
     */
    public function canGetFeeds($iId)
    {
        $aPage = Phpfox::getService('pages')->getPage($iId);
        if (!$aPage || empty($aPage['page_id'])) {
            return false;
        }

        return Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'pages.view_browse_updates');
    }

    /**
     * @description: return callback param for adding feed comment on page
     * @param $iId
     * @param $aVals
     *
     * @return array|bool
     */
    public function getFeedComment($iId, $aVals)
    {
        //check permission
        Phpfox::isUser(true);

        $bPostAsPage = Phpfox_Request::instance()->get('custom_pages_post_as_page', 0);

        if ($bPostAsPage && $bPostAsPage != $iId) {
            Phpfox_Error::set(_p('Cannot post as page on others pages.'));

            return false;
        }

        if (!$bPostAsPage && !(Phpfox::getService('pages')->hasPerm($iId, 'pages.share_updates'))) {
            return false;
        }

        //validate data
        if (Phpfox::getLib('parse.format')->isEmpty($aVals['user_status'])) {
            Phpfox_Error::set(_p('add_some_text_to_share'));

            return false;
        }

        $aPage = Phpfox::getService('pages')->getPage($iId);

        //check exists page
        if (!isset($aPage['page_id'])) {
            Phpfox_Error::set(_p('unable_to_find_the_page_you_are_trying_to_comment_on'));

            return false;
        }

        $sLink = Phpfox::getService('pages')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']);
        $aCallback = array(
            'module' => 'pages',
            'table_prefix' => 'pages_',
            'link' => $sLink,
            'email_user_id' => $aPage['user_id'],
            'subject' => _p('full_name_wrote_a_comment_on_your_page_title',
                array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aPage['title'])),
            'message' => _p('full_name_wrote_a_comment_link',
                array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aPage['title'])),
            'notification' => ($bPostAsPage ? null : 'pages_comment'),
            'feed_id' => 'pages_comment',
            'item_id' => $aPage['page_id'],
            'add_tag' => true
        );

        return $aCallback;
    }

    /**
     * @description: callback after a comment feed added on page
     * @param $iPageId
     */
    public function onAddFeedCommentAfter($iPageId)
    {
        Phpfox_Database::instance()->updateCounter('pages', 'total_comment', 'page_id', $iPageId);
    }

    /**
     * @description: check permission when add like for pages
     * @param $iId
     *
     * @return bool
     */
    public function canLikeItem($iId)
    {
        $aItem = Phpfox::getService('pages')->getForView($iId);
        if (empty($aItem) || empty($aItem['page_id'])) {
            return false;
        }
        if (($aItem['page_type'] == '1') && ($aItem['reg_method'] == 2 || $aItem['reg_method'] == 1)) {
            return false;
        }

        return true;
    }

    public function canShareOnMainFeed($iPageId, $sPerm, $bChildren)
    {
        return Phpfox::getService('pages')->hasPerm($iPageId, $sPerm);
    }

    /**
     * Login as pages comment
     *
     * @return string
     */
    public function getCommentItemName()
    {
        return 'pages';
    }


    /**
     * Check admin of page
     * @param $iPageId
     * @return bool
     * @throws \Exception
     */
    public function isAdmin($iPageId)
    {
        $aErrors = Phpfox_Error::get();
        $bIsAdmin = Phpfox::getService('pages')->isAdmin($iPageId);
        Phpfox_Error::reset();
        foreach ($aErrors as $sError) {
            Phpfox_Error::set($sError);
        }

        return $bIsAdmin;
    }

    /**
     * This function will add number of pending page to admin dashboard statistics
     * @return array
     */
    public function pendingApproval()
    {
        return array(
            'phrase' => _p('pages_app'),
            'value' => Phpfox::getService('pages')->getPendingTotal(),
            'link' => Phpfox_Url::instance()->makeUrl('pages', array('view' => 'pending'))
        );
    }

    public function getAdmincpAlertItems()
    {
        $iTotalPending = Phpfox::getService('pages')->getPendingTotal();
        return [
            'message'=> _p('you_have_total_pending_pages', ['total'=>$iTotalPending]),
            'value' => $iTotalPending,
            'link' => Phpfox_Url::instance()->makeUrl('pages', array('view' => 'pending'))
        ];
    }

    public function getNotificationDeny_Claim($aNotification)
    {
        $aPage = $this->getFacade()->getItems()->getPage($aNotification['item_id']);
        $sPhrase = _p('your_claim_has_been_denied',
            ['moderator' => $aNotification['full_name'], 'page' => $aPage['title']]);

        return [
            'link' => Phpfox::getService('pages')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']),
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        ];
    }

    public function getNotificationApprove_Claim($aNotification)
    {
        $aPage = $this->getFacade()->getItems()->getPage($aNotification['item_id']);
        $sPhrase = _p('your_claim_has_been_approved',
            ['moderator' => $aNotification['full_name'], 'page' => $aPage['title']]);

        return [
            'link' => Phpfox::getService('pages')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']),
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        ];
    }

    public function getNotificationRemove_Owner($aNotification)
    {
        $aPage = $this->getFacade()->getItems()->getPage($aNotification['item_id']);
        $sPhrase = _p('you_has_been_removed_as_owner_of_page',
            ['page' => $aPage['title'], 'moderator' => $aNotification['full_name']]);

        return [
            'link' => Phpfox::getService('pages')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']),
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        ];
    }

    public function getNotificationPost_Image($aNotification)
    {
        // get pages from photo id
        $aPhoto = Phpfox::getService('photo')->getPhotoItem($aNotification['item_id']);
        if (!$aPhoto) {
            return false;
        }

        $aPage = $this->getFacade()->getItems()->getPage($aPhoto['group_id']);
        if (!$aPage) {
            return false;
        }

        if (!empty($aPage['user_id']) && $aPage['user_id'] == Phpfox::getUserId()) {
            // notification of owner
            $sPhrase = _p('full_name_post_some_images_on_your_page_title', [
                'full_name' => $aNotification['full_name'],
                'title' => $aPage['title']
            ]);
        } else {
            // notification of admin
            $sPhrase = _p('full_name_post_some_images_on_page_title', [
                'full_name' => $aNotification['full_name'],
                'title' => $aPage['title']
            ]);
        }

        return [
            'link' => Phpfox::getService('photo.callback')->getLink(['item_id' => $aPhoto['photo_id']]),
            'message' => $sPhrase,
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        ];
    }

    /**
     * Check if need to hide profile photos or cover photos
     * @param $iPageId
     * @return array
     */
    public function getHiddenAlbums($iPageId)
    {
        $aHiddenAlbums = [];
        if (!Phpfox::getParam('pages.display_pages_profile_photo_within_gallery', true)) {
            list($iCnt, $aProfileAlbums) = Phpfox::getService('photo.album')->get([
                'pa.module_id = \'pages\'',
                'AND pa.group_id = ' . $iPageId,
                'AND pa.profile_id != 0'
            ]);
            $iCnt && ($aHiddenAlbums[] = $aProfileAlbums[0]['album_id']);
        }
        if (!Phpfox::getParam('pages.display_pages_cover_photo_within_gallery', true)) {
            list($iCnt, $aCoverAlbums) = Phpfox::getService('photo.album')->get([
                'pa.module_id = \'pages\'',
                'AND pa.group_id = ' . $iPageId,
                'AND pa.cover_id != 0'
            ]);
            $iCnt && ($aHiddenAlbums[] = $aCoverAlbums[0]['album_id']);
        }

        return $aHiddenAlbums;
    }

    /**
     * @deprecated this function will be removed in 4.6.0
     * @param $aNotification
     * @return array|bool
     */
    public function getNotificationJoined($aNotification)
    {
        $aRow = Phpfox::getService('pages')->getPage($aNotification['item_id']);

        if (!isset($aRow['page_id'])) {
            return false;
        }

        return array(
            'link' => Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']),
            'message' => _p('your_membership_has_been_accepted_to_join_the_page_title', array(
                'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'],
                    Phpfox::getParam('notification.total_notification_title_length'), '...')
            )),
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        );
    }

    /**
     * @deprecated this function will be removed in 4.6.
     * @param $aNotification
     * @return array|bool
     */
    public function getNotificationRegister($aNotification)
    {
        $aRow = $this->database()->select('p.*, pu.vanity_url, ' . Phpfox::getUserField())
            ->from(Phpfox::getT('pages_signup'), 'ps')
            ->join(Phpfox::getT('pages'), 'p', 'p.page_id = ps.page_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = ps.user_id')
            ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
            ->where('ps.signup_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        if (!isset($aRow['page_id'])) {
            return false;
        }

        return array(
            // 'no_profile_image' => true, 
            'link' => Phpfox::getService('pages')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']),
            'message' => _p('full_name_is_requesting_to_join_your_page_title', array(
                'full_name' => $aRow['full_name'],
                'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'],
                    Phpfox::getParam('notification.total_notification_title_length'), '...')
            )),
            'icon' => Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        );
    }

    /**
     * This callback will be call if a photo of module pages deleted
     * @param $aPhoto
     */
    public function onDeletePhoto($aPhoto)
    {
        $iCoverId = db()->select('cover_photo_id')->from(':pages')->where(['page_id' => $aPhoto['group_id']])->executeField();

        if ($iCoverId == $aPhoto['photo_id']) {
            db()->update(':pages', ['cover_photo_id' => null], ['page_id' => $aPhoto['group_id']]);
        }
    }

    /**
     * @return array
     */
    public function getUploadParams()
    {
        return Phpfox::getService('pages')->getUploadPhotoParams();
    }

    public function getActivityFeedPhoto($aItem, $aCallback = null, $bIsChildItem = false)
    {
        $sSelect = 'p.*';
        if (Phpfox::isModule('like')) {
            $sSelect .= ', count(l.like_id) as total_like';
            $this->database()->leftJoin(Phpfox::getT('like'), 'l',
                'l.type_id = \'pages_photo\' AND l.item_id = p.photo_id');

            //todo incompatible with default group by
            $this->database()->group('p.photo_id');

            $sSelect .= ', l2.like_id AS is_liked';
            $this->database()->leftJoin(Phpfox::getT('like'), 'l2',
                'l2.type_id = \'pages_photo\' AND l2.item_id = p.photo_id AND l2.user_id = ' . Phpfox::getUserId());
        }
        $aRow = $this->database()->select($sSelect . ' , p.destination, u.server_id')
            ->from(Phpfox::getT('photo'), 'p')
            ->join(':user', 'u', 'u.user_id=p.user_id')
            ->where([
                'p.photo_id' => (int)$aItem['item_id'],
                'p.is_profile_photo' => 1
            ])->execute('getSlaveRow');

        if (empty($aRow)) {
            return false;
        }

        $sImage = Phpfox::getLib('image.helper')->display(array(
            'server_id' => $aRow['server_id'],
            'path' => 'photo.url_photo',
            'file' => $aRow['destination'],
            'suffix' => '_500',
            'class' => 'photo_holder',
            'defer' => true
        ));
        $aReturn = [
            'feed_title' => '',
            'feed_info' => _p('updated_profile_photo'),
            'feed_link' => Phpfox_Url::instance()->permalink('photo', $aRow['photo_id'], $aRow['title']),
            'feed_image' => $sImage,
            'feed_icon' => Phpfox::getLib('image.helper')->display(array(
                'theme' => 'misc/report_user.png',
                'return_url' => true
            )),
            'time_stamp' => $aItem['time_stamp'],
            'feed_total_like' => $aRow['total_like'],
            'like_type_id' => 'pages_photo',
            'enable_like' => true,
            'feed_is_liked' => isset($aRow['is_liked']) ? $aRow['is_liked'] : false,
        ];

        if ($bIsChildItem) {
            $aReturn = array_merge($aReturn, $aItem);
        }

        return $aReturn;
    }

    public function addLikePhoto($iItemId, $bDoNotSendEmail = false)
    {
        $this->database()->updateCount('like', 'type_id = \'pages_photo\' AND item_id = ' . (int)$iItemId . '',
            'total_like', 'photo', 'photo_id = ' . (int)$iItemId);

        return true;
    }

    public function deleteLikePhoto($iItemId, $bDoNotSendEmail = false)
    {
        $this->database()->updateCount('like', 'type_id = \'pages_photo\' AND item_id = ' . (int)$iItemId . '',
            'total_like', 'photo', 'photo_id = ' . (int)$iItemId);

        return true;
    }

    public function getActivityFeedCover_Photo($aItem, $aCallback = null, $bIsChildItem = false)
    {
        $sSelect = 'p.*';
        if (Phpfox::isModule('like')) {
            $sSelect .= ', count(l.like_id) as total_like';
            $this->database()->leftJoin(Phpfox::getT('like'), 'l',
                'l.type_id = \'pages_cover_photo\' AND l.item_id = p.photo_id');

            $this->database()->group('p.photo_id');

            $sSelect .= ', l2.like_id AS is_liked';
            $this->database()->leftJoin(Phpfox::getT('like'), 'l2',
                'l2.type_id = \'pages_cover_photo\' AND l2.item_id = p.photo_id AND l2.user_id = ' . Phpfox::getUserId());
        }
        $aRow = $this->database()->select($sSelect . ' , p.destination, u.server_id')
            ->from(Phpfox::getT('photo'), 'p')
            ->join(':user', 'u', 'u.user_id=p.user_id')
            ->where([
                'p.photo_id' => (int)$aItem['item_id']
            ])->execute('getSlaveRow');

        if (empty($aRow)) {
            return false;
        }

        $sImage = Phpfox::getLib('image.helper')->display(array(
            'server_id' => $aRow['server_id'],
            'path' => 'photo.url_photo',
            'file' => $aRow['destination'],
            'suffix' => '_500',
            'class' => 'photo_holder',
            'defer' => true
        ));
        $aReturn = [
            'feed_title' => '',
            'feed_info' => _p('updated_cover_photo'),
            'feed_link' => Phpfox_Url::instance()->permalink('photo', $aRow['photo_id'], $aRow['title']),
            'feed_image' => $sImage,
            'feed_icon' => Phpfox::getLib('image.helper')->display(array(
                'theme' => 'misc/report_user.png',
                'return_url' => true
            )),
            'time_stamp' => $aItem['time_stamp'],
            'feed_total_like' => $aRow['total_like'],
            'like_type_id' => 'pages_cover_photo',
            'enable_like' => true,
            'feed_is_liked' => isset($aRow['is_liked']) ? $aRow['is_liked'] : false,
        ];

        if ($bIsChildItem) {
            $aReturn = array_merge($aReturn, $aItem);
        }

        return $aReturn;
    }

    public function addLikeCover_Photo($iItemId, $bDoNotSendEmail = false)
    {
        $this->database()->updateCount('like', 'type_id = \'pages_cover_photo\' AND item_id = ' . (int)$iItemId . '',
            'total_like', 'photo', 'photo_id = ' . (int)$iItemId);

        return true;
    }

    public function deleteLikeCover_Photo($iItemId, $bDoNotSendEmail = false)
    {
        $this->database()->updateCount('like', 'type_id = \'pages_cover_photo\' AND item_id = ' . (int)$iItemId . '',
            'total_like', 'photo', 'photo_id = ' . (int)$iItemId);

        return true;
    }

    /**
     * Get statistic for each user
     *
     * @param $iUserId
     * @return array|bool
     */
    public function getUserStatsForAdmin($iUserId)
    {
        if (!$iUserId) {
            return false;
        }

        $iTotalPages = db()->select('COUNT(*)')->from(':pages')->where(['user_id' => $iUserId, 'item_type' => 0])->executeField();

        return [
            'total_name' => _p('pages'),
            'total_value' => $iTotalPages,
            'type' => 'item'
        ];
    }
}
