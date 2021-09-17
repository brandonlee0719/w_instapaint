<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Polls\Ajax;

use Phpfox;
use Phpfox_Ajax;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Url;

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author           phpFox
 * @package          Module_Poll
 * @version          $Id: ajax.class.php 6472 2013-08-20 06:11:44Z phpFox $
 */
class Ajax extends Phpfox_Ajax
{
    /**
     * Deletes the image in a poll by calling the process service's deleteImage function
     * @deprecated in 4.6.0, remove in 4.7.0
     */
    public function deleteImage()
    {
        Phpfox::isUser(true);
        (($sPlugin = Phpfox_Plugin::get('poll.component_ajax_deleteimage_start')) ? eval($sPlugin) : false);
        $iPoll = (int)$this->get('iPoll');
        if (Phpfox::getService('poll.process')->deleteImage($iPoll, Phpfox::getUserId())) {
            $this->call('$("#js_submit_upload_image").show();');
            $this->call('$("#js_poll_current_image, .js_hide_upload_image").remove();');
        } else {
            $this->call('$("#js_event_current_image").after("' . _p('an_error_occured_and_your_image_could_not_be_deleted_please_try_again') . '");');
        }
        (($sPlugin = Phpfox_Plugin::get('poll.component_ajax_deleteimage_end')) ? eval($sPlugin) : false);
    }

    /**
     * Adds a vote to a specific poll and sets the message to show according
     * it also may show the poll result if the userParam is set to show it
     */
    public function addVote()
    {
        (($sPlugin = Phpfox_Plugin::get('poll.component_ajax_addvote_start')) ? eval($sPlugin) : false);

        Phpfox::isUser(true);

        $aVals = $this->get('val');
        if (!isset($aVals['answer'])) {
            return false;
        }

        // check if the poll is being moderated
        $bModerated = Phpfox::getService('poll')->isModerated((int)$aVals['poll_id']);

        if ($bModerated) {
            $this->call('$("#poll_holder_' . (int)$aVals['poll_id'] . '").html("' . _p('this_poll_is_being_moderated_and_no_votes_can_be_added_yet') . '");');
        } else {
            if (Phpfox::getService('poll.process')->addVote(Phpfox::getUserId(), (int)$aVals['poll_id'],
                $aVals['answer'])
            ) {
                if (Phpfox::getUserParam('poll.view_poll_results_after_vote')) {
                    Phpfox::getBlock('poll.vote', ['iPoll' => (int)$aVals['poll_id'], 'isViewing' => true]);
                    $this->call('$(\'#js_poll_results_' . $aVals['poll_id'] . '\').empty();');
                    $this->html('#js_poll_results_' . $aVals['poll_id'], $this->getContent(false));

                    Phpfox::getBlock('poll.votes', ['iPoll' => (int)$aVals['poll_id'], 'page' => 0]);
                    $this->call('if($(\'#js_votes\')) {$(\'#js_votes\').html(\'' . $this->getContent(false) . '\');$Core.loadInit();}');
                } else {
                    if (!empty($aVals['vote_again'])) {
                        $this->alert(_p('poll_vote_updated'));
                    } else {
                        $this->alert(_p('your_vote_has_successfully_been_cast'));
                    }
                }
            } else {
                $this->alert(implode(' ', Phpfox_Error::get()));
            }
        }

        (($sPlugin = Phpfox_Plugin::get('poll.component_ajax_addvote_end')) ? eval($sPlugin) : false);
    }

    /**
     * Process moderation on a poll
     */
    public function moderatePoll()
    {
        Phpfox::isUser(true);

        (($sPlugin = Phpfox_Plugin::get('poll.component_ajax_moderatepoll_start')) ? eval($sPlugin) : false);

        $iPoll = (int)$this->get('iPoll');
        $iResult = (int)$this->get('iResult');

        if ($iResult == 0) {
            Phpfox::getUserParam('poll.poll_can_moderate_polls', true);

            Phpfox::getService('poll.process')->moderatePoll($iPoll, $iResult);

            $this->call('var oSubsectionCountItem = $(\'.sub_section_menu .pending\'); if ($(oSubsectionCountItem).length > 0) { var iSubsectionCount = parseInt(oSubsectionCountItem.html()); if (iSubsectionCount > 1) { oSubsectionCountItem.html(parseInt(iSubsectionCount - 1)); } else { $(\'.sub_section_menu .pending\').parents(\'li\').remove(); } }');
            if ($this->get('inline')) {
                $this->alert(_p('poll_has_been_approved'), _p('poll_approved'), 300, 100, true);
                $this->hide('#js_item_bar_approve_image');
                $this->hide('.js_moderation_off');
                $this->show('.js_moderation_on');
                $sUrl = Phpfox::getLib('url')->makeUrl('poll');
                $this->call('if(!$(\'#js_approve_poll_message\').length) {$("#js_poll_id_' . $iPoll . '").remove(); var total_pending = parseInt($("#poll_pending").html()) - 1; if(total_pending > 0) $("#poll_pending").html(total_pending); else window.location.href = "' . $sUrl . '";}');
            } else {
                $this->alert(_p('poll_has_been_approved'));
                $this->call('window.location.reload();');
            }
        } elseif ($iResult == 2) {
            if (Phpfox::getService('user.auth')->hasAccess('poll', 'poll_id', $iPoll, 'poll.poll_can_delete_own_polls',
                    'poll.poll_can_delete_others_polls') && Phpfox::getService('poll.process')->moderatePoll($iPoll,
                    $iResult)
            ) {
                Phpfox::addMessage(_p('poll_successfully_deleted'));
                $this->call('window.location.reload();');
            }
        } else {
            $this->call("$('#poll_holder_" . $iPoll . "').html('" . _p('there_was_a_problem_moderating_this_poll',
                    ['phpfox_squote' => true]) . "');");
        }

        (($sPlugin = Phpfox_Plugin::get('poll.component_ajax_moderatepoll_end')) ? eval($sPlugin) : false);
    }

