<?php

namespace Apps\Phpfox_Videos\Service;

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Module;
use Phpfox_Plugin;
use Phpfox_Service;
use Phpfox_Url;

class Callback extends Phpfox_Service
{

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('video');
    }

    /**
     * @return bool
     */
    public function canShareItemOnFeed()
    {
        return true;
    }

    /**
     * @param $aParams
     * @return bool|mixed
     */
    public function enableSponsor($aParams)
    {
        return Phpfox::getService('v.process')->sponsor((int)$aParams['item_id'], 1);
    }

    /**
     * Returns information related to a video for sponsoring purposes
     * @param int $iId video_id
     * @return array in the format:
     * array(
     *    'title' => 'item title',            <-- required
     *    'link'  => 'makeUrl()'ed link',            <-- required
     *    'paypal_msg' => 'message for paypal'        <-- required
     *    'item_id' => int                <-- required
     *    'error' => 'phrase if item doesnt exit'        <-- optional
     *    'extra' => 'description'            <-- optional
     *    'image' => 'path to an image',            <-- optional
     *    'image_dir' => 'photo.url_photo|...        <-- optional (required if image)
     *    'server_id' => 'value from DB'            <-- optional (required if image)
     * );
     */
    public function getToSponsorInfo($iId)
    {
        $aVideo = $this->database()->select('v.user_id, v.title, v.video_id as item_id, vt.text_parsed as extra,
		       v.image_path, v.image_server_id')
            ->from(Phpfox::getT('video'), 'v')
            ->leftjoin(Phpfox::getT('video_text'), 'vt', 'vt.video_id = v.video_id')
            ->where('v.video_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (empty($aVideo)) {
            return array('error' => _p('video_sponsor_error_not_found'));
        }

        $aVideo['link'] = Phpfox::permalink('video.play', $aVideo['item_id'], $aVideo['title']);
        $aVideo['paypal_msg'] = _p('video_sponsor_paypal_message',
            array('sVideoTitle' => $aVideo['title']));//'Video Sponsor ' . $aVideo['title'];
        $aVideo['title'] = _p('video_sponsor_title', array('sVideoTitle' => $aVideo['title']));
        $aVideo['image_dir'] = 'core.url_pic';
        Phpfox::getService('v.video')->convertImagePath($aVideo);
        $aVideo['full_image_path'] = $aVideo['image_path'];
        $aVideo['message_completed'] = _p('purchase_video_sponsor_completed');
        $aVideo['message_pending_approval'] = _p('purchase_video_sponsor_pending_approval');
        $aVideo['redirect_completed'] = 'video';
        $aVideo['redirect_pending_approval'] = 'video';

        return $aVideo;
    }

    /**
     * @param $aParams
     * @return bool|string
     */
    public function getLink($aParams)
    {
        $aVideo = $this->database()->select('v.video_id, v.title')
            ->from(Phpfox::getT('video'), 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.video_id = ' . (int)$aParams['item_id'])
            ->execute('getSlaveRow');

        if (empty($aVideo)) {
            return false;
        }

        $sLink = Phpfox::permalink('video.play', (int)$aParams['item_id'], $aVideo['title']);

        return $sLink;
    }

    /**
     * @return array
     */
    public function getDashboardActivity()
    {
        $aUser = Phpfox::getService('user')->get(Phpfox::getUserId(), true);

        return array(
            _p('videos') => $aUser['activity_v']
        );
    }

    /**
     * @param int $iStartTime
     * @param int $iEndTime
     *
     * @return array
     */
    public function getSiteStatsForAdmin($iStartTime, $iEndTime)
    {
        $aCond = [];
        $aCond[] = 'view_id = 0 AND in_process = 0';
        if ($iStartTime > 0) {
            $aCond[] = 'AND time_stamp >= \'' . db()->escape($iStartTime) . '\'';
        }
        if ($iEndTime > 0) {
            $aCond[] = 'AND time_stamp <= \'' . db()->escape($iEndTime) . '\'';
        }

        $iCnt = (int)db()->select('COUNT(video_id)')
            ->from($this->_sTable)
            ->where($aCond)
            ->execute('getSlaveField');

        return [
            'phrase' => 'videos',
            'total' => $iCnt
        ];
    }

    /**
     * @return array
     */
    public function getSiteStatsForAdmins()
    {
        $iToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

        return array(
            'phrase' => _p('videos'),
            'value' => $this->database()->select('COUNT(*)')
                ->from($this->_sTable)
                ->where('view_id = 0 AND in_process = 0 AND time_stamp >= ' . $iToday)
                ->execute('getSlaveField')
        );
    }

    /**
     * @param $aItem
     * @param null $aCallback
     * @param bool $bIsChildItem
     * @return mixed
     */
    public function getActivityFeed($aItem, $aCallback = null, $bIsChildItem = false)
    {
        if (!user('pf_video_view')) {
            return false;
        }

        if (Phpfox::isUser() && Phpfox::isModule('like')) {
            db()->select('l.like_id AS is_liked, ')
                ->leftJoin(Phpfox::getT('like'), 'l',
                    'l.type_id = \'v\' AND l.item_id = v.video_id AND l.user_id = ' . Phpfox::getUserId());
        }

        if ($aCallback === null) {
            db()->select(Phpfox::getUserField('u', 'parent_') . ', ')->leftJoin(Phpfox::getT('user'), 'u',
                'u.user_id = v.parent_user_id');
        }

        if ($bIsChildItem) {
            db()->select(Phpfox::getUserField('u2') . ', ')->join(Phpfox::getT('user'), 'u2',
                'u2.user_id = v.user_id');
        }

        $aRow = db()->select('v.location_latlng, v.location_name, v.user_id, v.video_id, v.title, v.status_info, v.time_stamp, v.total_comment, v.total_view as video_total_view, v.privacy, v.total_like, v.module_id, v.item_id, v.image_path, v.image_server_id, v.is_stream, v.server_id, v.destination, vt.text_parsed as text, ve.video_url, ve.embed_code')
            ->from(Phpfox::getT('video'), 'v')
            ->leftJoin(Phpfox::getT('video_text'), 'vt', 'vt.video_id = v.video_id')
            ->leftJoin(Phpfox::getT('video_embed'), 've', 've.video_id = v.video_id')
            ->where('v.video_id = ' . (int)$aItem['item_id'])
            ->execute('getSlaveRow');
        if (!isset($aRow['video_id'])) {
            return false;
        }

        /**
         * Check active parent module
         */
        if (isset($aRow['module_id']) && $aRow['module_id'] && $aRow['module_id'] != 'video') {
            try {
                Phpfox_Module::instance()->get($aRow['module_id']);
            } catch (\Exception $e) {
                return false;
            }
        }

        if ($bIsChildItem) {
            $aItem = array_merge($aRow, $aItem);
        }

        if ((defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE') && !Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->hasPerm(null,
                    'pf_video.view_browse_videos'))
            || (!defined('PHPFOX_IS_PAGES_VIEW') && $aRow['module_id'] == 'pages' && Phpfox::isModule('pages') && !Phpfox::getService('pages')->hasPerm($aRow['item_id'],
                    'pf_video.view_browse_videos'))
            || ($aRow['module_id'] && Phpfox::isModule($aRow['module_id']) && Phpfox::hasCallback($aRow['module_id'],
                    'canShareOnMainFeed') && !Phpfox::callback($aRow['module_id'] . '.canShareOnMainFeed',
                    $aRow['item_id'], 'pf_video.view_browse_videos', $bIsChildItem))
        ) {
            return false;
        }

        (($sPlugin = Phpfox_Plugin::get('video.component_service_callback_getactivityfeed__1')) ? eval($sPlugin) : false);

        $aRow['is_in_feed'] = true;

        Phpfox::getLib('template')->assign('aVideo', $aRow);
        Phpfox_Component::setPublicParam('custom_param_' . $aItem['feed_id'], $aRow);
        $aRow = Phpfox::getService('v.video')->compileVideo($aRow);

        $aReturn = array(
            'feed_title' => $aRow['title'],
            'privacy' => $aRow['privacy'],
            'feed_status' => $aRow['status_info'],
            'feed_info' => _p('shared_a_video'),
            'feed_link' => Phpfox::permalink('video.play', $aRow['video_id'], $aRow['title']),
            'total_comment' => $aRow['total_comment'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => (isset($aRow['is_liked']) ? $aRow['is_liked'] : false),
            'feed_icon' => Phpfox::getLib('image.helper')->display(array(
                'theme' => 'module/video.png',
                'return_url' => true
            )),
            'time_stamp' => $aRow['time_stamp'],
            'enable_like' => true,
            'comment_type_id' => 'v',
            'like_type_id' => 'v',
            'custom_data_cache' => $aRow,
            'feed_content' => $aRow['text'],
            'video_total_view' => $aRow['video_total_view']
        );

        if (!empty($aRow['parent_user_id'])) {
            unset($aReturn['feed_info']);
        }
        if (!empty($aRow['location_name'])) {
            $aReturn['location_name'] = $aRow['location_name'];
        }
        if (!empty($aRow['location_latlng'])) {
            $aReturn['location_latlng'] = json_decode($aRow['location_latlng'], true);
        }

        if ($aCallback === null) {
            if (!PHPFOX_IS_AJAX && defined('PHPFOX_IS_USER_PROFILE') && !empty($aRow['parent_user_name']) && $aRow['parent_user_id'] != Phpfox::getService('profile')->getProfileUserId()) {
                $aReturn['feed_mini'] = true;
                $aReturn['feed_mini_content'] = _p('feed.full_name_posted_a_href_link_a_video_a_on_a_href_profile_parent_full_name_a_s_a_href_profile_link_wall_a',
                    array(
                        'full_name' => Phpfox::getService('user')->getFirstName($aItem['full_name']),
                        'link' => Phpfox::permalink('video', $aRow['video_id'], $aRow['title']),
                        'profile' => Phpfox::getLib('url')->makeUrl($aRow['parent_user_name']),
                        'parent_full_name' => $aRow['parent_full_name'],
                        'profile_link' => Phpfox::getLib('url')->makeUrl($aRow['parent_user_name'])
                    ));
                $aReturn['feed_title'] = '';
                unset($aReturn['feed_status'], $aReturn['feed_image'], $aReturn['feed_content']);
            }
        }

        $aReturn['type_id'] = 'v';
        if ($bIsChildItem) {
            $aReturn = array_merge($aReturn, $aItem);
        }

        if (!PHPFOX_IS_AJAX && defined('PHPFOX_IS_USER_PROFILE') && !empty($aRow['parent_user_name']) && $aRow['parent_user_id'] != Phpfox::getService('profile')->getProfileUserId()) {
        } else {
            $aReturn['load_block'] = 'v.feed_video';
            $aReturn['is_stream'] = isset($aRow['is_stream']) ? $aRow['is_stream'] : 0;
            $aReturn['embed_code'] = isset($aRow['embed_code']) ? $aRow['embed_code'] : "";
        }

        if (!defined('PHPFOX_IS_PAGES_VIEW') && (($aRow['module_id'] == 'groups' && Phpfox::isModule('groups')) || ($aRow['module_id'] == 'pages' && Phpfox::isModule('pages')))) {
            $aPage = db()->select('p.*, pu.vanity_url, ' . Phpfox::getUserField('u', 'parent_'))
                ->from(':pages', 'p')
                ->join(':user', 'u', 'p.page_id=u.profile_page_id')
                ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
                ->where('p.page_id=' . (int)$aRow['item_id'])
                ->execute('getSlaveRow');

            $aReturn['parent_user_name'] = Phpfox::getService($aRow['module_id'])->getUrl($aPage['page_id'],
                $aPage['title'], $aPage['vanity_url']);
            $aReturn['feed_table_prefix'] = 'pages_';
            if ($aRow['user_id'] != $aPage['parent_user_id']) {
                $aReturn['parent_user'] = Phpfox::getService('user')->getUserFields(true, $aPage, 'parent_');
                unset($aReturn['feed_info']);
            }
        }

        return $aReturn;
    }

    /**
     * @param $iItemId
     * @param bool $bDoNotSendEmail
     * @return bool|null
     */
    public function addLike($iItemId, $bDoNotSendEmail = false)
    {
        $aRow = db()->select('video_id, title, user_id, module_id, item_id')
            ->from($this->_sTable)
            ->where('video_id = ' . (int)$iItemId)
            ->execute('getSlaveRow');

        if (!isset($aRow['video_id'])) {
            return false;
        }
        db()->updateCount('like', 'type_id = \'v\' AND item_id = ' . (int)$iItemId, 'total_like', 'video',
            'video_id = ' . (int)$iItemId);
        if (!$bDoNotSendEmail) {
            $sLink = Phpfox::permalink('video.play', $aRow['video_id'], $aRow['title']);

            Phpfox::getLib('mail')->to($aRow['user_id'])
                ->subject(array(
                    'full_name_liked_your_video_title',
                    array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['title'])
                ))
                ->message(array(
                    'full_name_liked_your_video_message',
                    array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aRow['title'])
                ))
                ->notification('like.new_like')
                ->send();

            Phpfox::getService('notification.process')->add('v_like', $aRow['video_id'], $aRow['user_id']);

            if ($aRow['module_id'] == 'user') {
                Phpfox::getLib('mail')->to($aRow['item_id'])
                    ->subject(array(
                        'full_name_liked_a_video_title_on_your_wall',
                        array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aRow['title'])
                    ))
                    ->message(array(
                        'full_name_liked_a_video_title_on_your_wall_message',
                        array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'link' => $sLink,
                            'title' => $aRow['title']
                        )
                    ))
                    ->notification('like.new_like')
                    ->send();

                Phpfox::getService('notification.process')->add('v_like', $aRow['video_id'], $aRow['item_id']);
            }
        }

        return null;
    }

    /**
     * @param int $iItemId
     */
    public function deleteLike($iItemId)
    {
        db()->updateCount('like', 'type_id = \'video\' AND item_id = ' . (int)$iItemId . '', 'total_like',
            'video', 'video_id = ' . (int)$iItemId);
    }

    /**
     * @param $aRow
     * @return mixed
     */
    public function getNewsFeed($aRow)
    {
        (($sPlugin = Phpfox_Plugin::get('video.component_service_callback_getnewsfeed__start')) ? eval($sPlugin) : false);

        $oUrl = Phpfox::getLib('url');

        $aRow['text'] = _p('owner_full_name_added_a_new_video_title',
            array(
                'owner_full_name' => $aRow['owner_full_name'],
                'title' => Phpfox::getService('feed')->shortenTitle($aRow['content']),
                'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                'title_link' => $aRow['link']
            )
        );

        $aRow['icon'] = 'module/video.png';
        $aRow['enable_like'] = true;
        $aRow['comment_type_id'] = 'video';

        (($sPlugin = Phpfox_Plugin::get('video.component_service_callback_getnewsfeed__end')) ? eval($sPlugin) : false);

        return $aRow;
    }

    /**
     * @param $aRow
     * @return mixed
     */
    public function getCommentNewsFeed($aRow)
    {
        (($sPlugin = Phpfox_Plugin::get('video.component_service_callback_getcommentnewsfeed__start')) ? eval($sPlugin) : false);
        $oUrl = Phpfox::getLib('url');

        if ($aRow['owner_user_id'] == $aRow['item_user_id']) {
            $aRow['text'] = _p('user_added_a_new_comment_on_their_own_video', array(
                    'user_name' => $aRow['owner_full_name'],
                    'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                    'title_link' => $aRow['link']
                )
            );
        } elseif ($aRow['item_user_id'] == Phpfox::getUserBy('user_id')) {
            $aRow['text'] = _p('user_added_a_new_comment_on_your_video', array(
                    'user_name' => $aRow['owner_full_name'],
                    'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                    'title_link' => $aRow['link']
                )
            );
        } else {
            $aRow['text'] = _p('user_name_added_a_new_comment_on_item_user_name_video', array(
                    'user_name' => $aRow['owner_full_name'],
                    'user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['user_id'])),
                    'title_link' => $aRow['link'],
                    'item_user_name' => $aRow['viewer_full_name'],
                    'item_user_link' => $oUrl->makeUrl('feed.user', array('id' => $aRow['viewer_user_id']))
                )
            );
        }

        $aRow['text'] .= Phpfox::getService('feed')->quote($aRow['content']);
        (($sPlugin = Phpfox_Plugin::get('video.component_service_callback_getcommentnewsfeed__end')) ? eval($sPlugin) : false);

        return $aRow;
    }

    /**
     * @param $aNotification
     * @return array|bool
     */
    public function getCommentNotification($aNotification)
    {
        $aRow = Phpfox::getService('v.video')->getInfoForNotification((int)$aNotification['item_id']);

        if (!isset($aRow['video_id'])) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'],
            setting('notification.total_notification_title_length'), '...');

        if ($aNotification['user_id'] == $aRow['user_id'] && !isset($aNotification['extra_users'])) {
            $sPhrase = _p('users_commented_on_gender_video_title', array(
                'users' => $sUsers,
                'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1),
                'title' => $sTitle
            ));
        } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
            $sPhrase = _p('users_commented_on_your_video_title', array('users' => $sUsers, 'title' => $sTitle));
        } else {
            $sPhrase = _p('users_commented_on_span_class_drop_data_user_row_full_name_video',
                array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
        }

        return array(
            'link' => $aRow['link'],
            'message' => $sPhrase,
            'icon' => 'fa-video-camera'
        );
    }

    /**
     * @param $aNotification
     * @return array|bool
     */
    public function getNotificationLike($aNotification)
    {
        $aRow = Phpfox::getService('v.video')->getInfoForNotification((int)$aNotification['item_id']);

        if (!isset($aRow['video_id'])) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'],
            setting('notification.total_notification_title_length'), '...');

        if ($aNotification['user_id'] == $aRow['user_id']) {
            $sPhrase = _p('users_liked_gender_own_video_title', array(
                'users' => $sUsers,
                'gender' => Phpfox::getService('user')->gender($aRow['gender'], 1),
                'title' => $sTitle
            ));
        } elseif ($aRow['user_id'] == Phpfox::getUserId()) {
            $sPhrase = _p('users_liked_your_video_title', array('users' => $sUsers, 'title' => $sTitle));
        } else {
            $sPhrase = _p('users_liked_span_class_drop_data_user_row_full_name_s_span_video_title',
                array('users' => $sUsers, 'row_full_name' => $aRow['full_name'], 'title' => $sTitle));
        }

        return array(
            'link' => $aRow['link'],
            'message' => $sPhrase,
            'icon' => 'fa-video-camera'
        );
    }

    /**
     * @param $aNotification
     * @return array|bool
     */
    public function getNotificationApproved($aNotification)
    {
        $aRow = Phpfox::getService('v.video')->getInfoForNotification((int)$aNotification['item_id']);
        if (!isset($aRow['video_id'])) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'],
            setting('notification.total_notification_title_length'), '...');

        $sPhrase = _p('your_video_title_is_approved_by_sender', array('title' => $sTitle, 'sender' => $sUsers));

        return array(
            'link' => $aRow['link'],
            'message' => $sPhrase,
            'icon' => 'fa-video-camera'
        );
    }

    /**
     * @param $aNotification
     * @return array|bool
     */
    public function getNotificationFeatured($aNotification)
    {
        $aRow = Phpfox::getService('v.video')->getInfoForNotification((int)$aNotification['item_id']);

        if (!isset($aRow['video_id'])) {
            return false;
        }

        $sUsers = Phpfox::getService('notification')->getUsers($aNotification);
        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'],
            setting('notification.total_notification_title_length'), '...');

        $sPhrase = _p('your_video_title_is_featured_by_sender', array('title' => $sTitle, 'sender' => $sUsers));

        return array(
            'link' => $aRow['link'],
            'message' => $sPhrase,
            'icon' => 'fa-video-camera'
        );
    }

    /**
     * @param $aNotification
     * @return array|bool
     */
    public function getNotificationReady($aNotification)
    {
        $aRow = Phpfox::getService('v.video')->getInfoForNotification((int)$aNotification['item_id']);

        if (!isset($aRow['video_id'])) {
            return false;
        }

        $sTitle = Phpfox::getLib('parse.output')->shorten($aRow['title'],
            setting('notification.total_notification_title_length'), '...');

        if ($sTitle) {
            $sPhrase = _p('your_video_title_is_ready', array('title' => $sTitle));
        } else {
            $sPhrase = _p('video_is_ready');
        }

        return array(
            'link' => $aRow['link'],
            'message' => $sPhrase,
            'custom_icon' => 'fa-video-camera'
        );
    }

    /**
     * @return array
     */
    public function getPagePerms()
    {
        $aPerms = [
            'pf_video.share_videos' => _p('who_can_share_videos'),
            'pf_video.view_browse_videos' => _p('who_can_view_videos')
        ];

        return $aPerms;
    }

    /**
     * @return array
     */
    public function getGroupPerms()
    {
        $aPerms = [
            'pf_video.share_videos' => _p('who_can_share_videos')
        ];

        return $aPerms;
    }

    /**
     * @param $aPage
     * @return array|null
     */
    public function getPageMenu($aPage)
    {
        if (!Phpfox::getUserParam('v.pf_video_view')) {
            return null;
        }

        if (!Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'pf_video.view_browse_videos')) {
            return null;
        }

        $aMenus[] = [
            'phrase' => _p('Videos'),
            'url' => Phpfox::getService('pages')->getUrl($aPage['page_id'], $aPage['title'],
                    $aPage['vanity_url']) . 'video/',
            'icon' => 'module/video.png',
            'landing' => 'video'
        ];

        return $aMenus;
    }

    /**
     * @param $aPage
     * @return array|null
     */
    public function getPageSubMenu($aPage)
    {
        if (!user('pf_video_share', '1')) {
            return null;
        }
        if (!Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'pf_video.share_videos')) {
            return null;
        }

        return [
            [
                'phrase' => _p('share_a_video'),
                'url' => Phpfox::getLib('url')->makeUrl('video.share', [
                    'module' => 'pages',
                    'item' => $aPage['page_id']
                ])
            ]
        ];
    }

    /**
     * @param $aPage
     * @return array|null
     */
    public function getGroupMenu($aPage)
    {
        if (!Phpfox::getUserParam('v.pf_video_view')) {
            return null;
        }

        $aMenus[] = [
            'phrase' => _p('Videos'),
            'url' => Phpfox::getService('groups')->getUrl($aPage['page_id'], $aPage['title'],
                    $aPage['vanity_url']) . 'video/',
            'icon' => 'module/video.png',
            'landing' => 'video'
        ];

        return $aMenus;
    }

    /**
     * @param $aPage
     * @return array|null
     */
    public function getGroupSubMenu($aPage)
    {
        if (!user('pf_video_share', '1')) {
            return null;
        }
        if (!Phpfox::getService('groups')->hasPerm($aPage['page_id'], 'pf_video.share_videos')) {
            return null;
        }

        return [
            [
                'phrase' => _p('share_a_video'),
                'url' => Phpfox::getLib('url')->makeUrl('video.share', [
                    'module' => 'groups',
                    'item' => $aPage['page_id']
                ])
            ]
        ];
    }

    /**
     * @return array
     */
    public function getActivityPointField()
    {
        return [
            _p('Videos') => 'activity_video'
        ];
    }

    /**
     * @return array
     */
    public function pendingApproval()
    {
        return [
            'phrase' => _p('Videos'),
            'value' => Phpfox::getService('v.video')->getPendingTotal(),
            'link' => Phpfox::getLib('url')->makeUrl('video', ['view' => 'pending'])
        ];
    }

    public function getAdmincpAlertItems()
    {
        $iTotalPending = Phpfox::getService('v.video')->getPendingTotal();
        return [
            'target'=> '_blank',
            'message'=> _p('you_have_total_pending_videos', ['total'=>$iTotalPending]),
            'value' => $iTotalPending,
            'link' => Phpfox_Url::instance()->makeUrl('video', array('view' => 'pending'))
        ];
    }

    /**
     * @return array
     */
    public function getGlobalPrivacySettings()
    {
        return [
            'v.default_privacy_setting' => [
                'phrase' => _p('Videos')
            ]
        ];
    }

    /**
     * @param string $sQuery
     * @param bool $bIsTagSearch
     *
     * @return array|null
     */
    public function globalSearch($sQuery, $bIsTagSearch = false)
    {
        (($sPlugin = Phpfox_Plugin::get('video.component_service_callback_globalsearch__start')) ? eval($sPlugin) : false);
        $sCondition = 'v.in_process = 0 AND v.view_id = 0 AND v.privacy = 0 AND v.item_id = 0';
        if ($bIsTagSearch == false) {
            $sCondition .= ' AND (v.title LIKE \'%' . db()->escape($sQuery) . '%\' OR vt.text LIKE \'%' . db()->escape($sQuery) . '%\')';
        }

        $iCnt = db()->select('COUNT(*)')
            ->from($this->_sTable, 'v')
            ->leftJoin(Phpfox::getT('video_text'), 'vt', 'vt.video_id = v.video_id')
            ->where($sCondition)
            ->execute('getSlaveField');

        $aRows = db()->select('v.title, v.time_stamp, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where($sCondition)
            ->limit(10)
            ->order('v.time_stamp DESC')
            ->execute('getSlaveRows');

        if (count($aRows)) {
            $aResults = array();
            $aResults['total'] = $iCnt;
            $aResults['menu'] = _p('Videos');

            if ($bIsTagSearch == true) {
                $aResults['form'] = '<div><input type="button" value="' . _p('view_more_videos') . '" class="search_button" onclick="window.location.href = \'' . Phpfox::getLib('url')->makeUrl('video',
                        array('tag', $sQuery)) . '\';" /></div>';
            } else {
                $aResults['form'] = '<form method="post" action="' . Phpfox::getLib('url')->makeUrl('video') . '"><div><input type="hidden" name="' . Phpfox::getTokenName() . '[security_token]" value="' . Phpfox::getService('log.session')->getToken() . '" /></div><div><input name="search[search]" value="' . Phpfox::getLib('parse.output')->clean($sQuery) . '" size="20" type="hidden" /></div><div><input type="submit" name="search[submit]" value="' . _p('view_more_videos') . '" class="search_button" /></div></form>';
            }
            foreach ($aRows as $iKey => $aRow) {
                $aResults['results'][$iKey] = array(
                    'title' => $aRow['title'],
                    'link' => Phpfox::getLib('url')->makeUrl($aRow['user_name'], array('video', $aRow['title'])),
                    'image' => Phpfox::getLib('image.helper')->display(array(
                            'server_id' => $aRow['server_id'],
                            'title' => $aRow['full_name'],
                            'path' => 'core.url_user',
                            'file' => $aRow['user_image'],
                            'suffix' => '_120',
                            'max_width' => 75,
                            'max_height' => 75
                        )
                    ),
                    'extra_info' => _p('video_created_on_time_stamp_by_full_name', array(
                            'link' => Phpfox_Url::instance()->makeUrl('video'),
                            'time_stamp' => Phpfox::getTime(setting('core.global_update_time'),
                                $aRow['time_stamp']),
                            'user_link' => Phpfox_Url::instance()->makeUrl($aRow['user_name']),
                            'full_name' => $aRow['full_name']
                        )
                    )
                );
            }
            (($sPlugin = Phpfox_Plugin::get('video.component_service_callback_globalsearch__return')) ? eval($sPlugin) : false);

            return $aResults;
        }
        (($sPlugin = Phpfox_Plugin::get('video.component_service_callback_globalsearch__end')) ? eval($sPlugin) : false);

        return null;
    }

    /**
     * @param string $sSearch
     */
    public function globalUnionSearch($sSearch)
    {
        db()->select('v.video_id AS item_id, v.title AS item_title, v.time_stamp AS item_time_stamp, v.user_id AS item_user_id, \'v\' AS item_type_id, v.image_path AS item_photo, v.image_server_id AS item_photo_server')
            ->from(Phpfox::getT('video'), 'v')
            ->where(db()->searchKeywords('v.title',
                    $sSearch) . ' AND v.in_process = 0 AND v.view_id = 0 AND v.privacy = 0')
            ->union();
    }

    /**
     * @param array $aRow
     *
     * @return array
     */
    public function getSearchInfo($aRow)
    {
        $aRow['image_path'] = $aRow['item_photo'];
        $aRow['image_server_id'] = $aRow['item_photo_server'];
        Phpfox::getService('v.video')->convertImagePath($aRow);
        $aInfo = array();
        $aInfo['item_link'] = Phpfox::getLib('url')->permalink('video.play', $aRow['item_id'], $aRow['item_title']);
        $aInfo['item_name'] = _p('Videos');
        $aInfo['item_display_photo'] = '<img src="' . $aRow['image_path'] . '" class="image_deferred  built has_image">';

        return $aInfo;
    }

    /**
     * @return array
     */
    public function getSearchTitleInfo()
    {
        return array(
            'name' => _p('Videos')
        );
    }

    /**
     * @return array
     */
    public function updateCounterList()
    {
        $aList[] = [
            'name' => _p('users_video_count'),
            'id' => 'video-total'
        ];

        return $aList;
    }

    /**
     * @param int $iId
     * @param int $iPage
     * @param int $iPageLimit
     *
     * @return int
     */
    public function updateCounter($iId, $iPage, $iPageLimit)
    {
        (($sPlugin = Phpfox_Plugin::get('video.component_service_callback_updatecounter__start')) ? eval($sPlugin) : false);
        $iCnt = 0;
        if ($iId == 'video-total') {
            $iCnt = db()->select('COUNT(*)')
                ->from(Phpfox::getT('user'))
                ->execute('getSlaveField');

            $aRows = db()->select('u.user_id, u.user_name, u.full_name, COUNT(v.video_id) AS total_items')
                ->from(Phpfox::getT('user'), 'u')
                ->leftJoin($this->_sTable, 'v',
                    'v.module_id = \'video\' AND v.user_id = u.user_id AND v.view_id = 0 AND v.in_process = 0')
                ->limit($iPage, $iPageLimit, $iCnt)
                ->group('u.user_id')
                ->execute('getSlaveRows');

            foreach ($aRows as $aRow) {
                db()->update(Phpfox::getT('user_field'), array('total_video' => $aRow['total_items']),
                    'user_id = ' . $aRow['user_id']);
            }
        }

        return $iCnt;
    }

    /**
     * @param $iId
     * @return mixed
     */
    public function getCommentItem($iId)
    {
        $aRow = db()->select('video_id AS comment_item_id, privacy_comment, user_id AS comment_user_id')
            ->from($this->_sTable)
            ->where('video_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        $aRow['comment_view_id'] = '0';

        if (!Phpfox::getService('comment')->canPostComment($aRow['comment_user_id'], $aRow['privacy_comment'])) {
            Phpfox_Error::set(_p('unable_to_post_a_comment_on_this_item_due_to_privacy_settings'));

            unset($aRow['comment_item_id']);
        }

        return $aRow;
    }

    /**
     * @param $aVals
     * @param null $iUserId
     * @param null $sUserName
     */
    public function addComment($aVals, $iUserId = null, $sUserName = null)
    {
        (($sPlugin = Phpfox_Plugin::get('video.component_service_callback_addcomment__start')) ? eval($sPlugin) : false);

        $aVideo = Phpfox::getService('v.video')->getInfoForNotification($aVals['item_id']);

        if ($iUserId === null || empty($aVideo['video_id'])) {
            $iUserId = Phpfox::getUserId();
        }

        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add($aVals['type'] . '_comment',
            $aVals['comment_id'], 0, 0, 0, $iUserId) : null);
        // Update the post counter if its not a comment put under moderation or if the person posting the comment is the owner of the item.
        if (empty($aVals['parent_id'])) {
            db()->updateCount('comment', 'type_id = \'v\' AND item_id = ' . (int)$aVals['item_id'], 'total_comment',
                'video', 'video_id = ' . (int)$aVals['item_id']);
        }
        $aChecked = array();
        $aMatches = Phpfox::getService('user.process')->getIdFromMentions($aVideo['status_info'], true);
        foreach ($aMatches as $iKey => $iUserId) {
            if (in_array($iUserId, $aChecked) || empty($iUserId)) {
                continue;
            }
            $aChecked[] = $iUserId;
        }
        $sSubject = (Phpfox::getUserId() == $aVideo['user_id'] ? _p('full_name_commented_on_gender_video',
            array(
                'full_name' => Phpfox::getUserBy('full_name'),
                'gender' => Phpfox::getService('user')->gender($aVideo['gender'], 1)
            )) : _p('full_name_commented_on_video_full_name_s_video',
            array('full_name' => Phpfox::getUserBy('full_name'), 'video_full_name' => $aVideo['full_name'])));
        $sMessage = (Phpfox::getUserId() == $aVideo['user_id'] ? _p('full_name_commented_on_gender_video_message',
            array(
                'full_name' => Phpfox::getUserBy('full_name'),
                'gender' => Phpfox::getService('user')->gender($aVideo['gender'], 1),
                'link' => $aVideo['link'],
                'title' => $aVideo['title']
            )) : _p('full_name_commented_on_video_full_name_s_video_message', array(
                    'full_name' => Phpfox::getUserBy('full_name'),
                    'video_full_name' => $aVideo['full_name'],
                    'link' => $aVideo['link'],
                    'title' => $aVideo['title']
            )));
        $aExecutedUsers = [];
        foreach ($aChecked as $iUser) {
            Phpfox::getLib('mail')->to($iUser)
                ->subject($sSubject)
                ->message($sMessage)
                ->notification('comment.add_new_comment')
                ->send();
            if (Phpfox::isModule('notification')) {
                Phpfox::getService('notification.process')->add('comment_v', $aVideo['video_id'],
                    $iUser);
            }
            $aExecutedUsers[] = $iUser;
        }
        // Send the user an email
        Phpfox::getService('comment.process')->notify(array(
                'user_id' => $aVideo['user_id'],
                'item_id' => $aVideo['video_id'],
                'owner_subject' => _p('full_name_commented_on_your_video_title',
                    array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aVideo['title'])),
                'owner_message' => _p('full_name_commented_on_your_video_message',
                    array(
                        'full_name' => Phpfox::getUserBy('full_name'),
                        'link' => $aVideo['link'],
                        'title' => $aVideo['title']
                    )),
                'owner_notification' => 'comment.add_new_comment',
                'notify_id' => 'comment_v',
                'mass_id' => 'video',
                'mass_subject' => $sSubject,
                'mass_message' => $sMessage,
                'exclude_users' => $aExecutedUsers,
            )
        );

        if ($aVideo['module_id'] == 'user') {
            // Send the user an email
            Phpfox::getService('comment.process')->notify(array(
                    'user_id' => $aVideo['item_id'],
                    'item_id' => $aVideo['video_id'],
                    'owner_subject' => _p('full_name_commented_on_your_video_title',
                        array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aVideo['title'])),
                    'owner_message' => _p('full_name_commented_on_your_video_message',
                        array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'link' => $aVideo['link'],
                            'title' => $aVideo['title']
                        )),
                    'owner_notification' => 'comment.add_new_comment',
                    'notify_id' => 'comment_v',
                    'mass_id' => 'video',
                    'mass_subject' => (Phpfox::getUserId() == $aVideo['item_id'] ? _p('full_name_commented_on_gender_video',
                        array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'gender' => Phpfox::getService('user')->gender($aVideo['gender'], 1)
                        )) : _p('full_name_commented_on_video_full_name_s_video',
                        array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'video_full_name' => $aVideo['full_name']
                        ))),
                    'mass_message' => (Phpfox::getUserId() == $aVideo['item_id'] ? _p('full_name_commented_on_gender_video_message',
                        array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'gender' => Phpfox::getService('user')->gender($aVideo['gender'], 1),
                            'link' => $aVideo['link'],
                            'title' => $aVideo['title']
                        )) : _p('full_name_commented_on_video_full_name_s_video_message', array(
                        'full_name' => Phpfox::getUserBy('full_name'),
                        'video_full_name' => $aVideo['full_name'],
                        'link' => $aVideo['link'],
                        'title' => $aVideo['title']
                    ))),
                    'exclude_users' => $aExecutedUsers,
                )
            );
        }

        (($sPlugin = Phpfox_Plugin::get('video.component_service_callback_addcomment__end')) ? eval($sPlugin) : false);
    }

    /**
     * @param $iId
     */
    public function deleteComment($iId)
    {
        user('pf_video_comment', 1, null, true);
        db()->updateCounter('video', 'total_comment', 'video_id', $iId, true);
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
     * @param $iId
     * @return bool|string
     */
    public function getFeedRedirect($iId)
    {
        (($sPlugin = Phpfox_Plugin::get('video.component_service_callback_getfeedredirect__start')) ? eval($sPlugin) : false);

        $aVideo = Phpfox::getService('v.video')->getInfoForNotification($iId);

        if (!isset($aVideo['video_id'])) {
            return false;
        }

        (($sPlugin = Phpfox_Plugin::get('video.component_service_callback_getfeedredirect__end')) ? eval($sPlugin) : false);

        return Phpfox::permalink('video.play', $aVideo['video_id'], $aVideo['title']);
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
     * @return string
     */
    public function getCommentItemName()
    {
        return 'video';
    }

    /**
     * @param $aUser
     * @return array|bool
     */
    public function getProfileMenu($aUser)
    {
        if (!Phpfox::getUserParam('v.pf_video_view')) {
            return [];
        }
        if (!setting('profile.show_empty_tabs')) {
            if (!isset($aUser['total_video'])) {
                return false;
            }

            if (isset($aUser['total_video']) && (int)$aUser['total_video'] === 0) {
                return false;
            }
        }
        $aSubMenu = [];

        $aMenus[] = [
            'phrase' => _p('Videos'),
            'url' => 'profile.video',
            'total' => (int)(isset($aUser['total_video']) ? $aUser['total_video'] : 0),
            'sub_menu' => $aSubMenu,
            'icon' => 'feed/video.png'
        ];

        return $aMenus;
    }

    /**
     * @param int $iId
     * @param null|int $iUserId
     */
    public function addTrack($iId, $iUserId = null)
    {
        (($sPlugin = Phpfox_Plugin::get('video.component_service_callback_addtrack__start')) ? eval($sPlugin) : false);

        if ($iUserId == null) {
            $iUserId = Phpfox::getUserId();
        }
        $this->database()->insert(Phpfox::getT('track'), [
            'type_id' => 'v',
            'item_id' => (int)$iId,
            'ip_address' => db()->escape(Phpfox::getIp(true)),
            'user_id' => $iUserId,
            'time_stamp' => PHPFOX_TIME
        ]);

        (($sPlugin = Phpfox_Plugin::get('video.component_service_callback_addtrack__end')) ? eval($sPlugin) : false);
    }

    /**
     * @param int $iUserId
     *
     * @return array
     */
    public function getTotalItemCount($iUserId)
    {
        return [
            'field' => 'total_video',
            'total' => $this->database()->select('COUNT(*)')->from(Phpfox::getT('video'))
                ->where('user_id = ' . (int)$iUserId . ' AND module_id = \'video\' AND view_id = 0 AND in_process = 0')
                ->execute('getSlaveField')
        ];
    }

    /**
     * @return string
     */
    public function getProfileLink()
    {
        return 'profile.v';
    }

    /**
     * @return string
     */
    public function getAjaxCommentVar()
    {
        return 'pf_video_comment';
    }

    /**
     * @param $aNotification
     * @return array
     */
    public function getNotificationConverted($aNotification)
    {
        return array(
            'link' => Phpfox::getLib('url')->makeUrl('video'),
            'message' => _p("all_old_videos_feed_video_converted_new_videos"),
            'custom_icon' => 'fa-video-camera'
        );

    }

    /**
     * @param $aNotification
     * @return array|bool
     */
    public function getNotificationNewItem_Groups($aNotification)
    {
        if (!Phpfox::isModule('groups')) {
            return false;
        }
        $aItem = Phpfox::getService('v.video')->getInfoForNotification($aNotification['item_id']);
        if (empty($aItem) || empty($aItem['item_id']) || $aItem['module_id'] != 'groups') {
            return false;
        }
        $aRow = Phpfox::getService('groups')->getPage($aItem['item_id']);
        if (!isset($aRow['page_id'])) {
            return false;
        }
        $sPhrase = _p('users_added_a_video_in_the_group_title', array(
            'users' => Phpfox::getService('notification')->getUsers($aNotification),
            'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'],
                Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));

        return array(
            'link' => $aItem['link'],
            'message' => $sPhrase,
            'icon' => 'feed/video.png'
        );
    }

    /**
     * @param $aNotification
     * @return array|bool
     */
    public function getNotificationNewItem_Pages($aNotification)
    {
        if (!Phpfox::isModule('groups')) {
            return false;
        }
        $aItem = Phpfox::getService('v.video')->getInfoForNotification($aNotification['item_id']);
        if (empty($aItem) || empty($aItem['item_id']) || $aItem['module_id'] != 'pages') {
            return false;
        }
        $aRow = Phpfox::getService('pages')->getPage($aItem['item_id']);
        if (!isset($aRow['page_id'])) {
            return false;
        }
        $sPhrase = _p('users_added_a_video_in_the_page_title', array(
            'users' => Phpfox::getService('notification')->getUsers($aNotification),
            'title' => Phpfox::getLib('parse.output')->shorten($aRow['title'],
                Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));

        return array(
            'link' => $aItem['link'],
            'message' => $sPhrase,
            'icon' => 'feed/video.png'
        );
    }

    /**
     * @param $aNotification
     * @return array|bool
     */
    public function getNotificationNewItem_Wall($aNotification)
    {
        $aItem = Phpfox::getService('v.video')->getInfoForNotification($aNotification['item_id']);
        if (empty($aItem) || empty($aItem['item_id']) || $aItem['module_id'] != 'user') {
            return false;
        }
        $aRow = Phpfox::getService('user')->getUser($aItem['item_id']);
        if (!isset($aRow['user_id'])) {
            return false;
        }
        $sPhrase = _p('users_posted_a_video_title_on_in_your_wall', array(
            'users' => Phpfox::getService('notification')->getUsers($aNotification),
            'title' => Phpfox::getLib('parse.output')->shorten($aItem['title'],
                Phpfox::getParam('notification.total_notification_title_length'), '...')
        ));

        return array(
            'link' => $aItem['link'],
            'message' => $sPhrase,
            'icon' => 'feed/video.png'
        );
    }

    /**
     * Action to take when user cancelled their account
     *
     * @param int $iUser
     *
     * @return void
     * @throws \Exception
     */
    public function onDeleteUser($iUser)
    {
        $aVideos = db()
            ->select('video_id')
            ->from($this->_sTable)
            ->where('(user_id = ' . (int)$iUser . ' OR page_user_id = ' . (int)$iUser . ' OR (module_id = \'user\' AND item_id = ' . (int)$iUser . '))')
            ->execute('getSlaveRows');
        if (count($aVideos)) {
            foreach ($aVideos as $aVideo) {
                Phpfox::getService('v.process')->delete($aVideo['video_id'], '', 0, true, true);
            }
        }
    }

    /**
     * Remove is_sponsor if sponsor item in ad is removed
     *
     * @param array $aSponsor
     */
    public function deleteSponsorItem($aSponsor)
    {
        Phpfox::getLib('database')->update(':video', ['is_sponsor' => 0], 'video_id=' . (int)$aSponsor['item_id']);
        \Phpfox_Cache::instance()->remove(PHPFOX_DIR_CACHE . 'video' . PHPFOX_DS . 'sponsored.php', 'path');
    }

    public function getNotificationTagged($aRow)
    {
        $aVideo = Phpfox::getService('v.video')->getVideo($aRow['item_id']);

        return array(
            'message' => _p('user_name_tagged_you_in_video_tittle', array(
                'user_name' => $aRow['full_name'],
                'title' => $aVideo['title']
            )),
            'link' => Phpfox::getLib('url')->permalink('video.play', $aVideo['video_id'], $aVideo['title'])
        );
    }

    public function getCommentNotificationTag($aNotification)
    {
        $aRow = $this->database()
            ->select('v.video_id, v.title, u.user_name, u.full_name')
            ->from(Phpfox::getT('comment'), 'c')
            ->join(Phpfox::getT('video'), 'v', 'v.video_id = c.item_id')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = c.user_id')
            ->where('c.comment_id = ' . (int)$aNotification['item_id'])
            ->execute('getSlaveRow');

        $sPhrase = _p('user_name_tagged_you_in_a_comment_in_a_video', ['user_name' => $aRow['full_name']]);

        return [
            'link' => Phpfox_Url::instance()
                    ->permalink('video.play', $aRow['video_id'],
                        $aRow['title']) . 'comment_' . $aNotification['item_id'],
            'message' => $sPhrase,
            'icon' => \Phpfox_Template::instance()->getStyle('image', 'activity.png', 'blog')
        ];
    }

    /**
     * This callback will be called when a page or group be deleted
     * @param $iId
     * @param $sType
     * @throws \Exception
     */
    public function onDeletePage($iId, $sType)
    {
        $aVideos = db()->select('video_id')->from(':video')->where([
            'module_id' => $sType,
            'item_id' => $iId
        ])->executeRows();
        foreach ($aVideos as $aVideo) {
            Phpfox::getService('v.process')->delete($aVideo['video_id'], '', 0, true, true);
        }
    }

    /**
     * @return array
     */
    public function getUploadParams()
    {
        return Phpfox::getService('v.video')->getUploadVideoParams();
    }

    /**
     * @return array
     */
    public function getUploadParamsEdit_Video()
    {
        return Phpfox::getService('v.video')->getUploadPhotoParams();
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

        $iTotalVideos = db()->select('COUNT(*)')
            ->from(':video')
            ->where('user_id =' . (int)$iUserId)
            ->executeField();

        return [
            'total_name' => _p('videos'),
            'total_value' => $iTotalVideos,
            'type' => 'item'
        ];
    }
}
