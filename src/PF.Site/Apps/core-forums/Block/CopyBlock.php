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

class CopyBlock extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $aThread = Phpfox::getService('forum.thread')->getActualThread($this->request()->get('thread_id'));

        if (!isset($aThread['thread_id'])) {
            return Phpfox_Error::display(_p('not_a_valid_thread_to_copy'));
        }

        $this->template()->assign(array(
                'sForums' => Phpfox::getService('forum')->active($aThread['forum_id'])->noClosed(true)->getJumpTool(true),
                'aThread' => $aThread
            )
        );
        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('forum.component_block_copy_clean')) ? eval($sPlugin) : false);
    }
}