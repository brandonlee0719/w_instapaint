<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Ban_Component_Controller_Admincp_Username
 */
class Ban_Component_Controller_Admincp_Username extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $this->setParam('aBanFilter', array(
                'title' => _p('usernames'),
                'type' => 'username',
                'url' => 'admincp.ban.username',
                'form' => _p('username')
            )
        );

        return Phpfox_Module::instance()->setController('ban.admincp.default');
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ban.component_controller_admincp_username_clean')) ? eval($sPlugin) : false);
    }
}
