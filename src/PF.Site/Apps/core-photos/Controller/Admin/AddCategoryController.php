<?php

namespace Apps\Core_Photos\Controller\Admin;

use Admincp_Component_Controller_App_Index;
use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class AddCategoryController extends Admincp_Component_Controller_App_Index
{
    public function process()
    {
        parent::process();
        $bIsEdit = false;
        $bIsSub = false;
        $aLanguages = Phpfox::getService('language')->getAll(true);
        $iEditId = $this->request()->getInt('edit');
        $iSubEditId = $this->request()->getInt('sub');
        if ($iSubEditId) {
            $bIsSub = true;
            $iEditId = $iSubEditId;
        }

        if ($iEditId) {
            $aRow = Phpfox::getService('photo.category')->getCategory($iEditId);
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
                    $aVals['edit_id'] = $iEditId;
                    if (Phpfox::getService('photo.category.process')->update($aVals)) {
                        if ($bIsSub) {
                            $this->url()->send('admincp.app',
                                ['id' => 'Core_Photos', 'val[sub]' => $aVals['parent_id']],
                                _p('successfully_updated_a_new_category'));
                        } else {
                            $this->url()->send('admincp.app', ['id' => 'Core_Photos'],
                                _p('successfully_updated_a_new_category'));
                        }
                    }
                } else {
                    if (Phpfox::getService('photo.category.process')->add($aVals)) {
                        $this->url()->send('admincp.app', ['id' => 'Core_Photos'],
                            _p('successfully_created_a_new_category'));
                    }
                }
            }
        }

        $this->template()->setTitle($bIsEdit ? _p('edit_category') : _p('add_category'))
            ->setBreadCrumb($bIsEdit ? _p('edit_category') : _p('add_category'))
            ->assign([
                    'bIsEdit' => $bIsEdit,
                    'aCategories' => Phpfox::getService('photo.category')->getForAdmin(0, 0),
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
        (($sPlugin = Phpfox_Plugin::get('photo.component_controller_admincp_add_category_clean')) ? eval($sPlugin) : false);
    }
}