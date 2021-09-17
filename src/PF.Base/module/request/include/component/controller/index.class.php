<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Request_Component_Controller_Index extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true);

        $this->template()->setTitle(_p('confirm_requests'))->setBreadCrumb(_p('requests'));

        (($sPlugin = Phpfox_Plugin::get('request.component_controller_index_process')) ? eval($sPlugin) : false);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('request.component_controller_index_clean')) ? eval($sPlugin) : false);
    }
}
