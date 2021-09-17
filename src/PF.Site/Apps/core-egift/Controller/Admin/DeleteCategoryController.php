<?php

namespace Apps\Core_eGifts\Controller\Admin;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;
use Phpfox_Error;

class DeleteCategoryController extends Phpfox_Component
{
    public function process()
    {
        $iDeleteId = $this->request()->getInt('delete');
        if ($iDeleteId && $aRow = Phpfox::getService('egift.category')->getCategoryById($iDeleteId)) {
            $iTotalItems = Phpfox::getService('egift.category')->getTotalItemBelongToCategory($iDeleteId);
            $this->template()->assign([
                    'iTotalItems' => $iTotalItems,
                    'iDeleteId' => $iDeleteId,
                    'aCategories' => Phpfox::getService('egift.category')->getForAdmin($iDeleteId),
                ]
            );
            if (($aVals = $this->request()->getArray('val'))) {
                if (Phpfox::getService('egift.category.process')->deleteCategory($iDeleteId, $aVals)) {
                    $this->url()->send('admincp.app', ['id' => 'Core_eGifts'], _p('the_category_successfully_deleted'));
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
        (($sPlugin = Phpfox_Plugin::get('egift.component_controller_admincp_delete_category_clean')) ? eval($sPlugin) : false);
    }
}
