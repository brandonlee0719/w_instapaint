<?php

namespace Apps\PHPfox_Videos\Controller\Admin;

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class DeleteCategoryController extends Phpfox_Component
{
    public function process()
    {
        $iDeleteId = $this->request()->getInt('delete');
        if ($iDeleteId && $aCategory = Phpfox::getService('v.category')->getCategory($iDeleteId)) {
            $iTotalItems = Phpfox::getService('v.category')->getTotalItemBelongToCategory($iDeleteId);
            $hasSub = Phpfox::getService('v.category')->getChildIds($iDeleteId);
            $this->template()->assign([
                    'iTotalItems' => $iTotalItems,
                    'iDeleteId' => $iDeleteId,
                    'aCategories' => Phpfox::getService('v.category')->getForAdmin(0, 0, 0, $iDeleteId),
                    'hasSub' => $hasSub
                ]
            );

            if (($aVals = $this->request()->getArray('val'))) {
                if (Phpfox::getService('v.process')->deleteCategory($iDeleteId, $aVals)) {
                    if ($aCategory['parent_id']) {
                        $aRemainSubCategories = Phpfox::getService('v.category')->getForAdmin($aCategory['parent_id'], 1);
                    }
                    if ($aCategory['parent_id'] && !empty($aRemainSubCategories)) {
                        $this->url()->send('admincp.app',
                            ['id' => 'PHPfox_Videos', 'val[sub]' => $aCategory['parent_id']],
                            _p('successfully_deleted_the_category'));
                    } else {
                        $this->url()->send('admincp.app', ['id' => 'PHPfox_Videos'],
                            _p('successfully_deleted_the_category'));
                    }
                }
            }
        } else {
            Phpfox_Error::display(_p('category_not_found'));
        }

        $this->template()->setTitle(_p('delete_category'))
            ->setBreadCrumb(_p('delete_category'));
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('v.component_controller_admincp_delete_clean')) ? eval($sPlugin) : false);
    }
}
