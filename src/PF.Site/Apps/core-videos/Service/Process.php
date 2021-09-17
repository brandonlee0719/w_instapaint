<?php

namespace Apps\PHPfox_Videos\Service;

use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Service;
use Phpfox_Url;

defined('PHPFOX') or exit('NO DICE!');

class Process extends Phpfox_Service
{
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('video');
    }

    /**
     * START CATEGORIES PROCESS
     */

    /**
     * @param array $aVals
     * @return bool|int
     */
    public function addCategory($aVals)
    {
        //Add phrase for category
        $aLanguages = Phpfox::getService('language')->getAll();
        $name = $aVals['name_' . $aLanguages[0]['language_id']];
        $phrase_var_name = 'video_category_' . md5('Video Category' . $name . PHPFOX_TIME);

        //Add phrases
        $aText = [];
        foreach ($aLanguages as $aLanguage) {
            if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])) {
                $aText[$aLanguage['language_id']] = $aVals['name_' . $aLanguage['language_id']];
            } else {
                return \Phpfox_Error::set((_p('provide_a_language_name_name',
                    ['language_name' => $aLanguage['title']])));
            }
        }
        $aValsPhrase = [
            'product_id' => 'phpfox',
            'module' => 'video',
            'var_name' => $phrase_var_name,
            'text' => $aText
        ];
        $finalPhrase = Phpfox::getService('language.phrase.process')->add($aValsPhrase);

        $iId = db()->insert(Phpfox::getT('video_category'), [
            'name' => $finalPhrase,
            'time_stamp' => PHPFOX_TIME,
            'ordering' => '0',
            'parent_id' => (!empty($aVals['parent_id']) ? (int)$aVals['parent_id'] : 0),
            'is_active' => '1'
        ]);
        Phpfox::getLib('cache')->removeGroup('video');
        return $iId;
    }

    /**
     * @param $iCategoryId
     * @param array $aVals
     * @return bool
     */
    public function deleteCategory($iCategoryId, $aVals = array())
    {
        $aCategory = db()->select('*')
            ->from(Phpfox::getT('video_category'))
            ->where('category_id=' . intval($iCategoryId))
            ->execute('getSlaveRow');

        // Delete phrase of category
        if (isset($aCategory['name']) && Phpfox::isPhrase($aCategory['name'])) {
            Phpfox::getService('language.phrase.process')->delete($aCategory['name'], true);
        }

        if ($aVals && isset($aVals['delete_type'])) {
            switch ($aVals['delete_type']) {
                case 1:
                    $aSubs = db()->select('vc.category_id')
                        ->from(':video_category', 'vc')
                        ->where('vc.parent_id = ' . intval($iCategoryId))
                        ->execute('getSlaveRows');
                    $sCategoryIds = $iCategoryId;
                    foreach ($aSubs as $key => $aSub) {
                        $sCategoryIds .= ',' . $aSub['category_id'];
                    }
                    $aItems = db()->select('vcd.event_id')
                        ->from(':video_category_data', 'vcd')
                        ->where("vcd.category_id IN (" . $sCategoryIds . ')')
                        ->execute('getSlaveRows');
                    foreach ($aItems as $aItem) {
                        $iVideoId = $aItem['video_id'];
                        $this->delete($iVideoId);
                    }
                    db()->delete(':video_category', 'parent_id = ' . intval($iCategoryId));
                    break;
                case 2:
                    if (!empty($aVals['new_category_id'])) {
                        $aItems = db()->select('d.video_id')
                            ->from(Phpfox::getT('video_category_data'), 'd')
                            ->where("d.category_id = " . intval($iCategoryId))
                            ->execute('getSlaveRows');
                        foreach ($aItems as $aItem) {
                            $iVideoId = $aItem['video_id'];
                            db()->delete(Phpfox::getT('video_category_data'),
                                'category_id = ' . intval($aVals['new_category_id']) . ' AND video_id = ' . intval($iVideoId));
                        }
                        db()->update(Phpfox::getT('video_category_data'),
                            array('category_id' => intval($aVals['new_category_id'])),
                            'category_id = ' . intval($iCategoryId));
                        db()->update(':video_category', array('parent_id' => $aVals['new_category_id']),
                            'parent_id = ' . intval($iCategoryId));
                    }
                    break;
                default:
                    break;
            }
        }

        db()->delete(Phpfox::getT('video_category'), 'category_id = ' . intval($iCategoryId));
        Phpfox::getLib('cache')->removeGroup('video');

        return true;
    }

    /**
     * @param $iId
     * @param $aVals
     * @return bool
     */
    public function updateCategory($iId, $aVals)
    {
        $aLanguages = Phpfox::getService('language')->getAll();
        if (Phpfox::isPhrase($aVals['name'])) {
            foreach ($aLanguages as $aLanguage) {
                if (isset($aVals['name_' . $aLanguage['language_id']])) {
                    $name = $aVals['name_' . $aLanguage['language_id']];
                    Phpfox::getService('language.phrase.process')->updateVarName($aLanguage['language_id'],
                        $aVals['name'], $name);
                }
            }
        } else {
            //Add new phrase if before is not phrase
            $name = $aVals['name_' . $aLanguages[0]['language_id']];
            $phrase_var_name = 'video_category_' . md5('Video Category' . $name . PHPFOX_TIME);
            $aText = [];
            foreach ($aLanguages as $aLanguage) {
                if (isset($aVals['name_' . $aLanguage['language_id']]) && !empty($aVals['name_' . $aLanguage['language_id']])) {
                    $aText[$aLanguage['language_id']] = $aVals['name_' . $aLanguage['language_id']];
                } else {
                    Phpfox_Error::set((_p('provide_a_language_name_name',
                        ['language_name' => $aLanguage['title']])));
                }
            }
            $aValsPhrase = [
                'product_id' => 'phpfox',
                'module' => 'video',
                'var_name' => $phrase_var_name,
                'text' => $aText
            ];
            $aVals['name'] = Phpfox::getService('language.phrase.process')->add($aValsPhrase);
        }

        db()->update(Phpfox::getT('video_category'), array(
            'parent_id' => (!empty($aVals['parent_id']) ? (int)$aVals['parent_id'] : 0),
            'name' => $aVals['name'],
        ), 'category_id = ' . (int)$iId
        );
        Phpfox::getLib('cache')->removeGroup('video');

        return true;
    }

    /**
     * @param $iId
     * @param $iType
     */
    public function updateCategoryActivity($iId, $iType)
    {
        Phpfox::isAdmin(true);
        db()->update((Phpfox::getT('video_category')), array('is_active' => (int)($iType == '1' ? 1 : 0)),
            'category_id = ' . (int)$iId);
        // clear cache
        Phpfox::getLib('cache')->removeGroup('video');
    }

    /**
     *
     */

    /**
     * VIDEO PROCESS
     */

    /**
     * @param $aVals
     * @return int
     */
    public function addVideo($aVals)
    {
        if (!defined('PHPFOX_FORCE_IFRAME')) {
            define('PHPFOX_FORCE_IFRAME', true);
        }
        $aCategories = [];
        if (!empty($aVals['category'])) {
            foreach ($aVals['category'] as $iCategory) {
                if (empty($iCategory)) {
                    continue;
                }
                if (!is_numeric($iCategory)) {
                    continue;
                }
                $aCategories[] = $iCategory;
            }
        }

        if (!empty($aVals['url']) && !Phpfox::getService('link')->getLink($aVals['url'])) {
            return Phpfox_Error::set(_p('unable_to_embed_this_video_due_to_privacy_settings'));
        }

        if ($sPlugin = Phpfox_Plugin::get('video.service_process_addvideo__start')) {
            eval($sPlugin);
        }

        if (!isset($aVals['privacy'])) {
            $aVals['privacy'] = 0;
        }

        Phpfox::getService('ban')->checkAutomaticBan(isset($aVals['title']) ? $aVals['title'] : '' . isset($aVals['text']) ? $aVals['text'] : '');

        $sModule = 'video';
        $iPageUserId = 0;
        $iItem = 0;
        $aCallback = null;
        $iUserId = (isset($aVals['user_id']) ? $aVals['user_id'] : Phpfox::getUserId());

        if (!empty($aVals['callback_module']) && Phpfox::hasCallback($aVals['callback_module'], 'uploadVideo')) {
            $aCallback = Phpfox::callback($aVals['callback_module'] . '.uploadVideo', $aVals);
            $sModule = $aCallback['module'];
            $iItem = $aCallback['item_id'];
            if (in_array($sModule, array('pages', 'groups'))) {
                $iPageUserId = Phpfox::getService('v.video')->getPageUserId($iItem);
            }
            //post in pages/groups doesn't have parent_user_id
            if (isset($aVals['parent_user_id'])) {
                unset($aVals['parent_user_id']);
            }
        } elseif (!empty($aVals['parent_user_id'])) {
            $sModule = 'user';
            $iItem = $aVals['parent_user_id'];
        }

        //Check permission to create video inside pages/groups
        if ($sModule == 'pages' && Phpfox::isModule('pages')) {
            if (!Phpfox::getService('pages')->hasPerm($iItem, 'pf_video.share_videos')) {
                return Phpfox_Error::set(_p('You are not allow to create new video in this pages'));
            }
        } elseif ($sModule == 'groups' && Phpfox::isModule('groups')) {
            if (!Phpfox::getService('groups')->hasPerm($iItem, 'pf_video.share_videos')) {
                return Phpfox_Error::set(_p('You are not allow to create new video in this groups'));
            }
        }
        //check user_group_id  of uploaded user
        if ($iUserId) {
            $iUserGroupId = Phpfox::getLib('database')->select('user_group_id')
                ->from(':user')
                ->where('user_id=' . $iUserId)
                ->executeField();
            $iViewId = Phpfox::getUserGroupParam($iUserGroupId, 'v.pf_video_approve_before_publicly') ? 2: 0;
        } else {
            $iViewId = user('pf_video_approve_before_publicly') ? 2 : 0;
        }
        $aSql = array(
            'is_stream' => (isset($aVals['is_stream']) ? $aVals['is_stream'] : 1),
            'view_id' => $iViewId,
            'module_id' => $sModule,
            'item_id' => (int)$iItem,
            'page_user_id' => (int)$iPageUserId,
            'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
            'privacy_comment' => 0,
            'user_id' => $iUserId,
            'parent_user_id' => isset($aVals['parent_user_id']) ? $aVals['parent_user_id'] : 0,
            'time_stamp' => PHPFOX_TIME,
            'location_name' => (!empty($aVals['location_name'])) ? $aVals['location_name'] : null,
            'location_latlng' => (!empty($aVals['location_latlng'])) ? $aVals['location_latlng'] : null
        );

        $aSql['title'] = (!empty($aVals['title']) ? $this->preParse()->clean($aVals['title'],
            255) : _p('untitled_video'));
        $aSql['duration'] = (isset($aVals['duration']) ? $aVals['duration'] : 0);
        $aSql['status_info'] = (isset($aVals['status_info']) ? $aVals['status_info'] : '');

        $iId = db()->insert($this->_sTable, $aSql);

        if (!$iId) {
            return false;
        }

        $aUpdate = array();
        if (!empty($aVals['default_image'])) {
            if (isset($aVals['image_server_id']) && $aVals['image_server_id'] == -1) {
                $aUpdate['image_path'] = $aVals['default_image'];
                $aUpdate['image_server_id'] = $aVals['image_server_id'];
            } else {
                if (!empty($aVals['url'])) {
                    // check fb url
                    $regex = "/http(?:s?):\/\/(?:www\.|web\.|m\.)?facebook\.com\/([A-z0-9\.]+)\/videos(?:\/[0-9A-z].+)?\/(\d+)(?:.+)?$/";
                    preg_match($regex, $aVals['url'], $matches);
                    if (count($matches) > 2) {
                        $code = $matches[2];
                        if ($code) {
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/$code?fields=length");
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                            curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
                            $data = curl_exec($ch);
                            curl_close($ch);
                            $aData = json_decode($data, true);
                            if (isset($aData['length'])) {
                                $aUpdate['duration'] = sprintf("%s", $aData['length']);
                            }
                        }
                    }
                }

                if (strpos($aVals['default_image'], 'dailymotion.com/thumbnail')) {
                    $aUpdate['image_path'] = $aVals['default_image'];
                    $aUpdate['image_server_id'] = -2;
                } else {
                    list($sImagePath, $iPhotoSize) = $this->downloadImage($aVals['default_image']);
                    $aVals['photo_size'] = $iPhotoSize;
                    $aUpdate['image_path'] = $sImagePath;
                    $aUpdate['image_server_id'] = Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID');
                }
            }
        } else {
            if (!empty($aVals['image_path'])) {
                $aUpdate['image_path'] = $aVals['image_path'];
                $aUpdate['image_server_id'] = $aVals['image_server_id'];
            }
        }

        if (!empty($aVals['path'])) {
            $aUpdate['destination'] = $aVals['path'];
            $aUpdate['server_id'] = isset($aVals['server_id']) ? $aVals['server_id'] : Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID');
            $aUpdate['file_ext'] = isset($aVals['ext']) ? $aVals['ext'] : '';
        }

        if (count($aUpdate)) {
            db()->update($this->_sTable, $aUpdate, 'video_id = ' . $iId);
        }

        if (!empty($aVals['embed_code'])) {
            db()->insert(Phpfox::getT('video_embed'), array(
                    'video_id' => $iId,
                    'video_url' => $aVals['url'],
                    'embed_code' => $aVals['embed_code']
                )
            );
        }

        $sDescription = isset($aVals['text']) ? $aVals['text'] : '';
        db()->insert(Phpfox::getT('video_text'), array(
                'video_id' => $iId,
                'text' => $this->preParse()->clean($sDescription),
                'text_parsed' => $this->preParse()->prepare($sDescription)
            )
        );

        // hash tag in description
        if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support')) {
            Phpfox::getService('tag.process')->add('v', $iId, Phpfox::getUserId(), $aVals['text'], true);
        }

        if (count($aCategories)) {
            foreach ($aCategories as $iCategoryId) {
                db()->insert(Phpfox::getT('video_category_data'),
                    array('video_id' => $iId, 'category_id' => $iCategoryId));
            }
        }

        $aCallback = null;
        if ($sModule != 'video' && Phpfox::hasCallback($sModule, 'getFeedDetails')) {
            $aCallback = Phpfox::callback($sModule . '.getFeedDetails', $iId);
        }

        if (Phpfox::isModule('feed') && !defined('PHPFOX_SKIP_FEED_ENTRY') && user('pf_video_approve_before_publicly') == false) {
            Phpfox::getService('feed.process')->callback($aCallback)->add('v', $iId, $aVals['privacy'], 0,
                ($aCallback === null ? (isset($aVals['parent_user_id']) ? $aVals['parent_user_id'] : 0) : $aVals['callback_item_id']),
                $iUserId);
        }

        if (Phpfox::isModule('notification') && $sModule != 'video' && $iItem && Phpfox::isModule($sModule) && Phpfox::hasCallback($sModule,
                'addItemNotification') && user('pf_video_approve_before_publicly') == false) {
            Phpfox::callback($sModule . '.addItemNotification', [
                'page_id' => $iItem,
                'item_perm' => 'pf_video.view_browse_videos',
                'item_type' => 'v',
                'item_id' => $iId,
                'owner_id' => $iUserId,
                'items_phrase' => _p('videos__l')
            ]);
        }

        if (Phpfox::isModule('notification') && $sModule == 'user' && $iItem && user('pf_video_approve_before_publicly') == false) {
            Phpfox::getService('notification.process')->add('v_newItem_wall', $iId, $iItem, $iUserId);
        }

        if (Phpfox::isModule('notification') && $sModule == 'pages' && $iItem && user('pf_video_approve_before_publicly') == false) {
            $iPageUserId = Phpfox::getService('v.video')->getPageUserId($iItem);
            Phpfox::getService('notification.process')->add('v_newItem_pages', $iId, $iPageUserId, $iUserId);
        }

        if (isset($aVals['status_info'])) {
            $aCurrentUser = Phpfox::getService('user')->getUser(Phpfox::getUserId());
            $sTagger = (isset($aCurrentUser['full_name']) && $aCurrentUser['full_name']) ? $aCurrentUser['full_name'] : $aCurrentUser['user_name'];
            $mentions = Phpfox::getLib('parse.output')->mentionsRegex($aVals['status_info']);
            $link = Phpfox::getLib('url')->permalink('video.play', $iId, $aSql['title']);
            foreach ($mentions as $user) {
                Phpfox::getLib('mail')->to($user->id)
                    ->subject(_p('user_name_tagged_you_in_video_tittle', [
                        'user_name' => $sTagger,
                        'title' => $aSql['title'],
                    ]))
                    ->message(_p('user_name_tagged_you_in_video_tittle',
                            [
                                'user_name' => $sTagger,
                                'title' => $aSql['title'],
                            ]) . '. <a href="' . $link . '">' . _p('check_it_out') . '</a>')
                    ->send();
                if (Phpfox::isModule('notification')) {
                    Phpfox::getService('notification.process')->add('v_tagged', $iId, $user->id, Phpfox::getUserId());
                }
            }
        }

        // Update user space usage
        if (isset($aVals['photo_size']) && $aVals['photo_size'] > 0) {
            Phpfox::getService('user.space')->update($iUserId, 'photo', $aVals['photo_size']);
        }
        if (isset($aVals['video_size']) && $aVals['video_size'] > 0) {
            Phpfox::getService('user.space')->update($iUserId, 'video', $aVals['video_size']);
        }

        // Update user activity
        !user('pf_video_approve_before_publicly') && Phpfox::getService('user.activity')->update($iUserId, 'v');

        if ($aVals['privacy'] == '4') {
            Phpfox::getService('privacy.process')->add('v', $iId,
                (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
        }

        if (!Phpfox::getUserParam('v.pf_video_approve_before_publicly') && !empty($sModule) && Phpfox::hasCallback($sModule,
                'onVideoPublished')) {
            Phpfox::callback($sModule . '.onVideoPublished', [
                'video_id' => $iId,
                'item_id' => $aSql['item_id'],
                'user_id' => $aSql['user_id']
            ]);
        }

        // Plugin call
        if ($sPlugin = Phpfox_Plugin::get('video.service_process_addvideo__end')) {
            eval($sPlugin);
        }

        return $iId;
    }

    /**
     * @param $iId
     * @param $aVals
     * @return bool
     */
    public function update($iId, $aVals)
    {
        $aCategories = [];
        if (isset($aVals['category']) && count($aVals['category'])) {
            foreach ($aVals['category'] as $iCategory) {
                if (empty($iCategory)) {
                    continue;
                }
                if (!is_numeric($iCategory)) {
                    continue;
                }
                $aCategories[] = $iCategory;
            }
        }

        $aVideo = db()->select('v.video_id, v.privacy, v.view_id, v.user_id, v.is_featured, v.is_sponsor')
            ->from($this->_sTable, 'v')
            ->leftJoin(Phpfox::getT('video_text'), 'vt', 'vt.video_id = v.video_id')
            ->where('v.video_id = ' . (int)$iId)
            ->execute('getRow');

        if (!isset($aVideo['video_id'])) {
            return Phpfox_Error::set(_p('unable_to_find_the_video_you_plan_to_edit'));
        }

        Phpfox::getService('ban')->checkAutomaticBan(isset($aVals['title']) ? $aVals['title'] : '' . isset($aVals['text']) ? $aVals['text'] : '');

        if (($aVideo['user_id'] == Phpfox::getUserId() && user('pf_video_edit_own_video')) || user('pf_video_edit_all_video')) {
            if (Phpfox::getLib('parse.format')->isEmpty($aVals['title'])) {
                return Phpfox_Error::set(_p('provide_a_title_for_this_video'));
            }

            $aSql = array(
                'title' => $this->preParse()->clean($aVals['title'], 255)
            );

            if (isset($aVals['privacy'])) {
                $aSql['privacy'] = (int)$aVals['privacy'];
            } else {
                $aVals['privacy'] = $aVideo['privacy'];
            }

            if (isset($aVals['parent_user_id'])) {
                $aSql['parent_user_id'] = $aVals['parent_user_id'];
            }

            /*
             *  Remove video's thumbnail when
             *  1. upload new thumbnail, remove old.
             *  2. just remove thumbnail.
            */
            if (!empty($aVals['temp_file']) || !empty($aVals['remove_photo'])) {
                $this->deleteThumbnail($aVideo);
                $aSql['image_path'] = null;
                $aSql['image_server_id'] = 0;
            }

            // if user edit video's thumbnail image
            if (!empty($aVals['temp_file'])) {
                // get image from temp file
                $aFile = Phpfox::getService('core.temp-file')->get($aVals['temp_file']);
                if (!empty($aFile)) {
                    if (!Phpfox::getService('user.space')->isAllowedToUpload($aVideo['user_id'], $aFile['size'])) {
                        Phpfox::getService('core.temp-file')->delete($aVals['temp_file'], true);

                        return false;
                    }
                    $aSql['image_path'] = $aFile['path'];
                    $aSql['image_server_id'] = $aFile['server_id'];
                    Phpfox::getService('core.temp-file')->delete($aVals['temp_file']);
                }
            }

            /*
            if(!empty($_FILES['imageUpload']['name'])){
                $maxFileSize = ((int)user('pf_video_max_file_size_photo_upload', 500))/1024;
                $aImage = Phpfox::getLib('file')->load('imageUpload', array(
                    'jpg','gif','png'
                ), $maxFileSize);
                if($aImage)
                {
                    if(!empty($aVideo['image_path'])){
                        $iFileSize = 0;
                        if($aVideo['image_server_id'] == -1) {
                            $aHeaders = get_headers(setting('pf_video_s3_url'). $aVideo['image_path'], true);
                            if (preg_match('/200 OK/i', $aHeaders[0])) {
                                $iFileSize += (int)$aHeaders["Content-Length"];
                            }
                            $sPath = str_replace('.png/frame_0001.png', '', $aVideo['image_path']);
                            $s3 = new \S3(setting('pf_video_s3_key'), setting('pf_video_s3_secret'));
                            foreach ([
                                         '.png/frame_0000.png',
                                         '.png/frame_0001.png',
                                         '.png/frame_0002.png'
                                     ] as $ext) {
                                $s3->deleteObject(setting('pf_video_s3_bucket'), $sPath . $ext);
                            }
                        }
                        else {
                            $aSizes = array('_500', '_1024'); // Sizes now defined
                            if(strpos($aVideo['image_path'], 'video/') !== 0) { // support V3 video
                                $aVideo['image_path'] = 'video/' . $aVideo['image_path'];
                                $aSizes = array('_120');
                            }
                            // Foreach size
                            foreach ($aSizes as $sSize) {
                                // Get the possible image
                                $sImage = Phpfox::getParam('core.dir_pic') . sprintf($aVideo['image_path'], $sSize);
                                // if the image exists
                                if ($aVideo['image_server_id'] == 0 && file_exists($sImage)) {
                                    $iFileSize += filesize($sImage);
                                } else {
                                    if (Phpfox::getParam('core.allow_cdn') && $aVideo['image_server_id'] > 0) {
                                        // Get the file size stored when the photo was uploaded
                                        $sTempUrl = Phpfox::getLib('cdn')->getUrl(str_replace(Phpfox::getParam('core.dir_pic'), Phpfox::getParam('core.url_pic'), $sImage), $aVideo['image_server_id']);
                                        $aHeaders = get_headers($sTempUrl, true);
                                        if (preg_match('/200 OK/i', $aHeaders[0])) {
                                            $iFileSize += (int)$aHeaders["Content-Length"];
                                        }
                                    }
                                }
                                // Do not check if filesize is greater than 0, or CDN file will not be deleted
                                Phpfox::getLib('file')->unlink($sImage);
                            }
                        }

                        if ($iFileSize > 0) {
                            Phpfox::getService('user.space')->update($aVideo['user_id'], 'photo', $iFileSize, '-');
                        }
                    }
                    $sPicStorage = Phpfox::getParam('core.dir_pic') . 'video/';

                    if (!is_dir($sPicStorage)) {
                        @mkdir($sPicStorage, 0777, 1);
                        @chmod($sPicStorage, 0777);
                    }
                    $sNewFileName = Phpfox::getLib('file')->upload('imageUpload', $sPicStorage, PHPFOX_TIME);
                    Phpfox::getLib('image')->createThumbnail($sPicStorage.sprintf($sNewFileName,''), $sPicStorage.sprintf($sNewFileName,'_'. 500), 500, 500);
                    Phpfox::getLib('image')->createThumbnail($sPicStorage.sprintf($sNewFileName,''), $sPicStorage.sprintf($sNewFileName,'_'. 1024), 1024, 1024);

                    $iPhotoSize = 0;
                    if (file_exists($sPicStorage.sprintf($sNewFileName, '_' . 500)))
                        $iPhotoSize += filesize($sPicStorage.sprintf($sNewFileName, '_' . 500));
                    if (file_exists($sPicStorage.sprintf($sNewFileName, '_' . 1024)))
                        $iPhotoSize += filesize($sPicStorage.sprintf($sNewFileName, '_' . 1024));

                    if ($iPhotoSize > 0) {
                        Phpfox::getService('user.space')->update($aVideo['user_id'], 'photo', $iFileSize);
                    }
                    $aSql['image_path'] = 'video/'.$sNewFileName;
                    $aSql['image_server_id'] = Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID');
                    $sTempFile = $sPicStorage . sprintf($sNewFileName, '');
                    if (file_exists($sTempFile))
                    {
                        @unlink($sTempFile);
                        if(Phpfox::getParam('core.allow_cdn') && $aVideo['image_server_id'] > 0)
                        {
                            Phpfox::getLib('cdn')->remove($sTempFile);
                        }
                    }
                }
                else {
                    return false;
                }
            }*/
            // update video
            db()->update($this->_sTable, $aSql, 'video_id = ' . $iId);

            db()->update(Phpfox::getT('video_text'), array(
                'text' => (empty($aVals['text']) ? null : $this->preParse()->clean($aVals['text'])),
                'text_parsed' => (empty($aVals['text']) ? null : $this->preParse()->prepare($aVals['text']))
            ), 'video_id = ' . $iId
            );

            db()->delete(Phpfox::getT('video_category_data'), 'video_id = ' . (int)$iId);
            if (count($aCategories)) {
                foreach ($aCategories as $iCategoryId) {
                    db()->insert(Phpfox::getT('video_category_data'),
                        array('video_id' => $iId, 'category_id' => $iCategoryId));
                }
            }

            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->update('v', $iId, $aVals['privacy'],
                0) : null);

            if (Phpfox::isModule('privacy')) {
                if ($aVals['privacy'] == '4') {
                    Phpfox::getService('privacy.process')->update('v', $iId,
                        (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
                } else {
                    Phpfox::getService('privacy.process')->delete('v', $iId);
                }
            }
            Phpfox::getService('feed.process')->clearCache('v', $iId);

            if ($aVideo['is_sponsor'] == 1) {
                $this->cache()->remove('video_sponsored');
            }
            if ($aVideo['is_featured'] == 1) {
                $this->cache()->remove('video_featured');
            }

            if ($sPlugin = Phpfox_Plugin::get('video.service_process_update_1')) {
                eval($sPlugin);
            }

            return true;
        }

        return Phpfox_Error::set(_p('invalid_permissions'));
    }

    /**
     * Delete video's thumbnail
     * @param $aVideo
     * @return bool
     */
    public function deleteThumbnail($aVideo)
    {
        if (empty($aVideo['image_path'])) {
            return false;
        }

        if ($aVideo['image_server_id'] == -1) {
            // delete thumbnail image from Amazon S3
            $this->deleteThumbnailFromS3($aVideo);
        } else {
            $this->deleteThumbnailFromServer($aVideo);
        }

        return true;
    }

    /**
     * Delete video's thumbnails from server/cdn
     * @param $aVideo
     * @return bool
     */
    public function deleteThumbnailFromServer($aVideo)
    {
        if (!$aVideo['image_path']) {
            return true;
        }

        $aParams = Phpfox::getService('v.video')->getUploadPhotoParams();
        $aParams['type'] = 'v_edit_video';
        $aParams['path'] = $aVideo['image_path'];
        $aParams['user_id'] = $aVideo['user_id'];
        $aParams['update_space'] = true;
        $aParams['server_id'] = $aVideo['image_server_id'];

        return Phpfox::getService('user.file')->remove($aParams);
    }

    /**
     * Delete thumbnails from AmazonS3
     * @param $aVideo
     * @return bool
     */
    public function deleteThumbnailFromS3($aVideo)
    {
        if (!setting('pf_video_s3_url', false)) {
            return false;
        }

        $aHeaders = get_headers(setting('pf_video_s3_url') . $aVideo['image_path'], true);
        if (preg_match('/200 OK/i', $aHeaders[0])) {
            $iFileSize = (int)$aHeaders["Content-Length"];
            $iFileSize > 0 && Phpfox::getService('user.space')->update($aVideo['user_id'], 'photo', $iFileSize, '-');
        }
        $sPath = str_replace('.png/frame_0001.png', '', $aVideo['image_path']);
        $s3 = new \S3(setting('pf_video_s3_key'), setting('pf_video_s3_secret'));
        foreach ([
                     '.png/frame_0000.png',
                     '.png/frame_0001.png',
                     '.png/frame_0002.png'
                 ] as $ext) {
            $s3->deleteObject(setting('pf_video_s3_bucket'), $sPath . $ext);
        }

        return true;
    }

    /**
     * @param int $iId
     * @param int $iType
     *
     * @return bool|mixed
     */
    public function sponsor($iId, $iType)
    {
        if (!user('v.can_sponsor_v') && !user('v.can_purchase_sponsor') && !defined('PHPFOX_API_CALLBACK')) {
            return Phpfox_Error::set(_p('hack_attempt'));
        }

        $iType = (int)$iType;
        if ($iType != 1 && $iType != 0) {
            return false;
        }
        db()->query("UPDATE " . Phpfox::getT('video') . " SET is_sponsor = " . $iType . " WHERE video_id = " . (int)$iId);
        $this->cache()->remove('video_sponsored');
        if ($sPlugin = Phpfox_Plugin::get('video.service_process_sponsor__end')) {
            return eval($sPlugin);
        }

        return true;
    }

    /**
     * @param null $iId
     * @param string $sView
     * @param int $iUserId
     * @param bool $bShowError
     * @param bool $bForce
     * @return bool|string
     * @throws \Exception
     */
    public function delete($iId = null, $sView = '', $iUserId = 0, $bShowError = true, $bForce = false)
    {
        $aVideo = db()->select('v.video_id, v.module_id, v.item_id, v.user_id, v.destination, v.image_path, v.server_id, v.image_server_id, v.is_sponsor, v.is_featured')
            ->from($this->_sTable, 'v')
            ->where(($iId === null ? 'v.view_id = 1 AND v.user_id = ' . Phpfox::getUserId() : 'v.video_id = ' . (int)$iId))
            ->execute('getSlaveRow');

        if (!isset($aVideo['video_id'])) {
            if ($bShowError && !$bForce) {
                return Phpfox_Error::set(_p('unable_to_find_the_video_you_plan_to_delete'));
            } else {
                return false;
            }
        }

        // check current page to redirect when delete success
        $sParentReturn = true;
        if ($aVideo['module_id'] == 'pages' && Phpfox::getService('pages')->isAdmin($aVideo['item_id'])) {
            $sParentReturn = Phpfox::getService('pages')->getUrl($aVideo['item_id']) . 'video/';
            $bForce = true; // is owner of page
        } elseif ($aVideo['module_id'] == 'groups' && Phpfox::getService('groups')->isAdmin($aVideo['item_id'])) {
            $sParentReturn = Phpfox::getService('groups')->getUrl($aVideo['item_id']) . 'video/';
            $bForce = true; // is owner of group
        } elseif ($aVideo['module_id'] == 'user' && Phpfox::getUserId() == $aVideo['item_id']) {
            $sParentReturn = Phpfox::getService('user')->getLink($aVideo['item_id']);
            $bForce = true; // is owner of wall
        }
        if (!empty($sView)) {
            if ($sView != 'play' && $sView != 'profile') {
                $sParentReturn = Phpfox_Url::instance()->makeUrl('video', ['view' => $sView]);
            } elseif ($sView == 'profile' && $iUserId) {
                $sParentReturn = Phpfox::getService('user')->getLink($iUserId) . 'video/';
            }

        }

        // check permission delete video
        if (Phpfox::isUser(true) && (
                ($aVideo['user_id'] == Phpfox::getUserId() && user('pf_video_delete_own_video'))
                || user('pf_video_delete_all_video')
                || $bForce
            )
        ) {

            (Phpfox::isModule('comment') ? Phpfox::getService('comment.process')->deleteForItem(null,
                $aVideo['video_id'], 'v') : null);
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('v', $aVideo['video_id']) : null);
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('v_comment',
                $aVideo['video_id']) : null);

            (Phpfox::isModule('like') ? Phpfox::getService('like.process')->delete('v',(int) $aVideo['video_id'], 0, true) : null);
            (Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->deleteAllOfItem(['v_like', 'v_approved', 'v_ready', 'v_newItem_wall', 'v_newItem_pages'],(int) $aVideo['video_id']) : null);

            db()->delete(Phpfox::getT('video'), 'video_id = ' . $aVideo['video_id']);
            db()->delete(Phpfox::getT('video_category_data'), 'video_id = ' . $aVideo['video_id']);
            db()->delete(Phpfox::getT('video_text'), 'video_id = ' . $aVideo['video_id']);
            db()->delete(Phpfox::getT('video_embed'), 'video_id = ' . $aVideo['video_id']);
            db()->delete(Phpfox::getT('track'), 'item_id = ' . (int)$aVideo['video_id'] . ' AND type_id="v"');

            // Update user activity
            Phpfox::getService('user.activity')->update($aVideo['user_id'], 'v', '-');
            // remove images
            if (!empty($aVideo['image_path'])) {
                $iFileSize = 0;
                if ($aVideo['image_server_id'] == -1) {
                    $aHeaders = get_headers(setting('pf_video_s3_url') . $aVideo['image_path'], true);
                    if (preg_match('/200 OK/i', $aHeaders[0])) {
                        $iFileSize += (int)$aHeaders["Content-Length"];
                    }
                    $sPath = str_replace('.png/frame_0001.png', '', $aVideo['image_path']);
                    $s3 = new \S3(setting('pf_video_s3_key'), setting('pf_video_s3_secret'));
                    foreach (['.png/frame_0000.png', '.png/frame_0001.png', '.png/frame_0002.png'] as $ext) {
                        $s3->deleteObject(setting('pf_video_s3_bucket'), $sPath . $ext);
                    }
                } else {
                    $aSizes = array('_500', '_1024'); // Sizes now defined
                    if (strpos($aVideo['image_path'], 'video/') !== 0) { // support V3 video
                        $aVideo['image_path'] = 'video/' . $aVideo['image_path'];
                        $aSizes = array('_120');
                    }
                    // Foreach size
                    foreach ($aSizes as $sSize) {
                        // Get the possible image
                        $sImage = Phpfox::getParam('core.dir_pic') . sprintf($aVideo['image_path'], $sSize);
                        // if the image exists
                        if ($aVideo['image_server_id'] == 0 && file_exists($sImage)) {
                            $iFileSize += filesize($sImage);
                        } else {
                            if (Phpfox::getParam('core.allow_cdn') && $aVideo['image_server_id'] > 0) {
                                // Get the file size stored when the photo was uploaded
                                $sTempUrl = Phpfox::getLib('cdn')->getUrl(str_replace(Phpfox::getParam('core.dir_pic'),
                                    Phpfox::getParam('core.url_pic'), $sImage), $aVideo['image_server_id']);
                                $aHeaders = get_headers($sTempUrl, true);
                                if (preg_match('/200 OK/i', $aHeaders[0])) {
                                    $iFileSize += (int)$aHeaders["Content-Length"];
                                }
                            }
                        }
                        // Do not check if filesize is greater than 0, or CDN file will not be deleted
                        Phpfox::getLib('file')->unlink($sImage);
                    }
                }

                if ($iFileSize > 0) {
                    Phpfox::getService('user.space')->update($aVideo['user_id'], 'photo', $iFileSize, '-');
                }
            }
            // remove videos
            if (!empty($aVideo['destination'])) {
                $iFileSize = 0;
                if ($aVideo['server_id'] == -1) {
                    $aHeaders = get_headers(setting('pf_video_s3_url') . $aVideo['destination'], true);
                    if (preg_match('/200 OK/i', $aHeaders[0])) {
                        $iFileSize += (int)$aHeaders["Content-Length"];
                    }
                    $sPath = str_replace('.mp4', '', $aVideo['destination']);
                    $s3 = new \S3(setting('pf_video_s3_key'), setting('pf_video_s3_secret'));
                    foreach (['.webm', '-low.mp4', '.ogg', '.mp4'] as $ext) {
                        $s3->deleteObject(setting('pf_video_s3_bucket'), $sPath . $ext);
                    }
                } else {
                    $sPathVideo = Phpfox::getParam('core.dir_file') . 'video/' . sprintf($aVideo['destination'], '');
                    if ($aVideo['server_id'] == 0 && file_exists($sPathVideo)) {
                        $iFileSize += filesize($sPathVideo);
                    } else {
                        if (Phpfox::getParam('core.allow_cdn') && $aVideo['server_id'] > 0) {
                            $sTempUrl = Phpfox::getLib('cdn')->getUrl(Phpfox::getParam('core.url_file') . 'video/' . sprintf($aVideo['destination'],
                                    ''), $aVideo['server_id']);
                            $aHeaders = get_headers($sTempUrl, true);
                            if (preg_match('/200 OK/i', $aHeaders[0])) {
                                $iFileSize += (int)$aHeaders["Content-Length"];
                            }
                        }
                    }
                    Phpfox::getLib('file')->unlink($sPathVideo);
                }

                if ($iFileSize > 0) {
                    Phpfox::getService('user.space')->update($aVideo['user_id'], 'video', $iFileSize, '-');
                }
            }

            if ($sPlugin = Phpfox_Plugin::get('video.service_process_delete_1')) {
                eval($sPlugin);
            }

            if ($aVideo['is_sponsor'] == 1) {
                // close sponsorship
                Phpfox::getService('ad.process')->closeSponsorItem('v', $aVideo['video_id']);
                // clear cache
                $this->cache()->remove('video_sponsored');
            }
            if ($aVideo['is_featured'] == 1) {
                $this->cache()->remove('video_featured');
            }

            return $sParentReturn;
        }

        return Phpfox_Error::set(_p('invalid_permissions'));
    }

    /**
     * @param $iId
     * @param $iType
     * @return bool
     */
    public function feature($iId, $iType)
    {
        Phpfox::isUser(true);
        user('pf_video_feature', 0, null, true);
        db()->update($this->_sTable, array('is_featured' => ($iType ? '1' : '0')),
            'video_id = ' . (int)$iId);
        if ($iType) {
            $aVideo = Phpfox::getService('v.video')->getInfoForNotification($iId);
            $iSenderUserId = $aVideo['user_id'];
            if ((int)Phpfox::getUserId() > 0) {
                $iSenderUserId = Phpfox::getUserId();
            }
            Phpfox::getService("notification.process")->add("v_featured", $iId, $aVideo['user_id'], $iSenderUserId);
        }
        $this->cache()->remove('video_featured');

        return true;
    }

    /**
     * @param $iId
     * @param bool $bShowError
     * @return bool
     */
    public function approve($iId, $bShowError = true)
    {
        Phpfox::isUser(true);
        user('pf_video_approve', '0', null, true);

        $aVideo = db()->select('v.video_id, v.module_id, v.item_id, v.parent_user_id, v.view_id, v.title, v.privacy, v.privacy_comment, v.user_id')
            ->from($this->_sTable, 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.video_id = ' . (int)$iId)
            ->execute('getRow');

        if (!isset($aVideo['video_id'])) {
            if ($bShowError) {
                return Phpfox_Error::set(_p('unable_to_find_the_video_you_want_to_approve'));
            } else {
                return false;
            }
        }

        if ($aVideo['view_id'] == '0') {
            return false;
        }

        db()->update($this->_sTable, array('view_id' => '0', 'time_stamp' => PHPFOX_TIME),
            'video_id = ' . $aVideo['video_id']);

        if (Phpfox::isModule('notification')) {
            Phpfox::getService('notification.process')->add('v_approved', $aVideo['video_id'], $aVideo['user_id']);
        }

        (($sPlugin = Phpfox_Plugin::get('video.service_process_approve__1')) ? eval($sPlugin) : false);
        // Send the user an email
        $sLink = Phpfox::getLib('url')->permalink('video.play', $aVideo['video_id'], $aVideo['title']);
        Phpfox::getLib('mail')->to($aVideo['user_id'])
            ->subject(array(
                'your_video_has_been_approved_on_site_title',
                array('site_title' => Phpfox::getParam('core.site_title'))
            ))
            ->message(array(
                'your_video_has_been_approved_on_site_title_n_nto_view_this_video_follow_the_link_below_n_a_href',
                array('site_title' => Phpfox::getParam('core.site_title'), 'sLink' => $sLink)
            ))
            ->notification('video_is_approved')
            ->send();

        $aCallback = null;
        if ($aVideo['module_id'] != 'video' && Phpfox::hasCallback($aVideo['module_id'], 'getFeedDetails')) {
            $aCallback = Phpfox::callback($aVideo['module_id'] . '.getFeedDetails', $iId);
        }

        if (Phpfox::isModule('feed') && !defined('PHPFOX_SKIP_FEED_ENTRY')) {
            Phpfox::getService('feed.process')->callback($aCallback)->add('v', $iId, $aVideo['privacy'],
                $aVideo['privacy_comment'],
                ($aCallback === null ? (isset($aVideo['parent_user_id']) ? $aVideo['parent_user_id'] : 0) : $aVideo['item_id']),
                $aVideo['user_id']);
        }

        if (Phpfox::isModule('notification') && $aVideo['module_id'] != 'video' && $aVideo['item_id'] && Phpfox::isModule($aVideo['module_id']) && Phpfox::hasCallback($aVideo['module_id'],
                'addItemNotification')) {
            Phpfox::callback($aVideo['module_id'] . '.addItemNotification', [
                'page_id' => $aVideo['item_id'],
                'item_perm' => 'pf_video.view_browse_videos',
                'item_type' => 'v',
                'item_id' => $iId,
                'owner_id' => $aVideo['user_id'],
                'items_phrase' => _p('videos__l')
            ]);
        }

        if (Phpfox::isModule('notification') && $aVideo['module_id'] == 'user' && $aVideo['item_id']) {
            Phpfox::getService('notification.process')->add('v_newItem_wall', $aVideo['video_id'], $aVideo['item_id'],
                $aVideo['user_id']);
        }

        if (Phpfox::isModule('notification') && $aVideo['module_id'] == 'pages' && $aVideo['item_id'] && Phpfox::isModule('pages')) {
            $aPage = Phpfox::getService('pages')->getPage($aVideo['item_id']);
            Phpfox::getService('notification.process')->add('v_newItem_pages', $aVideo['video_id'], $aPage['user_id'],
                $aVideo['user_id']);
        }

        if ($sPlugin = Phpfox_Plugin::get('video.service_process_approve_1')) {
            eval($sPlugin);
        }

        if (!empty($aVideo['module_id']) && Phpfox::hasCallback($aVideo['module_id'], 'onVideoPublished')) {
            Phpfox::callback($aVideo['module_id'] . '.onVideoPublished', $aVideo);
        }

        // Update user activity
        Phpfox::getService('user.activity')->update($aVideo['user_id'], 'v');

        return true;
    }

    /**
     * @param $sImgUrl
     * @return array
     */
    public function downloadImage($sImgUrl)
    {
        if (!$sImgUrl) {
            return '';
        }
        $sImgUrl = str_replace('dailymotion.com/thumbnail/160x120', 'dailymotion.com/thumbnail/640x360', $sImgUrl);

        $pos = stripos($sImgUrl, ".bmp");
        if ($pos > 0) {
            return $sImgUrl;
        }
        //Check Folder Storage
        $sNewsPicStorage = Phpfox::getParam('core.dir_pic') . 'video';
        if (!is_dir($sNewsPicStorage)) {
            @mkdir($sNewsPicStorage, 0777, 1);
            @chmod($sNewsPicStorage, 0777);
        }

        // Generate Image object and store image to the temp file
        $iToken = rand();
        if (substr($sImgUrl, 0, 17) == '//img.youtube.com') {
            $sImgUrl = 'https:' . $sImgUrl;
        }
        $oImage = \Phpfox::getLib('request')->send($sImgUrl, array(), 'GET');

        if (empty($oImage) && (substr($sImgUrl, 0, 8) == 'https://')) {
            $sImgUrl = 'http://' . substr($sImgUrl, 8);
            $oImage = Phpfox::getLib('request')->send($sImgUrl, array(), 'GET');
        }
        $sTempImage = 'video_temp_thumbnail_' . $iToken . '_' . PHPFOX_TIME;
        \Phpfox::getLib('file')->writeToCache($sTempImage, $oImage);
        // Save image
        $ThumbNail = Phpfox::getLib('file')->getBuiltDir($sNewsPicStorage . PHPFOX_DS) . md5('image_' . $iToken . '_' . PHPFOX_TIME) . '%s.jpg';
        Phpfox::getLib('image')->createThumbnail(PHPFOX_DIR_CACHE . $sTempImage, sprintf($ThumbNail, '_' . 1024), 1024,
            1024);
        Phpfox::getLib('image')->createThumbnail(PHPFOX_DIR_CACHE . $sTempImage, sprintf($ThumbNail, '_' . 500), 500,
            500);
        @unlink(PHPFOX_DIR_CACHE . $sTempImage);

        $iPhotoSize = 0;
        if (file_exists(sprintf($ThumbNail, '_' . 500))) {
            $iPhotoSize += filesize(sprintf($ThumbNail, '_' . 500));
        }
        if (file_exists(sprintf($ThumbNail, '_' . 1024))) {
            $iPhotoSize += filesize(sprintf($ThumbNail, '_' . 1024));
        }

        $sFileName = str_replace(Phpfox::getParam('core.dir_pic'), "", $ThumbNail);
        $sFileName = str_replace("\\", "/", $sFileName);

        // Return logo file
        return array($sFileName, $iPhotoSize);
    }
}
