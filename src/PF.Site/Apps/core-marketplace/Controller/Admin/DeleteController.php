<?php

namespace Apps\Core_Marketplace\Controller\Admin;

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class DeleteController extends Phpfox_Component
{
    public function process()
    {
        $iDeleteId = $this->request()->getInt('delete');
        if ($iDeleteId && $aRow = Phpfox::getService('marketplace.category')->getCategory($iDeleteId)) {
            $iTotalItems = Phpfox::getService('marketplace.category')->getTotalItemBelongToCategory($iDeleteId);
            $hasSub = Phpfox::getService('marketplace.category')->getChildIds($iDeleteId);
            $this->template()->assign([
                    'iTotalItems' => $iTotalItems,
                    'iDeleteId' => $iDeleteId,
                    'aCategories' => Phpfox::getService('marketplace.category')->getForAdmin(0, 0, 0, $iDeleteId),
                    'hasSub' => $hasSub
                ]
            );

            if (($aVals = $this->request()->getArray('val'))) {
                if (Phpfox::getService('marketplace.category.process')->deleteCategory($iDeleteId, $aVals)) {
                    if ($aRow['parent_id']) {
                        $this->url()->send('admincp.app',
                            ['id' => 'Core_Marketplace', 'val[sub]' => $aRow['parent_id']],
                            _p('successfully_deleted_the_category'));
                    } else {
                        $this->url()->send('admincp.app', ['id' => 'Core_Marketplace'],
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
        (($sPlugin = Phpfox_Plugin::get('photo.component_controller_admincp_delete_category_clean')) ? eval($sPlugin) : false);
    }
}