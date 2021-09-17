<?php

namespace Apps\PHPfox_Videos\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_File;

defined('PHPFOX') or exit('NO DICE!');

class UploadController extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true);
        user('pf_video_share', '1', null, true);
        if (!setting('pf_video_support_upload_video', 1)) {
            return [
                'error' => _p('the_site_does_not_support_upload_videos_from_your_computer')
            ];
        }

        if (empty($_FILES['ajax_upload']['tmp_name'])) {
            switch ($_FILES['ajax_upload']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                    $message = "The uploaded file exceeds the upload_max_filesize (" . ini_get('upload_max_filesize') . ") directive in php.ini";
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $message = "the_uploaded_file_exceeds_the_MAX_FILE_SIZE_directive_that_was_specified_in_the_HTML_form";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $message = "the_uploaded_file_was_only_partially_uploaded";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $message = "no_file_was_uploaded";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $message = "missing_a_temporary_folder";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $message = "failed_to_write_file_to_disk";
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $message = "file_upload_stopped_by_extension";
                    break;

                default:
                    $message = "unknown_upload_error";
                    break;
            }

            http_response_code(400);

            return [
                'error' => _p($message)
            ];
        }

        header('Access-Control-Allow-Origin: *');
        header('Content-Type: text/plain');
        header('Accept-Ranges: bytes');

        $file_size = user('pf_video_file_size', 10);
        $path = PHPFOX_DIR_FILE . 'static' . PHPFOX_DS;
        $sId = md5(uniqid() . Phpfox::getUserId());
        $realName = $sId . '.' . Phpfox_File::instance()->getFileExt($_FILES['ajax_upload']['name']);
        $date = date('y/m/d/');
        $name = $date . $realName;
        $post = [];

        if (!empty($_SERVER['HTTP_X_POST_FORM'])) {
            foreach (explode('&', $_SERVER['HTTP_X_POST_FORM']) as $posts) {
                $part = explode('=', $posts);
                if (empty($part[0])) {
                    continue;
                }
                $post[$part[0]] = (isset($part[1]) ? $part[1] : '');
            }
        }

        if (isset($post['val[callback_module]']) && isset($post['val[callback_item_id]'])) {
            if (Phpfox::isModule($post['val[callback_module]']) &&
                Phpfox::hasCallback($post['val[callback_module]'], 'checkPermission') &&
                !Phpfox::callback($post['val[callback_module]'] . '.checkPermission', $post['val[callback_item_id]'],
                    'pf_video.share_videos')
            ) {
                http_response_code(400);

                return [
                    'error' => _p('you_dont_have_permission_to_share_videos_on_this_page')
                ];
            }
        }

        $ext = '3gp, aac, ac3, ec3, flv, m4f, mov, mj2, mkv, mp4, mxf, ogg, ts, webm, wmv, avi';
        $file = Phpfox_File::instance()->load('ajax_upload', array_map('trim', explode(',', $ext)), $file_size);
        if ($file === false) {
            http_response_code(400);

            return [
                'error' => implode('', Phpfox_Error::get())
            ];
        }

        if (!@move_uploaded_file($_FILES['ajax_upload']['tmp_name'], $path . $realName)) {
            http_response_code(400);

            return [
                'error' => _p('unable_to_upload_file_due_to_a_server_error_or_restriction')
            ];
        }

        $iMethodUpload = setting('pf_video_method_upload');
        if ($iMethodUpload == 1 && setting('pf_video_key')) {
            $bucket = setting('pf_video_s3_bucket');
            $s3 = new \S3(setting('pf_video_s3_key'), setting('pf_video_s3_secret'));
            $s3->putObjectFile($path . $realName, $bucket, $name, \S3::ACL_PUBLIC_READ);
            try {
                $zencoder = new \Services_Zencoder(setting('pf_video_key'));
                $params = [
                    "input" => 's3://' . $bucket . '/' . $name,
                    'notifications' => [
                        'url' => url('/video/callback')
                    ],
                    "outputs" => [
                        [
                            "label" => "mp4 high",
                            'h264_profile' => 'high',
                            'url' => 's3://' . $bucket . '/' . $date . $sId . '.mp4',
                            'public' => true,
                            'thumbnails' => [
                                'label' => 'thumb',
                                'size' => '640x360',
                                'base_url' => 's3://' . $bucket . '/' . $date . $sId . '.png',
                                'number' => 3
                            ]
                        ]
                    ]
                ];

                $encoding_job = $zencoder->jobs->create($params);

                storage()->set('pf_video_' . $encoding_job->id, [
                    'encoding_id' => $encoding_job->id,
                    'video_path' => $date . $sId . '.mp4',
                    'user_id' => Phpfox::getUserId(),
                    'id' => $sId,
                    'ext' => Phpfox_File::instance()->getFileExt($_FILES['ajax_upload']['name']),
                    'default_image' => $date . $sId . '.png/frame_0001.png'
                ]);

                return [
                    'upload' => true,
                    'id' => $encoding_job->id
                ];

            } catch (\Services_Zencoder_Exception $e) {
                http_response_code(400);

                return [
                    'error' => $e->getMessage()
                ];
            }
        } elseif ($iMethodUpload == 0 && setting('pf_video_ffmpeg_path')) {
            storage()->set('pf_video_' . $sId, [
                'path' => $path . $realName,
                'user_id' => Phpfox::getUserId(),
                'id' => $sId,
                'ext' => Phpfox_File::instance()->getFileExt($_FILES['ajax_upload']['name'])
            ]);

            return [
                'upload' => true,
                'id' => $sId
            ];
        } else {
            return [
                'error' => _p('the_site_does_not_support_upload_videos_from_your_computer')
            ];
        }
    }
}
