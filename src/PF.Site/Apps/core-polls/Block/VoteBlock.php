<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Polls\Block;

use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class VoteBlock extends \Phpfox_Component
{
    /**
     * Controller
     * Lods the results of a single poll
     */
    public function process()
    {
        (($sPlugin = Phpfox_Plugin::get('poll.component_block_vote_start')) ? eval($sPlugin) : false);

        Phpfox::isUser(true);

        if (!$this->getParam('iPoll')) {
            return false;
        }

        $aPoll = \Phpfox::getService('poll')->getPollByUrl($this->getParam('iPoll'), false, false, false, true);
        $aPoll['canViewResult'] = ((Phpfox::getUserParam('poll.can_view_user_poll_results_own_poll') && $aPoll['user_id'] == Phpfox::getUserId()) || Phpfox::getUserParam('poll.can_view_user_poll_results_other_poll'));
        $aPoll['canViewResultVote'] = isset($aPoll['user_voted_this_poll']) && ($aPoll['user_voted_this_poll'] == false && Phpfox::getUserParam('poll.view_poll_results_before_vote')) || ($aPoll['user_voted_this_poll'] == true && Phpfox::getUserParam('poll.view_poll_results_after_vote'));
        $this->template()->assign(array(
                'aPoll' => $aPoll,
                'bIsViewingPoll' => $this->getParam('isViewing', false),
            )
        );

        (($sPlugin = Phpfox_Plugin::get('poll.component_block_vote_end')) ? eval($sPlugin) : false);

        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('poll.component_block_vote_clean')) ? eval($sPlugin) : false);
    }
}