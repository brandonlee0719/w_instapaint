<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Quizzes\Block;

use Phpfox;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author        phpFox
 * @package        Quiz
 * @version        4.5.3
 */
class StatBlock extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        if ($this->getParam('bViewingQuiz', false) !== true) {
            return false;
        }
        $bCanViewResult = true;
        // get the recent visitors of this quiz
        $aRecent = $this->getParam('aTakers', '');
        $iQuizId = $this->request()->get('req2');
        $iOwner = $this->getParam('iTrackUserId');
        $iLimit = $this->getParam('limit');
        if (!(int)$iLimit) {
            return false;
        }
        if (!isset($aRecent['iSuccessPercentage'])) {
            $aRecent = Phpfox::getService('quiz')->getRecentTakers($iQuizId, $iLimit);
        }
        $bHasTaken = Phpfox::getService('quiz')->hasTakenQuiz(Phpfox::getUserId(), $iQuizId);
        // need it here to have the quiz' info
        if (!Phpfox::getUserParam('quiz.can_view_results_before_answering') && !$bHasTaken && ($iOwner != Phpfox::getUserId())) {
            $bCanViewResult = false;
        }

        if ($aRecent === false) {
            return false;
        }

        $iTakersToShow = $this->getParam('limit');
        $iLimit = ($iTakersToShow > 0) ? $iTakersToShow : 4;
        if (count($aRecent) > $iLimit) {
            array_splice($aRecent, $iLimit);
        }
        $this->template()->assign(array(
                'sHeader' => _p('recently_taken'),
                'aFooter' => $bCanViewResult ? array(
                    _p('view_more') => $this->url()->permalink(array('quiz', 'results'), $this->request()->get('req2'),
                        $this->request()->get('req3'))
                ) : null,
                'aQuizTakers' => $aRecent !== false ? $aRecent : array(),
                'bCanViewResult' => $bCanViewResult
            )
        );

        return 'block';
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('Recent Taken Limit'),
                'description' => _p('Define the limit of how many results can be displayed in this block when viewing the quiz detail. Set 0 will hide this block'),
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
                'title' => _p('"Recent Taken Limit" must be greater than or equal to 0')
            ],
        ];
    }
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('quiz.component_block_stat_clean')) ? eval($sPlugin) : false);
    }
}