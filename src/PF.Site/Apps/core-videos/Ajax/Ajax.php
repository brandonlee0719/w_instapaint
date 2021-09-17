<?php

namespace Apps\PHPfox_Videos\Ajax;

use Phpfox;
use Phpfox_Ajax;
use Phpfox_Cache;
use Phpfox_Mail;

class Ajax extends Phpfox_Ajax
{
    /**
     *
     */
    public function updateActivity()
    {
        Phpfox::getService('v.process')->updateCategoryActivity($this->get('id'), $this->get('active'));
    }

    /**
     *
     */
    public function feature()
    {
        if (Phpfox::getService('v.process')->feature($this->get('video_id'), $this->get('type'))) {
            $iVideoId = $this->get('video_id');
            if ($this->get('type') == '1') {
                $this->call('$("#js_video_unfeature_' . $iVideoId . '").show();');
                $this->call('$("#js_video_feature_' . $iVideoId . '").hide();');
                $this->alert(_p('video_successfully_featured'), null, 300, 100, true);
            } else {
                $this->call('$("#js_video_unfeature_' . $iVideoId . '").hide();');
                $this->call('$("#js_video_feature_' . $iVideoId . '").show();');
                $this->alert(_p('video_successfully_unfeatured'), null, 300, 100, true);
            }
        }
    }

    /**
     *
     */
    public function sponsor()
    {
        if (!Phpfox::isModule('ad')) {
            $this->alert('your_request_is_invalid');
        }
        if (Phpfox::getService('v.process')->sponsor($this->get('video_id'), $this->get('type'))) {
            $iVideoId = $this->get('video_id');
            $aVideo = Phpfox::getService('v.video')->getForEdit($iVideoId);
            if ($this->get('type') == '1') {
                Phpfox::getService('ad.process')->addSponsor(array(
                    'module' => 'v',
                    'item_id' => $iVideoId,
                    'name' => _p('default_campaign_custom_name', ['module' => _p('video'), 'name' => $aVideo['title']])
                ));
                $this->call('$("#js_video_unsponsor_' . $iVideoId . '").show();');
                $this->call('$("#js_video_sponsor_' . $iVideoId . '").hide();');
                $this->alert(_p('video_successfully_sponsored'), null, 300, 100, true);
            } else {
                Phpfox::getService('ad.process')->deleteAdminSponsor('v', $iVideoId);
                $this->call('$("#js_video_unsponsor_' . $iVideoId . '").hide();');
                $this->call('$("#js_video_sponsor_' . $iVideoId . '").show();');
                $this->alert(_p('video_successfully_un_sponsored'), null, 300, 100, true);
            }
            Phpfox::getLib('cache')->removeGroup(['ad', 'betterads']);
        }
    }

    /**
     *
     */
    public function approve()
    {
        if (Phpfox::getService('v.process')->approve($this->get('video_id'))) {
            $iVideoId = $this->get('video_id');
            $this->alert(_p('video_has_been_approved'), null, 300, 100, true);
            if ($this->get('is_detail')) {
                $this->call('window.location.reload();');
            }
            else {
                $sUrl = Phpfox::getLib('url')->makeUrl('video');
                $this->call('if(!$(\'#js_approve_video_message\').length) {$("#js_video_item_' . $iVideoId . '").remove(); var total_pending = parseInt($("#video_pending").html()) - 1; if(total_pending > 0) $("#video_pending").html(total_pending); else window.location.href = "' . $sUrl . '";}');
            }
            $this->hide('#js_approve_video_message');
        }
    }

