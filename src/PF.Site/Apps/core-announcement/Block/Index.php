<?php

namespace Apps\Core_Announcement\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class Index extends Phpfox_Component
{
    public function process()
    {
        (($sPlugin = Phpfox_Plugin::get('announcement.component_block_index__start')) ? eval($sPlugin) : false);
        $aAnnouncements = Phpfox::getService('announcement')->getLatest(null, true, Phpfox::getTime());

        if (empty($aAnnouncements)) {
            return false;
        }

        $this->template()->assign(array(
                'aAnnouncements' => $aAnnouncements
            )
        );
        (($sPlugin = Phpfox_Plugin::get('announcement.component_block_index__end')) ? eval($sPlugin) : false);
        return null;
    }
}
