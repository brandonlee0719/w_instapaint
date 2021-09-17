<?php

namespace Apps\Core_Pages\Controller;

use Phpfox;
use Phpfox_Plugin;

class MembersController extends \Phpfox_Component
{
    public function process()
    {
        $aPage = $this->getParam('aPage');
        list($iTotalMembers,) = Phpfox::getService('pages')->getMembers($aPage['page_id']);
        $iTotalPendings = Phpfox::getService('pages')->getPendingUsers($aPage['page_id'], true);
        $bIsAdmin = Phpfox::getService('pages')->isAdmin($aPage);
        $bCanViewAdmins = Phpfox::getService('pages')->hasPerm($aPage['page_id'], 'pages.view_admins');

        $this->template()
            ->clearBreadCrumb()
            ->setBreadCrumb($aPage['title'], url('pages/' . $aPage['page_id']))
            ->setBreadCrumb(_p('members'), url('pages/' . $aPage['page_id'] . '/members'))
            ->assign([
                'iTotalMembers' => $iTotalMembers,
                'iTotalPendings' => $iTotalPendings,
                'bShowFriendInfo' => true,
                'iPageId' => $aPage['page_id'],
                'bIsAdmin' => $bIsAdmin,
                'bIsOwner' => Phpfox::getService('pages')->getPageOwnerId($aPage['page_id']) == Phpfox::getUserId(),
                'bCanViewAdmins' => $bCanViewAdmins,
                'iTotalAdmins' => Phpfox::getService('pages')->getPageAdminsCount($aPage['page_id'])
            ]);

        $this->setParam(['mutual_list' => true]);

        // moderation
        if ($bIsAdmin && $iTotalMembers) {
            $aModerations[] = [
                'phrase' => _p('delete'),
                'action' => 'delete'
            ];
            $this->setParam('global_moderation', array(
                    'name' => 'pages',
                    'ajax' => 'pages.memberModeration',
                    'menu' => $aModerations,
                    'custom_fields' => '<input type="hidden" name="page_id" value="' . $aPage['page_id'] . '"/>'
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
        (($sPlugin = Phpfox_Plugin::get('pages.component_controller_members_clean')) ? eval($sPlugin) : false);
    }
}
