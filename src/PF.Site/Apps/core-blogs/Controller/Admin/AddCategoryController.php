<?php

namespace Apps\Core_Blogs\Controller\Admin;

use Admincp_Component_Controller_App_Index;
use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class AddCategoryController
 * @package Apps\Core_Blogs\Controller\Admin
 */
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
            $aRow = Phpfox::getService('blog.category')->getCategory($iEditId);
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
                    if (Phpfox::getService('blog.category.process')->update($aVals)) {
                        $sMessage = _p('successfully_updated_category');
                    } else {
                        $sMessage = _p('cannot_update_category');
                    }
                } else {
                    if (Phpfox::getService('blog.category.process')->add($aVals)) {
                        $sMessage = _p('successfully_created_a_new_category');
                    } else {
                        $sMessage = _p('cannot_add_category');
                    }
                }

                if ($bIsSub) {
                    $this->url()->send('admincp.app',
                        ['id' => 'Core_Blogs', 'val[sub]' => $aVals['parent_id']],
                        $sMessage);
                } else {
                    $this->url()->send('admincp.app', ['id' => 'Core_Blogs'],
                        $sMessage);
                }
            }
        }
        $this->template()->setTitle($bIsEdit ? _p('edit_category') : _p('add_category'))
            ->setBreadCrumb($bIsEdit ? _p('edit_category') : _p('add_category'))
            ->assign([
                    'bIsEdit' => $bIsEdit,
                    'aCategories' => Phpfox::getService('blog.category')->getForAdmin(0, 0),
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
