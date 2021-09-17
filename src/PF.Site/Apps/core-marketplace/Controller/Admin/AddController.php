<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Marketplace\Controller\Admin;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class AddController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {

        $bIsEdit = false;
        $aLanguages = Phpfox::getService('language')->getAll(true);
        if ($iEditId = $this->request()->getInt('id')) {
            $bIsEdit = true;
            $aCategory = Phpfox::getService('marketplace.category')->getForEdit($iEditId);
            if (!isset($aCategory['category_id'])) {
                $this->url()->send('admincp.marketplace', null, _p('not_found'));
            }
            $this->template()->assign([
                'aForms' => $aCategory,
                'iEditId' => $iEditId
            ]);
        }

        if ($aVals = $this->request()->getArray('val')) {
            $aVals['parent_id'] = (int)$aVals['parent_id'];
            if ($aVals['parent_id'] > 0) {
                $aRedirectParam = ['parent' => $aVals['parent_id']];
            } else {
                $aRedirectParam = [];
            }
            if ($aVals = $this->_validate($aVals)) {
                if ($bIsEdit) {
                    if (Phpfox::getService('marketplace.category.process')->update($aVals)) {
                        $this->url()->send('admincp.marketplace', $aRedirectParam, _p('category_successfully_updated'));
                    }
                } else {
                    if (Phpfox::getService('marketplace.category.process')->add($aVals)) {
                        $this->url()->send('admincp.marketplace', $aRedirectParam, _p('category_successfully_added'));
                    }
                }
            }
        }

        $aParentCategories = Phpfox::getService('marketplace.category')->getAllParentCategories();

        $this->template()->setTitle(($bIsEdit ? _p('edit_a_category') : _p('create_a_new_category')))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("Marketplace"), $this->url()->makeUrl('admincp.marketplace'))
            ->setBreadCrumb(($bIsEdit ? _p('edit_a_category') : _p('create_a_new_category')),
                $this->url()->makeUrl('admincp.marketplace.add'))
            ->assign([
                'bIsEdit' => $bIsEdit,
                'aLanguages' => $aLanguages,
                'aParentCategories' => $aParentCategories
            ]);
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
        (($sPlugin = Phpfox_Plugin::get('marketplace.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
    }
}