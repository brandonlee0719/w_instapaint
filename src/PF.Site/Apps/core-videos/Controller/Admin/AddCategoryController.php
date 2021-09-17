<?php

namespace Apps\PHPfox_Videos\Controller\Admin;

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
            $aRow = Phpfox::getService('v.category')->getForEdit($iEditId);
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
                    if (Phpfox::getService('v.process')->updateCategory($iEditId, $aVals)) {
                        if ($bIsSub) {
                            $this->url()->send('admincp.app',
                                ['id' => 'PHPfox_Videos', 'val[sub]' => $aVals['parent_id']],
                                _p('successfully_updated_a_new_category'));
                        } else {
                            $this->url()->send('admincp.app', ['id' => 'PHPfox_Videos'],
                                _p('successfully_updated_a_new_category'));
                        }
                    }
                } else {
                    if (Phpfox::getService('v.process')->addCategory($aVals)) {
                        $this->url()->send('admincp.app', ['id' => 'PHPfox_Videos'],
                            _p('successfully_created_a_new_category'));
                    }
                }
            }
        }

        $this->template()
            ->setTitle($bIsEdit ? _p('edit_category') : _p('add_category'))
            ->setBreadCrumb($bIsEdit ? _p('edit_category') : _p('add_category'))
            ->assign([
                    'bIsEdit' => $bIsEdit,
                    'aCategories' => Phpfox::getService('v.category')->getForAdmin(0, 0),
                    'aLanguages' => $aLanguages
                ]
            );
    }

    /**
     * validate input value
     * @param $aVals
     *
     * @return mixed
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
        (($sPlugin = Phpfox_Plugin::get('v.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
    }
}