    /**
     *
     */
    public function moderation()
    {
        Phpfox::isUser(true);
        $sMessage = '';
        switch ($this->get('action')) {
            case 'approve':
                user('pf_video_approve', 0, null, true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('v.process')->approve($iId, false);
                }
                Phpfox::addMessage(_p('video_s_successfully_approved'));
                $this->call('window.location.reload();');
                break;
            case 'delete':
                foreach ((array)$this->get('item_moderate') as $iId) {
                    if (!Phpfox::getService('v.video')->isAdminOfParentItem($iId)) {
                        user('pf_video_delete_all_video', 0, null, true);
                    }
                    Phpfox::getService('v.process')->delete($iId, '', 0, false);
                }
                Phpfox::addMessage(_p('video_s_successfully_deleted'));
                $this->call('window.location.reload();');
                break;
            case 'feature':
                user('pf_video_feature', 0, null, true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('v.process')->feature($iId, 1);
                    $this->call('$("#js_video_feature_' . $iId . '").hide();');
                    $this->call('$("#js_video_unfeature_' . $iId . '").show();');
                }
                $sMessage = _p('video_s_successfully_featured');
                break;
            case 'un-feature':
                user('pf_video_feature', 0, null, true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('v.process')->feature($iId, 0);
                    $this->call('$("#js_video_feature_' . $iId . '").show();');
                    $this->call('$("#js_video_unfeature_' . $iId . '").hide();');
                }
                $sMessage = _p('video_s_successfully_unfeatured');
                break;
        }

        if (!empty($sMessage)) {
            $this->alert($sMessage, _p('moderation'), 300, 150, true);
            $this->hide('.moderation_process');
        }
    }

    /**
     *
     */
    public function validationUrl()
    {
        $sUrl = $this->get('url');
        $sUrl = trim($sUrl);
        if (substr($sUrl, 0, 7) != 'http://' && substr($sUrl, 0, 8) != 'https://') {
            echo json_encode(array(
                'status' => "FAIL",
                'error_message' => _p('please_provide_a_valid_url')
            ));
            exit;
        }
        if ($parsed = Phpfox::getService('link')->getLink($sUrl)) {
            if (empty($parsed['embed_code'])) {
                echo json_encode(array(
                    'status' => "FAIL",
                    'error_message' => _p('unable_to_load_a_video_to_embed')
                ));
                exit;
            }
            $embed_code = str_replace('http://player.vimeo.com/', 'https://player.vimeo.com/', $parsed['embed_code']);
            // check fb
            $regex = "/http(?:s?):\/\/(?:www\.|web\.|m\.)?facebook\.com\/([A-z0-9\.]+)\/videos(?:\/[0-9A-z].+)?\/(\d+)(?:.+)?$/";
            preg_match($regex, $sUrl, $matches);
            if (count($matches) > 2) {
                $code = $matches[2];
                if ($code) {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/$code?fields=description");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
                    $data = curl_exec($ch);
                    curl_close($ch);
                    $aData = json_decode($data, true);
                    if (isset($aData['description'])) {
                        $parsed['description'] = sprintf("%s", $aData['description']);
                    }
                }
            }
            $description = str_replace("<br />", "\r\n", $parsed['description']);
            echo json_encode(array(
                'status' => "SUCCESS",
                'title' => $parsed['title'],
                'description' => $description,
                'embed_code' => $embed_code,
                'default_image' => $parsed['default_image'],
                'duration' => $parsed['duration']
            ));
            exit;
        } else {
            echo json_encode(array(
                'status' => "FAIL",
                'error_message' => _p('we_could_not_find_a_video_there_please_check_the_url_and_try_again')
            ));
        }
        exit;
    }

    public function cancelUpload()
    {
        $pf_video_id = $this->get('pf_video_id');
        $encoding = storage()->get('pf_video_' . $pf_video_id);
        $iMethodUpload = setting('pf_video_method_upload');
        if ($iMethodUpload == 0) {
            $sPath = $encoding->value->path;
            if (file_exists($sPath)) {
                @unlink($sPath);
            }
        } else {
            if (!empty($encoding->value->encoded)) {
                if ($encoding->value->server_id == -1) {
                    $sPath = str_replace('.mp4', '', $encoding->value->video_path);
                    $s3 = new \S3(setting('pf_video_s3_key'), setting('pf_video_s3_secret'));
                    foreach ([
                                 '.webm',
                                 '-low.mp4',
                                 '.ogg',
                                 '.mp4',
                                 '.png/frame_0000.png',
                                 '.png/frame_0001.png',
                                 '.png/frame_0002.png'
                             ] as $ext) {
                        $s3->deleteObject(setting('pf_video_s3_bucket'), $sPath . $ext);
                    }
                }
                if ($encoding->value->image_server_id !== -1) {
                    $aSizes = array('_500', '_1024'); // Sizes now defined
                    foreach ($aSizes as $sSize) {
                        $sImage = Phpfox::getParam('core.dir_pic') . sprintf($encoding->value->image_path, $sSize);
                        Phpfox::getLib('file')->unlink($sImage);
                    }
                }
                storage()->del('pf_video_' . $pf_video_id);
            } else {
                storage()->update('pf_video_' . $pf_video_id, [
                    'cancel_upload' => 1
                ]);
            }
        }
        storage()->del('pf_video_' . $pf_video_id);
    }

