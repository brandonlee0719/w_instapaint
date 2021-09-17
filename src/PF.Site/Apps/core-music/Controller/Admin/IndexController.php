<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Music\Controller\Admin;

use Phpfox;
use Phpfox_Component;

defined('PHPFOX') or exit('NO DICE!');

class IndexController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        //Get all category belong to this category
        $aGenres = Phpfox::getService('music.genre')->getForManage();

        $this->template()->setTitle(_p('manage_genres'))
            ->setBreadCrumb(_p('manage_genres'))
            ->assign([
                'aGenres' => $aGenres
            ]);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = \Phpfox_Plugin::get('music.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
    }
}