<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Forums\Block\Admin;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class ModeratorBlock extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        Phpfox::getUserParam('forum.can_manage_forum_moderators', true);
        $iForumId = $this->getParam('id');
        $aPerms = Phpfox::getService('forum.moderate')->getPerms();
        $aUsers = Phpfox::getService('forum.moderate')->getForForum($iForumId);

        if (count($aUsers)) {
            $aUserPerms = Phpfox::getService('forum.moderate')->getUserPerm($iForumId, $aUsers[0]['user_id'], true);
            foreach ($aUserPerms as &$aUserPerm) {
                $aUserPerm = $aUserPerm['var_name'];
            }
            if (count($aUserPerms)) {
                foreach ($aPerms as $key => $aPerm) {
                    if (in_array($key, $aUserPerms)) {
                        $aPerms[$key]['value'] = 1;
                    } else {
                        $aPerms[$key]['value'] = 0;
                    }
                }
            } else {
                foreach ($aPerms as $key => $aPerm) {
                    $aPerms[$key]['value'] = 0;
                }
            }
        }

        $this->template()->assign(array(
                'sForumDropDown' => Phpfox::getService('forum')->active($iForumId)->getJumpTool(true),
                'aPerms' => $aPerms,
                'aUsers' => $aUsers
            )
        );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('forum.component_block_admincp_moderator_clean')) ? eval($sPlugin) : false);
    }
}