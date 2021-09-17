<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Polls\Controller;

use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class ProfileController extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        Phpfox::getUserParam('poll.can_access_polls', true);

        (($sPlugin = Phpfox_Plugin::get('poll.component_controller_profile_process_start')) ? eval($sPlugin) : false);

        $this->setParam('bIsProfile', true);

        Phpfox::getComponent('poll.index', array('bNoTemplate' => true), 'controller');

        (($sPlugin = Phpfox_Plugin::get('poll.component_controller_profile_process_end')) ? eval($sPlugin) : false);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('poll.component_controller_profile_clean')) ? eval($sPlugin) : false);
    }
}