<?php

namespace Apps\Core_Photos\Controller\Admin;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class CategoryController extends Phpfox_Component
{
    public function process()
    {
        $bSubCategory = false;
        $aParent = [];
        if (($iId = $this->request()->getInt('sub'))) {
            $bSubCategory = true;
            $aParent = Phpfox::getService('photo.category')->getCategory($iId);

        }
        $this->template()->setTitle(($bSubCategory ? _p('manage_sub_categories') : _p('manage_categories')))
            ->setBreadCrumb(_p('manage_categories') . (($bSubCategory && isset($aParent['name'])) ? ': '. Phpfox::getSoftPhrase($aParent['name']) : ''))
            ->assign([
                    'bSubCategory' => $bSubCategory,
                    'aCategories' => Phpfox::getService('photo.category')->getForAdmin($iId),
                ]
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('photo.component_controller_admincp_category_clean')) ? eval($sPlugin) : false);
    }
}