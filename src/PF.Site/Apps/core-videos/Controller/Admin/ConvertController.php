<?php

namespace Apps\PHPfox_Videos\Controller\Admin;

use Admincp_Component_Controller_App_Index;
use Apps\PHPfox_Videos\Job\ConvertOldVideos;
use Phpfox;
use Phpfox_Queue;

defined('PHPFOX') or exit('NO DICE!');

class ConvertController extends Admincp_Component_Controller_App_Index
{
    public function process()
    {
        parent::process();
        $iNumberVideos = Phpfox::getService('v.video')->getCountConvertOldVideos();
        $iConvert = $this->request()->getInt('convert');
        $iCancelCron = $this->request()->getInt('cancel_cron');

        if ($iNumberVideos && $iConvert) {
            $iCron = $this->request()->getInt('cron');
            if ($iCron) {
                storage()->set('phpfox_job_queue_convert_video_run', Phpfox::getUserId());
                Phpfox_Queue::instance()->addJob('videos_convert_old_videos', []);
                Phpfox::addMessage(_p('your_job_is_running_you_will_receive_notice_when_it_done'));
            } else {
                (new ConvertOldVideos(0, 0, '', []))->perform();
                $iTotalConverted = $iNumberVideos;
                if ($iTotalConverted > 100) {
                    $iTotalConverted = 100;
                }
                Phpfox::addMessage(_p('number_old_videos_converted', ['number' => $iTotalConverted]));
            }
            $this->url()->send('admincp.v.convert', []);
        }

        $iConvertedUserId = 0;
        if ($iCancelCron) {
            storage()->del('phpfox_job_queue_convert_video_run');
            Phpfox::addMessage(_p('your_job_was_cancelled'));
            $this->url()->send('admincp.v.convert', []);
        } else {
            $store_data = storage()->get('phpfox_job_queue_convert_video_run');
            $iConvertedUserId = isset($store_data->value) ? $store_data->value : 0;
        }
        $this->template()
            ->setBreadcrumb(_p('convert_old_videos'), $this->url()->makeUrl('admincp.v.convert'))
            ->assign([
                'iNumberVideos' => $iNumberVideos,
                'iConvertedUserId' => $iConvertedUserId
            ]);
    }
}
