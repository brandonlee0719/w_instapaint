<?php

namespace Apps\PHPfox_Groups\Controller\Admin;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class CategoryController extends Phpfox_Component
{
    public function process()
    {
        // delete category
        if ($this->request()->get('delete')) {
            $sAction = $this->request()->get('child_action');
            $iCategoryId = $this->request()->get('category_id');
            $bIsSub = (boolean)$this->request()->get('is_sub');

            // process children
            if ($sAction === 'move') {
                $newCategory = explode('_', $this->request()->get('new_category_id'));
                // move groups
                Phpfox::getService('groups')->moveItemsToAnotherCategory($iCategoryId, $newCategory[0], $bIsSub,
                    count($newCategory) > 1, Phpfox::getService('groups.facade')->getItemTypeId());
                // move sub-categories and groups
                if (!$bIsSub) {
                    Phpfox::getService('groups.category')->moveSubCategoriesToAnotherType($iCategoryId,
                        $newCategory[0]);
                }
            }
            // delete category
            Phpfox::getService('groups.process')->deleteCategory($iCategoryId, $bIsSub, $sAction === 'del');

            $this->url()->send('admincp.app', ['id' => 'PHPfox_Groups'], _p('Successfully deleted the category.'));
        }

        $iId = $this->request()->getInt('sub');
        $bSubCategory = (boolean)$iId;
        if ($bSubCategory) {
            $this->template()->assign('sParentCategory', _p(Phpfox::getService('groups.type')->getById($iId)['name']));
        }

        $this->template()->setTitle(($bSubCategory ? _p('Manage Sub-Categories') : _p('Manage categories')))
            ->setBreadCrumb(($bSubCategory ? _p('Manage Sub-Categories') : _p('Manage categories')))
            ->assign([
                    'bSubCategory' => $bSubCategory,
                    'aCategories' => ($bSubCategory ? Phpfox::getService('groups.category')->getForAdmin($iId) : Phpfox::getService('groups.type')->getForAdmin()),
                ]
            )->setPhrase(['delete_category']);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('groups.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
    }
}
