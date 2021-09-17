<?php

namespace Apps\Core_Announcement\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

class ViewController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        Phpfox::getUserParam('announcement.can_view_announcements', true);
        $iId = $this->request()->getInt('id');
        $this->setParam('announcement_id', $iId);
        $aAnnouncement = Phpfox::getService('announcement')->getLatest($iId);
        if ($aAnnouncement === false || !count($aAnnouncement)) {
            $this->url()->send('announcement', null, _p('that_announcement_does_not_exist'));
        }
        if (is_array($aAnnouncement)) {
            $aAnnouncement = reset($aAnnouncement);
        }
        $sSubject = $aAnnouncement['subject_var'];

        $this->template()
            ->setBreadCrumb(_p($sSubject), $this->url()->current())
            ->setTitle(_p($sSubject))
            ->assign(array('aAnnouncement' => $aAnnouncement));

        return 'controller';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('announcement.component_controller_view_clean')) ? eval($sPlugin) : false);
    }
}
