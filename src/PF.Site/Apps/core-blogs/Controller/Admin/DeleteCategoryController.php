<?php

namespace Apps\Core_Blogs\Controller\Admin;

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

/**
 * Class DeleteCategoryController
 * @package Apps\Core_Blogs\Controller\Admin
 */
class DeleteCategoryController extends Phpfox_Component
{
    public function process()
    {
        $iDeleteId = $this->request()->getInt('delete');
        if ($iDeleteId && $aRow = Phpfox::getService('blog.category')->getCategory($iDeleteId)) {
            $iTotalItems = Phpfox::getService('blog.category')->getTotalItemBelongToCategory($iDeleteId);
            $hasSub = Phpfox::getService('blog.category')->getChildIds($iDeleteId);
            $this->template()->assign([
                    'iTotalItems' => $iTotalItems,
                    'iDeleteId' => $iDeleteId,
                    'aCategories' => Phpfox::getService('blog.category')->getForAdmin(0, 0, 0, $iDeleteId),
                    'hasSub' => $hasSub
                ]
            );
            if (($aVals = $this->request()->getArray('val'))) {
                if (Phpfox::getService('blog.category.process')->deleteCategory($iDeleteId, $aVals)) {
                    if ($aRow['parent_id'] && count(Phpfox::getService('blog.category')->getForAdmin($aRow['parent_id'], 1, false, $aRow['category_id']))) {
                        $this->url()->send('admincp.app',
                            ['id' => 'Core_Blogs', 'val[sub]' => $aRow['parent_id']],
                            _p('successfully_deleted_the_category'));
                    } else {
                        $this->url()->send('admincp.app', ['id' => 'Core_Blogs'],
                            _p('successfully_deleted_the_category'));
                    }
                }
            }
        } else {
            Phpfox_Error::display(_p('category_not_found'));
        }
        $this->template()->setTitle(_p('delete_category'))
            ->setBreadCrumb(_p('delete_category'));
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('blog.component_controller_admincp_delete_category_clean')) ? eval($sPlugin) : false);
    }
}
