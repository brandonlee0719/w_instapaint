<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Polls\Service;

use Phpfox;
use Phpfox_Database;
use Phpfox_Error;
use Phpfox_File;
use Phpfox_Plugin;
use Phpfox_Url;

defined('PHPFOX') or exit('NO DICE!');

class Process extends \Phpfox_Service
{
    private $_aPhotoSizes;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('poll');
        $this->_aPhotoSizes = array(50, 200, 500);
    }

    /**
     * Adds a poll
     *
     * @param array $aVals input from the form after validated
     * @param integer $iUser user_id of the owner of the poll
     * @param bool $bIsUpdate default is "false"
     *
     * @return mixed
     */
    public function add($iUser, $aVals, $bIsUpdate = false)
    {
        (($sPlugin = Phpfox_Plugin::get('poll.service_process_add_start')) ? eval($sPlugin) : false);
        $sAnswers = '';
        $aTotalVotes = [];
        if (isset($aVals['answer']) && is_array($aVals['answer'])) {
            foreach ($aVals['answer'] as $aAnswer) {
                $sAnswers .= $aAnswer['answer'] . ' ';
            }
        }
        \Phpfox::getService('ban')->checkAutomaticBan($aVals['question'] . ' ' . $sAnswers);
        if (!isset($aVals['randomize'])) {
            $aVals['randomize'] = 0;
        }

        if (!isset($aVals['hide_vote'])) {
            $aVals['hide_vote'] = 0;
        }

        if (!isset($aVals['privacy'])) {
            $aVals['privacy'] = 0;
        }
        $bIsCustom = ((!empty($aVals['module_id'])) ? true : false);
        if ($bIsCustom) {
            $aVals['randomize'] = '0';
        }

        $aInsert = array(
            'question' => Phpfox::getLib('parse.input')->clean($aVals['question']),
            'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
            'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
            'randomize' => isset($aVals['randomize']) ? (int)$aVals['randomize'] : '1',
            'hide_vote' => isset($aVals['hide_vote']) ? (int)$aVals['hide_vote'] : '0',
            'description' => (isset($aVals['description'])) ? $this->preParse()->clean($aVals['description']) : '',
            'description_parsed' => (empty($aVals['description']) ? null : $this->preParse()->prepare($aVals['description'])),
            'total_attachment' => 0,
            'is_multiple' => (isset($aVals['is_multiple'])) ? $aVals['is_multiple'] : '0'
        );
        if (!empty($aVals['enable_close'])) {
            $aInsert['close_time'] = Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->mktime($aVals['close_hour'], $aVals['close_minute'], 59, $aVals['close_month'],
                $aVals['close_day'], $aVals['close_year']));
        } else {
            $aInsert['close_time'] = 0;
        }
        if ($bIsUpdate) {
            $aPoll = db()->select('user_id,image_path')->from(':poll')->where('poll_id =' . (int)$aVals['poll_id'])->execute('getRow');
            if (!empty($aPoll['image_path']) && (!empty($aVals['temp_file']) || !empty($aVals['remove_photo']))) {
                if ($this->deleteImage($aVals['poll_id'],$aPoll['user_id'])) {
                    $aInsert['image_path'] = null;
                    $aInsert['server_id'] = 0;
                }
                else {
                    return false;
                }
            }
            $iUserId = $aPoll['user_id'];
        }
        else {
            $iUserId = $iUser;
        }
        if (Phpfox::getUserParam('poll.poll_can_upload_image')) {
            if (!empty($aVals['temp_file'])) {
                $aFile = Phpfox::getService('core.temp-file')->get($aVals['temp_file']);
                if (!empty($aFile)) {
                    if (!Phpfox::getService('user.space')->isAllowedToUpload($iUserId, $aFile['size'])) {
                        Phpfox::getService('core.temp-file')->delete($aVals['temp_file'], true);
                        return false;
                    }
                    $aInsert['image_path'] = $aFile['path'];
                    $aInsert['server_id'] = $aFile['server_id'];
                    Phpfox::getService('user.space')->update($iUserId, 'poll', $aFile['size']);
                    Phpfox::getService('core.temp-file')->delete($aVals['temp_file']);
                }
            }
        }
        if (!empty($aVals['expire'])) {

        }
        //if its an update then delete the older answers
        if ($bIsUpdate) {

            $iId = $aVals['poll_id'];

            $this->database()->update($this->_sTable, $aInsert, 'poll_id = ' . (int)$aVals['poll_id']);
            $aInsert = $this->database()->select('poll_id, question, view_id, image_path, user_id')
                ->from($this->_sTable)
                ->where('poll_id = ' . (int)$aVals['poll_id'])
                ->execute('getSlaveRow');
            $bHasAttachments = (!empty($aVals['attachment']));

            if ($bHasAttachments) {
                Phpfox::getService('attachment.process')->updateItemId($aVals['attachment'], Phpfox::getUserId(), $iId);
            }
            $this->database()->update($this->_sTable, [
                'total_attachment' => (Phpfox::isModule('attachment') ? Phpfox::getService('attachment')->getCountForItem($iId,
                    'poll') : '0')
            ], 'poll_id = ' . (int)$iId);
            if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support')) {
                Phpfox::getService('tag.process')->update('poll', $iId, Phpfox::getUserId(), $aVals['description'],
                    true);
            } else {
                if (Phpfox::isModule('tag')) {
                    Phpfox::getService('tag.process')->update('poll', $iId, Phpfox::getUserId(),
                        (!Phpfox::getLib('parse.format')->isEmpty($aVals['tag_list']) ? $aVals['tag_list'] : null));
                }
            }
            $aTotalVotes = $this->database()->select('pa.answer_id, pa.total_votes')
                ->from(Phpfox::getT('poll_answer'), 'pa')
                ->where('pa.poll_id = ' . (int)$aVals['poll_id'])
                ->execute('getSlaveRows');
            $this->database()->delete(Phpfox::getT('poll_answer'), 'poll_id = ' . $aVals['poll_id']);

            if (Phpfox::isModule('feed')) {
                (Phpfox::isModule('feed') ? \Phpfox::getService('feed.process')->update('poll', $iId, $aVals['privacy'],
                    (isset($aVals['privacy_comment']) ? (int)$aVals['privacy_comment'] : 0)) : null);
            }

            if (Phpfox::isModule('privacy')) {
                if ($aVals['privacy'] == '4') {
                    \Phpfox::getService('privacy.process')->update('poll', $iId,
                        (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
                } else {
                    \Phpfox::getService('privacy.process')->delete('poll', $iId);
                }
            }

            if (Phpfox::getParam('feed.cache_each_feed_entry')) {
                $this->cache()->remove(array('feeds', 'poll_' . $iId));
            }
        } else {
            $bHasAttachments = (!empty($aVals['attachment']));
            $aInsert['user_id'] = $iUser;
            $aInsert['time_stamp'] = PHPFOX_TIME;
            $aInsert['view_id'] = ((!$bIsCustom && Phpfox::getUserParam('poll.poll_requires_admin_moderation')) === true) ? 1 : 0;
            if ($bIsCustom) {
                $aInsert['module_id'] = $aVals['module_id'];
            }

            $iId = $this->database()->insert($this->_sTable, $aInsert);
            //Add hashtag
            if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support')) {
                Phpfox::getService('tag.process')->add('poll', $iId, Phpfox::getUserId(), $aVals['description'], true);
            } else {
                if (Phpfox::isModule('tag') && isset($aVals['tag_list']) && ((is_array($aVals['tag_list']) && count($aVals['tag_list'])) || (!empty($aVals['tag_list'])))) {
                    Phpfox::getService('tag.process')->add('poll', $iId, Phpfox::getUserId(), $aVals['tag_list']);
                }
            }
            // If we uploaded any attachments make sure we update the 'item_id'
            if ($bHasAttachments) {
                Phpfox::getService('attachment.process')->updateItemId($aVals['attachment'], Phpfox::getUserId(), $iId);
            }
            if (!Phpfox::getUserParam('poll.poll_requires_admin_moderation') && !$bIsCustom) {
                (Phpfox::isModule('feed') ? \Phpfox::getService('feed.process')->add('poll', $iId, $aVals['privacy'],
                    (isset($aVals['privacy_comment']) ? (int)$aVals['privacy_comment'] : 0)) : null);

                // Update user activity
                \Phpfox::getService('user.activity')->update(Phpfox::getUserId(), 'poll');
            }

            if (isset($aVals['privacy']) && $aVals['privacy'] == '4') {
                \Phpfox::getService('privacy.process')->add('poll', $iId,
                    (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
            }
        }

        // at this point there should only be valid answers ( != 'Answer number 1...', 'Answer number 2...')
        $iOrdering = 1;
        foreach ($aVals['answer'] as $aAnswer) {
            if (Phpfox::getLib('parse.format')->isEmpty($aAnswer['answer'])) {
                continue;
            }

            $aAnswerInsert = array(
                'poll_id' => ($bIsUpdate) ? $aVals['poll_id'] : (int)$iId,
                'answer' => Phpfox::getLib('parse.input')->clean($aAnswer['answer'], 255),
                'ordering' => $iOrdering
            );
            if (isset($aAnswer['answer_id'])) {
                $aAnswerInsert['answer_id'] = $aAnswer['answer_id'];
                foreach ($aTotalVotes as $aVotes) {
                    if ($aAnswer['answer_id'] == $aVotes['answer_id']) {
                        $aAnswerInsert['total_votes'] = $aVotes['total_votes'];
                    }
                }
            }

            $this->database()->insert(Phpfox::getT('poll_answer'), $aAnswerInsert);
            ++$iOrdering;
        }

        if (!$bIsCustom) {
            if ($bIsUpdate) {
                // sync the results
                $aResults = $this->database()->select('pr.*')
                    ->from(Phpfox::getT('poll_result'), 'pr')
                    ->join(Phpfox::getT('poll_answer'), 'pa', 'pa.answer_id = pr.answer_id')
                    ->where('pr.poll_id = ' . (int)$aVals['poll_id'])
                    ->execute('getSlaveRows');

                $this->database()->delete(Phpfox::getT('poll_result'), 'poll_id = ' . $aVals['poll_id']);
                foreach ($aResults as $aResult) {
                    $this->database()->insert(Phpfox::getT('poll_result'), $aResult);
                }
            }
        }

        (($sPlugin = Phpfox_Plugin::get('poll.service_process_add_end')) ? eval($sPlugin) : false);

        return array($iId, $aInsert);
    }

    /**
     * Deletes an image, this function is a response to an ajax call
     * @param integer $iPoll the identifier of the poll
     * @param integer $iUser the user who triggered the ajax call
     * @return boolean
     */
    public function deleteImage($iPoll, $iUser)
    {
        $iUser = (int)$iUser;
        $iPoll = (int)$iPoll;
        if ($sPlugin = Phpfox_Plugin::get('poll.service_process_deleteimage_start')) {
            eval($sPlugin);
        }

        $aPoll = db()->select('image_path, server_id, user_id')
            ->from(':poll')
            ->where('poll_id ='.(int)$iPoll)
            ->execute('getRow');

        // calculate space used
        if (!empty($aPoll['image_path'])) {
            $aParams = Phpfox::getService('poll')->getUploadParams();
            $aParams['type'] = 'poll';
            $aParams['path'] = $aPoll['image_path'];
            $aParams['user_id'] = $iUser;
            $aParams['update_space'] = ($iUser ? true : false);
            $aParams['server_id'] = $aPoll['server_id'];

            if (Phpfox::getService('user.file')->remove($aParams)) {
                if (!isset($bSkipDefaultReturn)) {
                    $this->database()->update(Phpfox::getT('poll'), array('image_path' => null,'server_id' => 0),
                        'poll_id = ' . $iPoll);
                }
            }
            else {
                return false;
            }
        }

        if ($sPlugin = Phpfox_Plugin::get('poll.service_process_deleteimage_end')) {
            eval($sPlugin);
        }

        return true;

    }

    /**
     * Changes the moderated state of a poll
     *
     * @param integer $iPoll poll_id
     * @param integer $iResult 0 = public, 1 = awaiting moderation, 2 = deleted
     * @return boolean if update, int if delete
     */
    public function moderatePoll($iPoll, $iResult)
    {
        (($sPlugin = Phpfox_Plugin::get('poll.service_process_moderate_start')) ? eval($sPlugin) : false);

        $aPoll = $this->database()->select('p.poll_id, p.view_id, p.user_id, p.image_path, p.question, p.privacy, p.privacy_comment, p.server_id, p.module_id, p.item_id')
            ->from($this->_sTable, 'p')
            ->where('p.poll_id = ' . (int)$iPoll)
            ->execute('getSlaveRow');

        if ($iResult == '0') {
            if ($aPoll['view_id'] == '0') {
                return false;
            }

            $this->database()->update($this->_sTable, array('view_id' => (int)$iResult, 'time_stamp' => PHPFOX_TIME),
                'poll_id = ' . $aPoll['poll_id']);

            if (Phpfox::isModule('notification')) {
                \Phpfox::getService('notification.process')->add('poll_approved', $aPoll['poll_id'], $aPoll['user_id']);
            }

            (Phpfox::isModule('feed') ? \Phpfox::getService('feed.process')->add('poll', $aPoll['poll_id'],
                $aPoll['privacy'], $aPoll['privacy_comment'], 0, $aPoll['user_id']) : null);

            // Send the user an email
            $sLink = Phpfox_Url::instance()->permalink('poll', $aPoll['poll_id'], $aPoll['question']);
            Phpfox::getLib('mail')->to($aPoll['user_id'])
                ->subject(array('poll.your_poll_title_has_been_approved', array('title' => $aPoll['question'])))
                ->message(_p('your_poll_a_href_link_title_a_has_been_approved_to_view_this_poll_follow_the_link_below_a_href_link_link_a',
                    array('link' => $sLink, 'title' => $aPoll['question'])))
                ->send();

            // Update user activity
            \Phpfox::getService('user.activity')->update($aPoll['user_id'], 'poll');

            (($sPlugin = Phpfox_Plugin::get('poll.service_process_moderatepoll__1')) ? eval($sPlugin) : false);

            return 1;
        }

        //delete image
        $this->deleteImage($iPoll,$aPoll['user_id']);
        
        $this->database()->delete($this->_sTable, 'poll_id = ' . (int)$iPoll);
        $this->database()->delete(Phpfox::getT('poll_answer'), 'poll_id = ' . (int)$iPoll);
        $this->database()->delete(Phpfox::getT('poll_result'), 'poll_id = ' . (int)$iPoll);
        $this->database()->delete(Phpfox::getT('poll_design'), 'poll_id = ' . (int)$iPoll);
        $this->database()->delete(Phpfox::getT('track'), 'item_id = ' . (int)$iPoll . ' AND type_id="poll"');

        \Phpfox::getService('user.activity')->update($aPoll['user_id'], 'poll', '-');
        //delete poll in parent item module
        if (!empty($aPoll['module_id']) && Phpfox::hasCallback($aPoll['module_id'], 'deletePollItem')) {
            Phpfox::callback($aPoll['module_id'] . '.deletePollItem', $aPoll['item_id']);
        }
        (Phpfox::isModule('attachment') ? \Phpfox::getService('attachment.process')->deleteForItem($aPoll['user_id'],
            $iPoll, 'poll') : null);
        (Phpfox::isModule('feed') ? \Phpfox::getService('feed.process')->delete('poll', $iPoll) : null);
        (Phpfox::isModule('feed') ? \Phpfox::getService('feed.process')->delete('comment_poll', $iPoll) : null);

        (Phpfox::isModule('comment') ? Phpfox::getService('comment.process')->deleteForItem($aPoll['user_id'], $iPoll,
            'poll') : null);

        (Phpfox::isModule('like') ? Phpfox::getService('like.process')->delete('poll', $iPoll, 0, true) : null);
        (Phpfox::isModule('notification') ? \Phpfox::getService('notification.process')->deleteAllOfItem([
            'poll_like',
            'poll_approved'
        ], (int)$iPoll) : null);

        //close all sponsorships
        (Phpfox::isModule('ad') ? Phpfox::getService('ad.process')->closeSponsorItem('poll',
            (int)$iPoll) : null);

        if (Phpfox::isModule('tag')) {
            $this->database()->delete(Phpfox::getT('tag'),
                'item_id = ' . $aPoll['poll_id'] . ' AND category_id = "poll"', 1);
            $this->cache()->remove();
        }

        (($sPlugin = Phpfox_Plugin::get('poll.service_process_moderate_end')) ? eval($sPlugin) : false);

        return 2;

    }

    /**
     * Updates the count of a poll comment by increasing it.
     * @param integer $iPoll The Poll identifier
     */
    public function updateCounter($iPoll)
    {
        $this->database()->update($this->_sTable, array('total_comment' => array('= total_comment +', 1)),
            'poll_id = ' . (int)$iPoll);
    }

    /**
     * Casts a vote on a poll
     * @param integer $iUser User identifier
     * @param integer $iPoll Poll identifier
     * @param integer $iAnswer Answer identifier
     * @return boolean|Phpfox_Error
     */
    public function addVote($iUser, $iPoll, $iAnswer)
    {
        if ($sPlugin = Phpfox_Plugin::get('poll.service_process_addvote_1')) {
            eval($sPlugin);
        }

        $bIsSingleChoice = !is_array($iAnswer) && (int)$iAnswer;
        if (($iVoted = $this->hasUserVoted($iUser, $iPoll)) !== false) {

            // user has voted on this poll already
            if (Phpfox::getUserParam('poll.poll_can_change_own_vote')) {
                // update the vote
                // first delete current vote
                $this->database()->delete(Phpfox::getT('poll_result'),
                    'user_id = ' . (int)$iUser . ' AND poll_id = ' . (int)$iPoll);
                // now insert the new vote
                if ($bIsSingleChoice) {
                    $this->database()->insert(Phpfox::getT('poll_result'), array(
                            'poll_id' => $iPoll,
                            'answer_id' => $iAnswer,
                            'user_id' => $iUser,
                            'time_stamp' => PHPFOX_TIME
                        )
                    );
                    //update poll answer
                    $this->database()->update(Phpfox::getT('poll_answer'),
                        array('total_votes' => array('= total_votes +', 1)), 'answer_id = ' . $iAnswer);

                } else {
                    foreach ($iAnswer as $iAns) {
                        $this->database()->insert(Phpfox::getT('poll_result'), array(
                                'poll_id' => $iPoll,
                                'answer_id' => $iAns,
                                'user_id' => $iUser,
                                'time_stamp' => PHPFOX_TIME
                            )
                        );
                        //update poll answer
                        $this->database()->update(Phpfox::getT('poll_answer'),
                            array('total_votes' => array('= total_votes +', 1)), 'answer_id = ' . $iAns);

                    }
                }
                foreach ($iVoted as $aVoted) {
                    $this->database()->update(Phpfox::getT('poll_answer'),
                        array('total_votes' => array('= total_votes -', 1)), 'answer_id = ' . $aVoted['answer_id']);
                }
                return true;
            } else {
                // send error
                return Phpfox_Error::set(_p('your_membership_group_does_not_have_rights'));
            }
        } else {
            // is user has not voted on this poll
            // check if user has permission to view this item
            $aPoll = $this->database()->select('p.poll_id, p.user_id, p.question, p.privacy')
                ->from($this->_sTable, 'p')
                ->where('p.poll_id = ' . (int)$iPoll)
                ->execute('getSlaveRow');

            if (!isset($aPoll['poll_id'])) {
                return Phpfox_Error::set(_p('unable_to_find_this_poll'));
            }
            $aAnswer = '';
            if ($bIsSingleChoice) {
                $aAnswer = $this->database()->select('pa.answer')
                    ->from(':poll_answer', 'pa')
                    ->where('pa.answer_id = ' . (int)$iAnswer)
                    ->execute('getSlaveField');
                $this->database()->insert(Phpfox::getT('poll_result'), array(
                        'poll_id' => $iPoll,
                        'answer_id' => $iAnswer,
                        'user_id' => $iUser,
                        'time_stamp' => PHPFOX_TIME
                    )
                );
                //update total vote
                $this->database()->update(Phpfox::getT('poll_answer'),
                    array('total_votes' => array('= total_votes +', 1)), 'answer_id = ' . $iAnswer);

            } else {
                foreach ($iAnswer as $iAns) {
                    $this->database()->insert(Phpfox::getT('poll_result'), array(
                            'poll_id' => $iPoll,
                            'answer_id' => $iAns,
                            'user_id' => $iUser,
                            'time_stamp' => PHPFOX_TIME
                        )
                    );
                    //update total vote
                    $this->database()->update(Phpfox::getT('poll_answer'),
                        array('total_votes' => array('= total_votes +', 1)), 'answer_id = ' . $iAns);
                }

            }
            $sLink = Phpfox::permalink('poll', $aPoll['poll_id'], $aPoll['question']);

            Phpfox::getLib('mail')->to($aPoll['user_id'])
                ->subject(array(
                    'poll.full_name_voted_on_your_poll_question',
                    array('full_name' => Phpfox::getUserBy('full_name'), 'question' => $aPoll['question'])
                ))
                ->message($bIsSingleChoice ? (array(
                    'poll.full_name_voted_answer_on_your_poll_question',
                    array(
                        'full_name' => Phpfox::getUserBy('full_name'),
                        'answer' => $aAnswer,
                        'question' => $aPoll['question'],
                        'link' => $sLink
                    )
                )) : array(
                    'poll.full_name_voted_some_answers_on_your_poll_question',
                    array(
                        'full_name' => Phpfox::getUserBy('full_name'),
                        'question' => $aPoll['question'],
                        'link' => $sLink
                    )
                ))
                ->send();

            if (Phpfox::isModule('notification')) {
                \Phpfox::getService('notification.process')->add('poll', $iPoll, $aPoll['user_id']);
            }

            return true;
        }
    }

    /**
     * Tells if a user has voted on a specific poll
     *
     * @param integer $iUser
     * @param integer $iPoll
     * @return false or numeric
     */
    public function hasUserVoted($iUser, $iPoll)
    {
        (($sPlugin = Phpfox_Plugin::get('poll.service_process_hasuservoted_start')) ? eval($sPlugin) : false);
        // check if user had already voted on this poll
        $iVoted = $this->database()->select('pr.answer_id')
            ->from(Phpfox::getT('poll_result'), 'pr')
            ->where('poll_id = ' . (int)$iPoll . ' AND user_id =  ' . (int)$iUser)
            ->execute('getSlaveRows');

        (($sPlugin = Phpfox_Plugin::get('poll.service_process_hasuservoted_end')) ? eval($sPlugin) : false);
        if (count($iVoted)) {
            return $iVoted;
        }

        return false;
    }

    /**
     * Updates the default design of a poll, when polls are added they dont have a default design so
     * this function checks if they are updating or creating a new one
     *
     * @param integer $iUser user_id
     * @param array $aPoll poll information
     * @return boolean true = success
     */
    public function updateDesign($iUser, $aPoll)
    {
        if (!$iUser) {
            return false; // correct permission check
        }

        // first check if there are (due to DB compatibility we cannot use INSERT SET)
        $aExistingColors = $this->database()->select('pd.*')
            ->from(Phpfox::getT('poll_design'), 'pd')
            ->where('pd.poll_id = ' . $aPoll['poll_id'])
            ->execute('getSlaveRow');

        // if the colors have been set
        if (isset($aExistingColors['background'])) {
            // we update
            $bColors = $this->database()->update(Phpfox::getT('poll_design'), array(
                'background' => "'" . $aPoll['js_poll_background'] . "'",
                'percentage' => "'" . $aPoll['js_poll_percentage'] . "'",
                'border' => "'" . $aPoll['js_poll_border'] . "'"
            ), 'poll_id = ' . $aPoll['poll_id'], false
            );
            return $bColors;
        } else {
            // we insert
            $iColors = $this->database()->insert(Phpfox::getT('poll_design'), array(
                    'poll_id' => (int)$aPoll['poll_id'],
                    'background' => isset($aPoll['js_poll_background']) ? $aPoll['js_poll_background'] : null,
                    'percentage' => isset($aPoll['js_poll_percentage']) ? $aPoll['js_poll_percentage'] : null,
                    'border' => isset($aPoll['js_poll_border']) ? $aPoll['js_poll_border'] : null
                )
            );
            return is_numeric($iColors);
        }
    }

    /**
     * Changes the text of a given answer
     * @param integer $iId Answer identifier
     * @param string $sTxt New text
     */
    public function updateAnswer($iId, $sTxt)
    {
        \Phpfox::getService('ban')->checkAutomaticBan($sTxt);
        $this->database()->update(Phpfox::getT('poll_answer'), array(
            'answer' => Phpfox_Database::instance()->escape($sTxt)
        ), 'answer_id = ' . (int)$iId
        );
    }

    /**
     * Updates the counter of a poll views (increments)
     *
     * @param integer $iId Poll identifier
     * @return true
     */
    public function updateView($iId)
    {
        $this->database()->update($this->_sTable, ['total_view' => 'total_view + 1'], ['poll_id' => (int)$iId], false);

        return true;
    }

    public function sponsor($iId, $sType)
    {
        if (!Phpfox::getUserParam('poll.can_sponsor_poll') && !Phpfox::getUserParam('poll.can_purchase_sponsor_poll') && !defined('PHPFOX_API_CALLBACK')) {
            return Phpfox_Error::set(_p('hack_attempt'));
        }

        $iType = (int)$sType;
        if ($iType != 0 && $iType != 1) {
            return false;
        }
        db()->update($this->_sTable, array('is_sponsor' => $iType), 'poll_id = ' . (int)$iId);
        if ($sPlugin = Phpfox_Plugin::get('poll.service_process_sponsor__end')) {
            eval($sPlugin);
        }
        return true;
    }

    public function feature($iId, $iType)
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('poll.can_feature_poll', true);

        $this->database()->update($this->_sTable, array('is_featured' => ($iType ? '1' : '0')),
            'poll_id = ' . (int)$iId);


        return true;
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     *
     * @return mixed
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('poll.service_process__call')) {
            eval($sPlugin);
            return null;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}