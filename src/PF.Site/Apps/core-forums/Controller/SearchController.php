<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Forums\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Module;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class SearchController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $aParentModule = $this->getParam('aParentModule');
        if ($aParentModule === null) {
            Phpfox::getService('forum')->buildMenu();
        }
        return Phpfox_Module::instance()->setController('forum.forum');
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('forum.component_controller_search_clean')) ? eval($sPlugin) : false);
    }
}