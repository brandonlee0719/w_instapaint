<?php

namespace Apps\PHPfox_Videos\Job;

use Core\Queue\JobAbstract;
use Phpfox;

/**
 * Class Convert
 *
 * @package Apps\PHPfox_Videos\Job
 */
class  ConvertOldVideos extends JobAbstract
{
    /**
     * @inheritdoc
     */
    public function perform()
    {
        Phpfox::getService('v.video')->convertOldVideos();
        $iNumberVideos = Phpfox::getService('v.video')->getCountConvertOldVideos();
        if ($iNumberVideos == 0) {
            //Delete category map
            storage()->del('pf_video_categories_map');

            // get user who click convert videos via job
            $iUserId = storage()->get('phpfox_job_queue_convert_video_run')->value;
            storage()->del('phpfox_job_queue_convert_video_run');
            if ($iUserId) {
                // send mail notification
                \Phpfox_Mail::instance()->to($iUserId)
                    ->subject(_p('videos_converted'))
                    ->message(_p("all_old_videos_feed_video_converted_new_videos"))
                    ->send();

                // send notification
                Phpfox::getService('notification.process')->add('v_converted', 0, $iUserId, 1);
            }
            // delete job
            $this->delete();
        }
    }
}
