<?php

namespace Apps\Core_Announcement\Controller\Admin;

use Phpfox;
use Phpfox_Component;

class ManageController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if ($iDelete = $this->request()->getInt('delete')) {
            if ($bDel = Phpfox::getService('announcement.process')->delete((int)$iDelete)) {
                // Return to Manage Announcement
                $this->url()->send('admincp.app', ['id' => 'Core_Announcement'], _p('announcement_successfully_deleted'));
            }
        }

        // find the default language to pass it to the template so it can load the appropriate announcements
        // by calling the block manage only once.
        // get available languages
        $aLanguages = Phpfox::getService('language')->get();
        $sDefLanguage = '';
        foreach ($aLanguages as $aLanguage) {
            if ($aLanguage['is_default']) {
                $sDefLanguage = $aLanguage['language_id'];
            }
        }

        $aAnnouncements = Phpfox::getService('announcement')->getAnnouncementsByLanguage($sDefLanguage);

        $this->template()->setTitle(_p('announcements'))
            ->setBreadCrumb(_p('manage_announcements'),null)
            ->setActionMenu([
                _p('new_announcement') => [
                    'class' => '',
                    'url' => $this->url()->makeUrl('admincp.announcement.add')
                ]
            ])
            ->assign(array(
                    'aLanguages' => $aLanguages,
                    'sDefaultLanguage' => $sDefLanguage,
                    'aAnnouncements' => $aAnnouncements
                )
            );
    }
}
