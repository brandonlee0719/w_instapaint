<?php

namespace Apps\PHPfox_Groups\Block;

use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class Pending extends \Phpfox_Component
{
    public function process()
    {
        $aGroup = $this->getParam('aPage', false);
        if (!$aGroup || $aGroup['view_id'] == 0) {
            return false;
        }

        \Phpfox::getService('groups')->getActionsPermission($aGroup, 'pending');
        $aActions = [];
        if ($aGroup['bCanApprove']) {
            $aActions['approve'] = [
                'is_ajax' => true,
                'action' => '$.ajaxCall(\'groups.approve\', \'page_id='. $aGroup['page_id'] .'\')',
                'label' => _p('approve')
            ];
        }
        if ($aGroup['bCanEdit']) {
            $aActions['edit'] = [
                'action' => url()->make('groups.add', ['id' => $aGroup['page_id']]),
                'label' => _p('edit')
            ];
        }
        if ($aGroup['bCanDelete']) {
            $aActions['delete'] = [
                'action' => url()->make('groups', ['delete' => $aGroup['page_id']]),
                'label' => _p('delete'),
                'is_confirm' => true,
                'confirm_message' => _p('are_you_sure_you_want_to_delete_this_group_permanently')
            ];
        }

        $this->template()->assign([
            'aPendingItem' => [
                'message' => _p('this_group_is_pending_approval'),
                'actions' => $aActions
            ]
        ]);

        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('groups.component_block_pending_clean')) ? eval($sPlugin) : false);
    }
}
