<?php

namespace Apps\Core_Announcement\Ajax;

use Phpfox;
use Phpfox_Ajax;
use Phpfox_Plugin;

class Ajax extends Phpfox_Ajax
{
    /**
     * @deprecated Will remove on version 4.6
     * Sets the new state of the announcements (active / inactive)
     */
    public function setActive()
    {
        Phpfox::isAdmin(true);
        (($sPlugin = Phpfox_Plugin::get('announcement.component_ajax_setactive__start')) ? eval($sPlugin) : false);
        $iId = (int)$this->get('id');
        $iState = (int)$this->get('active'); // we don't parse because its a potential risk since 0 is a valid value

        if ($iId < 1 || ($iState > 1 || $iState < 0)) {
            return false;
        }
        $mUpdate = Phpfox::getService('announcement.process')->setStatus($iId, $iState);
        if ($mUpdate !== true) {
            $this->alert($mUpdate);
        }
        (($sPlugin = Phpfox_Plugin::get('announcement.component_ajax_setactive__end')) ? eval($sPlugin) : false);
        return false;
    }

    public function toggleActiveAnnouncement()
    {
        $iAnnouncementId = $this->get('aid');
        $iActive = $this->get('active');
        Phpfox::getService('announcement.process')->toggleActiveAnnouncement($iAnnouncementId, $iActive);
    }

    /**
     * Hides the announcement block from the Dashboard
     */
    public function hideAnnouncement()
    {
        (($sPlugin = Phpfox_Plugin::get('announcement.component_ajax_hide__start')) ? eval($sPlugin) : false);
        if (Phpfox::getUserParam('announcement.can_close_announcement') == true) {
            if (($iId = $this->get('id')) && Phpfox::getService('announcement.process')->hide($iId)) {
                return true;
            }
        }
        (($sPlugin = Phpfox_Plugin::get('announcement.component_ajax_hide__end')) ? eval($sPlugin) : false);
        return $this->alert(_p('im_afraid_you_are_not_allowed_to_close_this_announcement'));
    }
}
