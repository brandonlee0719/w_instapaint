<?php

namespace Apps\Core_RSS\Ajax;

use Phpfox;
use Phpfox_Ajax;

class Ajax extends Phpfox_Ajax
{
    public function updateFeedActivity()
    {
        Phpfox::getService('rss.process')->updateActivity($this->get('id'), $this->get('active'));
    }

    public function updateGroupActivity()
    {
        Phpfox::getService('rss.group.process')->updateActivity($this->get('id'), $this->get('active'));
    }

    public function updateSiteWide()
    {
        Phpfox::getService('rss.process')->updateSiteWide($this->get('id'), $this->get('active'));
    }

    public function ordering()
    {
        Phpfox::getService('rss.process')->updateOrder($this->get('val'));
    }

    public function groupOrdering()
    {
        Phpfox::getService('rss.group.process')->updateOrder($this->get('val'));
    }

    public function log()
    {
        Phpfox::isUser(true);
        Phpfox::getBlock('rss.log', array(
                'rss' => array(
                    'table' => 'rss_log_user',
                    'field' => 'user_id',
                    'key' => Phpfox::getUserId()
                )
            )
        );
    }
}
