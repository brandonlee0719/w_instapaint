<?php

namespace Apps\Core_RSS\Controller\Admin;

use Phpfox;
use Phpfox_Plugin;
use Admincp_Component_Controller_App_Index;


class AddGroupController extends Admincp_Component_Controller_App_Index
{
    /**
     * Controller
     */
    public function process()
    {
        $bIsEdit = false;
        if (($iId = $this->request()->getInt('group_id'))) {
            if (($aGroup = Phpfox::getService('rss.group')->getForEdit($iId))) {
                $bIsEdit = true;
                $this->template()->assign('aForms', $aGroup);
            }
        }

        if (($aVals = $this->request()->getArray('val'))) {
            if (!Phpfox::isTechie()) {
                $aVals = array_merge($aVals, ['product_id' => 'phpfox', 'module_id' => 'core']);
            }

            if ($bIsEdit && isset($aGroup)) {
                if (Phpfox::getService('rss.group.process')->update($aGroup['group_id'], $aVals)) {
                    $this->url()->send('admincp.rss.group.add', array('group_id' => $aGroup['group_id']),
                        _p('group_successfully_updated'));
                }
            } else {
                if (Phpfox::getService('rss.group.process')->add($aVals)) {
                    $this->url()->send('admincp.rss.group', null, _p('group_successfully_added'));
                }
            }
        }

        $this->template()->setTitle(_p('add_new_group'))
            ->setBreadCrumb(_p('add_new_group'), null, true)
            ->assign(array(
                    'bIsEdit' => $bIsEdit,
                    'aLanguages' => Phpfox::getService('language')->getAll(),
                )
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('rss.component_controller_admincp_group_add_clean')) ? eval($sPlugin) : false);
    }
}
