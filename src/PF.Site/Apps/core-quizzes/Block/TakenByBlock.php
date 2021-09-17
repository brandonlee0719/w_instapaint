<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Quizzes\Block;

use Phpfox;

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author        phpFox
 * @package        Quiz
 * @version        4.5.3
 */
class TakenByBlock extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $iQuizId =  $this->getParam('iQuizId');
        $iPageSize = $this->getParam('iLimit', 3);
        $iPage = $this->getParam('page', 1);
        $aQuiz = Phpfox::getService('quiz')->getQuizByUrl($iQuizId, false,false, $iPageSize, $iPage);
        $aParamsPager = array(
            'page' => $iPage,
            'size' => $iPageSize,
            'count' => $aQuiz['total_play'],
            'paging_mode' => 'pagination',
            'ajax_paging' => [
                'block' => 'quiz.takenby',
                'params' => [
                    'iLimit' => $iPageSize,
                    'iQuizId' => $aQuiz['quiz_id']
                ],
                'container' => '.js_quiz_user_lists'
            ]
        );
        Phpfox::getLib('pager')->set($aParamsPager);
        $this->template()->assign([
            'aQuiz' => $aQuiz,
        ]);
        return 'block';
    }
}