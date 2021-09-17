<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Polls\Controller;

use Phpfox;
use Phpfox_Error;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class AddController extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        Phpfox::isUser(true);

        $bIsCustom = (($this->request()->get('item_id') && $this->request()->get('module_id')) ? true : false);

        // minimum answers
        $iMinAnswers = 2;
        $iMaxAnswers = (int)Phpfox::getUserParam('poll.maximum_answers_count');
        $iMaxAnswers = ($iMaxAnswers >= 2) ? $iMaxAnswers : 2;
        $iTotalDefaultAnswers = 4;
        $bIsEdit = false;

        (($sPlugin = Phpfox_Plugin::get('poll.component_controller_add_process_start')) ? eval($sPlugin) : false);

        // ajax validation
        // check input fields
        $aValidation = array(
            'question' => array(
                'def' => 'required',
                'title' => _p('provide_a_question_for_your_poll')
            )
        );

        // do they need to complete a captcha challenge?
        if (Phpfox::isModule('captcha') && Phpfox::getUserParam('poll.poll_require_captcha_challenge')) {
            $aValidation['image_verification'] = _p('complete_captcha_challenge');
        }

        $oValid = \Phpfox_Validator::instance()->set(array(
                'sFormName' => 'js_poll_form',
                'aParams' => $aValidation
            )
        );
        $this->template()->assign(array('aForms' => array('randomize' => 0)));
        if ($iReq = $this->request()->getInt('id')) {
            $aPoll = \Phpfox::getService('poll')->getPollById((int)$iReq);
            // did we get a result
            if (!empty($aPoll)) {
                $bIsOwnPoll = ($aPoll['user_id'] == Phpfox::getUserId());

                $bCanEditPoll = (($bIsOwnPoll && Phpfox::getUserParam('poll.poll_can_edit_own_polls')) || Phpfox::getUserParam('poll.poll_can_edit_others_polls'));

                if ($bCanEditPoll) {
                    if (!empty($aPoll['image_path'])) {
                        $aPoll['current_image'] = Phpfox::getLib('image.helper')->display([
                            'server_id' => $aPoll['server_id'],
                            'path' => 'poll.url_image',
                            'file' => $aPoll['image_path'],
                            'suffix' => '_150',
                            'return_url' => true
                        ]);
                    }
                    $bIsEdit = true;
                    $aAnswers = \Phpfox::getService('poll')->getAnswers($iReq);
                    $this->template()->assign(array(
                            'aForms' => $aPoll,
                            'aAnswers' => $aAnswers,
                        )
                    );
                } else {
                    return Phpfox_Error::display(_p('your_user_group_lacks_permissions_to_edit_that_poll'));
                }
            } else {
                return Phpfox_Error::display(_p('that_poll_does_not_exist'));
            }
        }

        if (!$bIsEdit) {
            //STill allow User edit Poll even they can't create It! bypass During edit.
            Phpfox::getUserParam('poll.can_create_poll', true);
        }
        if ($aVals = $this->request()->getArray('val')) {
            if (!$bIsEdit) {
                // avoid a flood
                $iFlood = Phpfox::getUserParam('poll.poll_flood_control');
                if ($iFlood != '0') {
                    $aFlood = array(
                        'action' => 'last_post', // The SPAM action
                        'params' => array(
                            'field' => 'time_stamp', // The time stamp field
                            'table' => Phpfox::getT('poll'), // Database table we plan to check
                            'condition' => 'user_id = ' . Phpfox::getUserId(), // Database WHERE query
                            'time_stamp' => $iFlood * 60 // Seconds);
                        )
                    );
                    // actually check if flooding
                    if (Phpfox::getLib('spam')->check($aFlood)) {
                        // Set an error
                        Phpfox_Error::set(_p('poll_flood_control', array('x' => $iFlood)));
                    }
                }
            }

            $mErrors = \Phpfox::getService('poll')->checkStructure($aVals);
            if (is_array($mErrors)) {
                foreach ($mErrors as $sError) {
                    Phpfox_Error::set($sError);
                }
                $this->template()->assign('aForms', $aVals);
            }
            // require image if add new poll or edit a poll did not have image
            // check theres an image
            if (empty($aVals['temp_file']) && Phpfox::getParam('poll.is_image_required')) {
                if (!$bIsEdit || ($bIsEdit && (empty($aPoll['image_path']) || !empty($aVals['remove_photo'])))) {
                    Phpfox_Error::set(_p('each_poll_requires_an_image'));
                }
            }
            if ($oValid->isValid($aVals)) {
                // we do the insert
                // check if its updating:
                if ($bIsEdit) {
                    $aVals['poll_id'] = $aPoll['poll_id'];

                    if (\Phpfox::getService('poll.process')->add(Phpfox::getUserId(), $aVals, true)) {
                        if ($this->request()->get('submit_poll')) {
                            $this->url()->permalink('poll', $aPoll['poll_id'], $aPoll['question'], true,
                                _p('your_poll_has_been_updated'));
                        } else {
                            $this->url()->send('poll.design', array('id' => $aPoll['poll_id']),
                                _p('your_poll_has_been_updated'));
                        }
                    } else {
                        $this->template()->assign('aForms', $aVals);
                    }
                } else {
                    if (list($iId, $aPoll) = \Phpfox::getService('poll.process')->add(Phpfox::getUserId(), $aVals)) {

                        if ($this->request()->get('submit_poll') || (!Phpfox::getUserParam('poll.poll_can_edit_own_polls') && !Phpfox::getUserParam('poll.poll_can_edit_others_polls'))) {
                            $this->url()->permalink('poll', $iId, $aPoll['question'], true,
                                _p('your_poll_has_been_added') . ((Phpfox::getUserParam('poll.poll_requires_admin_moderation') == true) ? ' ' . _p('your_poll_needs_to_be_approved_before_being_shown_on_the_site') : ''));
                        } else {
                            $this->url()->send('poll.design', array('id' => $iId),
                                _p('your_poll_has_been_added_feel_free_to_custom_design_it_the_way_you_want_here') . ((Phpfox::getUserParam('poll.poll_requires_admin_moderation') == true) ? ' ' . _p('your_poll_needs_to_be_approved_before_being_shown_on_the_site') : ''));
                        }
                    } else {
                        $this->template()->assign('aForms', $aVals);
                    }
                }
            } else {
                $this->template()->assign('aForms', $aVals);
            }
        }

        // final assigns
        $this->template()->setTitle(_p('polls'))
            ->setTitle(($bIsEdit ? _p('editing_a_new_poll') : _p('adding_a_new_poll')))
            ->setBreadCrumb(_p('polls'), $this->url()->makeUrl('poll'))
            ->setBreadCrumb(($bIsEdit ? _p('editing_poll') . ': ' . Phpfox::getLib('parse.output')->shorten($aPoll['question'],
                    \Phpfox::getService('core')->getEditTitleSize(), '...') : _p('adding_poll')),
                $this->url()->makeUrl('poll.add', array('id' => $this->request()->getInt('id'))), true)
            ->setHeader(array(
                    '<script type="text/javascript">$Behavior.setSortableAnswers = function() {iMaxAnswers = ' . $iMaxAnswers . '; iMinAnswers = ' . $iMinAnswers . ';}</script>',
                    'jquery/ui.js' => 'static_script',
                    '<script type="text/javascript">$Behavior.loadSortableAnswers = function() {$(".sortable").sortable({placeholder: "placeholder", axis: "y"});}</script>'
                )
            )
            ->setPhrase(array(
                    'you_have_reached_your_limit',
                    'answer',
                    'you_must_have_a_minimum_of_total_answers',
                    'are_you_sure',
                    'notice'
                )
            )
            ->assign(array(
                    'iTotalAnswers' => ($iMaxAnswers < $iTotalDefaultAnswers) ? $iMaxAnswers : $iTotalDefaultAnswers,
                    'iMaxAnswers' => $iMaxAnswers,
                    'iMin' => $iMinAnswers,
                    'bIsEdit' => $bIsEdit,
                    'sCreateJs' => $oValid->createJS(),
                    'sGetJsForm' => $oValid->getJsForm(($bIsCustom ? false : true)),
                    'bIsCustom' => $bIsCustom,
                    'iItemId' => (int)$this->request()->get('item_id'),
                    'sModuleId' => $this->request()->get('module_id', null),
                    'bRequiredImage' => Phpfox::getParam('poll.is_image_required'),
                    'iMaxFileSize' => (Phpfox::getUserParam('poll.poll_max_upload_size') === 0 ? null : Phpfox::getLib('file')->filesize((Phpfox::getUserParam('poll.poll_max_upload_size') / 1024) * 1048576)),
                )
            );

        if (!empty($aPoll)) {
            $this->template()->buildPageMenu('js_polls_block', [], [
                'link' => Phpfox::permalink('poll', $aPoll['poll_id'], $aPoll['question']),
                'phrase' => _p('view_poll')
            ]);
        }

        if (Phpfox::isModule('attachment')) {
            $this->setParam('attachment_share', array(
                    'type' => 'poll',
                    'id' => 'js_poll_form',
                    'edit_id' => $bIsEdit ? $iReq : 0,

                )
            );
        }
        (($sPlugin = Phpfox_Plugin::get('poll.component_controller_add_process_end')) ? eval($sPlugin) : false);
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('poll.component_controller_add_clean')) ? eval($sPlugin) : false);
    }
}