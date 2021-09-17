<?php

namespace Apps\PHPfox_Groups\Job;

use Core\Queue\JobAbstract;
use Phpfox;
use Phpfox_Mail;

/**
 * Class SendMemberNotification
 *
 * @package Apps\PHPfox_Groups\Job
 */
class  ConvertOldGroups extends JobAbstract
{
    /**
     * @inheritdoc
     */
    public function perform()
    {
        \Phpfox::getService('groups')->convertOldGroups();
        $iNumberGroups = \Phpfox::getService('groups')->getCountConvertibleGroups();
        if ($iNumberGroups == 0) {
            //Delete category of old groups
            db()->delete(':pages_category', 'page_type=1');
            $iUserId = storage()->get('phpfox_job_queue_convert_group_run')->value;
            storage()->del('phpfox_job_queue_convert_group_run');
            Phpfox_Mail::instance()->to($iUserId)
                ->subject(_p('Groups converted'))
                ->message(_p("All old groups (page type) converted new groups"))
                ->send();
            Phpfox::getService('notification.process')->add('groups_converted', 0, $iUserId, 1);
            $this->delete();
        }
    }
}
