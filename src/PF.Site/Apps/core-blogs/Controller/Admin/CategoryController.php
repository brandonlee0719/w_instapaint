<?php

namespace Apps\Core_Blogs\Controller\Admin;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class CategoryController
 * @package Apps\Core_Blogs\Controller\Admin
 */
class CategoryController extends Phpfox_Component
{
    public function process()
    {
        $bSubCategory = false;
        $aParentCategory = array();
        if (($iId = $this->request()->getInt('sub'))) {
            $aParentCategory = Phpfox::getService('blog.category')->getCategory($iId);
            $bSubCategory = true;
        }
        $sTitle = _p('manage_categories') . (($bSubCategory && isset($aParentCategory['name'])) ? ': '. Phpfox::getSoftPhrase($aParentCategory['name']) : '');
        $this->template()
            ->setTitle(_p('manage_categories'))
            ->setBreadCrumb($sTitle)
            ->assign([
                    'bSubCategory' => $bSubCategory,
                    'aParentCategory' => $aParentCategory,
                    'aCategories' => Phpfox::getService('blog.category')->getForAdmin($iId),
                ]
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('blog.component_controller_admincp_category_clean')) ? eval($sPlugin) : false);
    }
}
