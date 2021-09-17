<?php

namespace Apps\Core_eGifts\Controller\Admin;

use Phpfox_Component;
use Phpfox_Plugin;
use Phpfox;

class CategoriesController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $aCategories = Phpfox::getService('egift.category')->getCategories(true);
        $aLanguages = Phpfox::getService('language')->getAll(true);

        $this->template()->assign(array(
            'aCategories' => $aCategories,
            'aLanguages' => $aLanguages,
        ))
            ->setBreadCrumb(_p('manage_categories'), null, true);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('egift.component_controller_index_clean')) ? eval($sPlugin) : false);
    }
}
