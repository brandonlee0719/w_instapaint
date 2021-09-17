<?php

namespace Apps\PHPfox_Videos\Controller\Admin;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class CategoryController extends Phpfox_Component
{
    public function process()
    {
        $bSubCategory = false;
        if (($iId = $this->request()->getInt('sub'))) {
            $bSubCategory = true;
            $aParentCategory = Phpfox::getService('v.category')->getCategory($iId);
            if ($aParentCategory) {
                $this->template()->assign('sParentCategory', _p($aParentCategory['name']));
            }
        }

        $this->template()->setTitle(($bSubCategory ? _p('manage_sub_categories') : _p('manage_categories')))
            ->setBreadCrumb(($bSubCategory ? _p('manage_sub_categories') : _p('manage_categories')))
            ->assign([
                    'bSubCategory' => $bSubCategory,
                    'aCategories' => Phpfox::getService('v.category')->getForAdmin($iId)
                ]
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('video.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
    }
}
