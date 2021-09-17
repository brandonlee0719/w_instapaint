<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Events\Controller\Admin;

use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class AddController extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if ($iDelete = $this->request()->getInt('delete')) {
            if (Phpfox::getService('event.category.process')->delete($iDelete)) {
                $this->url()->send('admincp.event', null, _p('category_successfully_deleted'));
            }
        }

        $bIsEdit = false;
        $aLanguages = Phpfox::getService('language')->getAll(true);
        if ($iEditId = $this->request()->getInt('id')) {
            $bIsEdit = true;
            $aCategory = Phpfox::getService('event.category')->getForEdit($iEditId);
            if (!isset($aCategory['category_id'])) {
                $this->url()->send('admincp.event', null, _p('not_found'));
            }
            $this->template()->assign([
                'aForms' => $aCategory,
                'iEditId' => $iEditId
            ]);
        }

        if ($aVals = $this->request()->getArray('val')) {
            if ($aVals = $this->_validate($aVals)) {
                $aVals['parent_id'] = (int)$aVals['parent_id'];
                if ($aVals['parent_id'] > 0) {
                    $aRedirectParam = ['parent' => $aVals['parent_id']];
                } else {
                    $aRedirectParam = [];
                }
                if ($bIsEdit) {
                    if (Phpfox::getService('event.category.process')->update($aVals)) {
                        $this->url()->send('admincp.event', $aRedirectParam, _p('category_successfully_updated'));
                    }
                } else {
                    if (Phpfox::getService('event.category.process')->add($aVals)) {
                        $this->url()->send('admincp.event', $aRedirectParam, _p('category_successfully_added'));
                    }
                }
            }
        }

        $aParentCategories = Phpfox::getService('event.category')->getForAdmin(0, 0, 0, $iEditId);

        $this->template()->setTitle(($bIsEdit ? _p('edit_a_category') : _p('create_a_new_category')))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("Events"), $this->url()->makeUrl('admincp.event'))
            ->setBreadCrumb(($bIsEdit ? _p('edit_a_category') : _p('create_a_new_category')),
                $this->url()->makeUrl('admincp.event.add'))
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
        (($sPlugin = Phpfox_Plugin::get('event.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
    }
}