<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Subscribe_Component_Block_List
 */
class Subscribe_Component_Block_List extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if (Phpfox::isUser()) {
            $aGroup = Phpfox::getService('user.group')->getGroup(Phpfox::getUserBy('user_group_id'));
        }

        $this->template()->assign(array(
                'aPurchases' => (Phpfox::isUser() ? Phpfox::getService('subscribe.purchase')->get(Phpfox::getUserId(),
                    5) : array()),
                'aPackages' => Phpfox::getService('subscribe')->getPackages((Phpfox::isUser() ? false : true),
                    (Phpfox::isUser() ? true : false)),
                'sDefaultImagePath' => Phpfox::getParam('core.url_module') . 'subscribe/static/image/membership_thumbnail.jpg',
                'aGroup' => ((Phpfox::isUser() && isset($aGroup)) ? $aGroup : array()),
                'bIsOnSignup' => ($this->getParam('on_signup') ? true : false)
            )
        );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('subscribe.component_block_list_clean')) ? eval($sPlugin) : false);
    }
}
