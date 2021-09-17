<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Forums\Controller\Admin;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class AddController extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if ($iDeleteId = $this->request()->getInt('delete')) {
            Phpfox::getUserParam('forum.can_delete_forum', true);
            if (Phpfox::getService('forum.process')->delete($iDeleteId)) {
                $this->url()->send('admincp.forum', null, _p('forum_successfully_deleted'));
            }
        }
        $aLanguages = Phpfox::getService('language')->getAll(true);
        $bIsEdit = false;
        if ($iId = $this->request()->getInt('id')) {
            $bIsEdit = true;
            Phpfox::getUserParam('forum.can_edit_forum', true);
            $aForum = Phpfox::getService('forum')->getForEdit($iId);
            $this->template()->assign([
                'aForms' => $aForum,
                'iId' => $iId
            ]);
            $sTitle = _p('editing_forum') . ': ' . Phpfox::getSoftPhrase($aForum['name']);
            $sForumParents = Phpfox::getService('forum')->active($aForum['parent_id'])->edit($aForum['forum_id'])->getJumpTool(true,
                $bIsEdit);
        } else {
            Phpfox::getUserParam('forum.can_add_new_forum', true);
            $sTitle = _p('create_new_form');
            $sForumParents = Phpfox::getService('forum')->active($this->request()->getInt('child'))->edit(0)->getJumpTool(true,
                $bIsEdit);
        }

        if ($aVals = $this->request()->getArray('val')) {
            if ($aVals = $this->_validate($aVals)) {
                $aVals = Phpfox::getService('language')->validateInput($aVals, 'description', false, false);
                if ($bIsEdit) {
                    if (Phpfox::getService('forum.process')->update($aVals)) {
                        $this->url()->send('admincp.forum', null, _p('forum_successfully_updated'));
                    }
                } else {
                    if (Phpfox::getService('forum.process')->add($aVals)) {
                        $this->url()->send('admincp.forum.add', null, _p('forum_successfully_added'));
                    }
                }
            }
        }

        $this->template()->setTitle($sTitle)
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("Forum"), $this->url()->makeUrl('admincp.forum'))
            ->setBreadCrumb($sTitle, $this->url()->makeUrl('admincp.forum.add'))
            ->assign(array(
                    'aLanguages' => $aLanguages,
                    'sForumParents' => $sForumParents,
                    'bIsEdit' => $bIsEdit
                )
            );
    }

    /**
     * validate input value
     * @param $aVals
     *
     * @return bool
     */
    private function _validate($aVals)
    {
        return Phpfox::getService('language')->validateInput($aVals, 'name', false);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('forum.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
    }
}