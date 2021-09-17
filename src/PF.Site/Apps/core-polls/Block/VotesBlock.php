<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Polls\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class VotesBlock extends Phpfox_Component
{

    public function process()
    {
        (($sPlugin = Phpfox_Plugin::get('poll.component_block_votes_start')) ? eval($sPlugin) : false);

        $iPollId = $this->request()->getInt('req2');
        $iPageSize = 10;
        $iPage = $this->request()->getInt('page', 1);

        $aVotes = [];
        if (empty($iPollId)) {
            $iPollId = $this->getParam('iPoll');
        }

        $iCount = 0;
        if ($iPollId) {
            $aVotes = Phpfox::getService('poll')->getVotes($iPollId, $iPage, $iPageSize, $iCount);
        }

        $aParamsPager = array(
            'page' => $iPage,
            'size' => $iPageSize,
            'count' => $iCount,
            'paging_mode' => 'pagination',
            'ajax_paging' => [
                'block' => 'poll.votes',
                'params' => [
                    'iPoll' => $iPollId
                ],
                'container' => '.js_poll_view_results'
            ]
        );
        $this->template()->assign(array(
                'aVotes' => $aVotes,
                'iPage' => $iPage,
                'bIsPaging' => $this->getParam('ajax_paging', 0)
            )
        );
        Phpfox::getLib('pager')->set($aParamsPager);

        (($sPlugin = Phpfox_Plugin::get('poll.component_block_votes_end')) ? eval($sPlugin) : false);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('poll.component_block_votes_clean')) ? eval($sPlugin) : false);

    }
}