<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Forums\Controller\Admin;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class PermissionController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $aForum = Phpfox::getService('forum')->id($this->request()->getInt('id'))->getForum();

        $this->template()->setTitle(_p('manage_permissions'))
            ->setBreadCrumb(_p('manage_forums'), $this->url()->makeUrl('admincp.forum'))
            ->setBreadCrumb(_p('manage_permissions') . ': ' . _p($aForum['name']), null, true)
            ->assign(array(
                    'aUserGroups' => Phpfox::getService('user.group')->get(),
                    'iForumId' => $this->request()->getInt('id')
                )
            );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('forum.component_controller_admincp_permission_clean')) ? eval($sPlugin) : false);
    }
}