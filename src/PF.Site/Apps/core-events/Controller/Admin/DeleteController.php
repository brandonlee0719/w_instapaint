<?php

namespace Apps\Core_Events\Controller\Admin;

use Phpfox;
use Phpfox_Component;

defined('PHPFOX') or exit('NO DICE!');

class DeleteController extends Phpfox_Component
{
    public function process()
    {
        $iDeleteId = $this->request()->getInt('delete');
        if ($iDeleteId && $aRow = Phpfox::getService('event.category')->getForEdit($iDeleteId)) {
            $iTotalItems = Phpfox::getService('event.category')->getTotalItemBelongToCategory($iDeleteId);
            $iTotalSubs = Phpfox::getService('event.category')->getTotalSubBelongToCategory($iDeleteId);
            $this->template()->assign([
                    'iTotalItems' => $iTotalItems,
                    'iTotalSubs' => $iTotalSubs,
                    'iDeleteId' => $iDeleteId,
                    'aCategories' => Phpfox::getService('event.category')->getForAdmin(0, 0, 0, $iDeleteId),
                ]
            );

            if (($aVals = $this->request()->getArray('val'))) {
                if (Phpfox::getService('event.category.process')->deleteCategory($iDeleteId, $aVals)) {
                    $this->url()->send('admincp.app', ['id' => 'Core_Events'],
                        _p('category_successfully_deleted'));
                }
            }
        } else {
            \Phpfox_Error::display(_p('category_not_found'));
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
        (($sPlugin = \Phpfox_Plugin::get('photo.component_controller_admincp_delete_category_clean')) ? eval($sPlugin) : false);
    }
}