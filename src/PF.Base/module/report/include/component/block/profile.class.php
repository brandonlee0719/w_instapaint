<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright       [PHPFOX_COPYRIGHT]
 * @author          phpFox
 * @package         Phpfox_Component
 */
class Report_Component_Block_Profile extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        (($sPlugin = Phpfox_Plugin::get('report.component_block_profile_process')) ? eval($sPlugin) : false);

        if (isset($bHideReportLink)) {
            return false;
        }

        $aUser = $this->getParam('aUser');
        if (isset($aUser['is_page']) && $aUser['is_page'] || $aUser['user_id'] == Phpfox::getUserId()) {
            return false;
        }

        $this->template()->assign([
            'aUser' => $aUser,
            'bIsFriend' => Phpfox::getService('friend')->isFriend($aUser['user_id'], Phpfox::getUserId()),
            'bIsBlocked' => (Phpfox::isUser() ? Phpfox::getService('user.block')->isBlocked(Phpfox::getUserId(),
                $aUser['user_id']) : false)
        ]);

        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('report.component_block_profile_clean')) ? eval($sPlugin) : false);
    }
}
