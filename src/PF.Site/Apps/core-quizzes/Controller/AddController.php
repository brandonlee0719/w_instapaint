<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Quizzes\Controller;

use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Validator;

defined('PHPFOX') or exit('NO DICE!');


class AddController extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        Phpfox::isUser(true);

        (($sPlugin = Phpfox_Plugin::get('quiz.component_controller_add_process_start')) ? eval($sPlugin) : false);
        $iMaxAnswers = Phpfox::getUserParam('quiz.max_answers');
        $iMinAnswers = Phpfox::getUserParam('quiz.min_answers');
        $iMaxQuestions = Phpfox::getUserParam('quiz.max_questions');
        $iMinQuestions = Phpfox::getUserParam('quiz.min_questions');
        $sFormSubmit = $this->url()->makeUrl('quiz.add');
        // bErrors is used to tell JS when there has been errors so it knows when to add more
        // questions or not
        $bErrors = 'false';
        $bIsAdd = 'true';
        // determine if we should show the questions and the title sections
        $bShowQuestions = true;
        $bShowTitle = true;

        // Using it:
        $aValidation = array(
            'title' => array(
                'def' => 'required',
                'title' => _p('you_need_to_write_a_title')
            ),
            'description' => array(
                'def' => 'required',
                'title' => _p('you_need_to_write_a_description')
            )
        );
        $oValid = Phpfox_Validator::instance()->set(array(
                'sFormName' => 'js_form',
                'aParams' => $aValidation
            )
        );
        // is user editing?
        if ($iQuizId = $this->request()->getInt('id')) // Editing
        {
            $bIsAdd = 'false';
            $sFormSubmit = $this->url()->makeUrl('current');
            $aQuiz = Phpfox::getService('quiz')->getQuizToEdit($iQuizId);

            if (empty($aQuiz)) {
                return Phpfox_Error::display(_p('that_quiz_does_not_exist_or_its_awaiting_moderation'));
            }

            if ($aVals = $this->request()->getArray('val')) {
                $aQuestions = isset($aVals['q']) ? $aVals['q'] : false;
                $mValid = true;
                if ($aQuestions !== false) {
                    list($mValid, $bNull) = Phpfox::getService('quiz')->checkStructure($aQuestions);
                }

                if ($mValid === true && $oValid->isValid($aVals)) {
                    list($mEdit,) = Phpfox::getService('quiz.process')->update($aVals, Phpfox::getUserId());
                    if ($mEdit === true) {
                        $this->url()->permalink('quiz', $aQuiz['quiz_id'],
                            Phpfox::getLib('parse.input')->clean($aVals['title']), true,
                            _p('your_quiz_has_been_edited'));
                    }
                } else {
                    $aQuiz['questions'] = (isset($bNull) && is_array($bNull)) ? $bNull : ((isset($aVals['q'])) ? $aVals['q'] : array());
                    if (is_array($mValid)) {
                        foreach ($mValid as $sError) {
                            Phpfox_Error::set($sError);
                        }
                    }
                }
            }

            $this->template()->setTitle(_p('edit_quiz'))
                ->setBreadCrumb(_p('quizzes'), $this->url()->makeUrl('quiz'))
                ->assign(array('aQuiz' => $aQuiz, 'aForms' => $aQuiz))
                ->setBreadCrumb(_p('editing_quiz') . ': ' . Phpfox::getLib('parse.output')->shorten($aQuiz['title'],
                        Phpfox::getService('core')->getEditTitleSize(), '...'),
                    $this->url()->makeUrl('quiz.add', array('id' => $aQuiz['quiz_id'])), true);
        } else {
            Phpfox::getUserParam('quiz.can_create_quiz', true);
            $this->template()->setTitle(_p('add_new_quiz'))
                ->setBreadCrumb(_p('quizzes'), $this->url()->makeUrl('quiz'))
                ->setBreadCrumb(_p('add_new_quiz'), $this->url()->makeUrl('quiz.add'), true);
        }


        // are we getting a new quiz
        if (($aVals = $this->request()->getArray('val')) && empty($iQuizId)) {
            // check that there is at least one question and one answer:
            $aQuestions = (isset($aVals['q'])) ? $aVals['q'] : array();
            if (empty($aQuestions)) {
                Phpfox_Error::set(_p('you_need_at_least_one_question_to_create_quiz'));
            } else {
                // moved the contents of the whole check to be called as well when editing
                list($mValid,) = Phpfox::getService('quiz')->checkStructure($aQuestions);
                if ($mValid !== true && is_array($mValid)) {
                    foreach ($mValid as $sError) {
                        Phpfox_Error::set($sError);
                    }
                }
            }

            if (($iFlood = Phpfox::getUserParam('quiz.flood_control_quiz')) !== 0) {
                $aFlood = array(
                    'action' => 'last_post', // The SPAM action
                    'params' => array(
                        'field' => 'time_stamp', // The time stamp field
                        'table' => Phpfox::getT('quiz'), // Database table we plan to check
                        'condition' => 'user_id = ' . Phpfox::getUserId(), // Database WHERE query
                        'time_stamp' => $iFlood * 60 // Seconds);
                    )
                );

                // actually check if flooding
                if (Phpfox::getLib('spam')->check($aFlood)) {
                    Phpfox_Error::set(_p('you_are_creating_a_quiz_a_little_too_soon') . ' ' . Phpfox::getLib('spam')->getWaitTime());
                }
            }
            if (Phpfox::getUserParam('quiz.is_picture_upload_required') && (!isset($aVals['image_path']) || $aVals['image_path'] == '')) {
                Phpfox_Error::set(_p('please_select_quiz_banner'));
            } elseif ($oValid->isValid($aVals)) {
                if (($iId = Phpfox::getService('quiz.process')->add($aVals, Phpfox::getUserId()))) {
                    $this->url()->permalink('quiz', $iId, Phpfox::getLib('parse.input')->clean($aVals['title']), true,
                        (Phpfox::getUserParam('quiz.new_quizzes_need_moderation') ? _p('your_quiz_has_been_added_it_needs_to_be_approved_by_our_staff_before_it_can_be_shown') : _p('your_quiz_has_been_added')));
                } else {
                    Phpfox_Error::set(_p('there_was_an_error_with_your_quiz_please_try_again'));
                }
            } else {
                $aQQuestions['questions'] = (isset($aVals['q'])) ? $aVals['q'] : array();
                $bErrors = true;
                $this->template()->assign(array(
                        'aQuiz' => $aQQuestions,
                        'bErrors' => $bErrors
                    )
                );
            }
        }
        if ($this->request()->getInt('id')) {
            $iQuizOwner = (isset($aQuiz) ? (int)$aQuiz['user_id'] : '');
            if ($iQuizOwner == Phpfox::getUserId()) {
                $bShowTitle = $bShowQuestions = Phpfox::getUserParam('quiz.can_edit_own_questions') || Phpfox::getUserParam('quiz.can_edit_others_questions');

            } else {
                $bShowTitle = $bShowQuestions = Phpfox::getUserParam('quiz.can_edit_others_questions');
            }

            // redirect
            if ($bShowQuestions == false && $bShowTitle == false) {
                return Phpfox_Error::display(_p('you_are_not_allowed_to_edit_this_quiz'));
            }
        }

        $this->template()
            ->setPhrase(array(
                    'you_have_reached_the_maximum_questions_allowed_per_quiz',
                    'you_are_required_a_minimum_of_total_questions',
                    'you_have_reached_the_maximum_answers_allowed_per_question',
                    'you_are_required_a_minimum_of_total_answers_per_question',
                    'are_you_sure',
                    'answer',
                    'delete',
                    'you_cannot_write_more_then_limit_characters',
                    'you_have_limit_character_s_left',
                    'question_count',
                    'answer_count',
                    'notice',
                    'answer'
                )
            )
            ->setHeader(array(
                    'jquery/plugin/jquery.limitTextarea.js' => 'static_script',
                    '<script type="text/javascript">$Behavior.quizAddQuestion = function() { $Core.quiz.init({sRequired:"' . Phpfox::getParam('core.required_symbol') . '", isAdd: ' . $bIsAdd . ', bErrors: ' . $bErrors . ', iMaxAnswers: ' . $iMaxAnswers . ', iMinAnswers: ' . $iMinAnswers . ', iMaxQuestions: ' . $iMaxQuestions . ', iMinQuestions: ' . $iMinQuestions . '}); }</script>'
                )
            )
            ->assign(array(
                    'sCreateJs' => $oValid->createJS(),
                    'sGetJsForm' => $oValid->getJsForm(),
                    'sFormAction' => $sFormSubmit,
                    'bIsAdd' => $bIsAdd,
                    'bShowQuestions' => $bShowQuestions,
                    'bShowTitle' => $bShowTitle,
                    'iMaxFileSize' => (Phpfox::getUserParam('quiz.quiz_max_upload_size') === 0 ? null : Phpfox::getLib('file')->filesize((Phpfox::getUserParam('quiz.quiz_max_upload_size') / 1024) * 1048576)),
                    'iDefaultAnswers' => (setting('quiz.default_answers_count') > user('quiz.max_answers')) ? user('quiz.max_answers') : setting('quiz.default_answers_count')
                )
            );

        if (!empty($aQuiz)) {
            $this->template()->buildPageMenu('js_quizzes_block', [], [
                'link' => Phpfox::permalink('quiz', $aQuiz['quiz_id'], $aQuiz['title']),
                'phrase' => _p('view_quiz')
            ]);
        }

        if (Phpfox::isModule('attachment')) {
            $this->setParam('attachment_share', array(
                    'type' => 'quiz',
                    'id' => 'js_add_quiz_form',
                    'edit_id' => $iQuizId ? $iQuizId : 0,

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
        (($sPlugin = Phpfox_Plugin::get('quiz.component_controller_add_clean')) ? eval($sPlugin) : false);
    }
}