<?php

namespace Apps\PHPfox_Videos\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Mail;

defined('PHPFOX') or exit('NO DICE!');

class CallbackController extends Phpfox_Component
{
    public function process()
    {
        $notification = json_decode(trim(file_get_contents('php://input')), true);

        if (isset($notification['job']) && isset($notification['job']['state'])) {
            if ($notification['job']['state'] == 'finished') {
                $encoding = storage()->get('pf_video_' . $notification['job']['id']);
                if (empty($encoding->value->cancel_upload)) {
                    $iDuration = 0;
                    $iVideoSize = 0;
                    $iPhotoSize = 0;
                    if (isset($notification['outputs'][0])) {
                        $iDuration = (int)($notification['outputs'][0]['duration_in_ms'] / 1000);
                        $iVideoSize = (int)($notification['outputs'][0]['file_size_in_bytes']);
                        if (isset($notification['outputs'][0]['thumbnails'][0]['images'][1])) {
                            $iPhotoSize = (int)($notification['outputs'][0]['thumbnails'][0]['images'][1]['file_size_bytes']);
                        }
                    }
                    if (!empty($encoding->value->updated_info)) {
                        $aVals = array(
                            'privacy' => $encoding->value->privacy,
                            'callback_module' => $encoding->value->callback_module,
                            'callback_item_id' => $encoding->value->callback_item_id,
                            'parent_user_id' => $encoding->value->parent_user_id,
                            'title' => $encoding->value->title,
                            'category' => json_decode($encoding->value->category),
                            'text' => $encoding->value->text,
                            'status_info' => $encoding->value->status_info,
                            'is_stream' => 0,
                            'user_id' => $encoding->value->user_id,
                            'server_id' => -1,
                            'path' => $encoding->value->video_path,
                            'ext' => $encoding->value->ext,
                            'default_image' => $encoding->value->default_image,
                            'image_server_id' => -1,
                            'duration' => $iDuration,
                            'video_size' => $iVideoSize,
                            'photo_size' => $iPhotoSize,
                            'feed_values' => isset($encoding->value->feed_values) ? json_decode($encoding->value->feed_values) : [],
                            'location_name' => $encoding->value->location_name,
                            'location_latlng' => $encoding->value->location_latlng
                        );
                        if (!defined('PHPFOX_FEED_NO_CHECK')) {
                            define('PHPFOX_FEED_NO_CHECK', true);
                        }
                        $iId = Phpfox::getService('v.process')->addVideo($aVals);

                        if (Phpfox::isModule('notification')) {
                            Phpfox::getService('notification.process')->add('v_ready', $iId, $encoding->value->user_id,
                                $encoding->value->user_id, true);
                        }

                        Phpfox_Mail::instance()->to($encoding->value->user_id)
                            ->subject(_p('video_is_ready'))
                            ->message('Your video is ready.<br />' . url('/video/play/' . $iId))
                            ->send();
                        storage()->del('pf_video_' . $notification['job']['id']);
                    } else {
                        storage()->update('pf_video_' . $notification['job']['id'], [
                            'encoded' => 1,
                            'server_id' => -1,
                            'image_server_id' => -1,
                            'duration' => $iDuration,
                            'video_size' => $iVideoSize,
                            'photo_size' => $iPhotoSize
                        ]);
                    }
                } else {
                    $sVideoPath = str_replace('.mp4', '', $encoding->value->video_path);
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
                        $s3->deleteObject(setting('pf_video_s3_bucket'), $sVideoPath . $ext);
                    }
                    storage()->del('pf_video_' . $notification['job']['id']);
                }
                $file = PHPFOX_DIR_FILE . 'static/' . $encoding->value->id . '.' . $encoding->value->ext;
                if (file_exists($file)) {
                    @unlink($file);
                }
            }
        }
        exit;
    }
}
