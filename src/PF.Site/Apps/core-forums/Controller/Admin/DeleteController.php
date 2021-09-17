<?php

namespace Apps\Core_Forums\Controller\Admin;

use Phpfox;
use Phpfox_Component;

defined('PHPFOX') or exit('NO DICE!');

class DeleteController extends Phpfox_Component
{
    public function process()
    {
        Phpfox::getUserParam('forum.can_delete_forum', true);
        $iDeleteId = $this->getParam('delete');
        if (($aVals = $this->request()->getArray('val'))) {
            $iDeleteId = $this->request()->get('delete');
            if (Phpfox::getService('forum.process')->deleteForum($iDeleteId, $aVals)) {
                $this->url()->send('admincp.app', ['id' => 'Core_Forums'],
                    _p('forum_successfully_deleted'));
            }
        }
        if ($iDeleteId && $aRow = Phpfox::getService('forum')->id($iDeleteId)->getForum()) {
            $iTotalItems = Phpfox::getService('forum.thread')->getTotalThreadBelongToForum($iDeleteId);
            $iTotalSubs = Phpfox::getService('forum')->getTotalSubBelongToForum($iDeleteId);
            $this->template()->assign([
                    'iTotalItems' => $iTotalItems,
                    'iTotalSubs' => $iTotalSubs,
                    'iDeleteId' => $iDeleteId,
                    'sForums' => Phpfox::getService('forum')->active($aRow['parent_id'])->edit($iDeleteId)->getJumpTool(true,
                        true),
                ]
            );
        } else {
            \Phpfox_Error::display(_p('forum_not_found'));
        }

        $this->template()->setTitle(_p('delete_forum'))
            ->setBreadCrumb(_p('delete_forum'));
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