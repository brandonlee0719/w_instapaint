<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Quizzes\Controller;

use Phpfox;
use Phpfox_Error;
use Phpfox_Module;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author        phpFox
 * @package        Quiz
 * @version        4.5.3
 *
 */
class ViewController extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        Phpfox::getUserParam('quiz.can_access_quiz', true);

        if ($this->request()->get('req4') && ($this->request()->get('req4') == 'answer')) {
            // check that this user has not taken the quiz yet
            $aVals = $this->request()->getArray('val');
            if (Phpfox::getService('quiz')->hasTakenQuiz(Phpfox::getUserId(), $this->request()->get('req2'))) {
                Phpfox_Error::set(_p('you_have_already_answered_this_quiz'));
            } elseif (!isset($aVals['answer']))// check to see all questions have been answered
            {
                Phpfox_Error::set(_p('you_have_to_answer_the_questions_if_you_want_to_do_that'));
            } else {
                Phpfox::isUser(true);
                // check if user is allowed to answer their own quiz
                $aQuizC = Phpfox::getService('quiz')->getQuizById($this->request()->get('req2'));
                if (!isset($aQuizC['user_id']) || empty($aQuizC['user_id'])) {
                    $this->url()->send('quiz', null, _p('that_quiz_does_not_exist_or_its_awaiting_moderation'));
                }
                $iScore = Phpfox::getService('quiz.process')->answerQuiz($this->request()->get('req2'),
                    $aVals['answer']);
                if (is_numeric($iScore)) { // Answers submitted correctly
                    $this->url()->permalink('quiz', $this->request()->get('req2'), $this->request()->get('req3'), true,
                        _p('your_answers_have_been_submitted_and_your_score_is_score', array('score' => $iScore)),
                        array('results', 'id' => Phpfox::getUserId()));
                } else {
                    $this->template()->assign([
                        'aAnswers' => $aVals['answer']
                    ]);
                    Phpfox_Error::set($iScore);
                }
            }
        }

        $this->setParam('bViewingQuiz', true);
        $bShowResults = false;
        $bShowUsers = false;
        $bCanTakeQuiz = true;
        // $bShowResults == true -> only when viewing results for one user only
        // $bShowUsers == true -> when viewing all results from a quiz

        $sQuizUrl = $this->request()->get('req2');
        $sQuizUrl = Phpfox::getLib('parse.input')->clean($sQuizUrl);

        if ($this->request()->get('req4') == 'results') {
            $bHasTaken = Phpfox::getService('quiz')->hasTakenQuiz(Phpfox::getUserId(), $sQuizUrl);
            if ($bHasTaken) {
                $bCanTakeQuiz = false;
            }

            if ($iUser = $this->request()->getInt('id')) {
                // show the results of just one user
                $aQuiz = Phpfox::getService('quiz')->getQuizByUrl($sQuizUrl, $iUser);
                if (count($aQuiz['results']) && !empty($aQuiz['results'][key($aQuiz['results'])]['user_name'])) {
                    $bShowResults = true;
                }
            } else {
                $bShowUsers = true;
                $iPage = $this->getParam('page', 1);
                $iPageSize = 3;
                $aQuiz = Phpfox::getService('quiz')->getQuizByUrl($sQuizUrl, false,false, $iPageSize, $iPage);
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
            }

            // need it here to have the quiz' info
            if (!Phpfox::getUserParam('quiz.can_view_results_before_answering') && !$bHasTaken && ($aQuiz['user_id'] != Phpfox::getUserId())) {
                $this->url()->send($this->request()->get('req1') . '/' . $this->request()->get('req2') . '/' . $this->request()->get('req3'),
                    null, _p('you_need_to_answer_the_quiz_before_looking_at_the_results'));
            }
            if (Phpfox::getUserParam('quiz.can_post_comment_on_quiz')) {
                $this->template()->assign(array('bShowInputComment' => true))
                    ->setHeader(array(
                            'jquery/plugin/jquery.scrollTo.js' => 'static_script',
                        )
                    );
            }
        } elseif ($this->request()->get('req4') == 'take') {
            $bShowResults = false;
            $bShowUsers = false;
            $bCanTakeQuiz = false;
            $aQuiz = Phpfox::getService('quiz')->getQuizByUrl($sQuizUrl, true, true);
        } else {
            if (Phpfox::getService('quiz')->hasTakenQuiz(Phpfox::getUserId(), $sQuizUrl)) {
                $bCanTakeQuiz = false;
                $bShowResults = false;
                $bShowUsers = true;
                $iPage = $this->getParam('page', 1);
                $iPageSize = 3;
                $aQuiz = Phpfox::getService('quiz')->getQuizByUrl($sQuizUrl, false,false, $iPageSize, $iPage);
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
            } else {
                $bCanTakeQuiz = true;
                $aQuiz = Phpfox::getService('quiz')->getQuizByUrl($sQuizUrl, false, true);
            }
            if (Phpfox::getUserParam('quiz.can_post_comment_on_quiz')) {
                $this->template()->assign(array('bShowInputComment' => true))
                    ->setHeader(array(
                            'jquery/plugin/jquery.scrollTo.js' => 'static_script',
                        )
                    );
            }
        }

        // crash control, in a perfect world this shouldn't happen
        if (empty($aQuiz)) {
            $this->url()->send('quiz', null, _p('that_quiz_does_not_exist_or_its_awaiting_moderation'));
        }
        Phpfox::getService('quiz')->getPermissions($aQuiz);
        if (Phpfox::isUser() && Phpfox::getService('user.block')->isBlocked(null, $aQuiz['user_id'])) {
            return Phpfox_Module::instance()->setController('error.invalid');
        }


        if (Phpfox::isModule('privacy')) {
            if (!isset($aQuiz['is_friend'])) {
                $aQuiz['is_friend'] = 0;
            }
            Phpfox::getService('privacy')->check('quiz', $aQuiz['quiz_id'], $aQuiz['user_id'], $aQuiz['privacy'],
                $aQuiz['is_friend']);
        }

        // extra info: used for displaying results for one user
        if (isset($aQuiz['results']) && count($aQuiz['results'])) {
            $iFirstKey = key($aQuiz['results']);
            $aQuiz['takerInfo']['userinfo'] = array(
                'user_name' => $aQuiz['results'][$iFirstKey]['user_name'],
                'user_id' => $aQuiz['results'][$iFirstKey]['user_id'],
                'server_id' => $aQuiz['results'][$iFirstKey]['user_server_id'],
                'full_name' => $aQuiz['results'][$iFirstKey]['full_name'],
                'gender' => $aQuiz['results'][$iFirstKey]['gender'],
                'user_image' => $aQuiz['results'][$iFirstKey]['user_image']
            );
            $aQuiz['takerInfo']['time_stamp'] = $aQuiz['results'][$iFirstKey]['time_stamp'];
        }

        if (!isset($aQuiz['is_viewed'])) {
            $aQuiz['is_viewed'] = 0;
        }

        // Increment the view counter
        $bUpdateCounter = false;
        if (Phpfox::isModule('track')) {
            if (!$aQuiz['is_viewed'] && !Phpfox::getUserBy('is_invisible')) {
                $bUpdateCounter = true;
                Phpfox::getService('track.process')->add('quiz', $aQuiz['quiz_id']);
            } elseif ($aQuiz['is_viewed'] && !Phpfox::getUserBy('is_invisible')) {
                if (!setting('track.unique_viewers_counter')) {
                    $bUpdateCounter = true;
                    Phpfox::getService('track.process')->add('quiz', $aQuiz['quiz_id']);
                } else {
                    Phpfox::getService('track.process')->update('quiz', $aQuiz['quiz_id']);
                }
            }
        } else {
            $bUpdateCounter = true;
        }
        if ($bUpdateCounter) {
            Phpfox::getService('quiz.process')->updateCounter($aQuiz['quiz_id']);
        }

        if (isset($aQuiz['aTakenBy'])) {
            $this->setParam('aTakers', $aQuiz['aTakenBy']);
        }

        if (Phpfox::isModule('notification') && $aQuiz['user_id'] == Phpfox::getUserId()) {
            Phpfox::getService('notification.process')->delete('quiz_notifyLike', $aQuiz['quiz_id'],
                Phpfox::getUserId());
        }
        $aTitleLabel = [
            'type_id' => 'quiz'
        ];

        if ($aQuiz['is_featured']) {
            $aTitleLabel['label']['featured'] = [
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'label_class' => 'flag_style',
                'icon_class' => 'diamond'

            ];
        }
        if ($aQuiz['is_sponsor']) {
            $aTitleLabel['label']['sponsored'] = [
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'label_class' => 'flag_style',
                'icon_class' => 'sponsor'

            ];
        }
        $aTitleLabel['total_label'] = isset($aTitleLabel['label']) ? count($aTitleLabel['label']) : 0;
        if ($aQuiz['view_id'] == 1) {
            $aTitleLabel['label']['pending'] = [
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'icon_class' => 'clock-o'

            ];
            $aPendingItem = [
                'message' => _p('this_quiz_is_awaiting_moderation'),
                'actions' => []
            ];
            if ($aQuiz['canApprove']) {
                $aPendingItem['actions']['approve'] = [
                    'is_ajax' => true,
                    'label' => _p('approve'),
                    'action' => '$.ajaxCall(\'quiz.approve\',\'iQuiz='.$aQuiz['quiz_id'].'\')'
                ];
            }
            if ($aQuiz['canEdit']) {
                $aPendingItem['actions']['edit'] = [
                    'label' => _p('edit'),
                    'action' => $this->url()->makeUrl('quiz.add',['id' => $aQuiz['quiz_id']]),
                ];
            }
            if ($aQuiz['canDelete']) {
                $aPendingItem['actions']['delete'] = [
                    'is_ajax' => true,
                    'label' => _p('delete'),
                    'action' => '$Core.quiz_moderate.deleteQuiz('.$aQuiz['quiz_id'].',\'viewing\')'
                ];
            }
            $this->template()->assign([
                'aPendingItem' => $aPendingItem
            ]);
        }
        /*
         * the track table is used to track who has viewed the quiz
         * the quiz_result to track who has taken the quiz.
         */
        $this->setParam(array(
                'sTrackType' => 'quiz',
                'iTrackId' => $aQuiz['quiz_id'],
                'iTrackUserId' => $aQuiz['user_id'],
            )
        );

        $this->setParam('aFeed', array(
                'comment_type_id' => 'quiz',
                'privacy' => $aQuiz['privacy'],
                'comment_privacy' => Phpfox::getUserParam('quiz.can_post_comment_on_quiz') ? 0 : 3,
                'like_type_id' => 'quiz',
                'feed_is_liked' => $aQuiz['is_liked'],
                'feed_is_friend' => $aQuiz['is_friend'],
                'item_id' => $aQuiz['quiz_id'],
                'user_id' => $aQuiz['user_id'],
                'total_comment' => $aQuiz['total_comment'],
                'total_like' => $aQuiz['total_like'],
                'feed_link' => $this->url()->permalink('quiz', $aQuiz['quiz_id'], $aQuiz['title']),
                'feed_title' => $aQuiz['title'],
                'feed_display' => 'view',
                'feed_total_like' => $aQuiz['total_like'],
                'report_module' => 'quiz',
                'report_phrase' => _p('report_this_quiz')
            )
        );

        $this->template()->setTitle($aQuiz['title'])
            ->setTitle(_p('quizzes'))
            ->setBreadCrumb(_p('quizzes'), $this->url()->makeUrl('quiz'))
            ->setBreadCrumb($aQuiz['title'], $this->url()->permalink('quiz', $aQuiz['quiz_id'], $aQuiz['title']),
                true)
            ->setMeta('description', _p('full_name_s_quiz_from_time_stamp_title', array(
                        'full_name' => $aQuiz['full_name'],
                        'time_stamp' => Phpfox::getTime(Phpfox::getParam('core.description_time_stamp'),
                            $aQuiz['time_stamp']),
                        'title' => $aQuiz['title']
                    )
                )
            )
            ->setMeta('og:image', Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aQuiz['server_id'],
                    'path' => 'quiz.url_image',
                    'file' => $aQuiz['image_path'],
                    'suffix' => '',
                    'return_url' => true
                )
            ))
            ->setMeta('keywords', $this->template()->getKeywords($aQuiz['title']))
            ->setMeta('keywords', Phpfox::getParam('quiz.quiz_meta_keywords'))
            ->setMeta('description', Phpfox::getParam('quiz.quiz_meta_description'))
            ->setMeta('description', $aQuiz['description'])
            ->setMeta(array(
                'og:image:type' => 'image/jpeg',
                'og:image:width' => '500',
                'og:image:height' => '500'
            ))
            ->assign(array(
                    'bIsViewingQuiz' => true,
                    'bShowResults' => $bShowResults,
                    'bShowUsers' => $bShowUsers,
                    'bCanTakeQuiz' => $bCanTakeQuiz,
                    'bCanAnswer' => ((Phpfox::getUserParam('quiz.can_answer_own_quiz') && $aQuiz['user_id'] == Phpfox::getUserId()) || ($aQuiz['user_id'] != Phpfox::getUserId())),
                    'aQuiz' => $aQuiz,
                    'sShareDescription' => str_replace(array("\n", "\r", "\r\n"), '', $aQuiz['description']),
                    'sAddThisPubId' => setting('core.addthis_pub_id', ''),
                    'aTitleLabel' => $aTitleLabel,
                    'iUserResult' => $this->request()->getInt('id',0)
                )
            )
            ->setPhrase(array(
                    'are_you_sure_you_want_to_delete_this_quiz'
                )
            )
            ->setHeader('cache', array(
                    'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                    'jquery/plugin/jquery.scrollTo.js' => 'static_script',
                )
            );

        Phpfox::getService('quiz')->buildSectionMenu();

        (($sPlugin = Phpfox_Plugin::get('quiz.component_controller_view_process_end')) ? eval($sPlugin) : false);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('quiz.component_controller_view_clean')) ? eval($sPlugin) : false);
    }
}