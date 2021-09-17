<?php

namespace Apps\Core_Announcement\Controller\Admin;

use Phpfox;
use Admincp_Component_Controller_App_Index;
use Phpfox_Plugin;
use Apps\Core_Announcement\Service\Announcement;

class AddController extends Admincp_Component_Controller_App_Index
{
    /**
     * Controller
     */
    public function process()
    {
        $bIsEdit = false;
        $aAnnouncement = array();
        $aLanguages = Phpfox::getService('language')->get();
        foreach ($aLanguages as $aLanguage)
        {
            if ($aLanguage['is_default']) {
                $this->template()->assign(array(
                    'aDefaultLanguage' => $aLanguage
                ));
                break;
            }
        }

        if ($iEditId = $this->request()->getInt('announcement_id')) {
            if ($aAnnouncement = Phpfox::getService('announcement')->getAnnouncementById($iEditId)) {
                // set the access user groups
                $this->template()->assign(array(
                    'aAnnouncement' => $aAnnouncement,
                    'aForms' => $aAnnouncement,
                    'aAccess' => $aAnnouncement['user_group']
                ));

                $bIsEdit = true;
            }
        }

        // Is user submitting a form?
        if ($aVals = $this->request()->get('val')) {
            if (!empty($aVals)) {
                if ($aVals = $this->_validate($aVals)) {
                    $aVals = Phpfox::getService('language')->validateInput($aVals, 'intro', false, false);
                    $aVals = Phpfox::getService('language')->validateInput($aVals, 'content', false, false, false);
                    if ($bIsEdit) {
                        if (Phpfox::getService('announcement.process')->update($aVals, $iEditId)) {
                            $sMessage = _p('the_announcement_successfully_updated');
                            $this->url()->send('admincp.app', ['id' => 'Core_Announcement'], $sMessage);
                        }
                    } else {
                        if (Phpfox::getService('announcement.process')->add($aVals)) {
                            $sMessage = _p('new_announcement_successfully_added');
                            $this->url()->send('admincp.app', ['id' => 'Core_Announcement'], $sMessage);
                        }
                    }
                }
            }
        }

        // Get default age range
        $iAgeEnd = date('Y') - Phpfox::getParam('user.date_of_birth_start');
        $iAgeStart = date('Y') - Phpfox::getParam('user.date_of_birth_end');
        $aAge = range($iAgeStart, $iAgeEnd);

        $sTitle = !empty($iEditId) ? _p('edit_announcement') . ': ' . _p($aAnnouncement['subject_var']) : _p('add_announcement');
        $this->template()->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('announcement_app'),$this->url()->makeUrl('admincp.app',['id'=>'Core_Announcement']))
            ->setBreadCrumb($sTitle, null, true);

        $this->template()->setTitle($sTitle)
            ->setEditor(['content'])
            ->setPhrase([
                'min_age_cannot_be_higher_than_max_age',
                'max_age_cannot_be_lower_than_the_min_age',
                'notice'
            ])->assign([
                'aLanguages' => $aLanguages,
                'aDefaultStyle' => Announcement::$aSupportStyle,
                'aAnnouncement' => $aAnnouncement,
                'bIsEdit' => $bIsEdit,
                'iEditId' => $iEditId,
                'aUserGroups' => Phpfox::getService('user.group')->get(),
                'aAge' => $aAge,
                'iUser' => Phpfox::getUserId()
            ])->setHeader(['jscript/admin_manage.js' => 'app_core-announcement']);
    }
    /**
     * validate input value
     * @param $aVals
     *
     * @return bool
     */
    private function _validate($aVals)
    {
        return Phpfox::getService('language')->validateInput($aVals, 'subject', false);
    }
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('announcement.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
    }
}
