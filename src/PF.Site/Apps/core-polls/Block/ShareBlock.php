<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Polls\Block;

use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class ShareBlock extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $this->template()->assign(array(
                'iMaxAnswers' => (int)Phpfox::getUserParam('poll.maximum_answers_count'),
                'iMinAnswers' => 2,
                'sPhraseKey' => 'poll.you_have_reached_your_limit',
                'sPhraseValue' => _p('you_have_reached_your_limit')
            )
        );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('poll.component_block_share_clean')) ? eval($sPlugin) : false);
    }
}