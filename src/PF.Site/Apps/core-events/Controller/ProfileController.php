<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Events\Controller;

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
        $this->setParam('bIsProfile', true);

        Phpfox::getComponent('event.index', array('bNoTemplate' => true), 'controller');
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('event.component_controller_profile_clean')) ? eval($sPlugin) : false);
    }
}