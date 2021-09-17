<?php

namespace Apps\PHPfox_Videos\Job;

use Core\Queue\JobAbstract;
use Phpfox;
use Phpfox_Mail;

/**
 * Class Convert
 *
 * @package Apps\PHPfox_Videos\Job
 */
class  Encode extends JobAbstract
{
    /**
     * @inheritdoc
     */
    public function perform()
    {
        $encoding = storage()->get('pf_video_' . $this->getJobId());
        $aUser = Phpfox::getService('user')->getUser($encoding->value->user_id);
        Phpfox::getService('user.auth')->setUser($aUser);
        $sPath = $encoding->value->path;
        $sExt = $encoding->value->ext;
        $sTitle = $encoding->value->id;
        $aConverts = $this->_convertVideo($sPath, $sExt, $sTitle);
        if ($aConverts && $aConverts['status'] == 1) {
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
                'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
                'path' => $aConverts['video_path'],
                'ext' => $sExt,
                'image_path' => $aConverts['image_path'],
                'image_server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
                'duration' => $aConverts['duration'],
                'video_size' => $aConverts['video_size'],
                'photo_size' => $aConverts['photo_size'],
                'feed_values' => isset($encoding->value->feed_values) ? json_decode($encoding->value->feed_values) : [],
                'location_name' => $encoding->value->location_name,
                'location_latlng' => $encoding->value->location_latlng
            );
            if (!defined('PHPFOX_FEED_NO_CHECK')) {
                define('PHPFOX_FEED_NO_CHECK', true);
            }

            // try to reconnect for a long task.
            Phpfox::getLib('database')->reconnect();

            $iId = Phpfox::getService('v.process')->addVideo($aVals);

            if (Phpfox::isModule('notification')) {
                Phpfox::getService('notification.process')->add('v_ready', $iId, $encoding->value->user_id,
                    $encoding->value->user_id, true);
            }

            Phpfox_Mail::instance()->to($encoding->value->user_id)
                ->subject(_p('video_is_ready'))
                ->message(_p('your_video_is_ready') . '.<br />' . url('/video/play/' . $iId))
                ->send();
            storage()->del('pf_video_' . $this->getJobId());
        }
        $this->delete();
    }

    /**
     * @param $videoPath
     * @param $sExt
     * @param $sTitle
     * @return array
     */
    private function _convertVideo($videoPath, $sExt, $sTitle)
    {
        if (empty($videoPath)) {
            echo _p('argument_was_not_a_valid_video');

            return [];
        }
        // Make sure FFMPEG path is set
        $ffmpeg_path = setting('pf_video_ffmpeg_path');
        if (!$ffmpeg_path) {
            echo _p('ffmpeg_not_configured');

            return [];
        }
        // Make sure FFMPEG can be run
        if (!@file_exists($ffmpeg_path) || !@is_executable($ffmpeg_path)) {
            $output = null;
            $return = null;
            exec($ffmpeg_path . ' -version', $output, $return);
            if ($return > 0) {
                echo _p('ffmpeg_found_but_is_not_executable');

                return [];
            }
        }

        // Check we can execute
        if (!function_exists('shell_exec')) {
            echo _p('unable_to_execute_shell_commands_using_shell_exec_the_function_is_disabled');

            return [];
        }

        // Check the video directory
        $tmpDir = PHPFOX_DIR_FILE . 'video' . PHPFOX_DS;
        if (!is_dir($tmpDir)) {
            if (!mkdir($tmpDir, 0777, true)) {
                echo _p('video_directory_did_not_exist_and_could_not_be_created');

                return [];
            }
        }
        if (!is_writable($tmpDir)) {
            echo _p('video_directory_is_not_writable');

            return [];
        }
        if (!file_exists($videoPath)) {
            echo _p('could_not_pull_to_temporary_file');

            return [];
        }
        // Get rotate
        $ffprobe = str_replace('ffmpeg', 'ffprobe', $ffmpeg_path);
        $cmd = $ffprobe . " " . $videoPath . " -show_streams 2>/dev/null";
        $result = shell_exec($cmd);
        $orientation = 0;
        if (strpos($result, 'TAG:rotate') !== false) {
            $result = explode("\n", $result);
            foreach ($result as $line) {
                if (strpos($line, 'TAG:rotate') !== false) {
                    $stream_info = explode("=", $line);
                    $orientation = $stream_info[1];
                }
            }
        }
        $iToken = rand();
        $outputPath = PHPFOX_DIR_FILE . 'static' . PHPFOX_DS . $iToken . '_' . PHPFOX_TIME . '_vconvert.mp4';
        $thumbTempPath = PHPFOX_DIR_FILE . 'static' . PHPFOX_DS . $iToken . '_' . PHPFOX_TIME . '_vthumb_large.jpg';

        //Convert to Mp4 (h264 - HTML5, mpeg4 - IOS)
        $videoCommand = $ffmpeg_path . ' '
            . '-i ' . escapeshellarg($videoPath) . ' '
            . '-ab 64k' . ' '
            . '-ar 44100' . ' '
            . '-q:v 5' . ' '
            . '-r 25' . ' ';

        $videoCommand .= '-vcodec libx264' . ' '
            . '-acodec aac' . ' '
            . '-strict experimental' . ' '
            . '-preset fast' . ' '
            . '-f mp4' . ' ';

        // Add rotate command
        if ($orientation > 0) {
            $transpose = 1;
            switch ($orientation) {
                case 90 :
                    $transpose = 1;
                    break;

                case 180 :
                    $transpose = 3;
                    break;

                case 270 :
                    $transpose = 2;
                    break;
            }
            $h = '';
            if (strtolower($sExt) == '3gp' || strtolower($sExt) == '3gpp') {
                $h = '-s 352x288';
            }
            if ($transpose == 3) {
                $videoCommand .= '-vf "vflip,hflip' . '" ' . $h . ' -b 2000k -metadata:s:v:0 rotate=0 ';
            } else {
                $videoCommand .= '-vf "transpose=' . $transpose . '" ' . $h . ' -b 2000k -metadata:s:v:0 rotate=0 ';
            }
        }

        $videoCommand .=
            '-y ' . escapeshellarg($outputPath) . ' '
            . '2>&1';
        // Prepare output header
        $output = PHP_EOL;
        $output .= $videoPath . PHP_EOL;
        $output .= $outputPath . PHP_EOL;
        // Execute video encode command
        $videoOutput = $output . $videoCommand . PHP_EOL . shell_exec($videoCommand);
        // Check for failure

        $success = true;
        $status = 0;
        // Unsupported format
        if (preg_match('/Unknown format/i', $videoOutput) || preg_match('/Unsupported codec/i',
                $videoOutput) || preg_match('/patch welcome/i', $videoOutput) || preg_match('/Audio encoding failed/i',
                $videoOutput) || !is_file($outputPath) || filesize($outputPath) <= 0) {
            $success = false;
            $status = 3;
        } // This is for audio files
        else {
            if (preg_match('/video:0kB/i', $videoOutput)) {
                $success = false;
                $status = 5;
            }
        }
        $aVals = array('status' => $status);
        if (!$success) {
            try {
                if ($status == 3) {
                    $notificationMessage = _p('your_video_conversion_failed_video_format_is_not_supported_by_ffmpeg');
                    echo $notificationMessage;
                } elseif ($status == 5) {
                    $notificationMessage = _p('your_video_conversion_failed_audio_files_are_not_supported');
                    echo $notificationMessage;
                } else {
                    $exceptionMessage = _p('unknown_encoding_error');
                    echo $exceptionMessage;
                }
            } catch (\Exception $e) {
            }
        } else {
            // Get duration of the video to caculate where to get the thumbnail
            if (preg_match('/Duration:\s+(.*?)[.]/i', $videoOutput, $matches)) {
                list($hours, $minutes, $seconds) = preg_split('[:]', $matches[1]);
                $duration = ceil($seconds + ($minutes * 60) + ($hours * 3600));
            } else {
                $duration = 0;
            }

            $aVals['duration'] = $duration;
            // Fetch where to take the thumbnail
            $thumb_splice = $duration / 2;

            // Thumbnail proccess command
            $thumbCommand = $ffmpeg_path . ' ' . '-i ' . escapeshellarg($outputPath) . ' ' . '-f image2' . ' ' . '-ss ' . $thumb_splice . ' ' . '-vframes ' . '1' . ' ' . '-v 2' . ' ' . '-y ' . escapeshellarg($thumbTempPath) . ' ' . '2>&1';

            // Process thumbnail
            $thumbOutput = $output . $thumbCommand . PHP_EOL . shell_exec($thumbCommand);

            // Check output message for success
            $thumbSuccess = true;
            if (preg_match('/video:0kB/i', $thumbOutput)) {
                $thumbSuccess = false;
            }
            // Resize thumbnail
            if ($thumbSuccess) {
                try {
                    if (is_file($thumbTempPath)) {
                        $sNewsPicStorage = Phpfox::getParam('core.dir_pic') . 'video';
                        if (!is_dir($sNewsPicStorage)) {
                            @mkdir($sNewsPicStorage, 0777, 1);
                            @chmod($sNewsPicStorage, 0777);
                        }
                        $ThumbNail = Phpfox::getLib('file')->getBuiltDir($sNewsPicStorage . PHPFOX_DS) . md5('image_' . $iToken . '_' . PHPFOX_TIME) . '%s.jpg';
                        Phpfox::getLib('image')->createThumbnail($thumbTempPath, sprintf($ThumbNail, '_' . 500), 500,
                            500);
                        Phpfox::getLib('image')->createThumbnail($thumbTempPath, sprintf($ThumbNail, '_' . 1024), 1024,
                            1024);
                        $sFileName = str_replace(Phpfox::getParam('core.dir_pic'), "", $ThumbNail);
                        $sFileName = str_replace("\\", "/", $sFileName);
                        $aVals['image_path'] = $sFileName;
                        $aVals['status'] = 1;
                        $iPhotoSize = 0;
                        if (file_exists(sprintf($ThumbNail, '_' . 500))) {
                            $iPhotoSize += filesize(sprintf($ThumbNail, '_' . 500));
                        }
                        if (file_exists(sprintf($ThumbNail, '_' . 1024))) {
                            $iPhotoSize += filesize(sprintf($ThumbNail, '_' . 1024));
                        }
                        $aVals['photo_size'] = $iPhotoSize;
                        @unlink($thumbTempPath);
                    }
                } catch (\Exception $e) {
                }
            }
            // Save video
            try {
                $saveVideoPath = Phpfox::getLib('file')->upload($outputPath, PHPFOX_DIR_FILE . 'video' . PHPFOX_DS,
                    $sTitle);
                $aVals['video_path'] = sprintf($saveVideoPath, '');
                if (file_exists($outputPath)) {
                    $aVals['video_size'] = filesize($outputPath);
                }

                // delete the files from temp dir
                @unlink($outputPath);
                @unlink($videoPath);

            } catch (\Exception $e) {
                @unlink($videoPath);
                @unlink($outputPath);
            }

        }

        return $aVals;
    }
}