    /**
     * @return mixed
     */
    public function shareFeed()
    {
        Phpfox::isUser(true);
        user('pf_video_share', '1', null, true);
        $aVals = $this->get('val');

        if (isset($aVals['callback_module']) && isset($aVals['callback_item_id'])) {
            if (Phpfox::isModule($aVals['callback_module']) &&
                Phpfox::hasCallback($aVals['callback_module'], 'checkPermission') &&
                !Phpfox::callback($aVals['callback_module'] . '.checkPermission', $aVals['callback_item_id'],
                    'pf_video.share_videos')
            ) {
                error(_p('you_dont_have_permission_to_share_videos_on_this'));
            }
        }
        $status = text()->clean($aVals['status_info']);
        if (isset($aVals['pf_video_id'])) {
            if (empty($aVals['pf_video_id'])) {
                error(_p('we_could_not_find_a_video_there_please_try_again'));
            }
            $encoding = storage()->get('pf_video_' . $aVals['pf_video_id']);

            // set owner of video
            $iUserId = 0;
            if (!empty($aVals['callback_item_id']) && !empty($aVals['callback_module']) && Phpfox::isModule($aVals['callback_module'])) {
                if (in_array($aVals['callback_module'], ['pages', 'groups']) && $aVals['custom_pages_post_as_page']) {
                    $iUserId = Phpfox::getService($aVals['callback_module'])->getUserId($aVals['callback_item_id']);
                } elseif (Phpfox::hasCallback($aVals['callback_module'], 'getUserId')) {
                    $iUserId = Phpfox::callback($aVals['callback_module'] . '.getUserId', $aVals['callback_item_id']);
                }
            }
            $iUserId ?: $iUserId = $encoding->value->user_id;

            if (!empty($encoding->value->encoded)) {
                $aVals = array_merge($aVals, array(
                    'text' => '',
                    'status_info' => $status,
                    'is_stream' => 0,
                    'user_id' => $iUserId,
                    'server_id' => $encoding->value->server_id,
                    'path' => $encoding->value->video_path,
                    'ext' => $encoding->value->ext,
                    'default_image' => isset($encoding->value->default_image) ? $encoding->value->default_image : '',
                    'image_path' => isset($encoding->value->image_path) ? $encoding->value->image_path : '',
                    'image_server_id' => $encoding->value->image_server_id,
                    'duration' => $encoding->value->duration,
                    'video_size' => $encoding->value->video_size,
                    'photo_size' => $encoding->value->photo_size,
                    'feed_values' => $aVals
                ));

                $this->extractLocation($aVals, $aVals);

                $iId = Phpfox::getService('v.process')->addVideo($aVals);

                if (Phpfox::isModule('notification')) {
                    Phpfox::getService('notification.process')->add('v_ready', $iId, $encoding->value->user_id,
                        $encoding->value->user_id, true);
                }

                Phpfox_Mail::instance()->to($encoding->value->user_id)
                    ->subject(_p('video_is_ready'))
                    ->message('Your video is ready.<br />' . url('/video/play/' . $iId))
                    ->send();

                $file = PHPFOX_DIR_FILE . 'static/' . $encoding->value->id . '.' . $encoding->value->ext;
                if (file_exists($file)) {
                    unlink($file);
                }

                storage()->del('pf_video_' . $aVals['pf_video_id']);
            } else {
                $iMethodUpload = setting('pf_video_method_upload');
                if ($iMethodUpload == 0 && setting('pf_video_ffmpeg_path')) {
                    $iJobId = \Phpfox_Queue::instance()->addJob('videos_ffmpeg_encode', []);
                    $aStorageData = [
                        'encoding_id' => $iJobId,
                        'id' => $encoding->value->id,
                        'user_id' => $iUserId,
                        'path' => $encoding->value->path,
                        'ext' => $encoding->value->ext,
                        'privacy' => (isset($aVals['privacy']) ? (int)$aVals['privacy'] : 0),
                        'callback_module' => (isset($aVals['callback_module']) ? $aVals['callback_module'] : ''),
                        'callback_item_id' => (isset($aVals['callback_item_id']) ? (int)$aVals['callback_item_id'] : 0),
                        'parent_user_id' => (isset($aVals['parent_user_id']) ? $aVals['parent_user_id'] : 0),
                        'title' => $aVals['title'],
                        'category' => json_encode(isset($aVals['category']) ? $aVals['category'] : []),
                        'text' => isset($aVals['text']) ? $aVals['text'] : '',
                        'status_info' => $status,
                        'feed_values' => json_encode($aVals),
                    ];
                    $this->extractLocation($aVals, $aStorageData);

                    storage()->set('pf_video_' . $iJobId, $aStorageData);
                    storage()->del('pf_video_' . $aVals['pf_video_id']);
                } else {
                    $aStorageData = [
                        'privacy' => (isset($aVals['privacy']) ? (int)$aVals['privacy'] : 0),
                        'callback_module' => (isset($aVals['callback_module']) ? $aVals['callback_module'] : ''),
                        'callback_item_id' => (isset($aVals['callback_item_id']) ? (int)$aVals['callback_item_id'] : 0),
                        'parent_user_id' => isset($aVals['parent_user_id']) ? $aVals['parent_user_id'] : 0,
                        'title' => $aVals['title'],
                        'category' => json_encode([]),
                        'text' => '',
                        'status_info' => $status,
                        'updated_info' => 1,
                        'feed_values' => json_encode($aVals),
                    ];
                    $this->extractLocation($aVals, $aStorageData);
                    storage()->update('pf_video_' . $aVals['pf_video_id'], $aStorageData);
                }
            }

            echo json_encode([
                'run' => '$Core.Video.processAfterSharingVideoInFeed();'
            ]);
        } elseif (!empty($aVals['url'])) {
            $sUrl = trim($aVals['url']);
            if (substr($sUrl, 0, 7) != 'http://' && substr($sUrl, 0, 8) != 'https://') {
                error(_p('please_provide_a_valid_url'));
            }
            $iVideoid = '';
            if ($parsed = Phpfox::getService('link')->getLink($sUrl)) {
                if (empty($parsed['embed_code'])) {
                    error(_p('unable_to_load_a_video_to_embed'));
                }
                $embed_code = str_replace('http://player.vimeo.com/', 'https://player.vimeo.com/',
                    $parsed['embed_code']);
                $aVals['title'] = $parsed['title'];
                $aVals['text'] = str_replace("<br />", "\r\n", $parsed['description']);
                $aVals['status_info'] = $status;
                $aVals['embed_code'] = $embed_code;
                $aVals['default_image'] = $parsed['default_image'];
                $this->extractLocation($aVals, $aVals);

                $iVideoid = Phpfox::getService('v.process')->addVideo($aVals);
            } else {
                error(_p('we_could_not_find_a_video_there_please_check_the_url_and_try_again'));
            }
            if (user('pf_video_approve_before_publicly')) {
                echo json_encode([
                    'run' => 'window.location.href = "' . Phpfox::getLib('url')->permalink('video.play', $iVideoid,
                            $aVals['title']) . '";'
                ]);
            } else {
                echo json_encode([
                    'run' => 'location.reload();'
                ]);
            }
        } else {
            return error(_p('we_could_not_find_a_video_there_please_try_again'));
        }
        exit;
    }

    public function extractLocation($aVals, &$aReturn)
    {
        $aReturn['location_name'] = (!empty($aVals['location']['name'])) ? Phpfox::getLib('parse.input')->clean($aVals['location']['name']) : null;
        if ((!empty($aVals['location']['latlng']))) {
            $aMatch = explode(',', $aVals['location']['latlng']);
            $aMatch['latitude'] = floatval($aMatch[0]);
            $aMatch['longitude'] = floatval($aMatch[1]);
            $aReturn['location_latlng'] = json_encode(array(
                'latitude' => $aMatch['latitude'],
                'longitude' => $aMatch['longitude']
            ));
        } else {
            $aReturn['location_latlng'] = null;
        }
    }
}
