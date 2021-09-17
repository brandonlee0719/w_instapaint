<?php

namespace Apps\Core_Announcement\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class Manage extends Phpfox_Component
{
    public function process()
    {
        (($sPlugin = Phpfox_Plugin::get('announcement.component_block_manage_start')) ? eval($sPlugin) : false);

        $sLanguage = $this->getParam('sLanguage');
        $aAnnouncements = $this->getParam('aAnnouncements');

        if (empty($aAnnouncements)) {
            $aAnnouncements = Phpfox::getService('announcement')->getAnnouncementsByLanguage($sLanguage);
        }

        $this->template()->assign(array(
            'aAnnouncements' => $aAnnouncements
        ));

        (($sPlugin = Phpfox_Plugin::get('announcement.component_block_manage_end')) ? eval($sPlugin) : false);
    }

    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('announcement.component_block_manage_clean')) ? eval($sPlugin) : false);
    }
}
