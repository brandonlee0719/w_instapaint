<?php

namespace Apps\Core_eGifts\Controller\Admin;

use Phpfox;
use Phpfox_Plugin;
use Admincp_Component_Controller_App_Index;


class AddCategoryController extends Admincp_Component_Controller_App_Index
{
    public function process()
    {
        $bIsEdit = false;
        $aLanguages = Phpfox::getService('language')->getAll(true);
        $iEditId = $this->request()->getInt('edit');
        if ($iEditId) {
            $aRow = Phpfox::getService('egift.category')->getCategoryById($iEditId);
            $bIsEdit = true;
            $this->template()->assign([
                    'aForms' => $aRow,
                    'iEditId' => $iEditId,
                ]
            );
        }

        if (($aVals = $this->request()->getArray('val'))) {
            if ($aVals = $this->_validate($aVals)) {
                if ($bIsEdit) {
                    if (Phpfox::getService('egift.category.process')->updateCategory($iEditId, $aVals)) {
                        $this->url()->send('admincp.app', ['id' => 'Core_eGifts'], _p('category_successfully_updated'));
                    }
                } else {
                    if (Phpfox::getService('egift.category.process')->addCategory($aVals)) {
                        $this->url()->send('admincp.app', ['id' => 'Core_eGifts'],
                            _p('new_category_successfully_created'));
                    }
                }
            }
        }

        $this->template()->setTitle($bIsEdit ? _p('edit_category') : _p('add_category'))
            ->setBreadCrumb($bIsEdit ? _p('edit_category') : _p('add_category'))
            ->assign([
                    'bIsEdit' => $bIsEdit,
                    'aLanguages' => $aLanguages
                ]
            );
    }

    /**
     * validate input value
     * @param $aVals
     *
     * @return bool
     */
    private function _validate($aVals)
    {
        return Phpfox::getService('language')->validateInput($aVals, 'name', false);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('blog.component_controller_admincp_add_category_clean')) ? eval($sPlugin) : false);
    }
}
