<?php

namespace Apps\Core_Announcement\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class IndexController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        Phpfox::getUserParam('announcement.can_view_announcements', true);
        $aAnnouncements = Phpfox::getService('announcement')->getLatest();

        $this->template()->setBreadCrumb(_p('announcements'), $this->url()->makeUrl('announcement.view'))
            ->setTitle(_p('announcements'))
            ->assign(array('aAnnouncements' => $aAnnouncements));
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('announcement.component_controller_index_clean')) ? eval($sPlugin) : false);
    }
}
