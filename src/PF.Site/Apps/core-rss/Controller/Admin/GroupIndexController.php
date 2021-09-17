<?php

namespace Apps\Core_RSS\Controller\Admin;

use Phpfox;
use Phpfox_Plugin;
use Admincp_Component_Controller_App_Index;


class GroupIndexController extends Admincp_Component_Controller_App_Index
{
    /**
     * Controller
     */
    public function process()
    {
        if (($iDeleteId = $this->request()->get('delete'))) {
            if (Phpfox::getService('rss.group.process')->delete($iDeleteId)) {
                $this->url()->send('admincp.rss.group', null, _p('group_successfully_deleted'));
            }
        }

        $this->template()
            ->setTitle(_p('manage_groups'))
            ->setBreadCrumb(_p('manage_groups'), $this->url()->makeUrl('admincp.rss.group'))
            ->assign(array(
                    'aGroups' => Phpfox::getService('rss.group')->get()
                )
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('rss.component_controller_admincp_group_index_clean')) ? eval($sPlugin) : false);
    }
}
