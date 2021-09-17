<?php

namespace Apps\Core_Pages\Controller\Admin;

use Admincp_Component_Controller_App_Index;
use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class AddCategoryController extends Admincp_Component_Controller_App_Index
{
    /**
     * Controller
     */
    public function process()
    {
        parent::process();
        $bIsEdit = false;
        $bIsSub = false;
        if (($iEditId = $this->request()->getInt('category_id'))) {
            $aRow = Phpfox::getService('pages.type')->getForEdit($iEditId);
            $bIsEdit = true;
            $this->template()->assign(array(
                    'aForms' => $aRow,
                    'iEditId' => $iEditId
                )
            );
        }

        if (($iSubtEditId = $this->request()->getInt('sub'))) {
            $aRow = Phpfox::getService('pages.category')->getForEdit($iSubtEditId);
            $iEditId = $iSubtEditId;
            $bIsEdit = true;
            $bIsSub = true;
            $this->template()->assign(array(
                    'aForms' => $aRow,
                    'iEditId' => $iEditId
                )
            );
        }

        if (($aVals = $this->request()->getArray('val'))) {
            if ($aVals = $this->_validate($aVals)) {
                if ($bIsEdit) {
                    if (Phpfox::getService('pages.process')->updateCategory($iEditId, $aVals)) {
                        if ($bIsSub) {
                            $this->url()->send('admincp.pages', ['sub' => $aVals['type_id']],
                                _p('successfully_updated_the_category'));
                        } else {
                            $this->url()->send('admincp.pages', null, _p('successfully_updated_the_category'));
                        }
                    }
                } else {
                    if (Phpfox::getService('pages.process')->addCategory($aVals)) {
                        $this->url()->send('admincp.pages', null, _p('successfully_created_a_new_category'));
                    }
                }
            }
        }

        $this->template()->setTitle(_p('add_category'))
            ->setBreadCrumb(_p('add_category'))
            ->assign(array(
                    'bIsEdit' => $bIsEdit,
                    'aTypes' => Phpfox::getService('pages.type')->get(),
                    'bIsSub' => $bIsSub
                )
            )
            ->setPhrase(['are_you_sure_you_want_to_delete_this_category_image']);
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
        (($sPlugin = Phpfox_Plugin::get('pages.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
    }
}
