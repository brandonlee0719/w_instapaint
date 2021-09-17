<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Profile_Component_Controller_Private
 */
class Profile_Component_Controller_Private extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        define('PHPFOX_PROFILE_PRIVACY', true);

        $aUser = $this->getParam('aUser');
        $bCanFrRequest = true;
        if (Phpfox::getService('user.block')->isBlocked($aUser['user_id'], Phpfox::getUserId())) {
            $bCanFrRequest = false;
        }
        $this->template()->setTitle($aUser['full_name'])
            ->assign(array(
                    'aUser' => $aUser,
                    'bIsFriend' => (Phpfox::getUserId() && Phpfox::isModule('friend') ? Phpfox::getService('friend')->isFriend(Phpfox::getUserId(),
                        $aUser['user_id']) : false),
                    'bIsBlocked' => (Phpfox::isUser() ? Phpfox::getService('user.block')->isBlocked(Phpfox::getUserId(),
                        $aUser['user_id']) : false),
                    'bCanFrRequest' => $bCanFrRequest
                )
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('profile.component_controller_private_clean')) ? eval($sPlugin) : false);
    }
}