    /**
     * Shows the votes result in a poll
     */
    public function pageVotes()
    {
        $this->setTitle(_p('poll_results'));
        Phpfox::getBlock('poll.votes',['iPoll' => $this->get('poll_id')]);
        $this->call('<script>$Core.loadInit();</script>');
    }

    /**
     * Shows the newest polls
     */
    public function getNew()
    {
        Phpfox::getBlock('poll.new');

        $this->html('#' . $this->get('id'), $this->getContent(false));
        $this->call('$(\'#' . $this->get('id') . '\').parents(\'.block:first\').find(\'.bottom li a\').attr(\'href\', \'' . Phpfox_Url::instance()->makeUrl('poll') . '\');');
    }

    public function add()
    {
        $sDivId = $this->get('div_id', '');
        if (!$sDivId) {
            echo '<div style="position:relative;">';
            Phpfox::getComponent('poll.add', [], 'controller');
            echo '</div>';
            echo '<script type="text/javascript">$Core.loadInit();</script>';
        } else {
            Phpfox::getComponent('poll.add', [], 'controller');
            $this->call('$(\'#' . $sDivId . '\').html(\'' . $this->getContent() . '\');');
            $this->call('$Core.loadInit();');
        }
    }

    public function addCustom()
    {

        $aVals = $this->get('val');
        if (empty($aVals['temp_file']) && Phpfox::getParam('poll.is_image_required')) {
            $this->call('$(\'.js_poll_submit_button\').removeClass(\'disabled\');');
            Phpfox_Error::set(_p('each_poll_requires_an_image'));
        }
        $mErrors = Phpfox::getService('poll')->checkStructure($aVals);
        if (is_array($mErrors)) {
            $this->call('$(\'.js_poll_submit_button\').removeClass(\'disabled\');');
            foreach ($mErrors as $sError) {
                Phpfox_Error::set($sError);
            }
        }

        if (Phpfox_Error::isPassed()) {
            if ((list($iId, $aPoll) = Phpfox::getService('poll.process')->add(Phpfox::getUserId(), $aVals))) {
                $this->val('#js_poll_id', $iId);
                $this->call('tb_remove();');
                $this->html('#js_attach_poll_question',
                    Phpfox::getLib('parse.output')->clean($aPoll['question']) . ' - <a href="#" onclick="$.ajaxCall(\'forum.deletePoll\', \'poll_id=' . $iId . '&amp;thread_id=\' + $(\'#js_poll_id\').val()); return false;" title="' . _p('click_to_delete_this_poll') . '">' . _p('delete') . '</a>');
                $this->hide('#js_attach_poll');
            }
        }
    }

    public function moderation()
    {
        Phpfox::isUser(true);
        $sMessage = '';
        switch ($this->get('action')) {
            case 'approve':
                Phpfox::getUserParam('poll.poll_can_moderate_polls', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('poll.process')->moderatePoll($iId, '0');
                }
                Phpfox::addMessage(_p('poll_s_successfully_approved'));
                $this->call('window.location.reload();');
                break;
            case 'delete':
                Phpfox::getUserParam('poll.poll_can_delete_others_polls', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('poll.process')->moderatePoll($iId, 2);
                    $this->call('$("#js_poll_id_' . $iId . '").prev().remove();');
                    $this->remove('#js_poll_id_' . $iId);
                }
                Phpfox::addMessage(_p('poll_s_successfully_deleted'));
                $this->call('window.location.reload();');
                break;
            case 'feature':
                Phpfox::getUserParam('poll.can_feature_poll', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('poll.process')->feature($iId, 1);
                    $this->addClass('#js_poll_id_' . $iId, 'row_featured');
                }
                $sMessage = _p('poll_s_successfully_featured');
                break;
            case 'un-feature':
                Phpfox::getUserParam('poll.can_feature_poll', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('poll.process')->feature($iId, 0);
                    $this->removeClass('#js_poll_id_' . $iId, 'row_featured');
                }
                $sMessage = _p('poll_s_successfully_un_featured');
                break;
        }
        if (!empty($sMessage)) {
            $this->alert($sMessage, _p('moderation'), 300, 150, true);
        }
        $this->hide('.moderation_process');
    }

