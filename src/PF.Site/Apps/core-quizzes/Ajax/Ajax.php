<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Quizzes\Ajax;

use Phpfox;
use Phpfox_Ajax;
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
class Ajax extends Phpfox_Ajax
{
    public function browseUsers()
    {
        Phpfox::getBlock('quiz.takenby');

        $this->replaceWith('._block_content .js_pager_popup_view_more_link', $this->getContent(false));

        $this->call('$Core.loadInit()');
    }

    /**
     * Delete quiz image
     */
    public function deleteImage()
    {
        Phpfox::isUser(true);

        (($sPlugin = Phpfox_Plugin::get('quiz.component_ajax_deleteimage_start')) ? eval($sPlugin) : false);

        $iQuiz = (int)$this->get('iQuiz');

        if (Phpfox::getService('quiz.process')->deleteImage($iQuiz, Phpfox::getUserId())) {
            $this->call('$("#js_submit_upload_image").show();');
            $this->call('$("#js_quiz_current_image, .js_hide_upload_image").remove();');
        } else {
            $this->call('$("#js_quiz_current_image").after("' . _p('an_error_occured_and_your_image_could_not_be_deleted_please_try_again') . '");');
        }

        (($sPlugin = Phpfox_Plugin::get('quiz.component_ajax_deleteimage_end')) ? eval($sPlugin) : false);
    }

    /**
     * Validates the approval and calls the processing function
     */
    public function approve()
    {
        Phpfox::getUserParam('quiz.can_approve_quizzes', true);

        $iQuiz = (int)$this->get('iQuiz');
        $bApproved = Phpfox::getService('quiz.process')->approveQuiz($iQuiz);

        if ($bApproved == true) {
            if ($this->get('inline')) {
                $this->alert(_p('quiz_has_been_approved'), _p('quiz_approved'), 300, 100, true);
                $this->hide('#js_item_bar_approve_image');
                $this->hide('.js_moderation_off');
                $this->show('.js_moderation_on');
            } else {
                $this->alert(_p('quiz_has_been_approved'));
                $this->call('window.location.reload();');
            }
        } else {
            $this->alert(_p('an_error_kept_the_system_from_approving_the_quiz_please_try_again'));
        }

        return false;
    }

    public function moderation()
    {
        Phpfox::isUser(true);
        $sMessage = '';
        switch ($this->get('action')) {
            case 'approve':
                Phpfox::getUserParam('quiz.can_approve_quizzes', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('quiz.process')->approveQuiz($iId);
                }
                Phpfox::addMessage(_p('quiz_zes_successfully_approved'));
                $this->call('window.location.reload();');
                break;
            case 'delete':
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('quiz.process')->deleteQuiz($iId, Phpfox::getUserId());
                    $this->call('$("#js_quiz_' . $iId . '").prev().remove();');
                    $this->remove('#js_quiz_' . $iId);
                }
                Phpfox::addMessage(_p('quiz_zes_successfully_deleted'));
                $this->call('window.location.reload();');
                break;
            case 'feature':
                Phpfox::getUserParam('quiz.can_feature_quiz', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    \Phpfox::getService('quiz.process')->feature($iId, 1);
                }
                $sMessage = _p('quiz_zes_successfully_featured');
                break;
            case 'un-feature':
                Phpfox::getUserParam('quiz.can_feature_quiz', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    \Phpfox::getService('quiz.process')->feature($iId, 0);
                }
                $sMessage = _p('quiz_zes_successfully_un_featured');
                break;
        }

        $this->alert($sMessage, _p('moderation'), 300, 150, true);
        $this->hide('.moderation_process');
    }

    /**
     * This function deletes a quiz, if quiz.process->deleteQuiz returns true it also visually removes the
     * quiz entry with a hide and then with a remove
     * @return false
     */
    public function delete()
    {
        $iQuiz = (int)$this->get('iQuiz');
        $bDeleted = Phpfox::getService('quiz.process')->deleteQuiz($iQuiz, Phpfox::getUserId());

        if ($bDeleted == true) {
            if ($this->get('type') == 'viewing') {
                Phpfox::addMessage(_p('quiz_successfully_deleted'));

                $this->call('window.location.href = \'' . Phpfox_Url::instance()->makeUrl('quiz') . '\';');
            } else {
                $this->call('window.location.reload()');
            }

            return true;
        } else {
            $this->alert(_p('your_membership_does_not_allow_you_to_delete_this_quiz'));
        }
        return false;
    }

    public function sponsor()
    {

        Phpfox::isUser(true);

        if (!Phpfox::isModule('ad')) {
            return $this->alert('your_request_is_invalid');
        }

        if (\Phpfox::getService('quiz.process')->sponsor($this->get('quiz_id'), $this->get('type'))) {
            $aQuiz = Phpfox::getService('quiz')->getQuizById($this->get('quiz_id'));
            if ($this->get('type') == '1') {
                $sModule = _p('quizzes');
                Phpfox::getService('ad.process')->addSponsor(array(
                    'module' => 'quiz',
                    'item_id' => $this->get('quiz_id'),
                    'name' => _p('default_campaign_custom_name', ['module' => $sModule, 'name' => $aQuiz['title']])
                ));
                // image was sponsored
                $sHtml = '<a href="#" title="' . _p('unsponsor_this_song') . '" onclick="$.ajaxCall(\'quiz.sponsor\', \'quiz_id=' . $this->get('quiz_id') . '&amp;type=0\'); return false;"><img src="' . $this->template()->getStyle('image',
                        'misc/medal_gold_delete.png') . '" class="v_middle" alt="' . _p('unsponsor_this_quiz') . '" width="16" height="16" /></a>';
            } else {
                Phpfox::getService('ad.process')->deleteAdminSponsor('quiz', $this->get('quiz_id'));
                $sHtml = '<a href="#" title="' . _p('sponsor_this_song') . '" onclick="$.ajaxCall(\'quiz.sponsor\', \'quiz_id=' . $this->get('quiz_id') . '&amp;type=1\'); return false;"><img src="' . $this->template()->getStyle('image',
                        'misc/medal_gold_add.png') . '" class="v_middle" alt="' . _p('sponsor_this_quiz') . '" width="16" height="16" /></a>';
            }
            $this->html('#js_quiz_sponsor_' . $this->get('quiz_id'), $sHtml)
                ->alert($this->get('type') == '1' ? _p('quiz_successfully_sponsored') : _p('quiz_successfully_un_sponsored'));
        }

    }

    public function feature()
    {
        if (\Phpfox::getService('quiz.process')->feature($this->get('quiz_id'), $this->get('type'))) {
            if ($this->get('type')) {
                $this->alert(_p('quiz_successfully_featured'), _p('feature'), 300, 150, true);
            } else {
                $this->alert(_p('quiz_successfully_un_featured'), _p('un_feature'), 300, 150, true);
            }
        }
    }
}