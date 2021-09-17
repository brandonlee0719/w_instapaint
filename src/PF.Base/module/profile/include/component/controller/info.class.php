<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Profile_Component_Controller_Info
 */
class Profile_Component_Controller_Info extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $aRow = $this->getParam('aUser');
        if (!isset($aRow['user_id'])) {
            return false;
        }
        if (!Phpfox::getService('user.privacy')->hasAccess($aRow['user_id'], 'profile.profile_info')) {
            Phpfox::getLib('url')->send('privacy.invalid');
        }
        if (!isset($aRow['has_rated'])) {
            $aRow['has_rated'] = false;
        }
        $this->setParam('template', 'info');
        $this->template()->setTitle(Phpfox::getService('profile')->getProfileTitle($aRow));
        $this->template()->setEditor();
        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('profile.component_controller_info_clean')) ? eval($sPlugin) : false);
    }
}
