<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Forums\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class ReplyBlock extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $iThreadId = $this->getParam('iThreadId');
        $aThread = Phpfox::getService('forum.thread')->getActualThread($iThreadId);
        if (!$aThread) {
            return false;
        }
        $aCallback = null;
        if ($aThread['group_id'] > 0 && (Phpfox::isModule('pages') || Phpfox::isModule('groups')) && ($sParentId = Phpfox::getPagesType($aThread['group_id'])) && Phpfox::isModule($sParentId)) {
            $aCallback = Phpfox::callback($sParentId . '.addForum', $aThread['group_id']);
            if (isset($aCallback['module']) && !isset($aCallback['module_id'])) {
                $aCallback['module_id'] = $aCallback['module'];
            }
            if (!Phpfox::getService($sParentId)->hasPerm($aThread['group_id'], 'forum.view_browse_forum')) {
                return Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
            }
        }
        $this->template()->assign([
            'aThread' => $aThread,
            'aForms' => $aThread,
            'aCallback' => $aCallback,
            'iThreadId' => $iThreadId
        ]);

        if (Phpfox::getUserParam('forum.can_add_forum_attachments')) {
            $this->setParam('attachment_share', array(
                    'type' => 'forum',
                    'inline' => true,
                    'id' => 'js_forum_reply',
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
        (($sPlugin = Phpfox_Plugin::get('forum.component_block_reply_clean')) ? eval($sPlugin) : false);
    }
}