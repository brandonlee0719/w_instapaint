<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Quizzes\Service;

use Phpfox;
use Phpfox_Error;
use Phpfox_File;
use Phpfox_Plugin;
use Phpfox_Url;

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author        phpFox
 * @package        Quiz
 * @version        4.5.3
 */
class Process extends \Phpfox_Service
{
    private $_aPhotoSizes;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('quiz');
        $this->_aPhotoSizes = array(50, 200, 500);
    }

    /**
     * submits one user's answers to a quiz
     * @param integer $iUser
     * @param array $aAnswers array('questionid' => 'answerid')
     * @return mixed    int if ok (score), string on error
     */
    public function answerQuiz($sQuiz, $aAnswers)
    {
        // security checks
        $iUser = Phpfox::getUserId();
        // we need to count how many questions are there for this quiz...

        // get the questions for this quiz
        $aDbQuiz = $this->database()->select('q.*, qq.*')
            ->from($this->_sTable, 'q')
            ->join(Phpfox::getT('quiz_question'), 'qq', 'qq.quiz_id = q.quiz_id')
            ->where('q.quiz_id = ' . (int)$sQuiz)
            ->execute('getSlaveRows');

        if ($aDbQuiz[0]['view_id'] == 1) {
            return _p('you_cannot_answer_a_quiz_that_has_not_been_approved');
        }
        if (count($aDbQuiz) != count($aAnswers)) {
            return _p('you_need_to_answer_every_question');
        }

        // check if user can answer his own quizzes
        if (!Phpfox::getUserParam('quiz.can_answer_own_quiz')) {
            // check if its the same user
            if ($aDbQuiz[0]['user_id'] == $iUser) {
                return _p('you_cannot_answer_your_own_quiz');
            }
        }
        // insert all the answers to the DB and build OR query
        $sQuestionsId = 'is_correct = 1 AND ( 1 = 2';
        foreach ($aAnswers as $iQuestion => $iAnswer) {

            $this->database()->insert(Phpfox::getT('quiz_result'), array(
                    'quiz_id' => $aDbQuiz[0]['quiz_id'],
                    'question_id' => $iQuestion,
                    'answer_id' => $iAnswer,
                    'user_id' => $iUser,
                    'time_stamp' => PHPFOX_TIME
                )
            );
            $sQuestionsId .= ' OR question_id = ' . $iQuestion;
        }
        //update total play for quiz
        db()->updateCounter('quiz', 'total_play', 'quiz_id', $aDbQuiz[0]['quiz_id']);

        if (Phpfox::isModule('notification')) {
            Phpfox::getService('notification.process')->add('quiz_answer', $aDbQuiz[0]['quiz_id'], $aDbQuiz[0]['user_id'], $iUser);
        }
        //get the success for this quiz by this user
        $aCorrectAnswers = $this->database()->select('answer_id')
            ->from(Phpfox::getT('quiz_answer'))
            ->where($sQuestionsId . ')')
            ->execute('getSlaveRows');

        $iTotalCorrect = 0;
        foreach ($aCorrectAnswers as $iAnswerId) {
            $mSearch = array_search($iAnswerId['answer_id'], $aAnswers);

            if ($mSearch !== false) {
                $iTotalCorrect++;
            }
        }
        if ($sPlugin = Phpfox_Plugin::get('quiz.service_process_answerquiz_1')) {
            eval($sPlugin);
        }

        return round(($iTotalCorrect / count($aAnswers)) * 100);
    }

    /**
     * Approves a quiz -> sets its view_id to 0
     * @param int $iQuiz
     * @return boolean true on success
     */
    public function approveQuiz($iQuiz)
    {
        $aQuiz = $this->database()->select('*')
            ->from(Phpfox::getT('quiz'))
            ->where('quiz_id = ' . (int)$iQuiz)
            ->execute('getSlaveRow');

        if (!isset($aQuiz['quiz_id'])) {
            return false;
        }

        if (Phpfox::isModule('notification')) {
            Phpfox::getService('notification.process')->add('quiz_approved', $aQuiz['quiz_id'], $aQuiz['user_id']);
        }

        $bUpdate = $this->database()->update($this->_sTable, array('view_id' => '0', 'time_stamp' => PHPFOX_TIME),
            'quiz_id = ' . (int)$iQuiz) == 1 ? true : false;

        // check if it had been added before
        if (Phpfox::isModule('feed')) {
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('quiz', $aQuiz['quiz_id'],
                $aQuiz['privacy'], (isset($aQuiz['privacy_comment']) ? (int)$aQuiz['privacy_comment'] : 0), 0,
                $aQuiz['user_id']) : null);
        }

        // Send the user an email
        $sLink = Phpfox_Url::instance()->permalink('quiz', $aQuiz['quiz_id'], $aQuiz['title']);
        Phpfox::getLib('mail')->to($aQuiz['user_id'])
            ->subject('Your quiz "' . $aQuiz['title'] . '" has been approved')
            ->message("Your quiz \"<a href=\"" . $sLink . "\">" . $aQuiz['title'] . "</a>\" has been approved.\nTo view this quiz follow the link below:\n<a href=\"" . $sLink . "\">" . $sLink . "</a>")
            ->send();

        // Update user activity
        Phpfox::getService('user.activity')->update($aQuiz['user_id'], 'quiz');
        if ($sPlugin = Phpfox_Plugin::get('quiz.service_process_approvequiz_1')) {
            eval($sPlugin);
        }
        return $bUpdate;
    }

    /**
     * It deletes the existing questions and answers (if user has permission to edit that)
     * and reinserts, it relies on JS to keep the indexes and runs one query to be able to
     * compare users and set the title right on the "new" quiz.
     * @param array $aQuiz This array holds all the information that is going to be the final quiz
     * @return string on error | true on success
     */
    public function update($aQuiz, $iUser)
    {
        // sanity check
        if (!isset($aQuiz) || empty($aQuiz)) {
            return 'Corrupted input';
        }

        Phpfox::getService('ban')->checkAutomaticBan($aQuiz['title'] . ' ' . $aQuiz['description']);
        // check permissions
        $iCurrent = Phpfox::getUserId();
        $aOriginalQuiz = $this->database()
            ->select('user_id, title, image_path, server_id')
            ->from($this->_sTable)
            ->where('quiz_id = ' . (int)$aQuiz['quiz_id'])
            ->execute('getSlaveRow');
        $iQuizOwner = $aOriginalQuiz['user_id'];

        // check if can edit own items
        $bGuestIsOwner = $iCurrent == $iQuizOwner;
        $bEditOwn = (Phpfox::getUserParam('quiz.can_edit_own_questions'));
        $bEditOthers = (Phpfox::getUserParam('quiz.can_edit_others_questions'));
        // check if user can edit anything
        if (!$bEditOthers && !$bEditOwn) {
            return _p('you_do_not_have_the_permission_to_edit_this_quiz');
        }

        if (empty($aQuiz['privacy'])) {
            $aQuiz['privacy'] = 0;
        }

        if (empty($aQuiz['privacy_comment'])) {
            $aQuiz['privacy_comment'] = 0;
        }
        if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support')) {
            Phpfox::getService('tag.process')->update('quiz', $aQuiz['quiz_id'], Phpfox::getUserId(),
                $aQuiz['description'], true);
        } else {
            if (Phpfox::isModule('tag')) {
                Phpfox::getService('tag.process')->update('quiz', $aQuiz['quiz_id'], Phpfox::getUserId(),
                    (!Phpfox::getLib('parse.format')->isEmpty($aQuiz['tag_list']) ? $aQuiz['tag_list'] : null));
            }
        }
        $bHasAttachments = (!empty($aQuiz['attachment']));

        if ($bHasAttachments) {
            Phpfox::getService('attachment.process')->updateItemId($aQuiz['attachment'], Phpfox::getUserId(),
                $aQuiz['quiz_id']);
        }


        // update title, description and privacy
        $aUpdate = array(
            'privacy' => (isset($aQuiz['privacy']) ? $aQuiz['privacy'] : '0'),
            'privacy_comment' => (isset($aQuiz['privacy_comment']) ? $aQuiz['privacy_comment'] : '0'),
            'title' => Phpfox::getLib('parse.input')->clean($aQuiz['title']),
            'description' => Phpfox::getLib('parse.input')->clean($aQuiz['description']),
            'description_parsed' => Phpfox::getLib('parse.input')->prepare($aQuiz['description']),
            'total_attachment' => (Phpfox::isModule('attachment') ? Phpfox::getService('attachment')->getCountForItem($aQuiz['quiz_id'],
                'quiz') : '0'),
            'server_id' => !empty($aQuiz['server_id']) ? $aQuiz['server_id'] : 0,
        );
        if (!empty($aOriginalQuiz['image_path']) && (!empty($aQuiz['temp_file']) || !empty($aQuiz['remove_photo']))) {
            if ($this->deleteImage($aQuiz['quiz_id'],$iQuizOwner)) {
                $aUpdate['image_path'] = null;
                $aUpdate['server_id'] = 0;
            }
            else {
                return false;
            }
        }
        if (!empty($aQuiz['temp_file'])) {
            $aFile = Phpfox::getService('core.temp-file')->get($aQuiz['temp_file']);
            if (!empty($aFile)) {
                if (!Phpfox::getService('user.space')->isAllowedToUpload($iQuizOwner, $aFile['size'])) {
                    Phpfox::getService('core.temp-file')->delete($aQuiz['temp_file'], true);
                    return false;
                }
                $aUpdate['image_path'] = $aFile['path'];
                $aUpdate['server_id'] = $aFile['server_id'];
                Phpfox::getService('user.space')->update($iQuizOwner, 'quiz', $aFile['size']);
                Phpfox::getService('core.temp-file')->delete($aQuiz['temp_file']);
            }
        }
        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->update('quiz', $aQuiz['quiz_id'],
            $aQuiz['privacy'], (isset($aQuiz['privacy_comment']) ? (int)$aQuiz['privacy_comment'] : 0)) : null);


        $this->database()->update($this->_sTable, $aUpdate, 'quiz_id = ' . (int)$aQuiz['quiz_id']);


        if (isset($aQuiz['q']) && ((Phpfox::getUserParam('quiz.can_edit_others_questions') && !$bGuestIsOwner) ||
                (Phpfox::getUserParam('quiz.can_edit_own_questions') && $bGuestIsOwner))
        ) {

            // Step 1 : Delete all the questions from this quiz.
            $aFormerQuestions = $this->database()->select('qq.question_id')
                ->from(Phpfox::getT('quiz_question'), 'qq')
                ->where('qq.quiz_id = ' . (int)$aQuiz['quiz_id'])
                ->execute('getSlaveRows');

            $sQuestionId = '';
            foreach ($aFormerQuestions as $aFormer) {
                $sQuestionId .= ' OR question_id = ' . $aFormer['question_id'];
            }
            $sQuestionId = substr($sQuestionId, 4);

            // Step 1. Delete all current answers and questions
            $this->database()->delete(Phpfox::getT('quiz_question'), $sQuestionId);
            $this->database()->delete(Phpfox::getT('quiz_answer'), $sQuestionId);
            foreach ($aQuiz['q'] as $aKey => $aQuestion) {
                // Step 2. Insert the question
                $aQuestionInsert = array(
                    'question' => Phpfox::getLib('parse.input')->clean($aQuestion['question'],255),
                    'quiz_id' => $aQuiz['quiz_id']
                );

                // safer if we get the question_id from the answer
                $aFirstAnswer = reset($aQuestion['answers']);
                $iQuestionId = $aFirstAnswer['question_id'];
                if (isset($aQuestion['question_id'])) { // it means we're updating
                    $aQuestionInsert['question_id'] = $iQuestionId;
                }
                $iQuestionId = $this->database()->insert(Phpfox::getT('quiz_question'), $aQuestionInsert);

                // Step 3 Insert the answers
                foreach ($aQuestion['answers'] as $aAnswer) {
                    $aAnswerInsert = array(
                        'question_id' => $iQuestionId,
                        'answer' => Phpfox::getLib('parse.input')->clean($aAnswer['answer'],255),
                        'is_correct' => $aAnswer['is_correct']
                    );
                    if (isset($aAnswer['answer_id']) && !empty($aAnswer['answer_id'])) {
                        // An update means Delete + Insert
                        $aAnswerInsert['answer_id'] = $aAnswer['answer_id'];
                    }
                    $this->database()->insert(Phpfox::getT('quiz_answer'), $aAnswerInsert);
                } // end loop answers
            } // end loop questions
        } // end editing questions/answers

        if (Phpfox::isModule('privacy')) {
            if ($aQuiz['privacy'] == '4') {
                Phpfox::getService('privacy.process')->update('quiz', $aQuiz['quiz_id'],
                    (isset($aQuiz['privacy_list']) ? $aQuiz['privacy_list'] : array()));
            } else {
                Phpfox::getService('privacy.process')->delete('quiz', $aQuiz['quiz_id']);
            }
        }

        Phpfox::getService('feed.process')->clearCache('quiz', $aQuiz['quiz_id']);

        if ($sPlugin = Phpfox_Plugin::get('quiz.service_process_update_1')) {
            eval($sPlugin);
        }

        if (isset($sTitleUrl)) {
            return array(true, $sTitleUrl);
        }

        return array(true, false);
    }

    /**
     * Deletes the image in a quiz
     * @param integer $iQuiz Quiz identifier
     * @param integer $iUser User identifier
     * @return boolean
     */
    public function deleteImage($iQuiz, $iUser)
    {
        $iUser = (int)$iUser;
        $iQuiz = (int)$iQuiz;
        if ($sPlugin = Phpfox_Plugin::get('quiz.service_process_deleteimage_start')) {
            eval($sPlugin);
        }

        $aQuiz = db()->select('image_path, server_id, user_id')
                        ->from(':quiz')
                        ->where('quiz_id ='.(int)$iQuiz)
                        ->execute('getRow');

        // calculate space used
        if (!empty($aQuiz['image_path'])) {
            $aParams = Phpfox::getService('quiz')->getUploadParams();
            $aParams['type'] = 'quiz';
            $aParams['path'] = $aQuiz['image_path'];
            $aParams['user_id'] = $iUser;
            $aParams['update_space'] = ($iUser ? true : false);
            $aParams['server_id'] = $aQuiz['server_id'];

            if (Phpfox::getService('user.file')->remove($aParams)) {
                if (!isset($bSkipDefaultReturn)) {
                    $this->database()->update(Phpfox::getT('quiz'), array('image_path' => null,'server_id' => 0),
                        'quiz_id = ' . $iQuiz);
                }
            }
            else {
                return false;
            }
        }

        if ($sPlugin = Phpfox_Plugin::get('quiz.service_process_deleteimage_end')) {
            eval($sPlugin);
        }
        return true;

    }

    /**
     * Updates the total comment counter
     * @param integer $iId Quiz Id
     * @param boolean $bMinus if true it decrements, if false it increses the counter
     */
    public function updateCounter($iId, $bMinus = false)
    {
        $this->database()->update($this->_sTable, ['total_view' => 'total_view ' . ($bMinus ? "-" : "+") . ' 1'],
            ['quiz_id' => (int)$iId], false);
    }

    /**
     * Updates the counter of a quiz views (increments) if the current user has
     * not seen it (avoid false positives)
     *
     * @param int $iId quiz_id
     * @param $iUser int, deprecated, will be removed in 4.7.0
     * @return true
     * @deprecated from 4.6.0
     */
    public function updateView(&$aQuiz, $iUser = null)
    {
        $iId = (int)$aQuiz['quiz_id'];

        return $this->database()->update($this->_sTable, ['total_view' => 'total_view + 1'], ['quiz_id' => (int)$iId],
            false);
    }

    /**
     * Deletes a quiz from the database along with its results, answers and questions
     * @param int $iQuiz
     * @param int $iUser User deleting the quiz (can be an admin or the quiz owner)
     * @return boolean
     */
    public function deleteQuiz($iQuiz, $iUser)
    {
        // we need to get all the questions by joining to the questions table
        $aAnswers = $this->database()->select('qq.question_id, q.user_id')
            ->from(Phpfox::getT('quiz_question'), 'qq')
            ->join($this->_sTable, 'q', 'q.quiz_id = ' . (int)$iQuiz)
            ->where('qq.quiz_id  = ' . (int)$iQuiz)
            ->execute('getSlaveRows');

        $sAnswers = "(1 = 2) ";
        $iUserId = 0;
        foreach ($aAnswers as $aAnswer) {
            $sAnswers .= ' OR question_id = ' . $aAnswer['question_id'];
            $iUserId = $aAnswer['user_id'];
        }
        $isOwner = ($iUserId == $iUser);
        if (($isOwner && !Phpfox::getUserParam('quiz.can_delete_own_quiz') ||
            (!$isOwner && !Phpfox::getUserParam('quiz.can_delete_others_quizzes')))
        ) {
            return false;
        }

        $this->deleteImage($iQuiz, $iUser);

        $bDel = true;
        $bDel = $bDel && $this->database()->delete($this->_sTable, 'quiz_id = ' . (int)$iQuiz);
        $bDel = $bDel && $this->database()->delete(Phpfox::getT('track'),
                'item_id = ' . (int)$iQuiz . ' AND type_id="quiz"');
        $bDel = $bDel && $this->database()->delete(Phpfox::getT('quiz_answer'), $sAnswers);
        $bDel = $bDel && $this->database()->delete(Phpfox::getT('quiz_question'), 'quiz_id = ' . (int)$iQuiz);
        $bDel = $bDel && $this->database()->delete(Phpfox::getT('quiz_result'), 'quiz_id = ' . (int)$iQuiz);

        // Update user activity
        Phpfox::getService('user.activity')->update($iUserId, 'quiz', '-');

        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('quiz', $iQuiz) : null);
        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('comment_quiz', $iQuiz) : null);
        (Phpfox::isModule('like') ? Phpfox::getService('like.process')->delete('quiz', $iQuiz, 0, true) : null);

        (Phpfox::isModule('comment') ? Phpfox::getService('comment.process')->deleteForItem($iUserId, $iQuiz,
            'quiz') : null);

        (Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->deleteAllOfItem([
            'quiz_like',
            'quiz_approved'
        ], (int)$iQuiz) : null);
        if (Phpfox::isModule('tag')) {
            $this->database()->delete(Phpfox::getT('tag'), 'item_id = ' . $iQuiz . ' AND category_id = "quiz"', 1);
            $this->cache()->remove();
        }
        //close all sponsorships
        (Phpfox::isModule('ad') ? Phpfox::getService('ad.process')->closeSponsorItem('quiz',
            (int)$iQuiz) : null);
        if ($sPlugin = Phpfox_Plugin::get('quiz.service_process_deletequiz_1')) {
            eval($sPlugin);
        }

        return $bDel;
    }

    /**
     *    Adds a new Quiz
     * @param array $aVals
     * @param int $iUser
     * @return boolean
     */
    public function add(&$aVals, $iUser)
    {
        // case where user had JS disabled
        if (!isset($aVals['q'])) {
            return false;
        }
        /* check for banned words */
        foreach ($aVals['q'] as $aQuestions) {
            Phpfox::getService('ban')->checkAutomaticBan($aQuestions['question']);
            foreach ($aQuestions['answers'] as $aAnswer) {
                Phpfox::getService('ban')->checkAutomaticBan($aAnswer['answer']);
            }
        }
        Phpfox::getService('ban')->checkAutomaticBan($aVals['title'] . ' ' . $aVals['description']);

        if (empty($aVals['privacy'])) {
            $aVals['privacy'] = 0;
        }

        if (empty($aVals['privacy_comment'])) {
            $aVals['privacy_comment'] = 0;
        }
        $bHasAttachments = (!empty($aVals['attachment']));

        $aInsert = [
            'view_id' => $aVals['view_id'] = Phpfox::getUserParam('quiz.new_quizzes_need_moderation') ? 1 : 0,
            'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
            'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
            'user_id' => (int)$iUser,
            'title' => $this->preParse()->clean($aVals['title'], 255),
            'description' => $this->preParse()->clean($aVals['description']),
            'description_parsed' => $this->preParse()->prepare($aVals['description']),
            'time_stamp' => PHPFOX_TIME,
            'total_attachment' => 0,
        ];
        // insert to the quiz table:
        if (Phpfox::getUserParam('quiz.can_upload_picture')) {
            if (!empty($aVals['temp_file'])) {
                $aFile = Phpfox::getService('core.temp-file')->get($aVals['temp_file']);
                if (!empty($aFile)) {
                    if (!Phpfox::getService('user.space')->isAllowedToUpload($iUser, $aFile['size'])) {
                        Phpfox::getService('core.temp-file')->delete($aVals['temp_file'], true);
                        return false;
                    }
                    $aInsert['image_path'] = $aFile['path'];
                    $aInsert['server_id'] = $aFile['server_id'];
                    Phpfox::getService('user.space')->update($iUser, 'quiz', $aFile['size']);
                    Phpfox::getService('core.temp-file')->delete($aVals['temp_file']);
                }
            }
        }
        $iQuizId = $this->database()->insert($this->_sTable, $aInsert);

        //Add hashtag
        if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support')) {
            Phpfox::getService('tag.process')->add('quiz', $iQuizId, Phpfox::getUserId(), $aVals['description'], true);
        } else {
            if (Phpfox::isModule('tag') && isset($aVals['tag_list']) && ((is_array($aVals['tag_list']) && count($aVals['tag_list'])) || (!empty($aVals['tag_list'])))) {
                Phpfox::getService('tag.process')->add('quiz', $iQuizId, Phpfox::getUserId(), $aVals['tag_list']);
            }
        }
        // If we uploaded any attachments make sure we update the 'item_id'
        if ($bHasAttachments) {
            Phpfox::getService('attachment.process')->updateItemId($aVals['attachment'], Phpfox::getUserId(), $iQuizId);
        }
        // now we insert the questions and the answers
        foreach ($aVals['q'] as $aQuestions) {
            // first we need to insert the question to get its ID
            $iQuestionId = $this->database()->insert(Phpfox::getT('quiz_question'), array(
                    'quiz_id' => $iQuizId,
                    'question' => Phpfox::getLib('parse.input')->clean($aQuestions['question'], 255)
                )
            );

            foreach ($aQuestions['answers'] as $aAnswer) {
                $this->database()->insert(Phpfox::getT('quiz_answer'), array(
                        'question_id' => $iQuestionId,
                        'answer' => Phpfox::getLib('parse.input')->clean($aAnswer['answer'], 255),
                        'is_correct' => (int)$aAnswer['is_correct']
                    )
                );
            }
        }

        if (!Phpfox::getUserParam('quiz.new_quizzes_need_moderation')) {
            if (Phpfox::isModule('feed')) {
                (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('quiz', $iQuizId, $aVals['privacy'],
                    (isset($aVals['privacy_comment']) ? (int)$aVals['privacy_comment'] : 0)) : null);
            }

            // Update user activity
            Phpfox::getService('user.activity')->update(Phpfox::getUserId(), 'quiz');
        }

        if ($aVals['privacy'] == '4') {
            Phpfox::getService('privacy.process')->add('quiz', $iQuizId,
                (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
        }

        // Plugin call
        if ($sPlugin = Phpfox_Plugin::get('quiz.service_process_add__end')) {
            eval($sPlugin);
        }

        return $iQuizId;
    }

    /**
     * @param $iId
     * @param $sType
     * @return bool
     */
    public function sponsor($iId, $sType)
    {
        if (!Phpfox::getUserParam('quiz.can_sponsor_quiz') && !Phpfox::getUserParam('quiz.can_purchase_sponsor_quiz') && !defined('PHPFOX_API_CALLBACK')) {
            return Phpfox_Error::set(_p('hack_attempt'));
        }

        $iType = (int)$sType;
        if ($iType != 0 && $iType != 1) {
            return false;
        }
        db()->update($this->_sTable, array('is_sponsor' => $iType), 'quiz_id = ' . (int)$iId);
        if ($sPlugin = Phpfox_Plugin::get('quiz.service_process_sponsor__end')) {
            eval($sPlugin);
        }
        return true;
    }

    /**
     * @param $iId
     * @param $iType
     * @return bool
     */
    public function feature($iId, $iType)
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('quiz.can_feature_quiz', true);

        $this->database()->update($this->_sTable, array('is_featured' => ($iType ? '1' : '0')),
            'quiz_id = ' . (int)$iId);


        return true;
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('quiz.service_process__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}