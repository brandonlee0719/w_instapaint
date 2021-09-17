<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Controller_Pending
 */
class User_Component_Controller_Pending extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $this->template()->assign(array(
            'iStatus' => $this->request()->get('s'),
            'iViewId' => $this->request()->get('v'),
        ));
        if (Phpfox::isUser()) {
            $this->url()->send($this->url()->makeUrl(''));
        }

    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('user.component_controller_pending_clean')) ? eval($sPlugin) : false);
    }
}
