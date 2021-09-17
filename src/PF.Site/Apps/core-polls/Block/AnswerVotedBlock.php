<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Polls\Block;

use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class AnswerVotedBlock extends \Phpfox_Component
{
    /**
     * Controller
     * Lods the results of a single poll
     */
    public function process()
    {

        // Get params
        Phpfox::isUser(true);
        $iPage = $this->getParam('page', 1);
        $iLimit = 12;
        $iAnswerId = $this->getParam('answer_id');
        if (!$iAnswerId) {
            return false;
        }

        $aAnswerVote = Phpfox::getService('poll')->getVotesByAnswer($iAnswerId, $iPage, $iLimit, $iCount);

        $bIsVoted = Phpfox::getService('poll')->isVoted($iAnswerId);
        $aParamsPager = array(
            'page' => $iPage,
            'size' => $iLimit,
            'count' => $iCount,
            'paging_mode' => 'pagination',
            'ajax_paging' => [
                'block' => 'poll.answer-voted',
                'params' => [
                    'answer_id' => $iAnswerId,
                    'answer' => $this->getParam('answer')
                ],
                'container' => '.js_poll_answer_voted'
            ]
        );
        $this->template()->assign(array(
                'aAnswerVote' => $aAnswerVote,
                'sAnswer' => $this->getParam('answer'),
                'bIsVoted' => $bIsVoted,
                'iTotalVotes' => $bIsVoted ? ($iCount - 1) : $iCount,
                'iPage' => $iPage,
                'bIsPaging' => $this->getParam('ajax_paging', 0)
            )
        );
        Phpfox::getLib('pager')->set($aParamsPager);

        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('poll.component_block_answer_voted_clean')) ? eval($sPlugin) : false);
    }
}