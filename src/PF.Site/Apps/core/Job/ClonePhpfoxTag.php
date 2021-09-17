<?php

namespace Apps\Phpfox_Core\Job;

use Core\Queue\JobAbstract;
use Phpfox;

class ClonePhpfoxTag extends JobAbstract
{
    /**
     * Perform a job item
     */
    public function perform()
    {
        $db = Phpfox::getLib('database');

        $sql = strtr('insert ignore into `phpfox_tag` (`item_id`, `category_id`,`user_id`,`tag_type`, `tag_text`, `tag_url`, `added`) 
Select `item_id`, `category_id`,  `user_id`,0 as `tag_type`, `tag_text`, `tag_url`, `added`
from phpfox_tag where tag_type =1;', ['phpfox_tag' => Phpfox::getT('tag')]);

        $db->query($sql);

        $this->delete();
    }
}