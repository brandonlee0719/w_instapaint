<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Polls\Block;

use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class LatestVotesBlock extends \Phpfox_Component
{
    /**
     * Controller
     * Lods the results of a single poll
     */
    public function process()
    {
        (($sPlugin = Phpfox_Plugin::get('poll.component_block_latest_votes_start')) ? eval($sPlugin) : false);

        $aPoll = $this->getParam('aPoll');
        $iLimit = $this->getParam('limit', 4);
        if (!$aPoll || !(int)$iLimit || !$aPoll['canViewResult'] || !$aPoll['canViewResultVote']) {
            return false;
        }
        $aVotes = Phpfox::getService('poll')->getVotes($aPoll['poll_id'], 1, $iLimit, $iCount);

        if (!count($aVotes)) {
            return false;
        }
        $this->template()->assign(array(
                'sHeader' => _p('latest_votes'),
                'aVotes' => $aVotes,
                'aFooter' => array(
                    _p('view_all_total_votes',
                        ['total' => $aPoll['total_votes']]) => 'javascript:$Core.box(\'poll.pageVotes\', 1000, \'poll_id=' . $aPoll['poll_id'] . '\'); void(0);',
                )
            )
        );
        (($sPlugin = Phpfox_Plugin::get('poll.component_block_latest_votes_end')) ? eval($sPlugin) : false);

        return 'block';
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('Latest Votes Limit'),
                'description' => _p('Define the limit of how many votes can be displayed when viewing the polls detail section. Set 0 will hide this block'),
                'value' => 4,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
        ];
    }

    /**
     * @return array
     */
    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int',
                'min' => 0,
                'title' => _p('"Latest Votes Limit" must be greater than or equal to 0')
            ]
        ];
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('poll.component_block_latest_votes_clean')) ? eval($sPlugin) : false);
    }
}