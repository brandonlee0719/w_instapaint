<?php

namespace Apps\Core_Pages\Block;

defined('PHPFOX') or exit('NO DICE!');

class Pending extends \Phpfox_Component
{
    public function process()
    {
        $aPage = $this->getParam('aPage', false);
        if (!$aPage || $aPage['view_id'] == 0) {
            return false;
        }
        \Phpfox::getService('pages')->getActionsPermission($aPage, 'pending');
        $aActions = [];
        if ($aPage['bCanApprove']) {
            $aActions['approve'] = [
                'is_ajax' => true,
                'action' => '$.ajaxCall(\'pages.approve\', \'page_id='. $aPage['page_id'] .'\')',
                'label' => _p('approve')
            ];
        }
        if ($aPage['bCanEdit']) {
            $aActions['edit'] = [
                'action' => url()->make('pages.add', ['id' => $aPage['page_id']]),
                'label' => _p('edit')
            ];
        }
        if ($aPage['bCanDelete']) {
            $aActions['delete'] = [
                'action' => url()->make('pages', ['delete' => $aPage['page_id']]),
                'label' => _p('delete'),
                'is_confirm' => true,
                'confirm_message' => _p('are_you_sure_you_want_to_delete_this_page_permanently')
            ];
        }

        $this->template()->assign([
            'aPendingItem' => [
                'message' => _p('this_page_is_pending_approval'),
                'actions' => $aActions
            ]
        ]);

        return 'block';
    }
}
