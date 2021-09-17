<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Events\Controller\Admin;

use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class IndexController extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $bSubCategory = false;
        if ($iParentId = $this->request()->getInt('sub')) {
            $aParentCategory = Phpfox::getService('event.category')->getForEdit($iParentId);
            $bSubCategory = true;
        }

        //Get all category belong to this category
        $aCategories = Phpfox::getService('event.category')->getForManage($iParentId);

        $this->template()->setTitle(_p('manage_categories'))
            ->setBreadCrumb(_p('manage_categories') . (($bSubCategory && isset($aParentCategory['name'])) ? ': '. Phpfox::getSoftPhrase($aParentCategory['name']) : ''))
            ->assign([
                'aCategories' => $aCategories,
                'bSubCategory' => $bSubCategory
            ]);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('event.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
    }
}