    public function addViaStatusUpdate()
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('poll.can_create_poll', true);

        $this->error(false);

        $aVals = (array)$this->get('val');

        $aVals['question'] = $aVals['poll_question'];

        $iFlood = Phpfox::getUserParam('poll.poll_flood_control');
        if ($iFlood != '0') {
            $aFlood = [
                'action' => 'last_post', // The SPAM action
                'params' => [
                    'field' => 'time_stamp', // The time stamp field
                    'table' => Phpfox::getT('poll'), // Database table we plan to check
                    'condition' => 'user_id = ' . Phpfox::getUserId(), // Database WHERE query
                    'time_stamp' => $iFlood * 60 // Seconds);
                ],
            ];
            // actually check if flooding
            if (Phpfox::getLib('spam')->check($aFlood)) {
                // Set an error
                Phpfox_Error::set(_p('poll_flood_control', ['x' => $iFlood]));
            }
        }

        $mErrors = Phpfox::getService('poll')->checkStructure($aVals);
        if (is_array($mErrors)) {
            foreach ($mErrors as $sError) {
                Phpfox_Error::set($sError);
            }
        }

        $bIsError = false;
        if (Phpfox_Error::isPassed()) {
            if (Phpfox::getService('poll.process')->add(Phpfox::getUserId(), $aVals)) {
                $iId = Phpfox::getService('feed.process')->getLastId();

                (($sPlugin = Phpfox_Plugin::get('user.component_ajax_addviastatusupdate')) ? eval($sPlugin) : false);

                Phpfox::getService('feed')->processAjax($iId);
            } else {
                $bIsError = true;
            }

        } else {
            $bIsError = true;
        }

        if ($bIsError) {
            $this->call('$Core.resetActivityFeedError(\'' . implode('<br />', Phpfox_Error::get()) . '\');');
        } else {
            $this->call('$("#global_attachment_poll input:text").val(" ");');
        }
    }

    public function sponsor()
    {

        Phpfox::isUser(true);

        if (!Phpfox::isModule('ad')) {
            return $this->alert('your_request_is_invalid');
        }

        if (Phpfox::getService('poll.process')->sponsor($this->get('poll_id'), $this->get('type'))) {
            $aPoll = Phpfox::getService('poll')->getPollById($this->get('poll_id'));
            if ($this->get('type') == '1') {
                $sModule = _p('poll');
                Phpfox::getService('ad.process')->addSponsor(array(
                    'module' => 'poll',
                    'item_id' => $this->get('poll_id'),
                    'name' => _p('default_campaign_custom_name', ['module' => $sModule, 'name' => $aPoll['question']])
                ));
                // image was sponsored
                $sHtml = '<a href="#" title="' . _p('unsponsor_this_song') . '" onclick="$.ajaxCall(\'poll.sponsor\', \'poll_id=' . $this->get('poll_id') . '&amp;type=0\'); return false;"><img src="' . $this->template()->getStyle('image',
                        'misc/medal_gold_delete.png') . '" class="v_middle" alt="' . _p('unsponsor_this_poll') . '" width="16" height="16" /></a>';
            } else {
                Phpfox::getService('ad.process')->deleteAdminSponsor('poll', $this->get('poll_id'));
                $sHtml = '<a href="#" title="' . _p('sponsor_this_song') . '" onclick="$.ajaxCall(\'poll.sponsor\', \'poll_id=' . $this->get('poll_id') . '&amp;type=1\'); return false;"><img src="' . $this->template()->getStyle('image',
                        'misc/medal_gold_add.png') . '" class="v_middle" alt="' . _p('sponsor_this_poll') . '" width="16" height="16" /></a>';
            }
            $this->html('#js_poll_sponsor_' . $this->get('poll_id'), $sHtml)
                ->alert($this->get('type') == '1' ? _p('poll_successfully_sponsored') : _p('poll_successfully_un_sponsored'));
        }

    }

    public function feature()
    {
        if (Phpfox::getService('poll.process')->feature($this->get('poll_id'), $this->get('type'))) {
            if ($this->get('type')) {
                $this->alert(_p('poll_successfully_featured'), _p('feature'), 300, 150, true);
            } else {
                $this->alert(_p('poll_successfully_un_featured'), _p('un_feature'), 300, 150, true);
            }
        }
    }

    public function showAnswerVote()
    {
        $aAnswer = Phpfox::getService('poll')->getAnswersById($this->get('answer_id'));
        if (!$aAnswer) {
            return false;
        }
        Phpfox::getBlock('poll.answer-voted',
            ['answer_id' => $this->get('answer_id'), 'answer' => $aAnswer['answer']]);
        $this->call('<script>$Core.loadInit();</script>');
    }
}