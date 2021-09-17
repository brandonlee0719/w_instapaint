<?php

namespace Apps\PHPfox_Groups\Controller;

use Phpfox;
use Phpfox_Plugin;

class MembersController extends \Phpfox_Component
{
    public function process()
    {
        $aGroup = $this->getParam('aPage');
        list($iTotalMembers,) = Phpfox::getService('groups')->getMembers($aGroup['page_id']);
        $iTotalPendings = Phpfox::getService('groups')->getPendingUsers($aGroup['page_id'], true);
        $bIsAdmin = Phpfox::getService('groups')->isAdmin($aGroup);
        $bCanViewAdmins = Phpfox::getService('groups')->hasPerm($aGroup['page_id'], 'groups.view_admins');

        $this->template()
            ->clearBreadCrumb()
            ->setBreadCrumb($aGroup['title'], url('groups/' . $aGroup['page_id']))
            ->setBreadCrumb(_p('members'), url('groups/' . $aGroup['page_id'] . '/members'))
            ->assign([
                'iTotalMembers' => $iTotalMembers,
                'iTotalPendings' => $iTotalPendings,
                'bShowFriendInfo' => true,
                'iGroupId' => $aGroup['page_id'],
                'bIsAdmin' => $bIsAdmin,
                'bIsOwner' => Phpfox::getService('groups')->getPageOwnerId($aGroup['page_id']) == Phpfox::getUserId(),
                'iTotalAdmins' => Phpfox::getService('groups')->getGroupAdminsCount($aGroup['page_id']),
                'bCanViewAdmins' => $bCanViewAdmins
            ]);

        $this->setParam(['mutual_list' => true]);

        // moderation
        if ($bIsAdmin && $iTotalMembers) {
            $aModerations[] = [
                'phrase' => _p('delete'),
                'action' => 'delete'
            ];
            $this->setParam('global_moderation', array(
                    'name' => 'groups',
                    'ajax' => 'groups.memberModeration',
                    'menu' => $aModerations,
                    'custom_fields' => '<input type="hidden" name="page_id" value="' . $aGroup['page_id'] . '"/>'
                )
            );
        }
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('groups.component_controller_members_clean')) ? eval($sPlugin) : false);
    }
}
