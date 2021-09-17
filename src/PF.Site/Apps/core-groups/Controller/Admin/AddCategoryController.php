<?php

namespace Apps\PHPfox_Groups\Controller\Admin;

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
        if (($iEditId = $this->request()->getInt('category_id'))) {
            $aRow = Phpfox::getService('groups.type')->getForEdit($iEditId);
            $bIsEdit = true;
            $this->template()->assign([
                    'aForms' => $aRow,
                    'iEditId' => $iEditId,
                ]
            );
        }

        if (($iSubtEditId = $this->request()->getInt('sub'))) {
            $aRow = Phpfox::getService('groups.category')->getForEdit($iSubtEditId);
            $iEditId = $iSubtEditId;
            $bIsEdit = true;
            $bIsSub = true;
            $this->template()->assign([
                    'aForms' => $aRow,
                    'iEditId' => $iEditId,
                ]
            );
        }

        if (($aVals = $this->request()->getArray('val'))) {
            if ($aVals = $this->_validate($aVals)) {
                if ($bIsEdit) {
                    if (Phpfox::getService('groups.process')->updateCategory($iEditId, $aVals)) {
                        if ($bIsSub) {
                            $this->url()->send('admincp.app',
                                ['id' => 'PHPfox_Groups', 'val[sub]' => $aVals['type_id']],
                                _p('Successfully updated the category.'));
                        } else {
                            $this->url()->send('admincp.app', ['id' => 'PHPfox_Groups'],
                                _p('Successfully updated the category.'));
                        }
                    }
                } else {
                    if (Phpfox::getService('groups.process')->addCategory($aVals)) {
                        $this->url()->send('admincp.app', ['id' => 'PHPfox_Groups'],
                            _p('Successfully created a new category.'));
                    }
                }
            }
        }

        $this->template()->setTitle(_p('Add category'))
            ->setBreadCrumb(_p('Add category'))
            ->setPhrase(['are_you_sure_you_want_to_delete_this_category_image'])
            ->assign([
                    'bIsEdit' => $bIsEdit,
                    'aTypes' => Phpfox::getService('groups.type')->getForAdmin(true),
                    'bIsSub' => $bIsSub
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
        (($sPlugin = Phpfox_Plugin::get('groups.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
    }
}
