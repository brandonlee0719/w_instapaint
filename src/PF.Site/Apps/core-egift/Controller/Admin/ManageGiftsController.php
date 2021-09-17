<?php

namespace Apps\Core_eGifts\Controller\Admin;

use Admincp_Component_Controller_App_Index;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox;


class ManageGiftsController extends Admincp_Component_Controller_App_Index
{
    /**
     * Controller
     */
    public function process()
    {
        $aCategories = Phpfox::getService('egift.category')->getCategories();
        $iCategoryId = $this->request()->getInt('category', 0);

        if (!count($aCategories)) {
            Phpfox_Error::display(_p('egift_not_have_category'));
        }

        $aEgifts = Phpfox::getService('egift')->getEgifts($iCategoryId);

        if ($iId = $this->request()->getInt('delete')) {
            if (Phpfox::getService('egift.process')->deleteGift($iId)) {
                $this->url()->send('admincp.egift.manage-gifts', array(), _p('egift_deleted_successfully'));
            } else {
                $this->url()->send('admincp.egift.manage-gifts', array(), _p('egift_deleted_fail'));
            }
        }

        $this->template()
            ->setBreadCrumb(_p('manage_egift'), $this->url()->makeUrl('admincp.egift.manage-gifts'))
            ->assign(array(
                'aCategories' => $aCategories,
                'aEgifts' => $aEgifts,
                'iCategoryId' => $iCategoryId,
                'aForms' => array('category' => $iCategoryId)
            ));
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
