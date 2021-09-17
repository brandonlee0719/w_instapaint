<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\Core_Polls\Controller;

use Phpfox;
use Phpfox_Error;
use Phpfox_Module;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');


class ViewController extends \Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        Phpfox::getUserParam('poll.can_access_polls', true);

        (($sPlugin = Phpfox_Plugin::get('poll.component_controller_view_process_start')) ? eval($sPlugin) : false);

        // there are times when this controller is actually called
        // in the Poll_Component_Controller_Profile like when the poll
        // is in the profile

        $iPage = $this->request()->getInt('page', 0);
        $iPageSize = 10;

        // we need to make sure we're getting the
        if (!($iPoll = $this->request()->getInt('req2'))) {
            $this->url()->send('poll');
        }


        // we need to load one poll
        $aPoll = \Phpfox::getService('poll')->getPollByUrl($iPoll, $iPage, $iPageSize, true, true);

        if ($aPoll === false) {
            return Phpfox_Error::display('Not a valid poll.');
        }

        if (Phpfox::isUser() && \Phpfox::getService('user.block')->isBlocked(null, $aPoll['user_id'])) {
            return Phpfox_Module::instance()->setController('error.invalid');
        }


        if (!isset($aPoll['is_friend'])) {
            $aPoll['is_friend'] = 0;
        }

        if (Phpfox::isModule('privacy')) {
            \Phpfox::getService('privacy')->check('poll', $aPoll['poll_id'], $aPoll['user_id'], $aPoll['privacy'],
                $aPoll['is_friend']);
        }
        //get permission for poll
        Phpfox::getService('poll')->getPermissions($aPoll);
        $aPoll['canViewResult'] = ((Phpfox::getUserParam('poll.can_view_user_poll_results_own_poll') && $aPoll['user_id'] == Phpfox::getUserId()) || Phpfox::getUserParam('poll.can_view_user_poll_results_other_poll'));
        $aPoll['canViewResultVote'] = isset($aPoll['user_voted_this_poll']) && ($aPoll['user_voted_this_poll'] == false && Phpfox::getUserParam('poll.view_poll_results_before_vote')) || ($aPoll['user_voted_this_poll'] == true && Phpfox::getUserParam('poll.view_poll_results_after_vote'));
        // set if we can show the poll results
        // is guest the owner of the poll
        $bIsOwner = $aPoll['user_id'] == Phpfox::getUserId();
        $bShowResults = false;
        if ($bIsOwner && Phpfox::getUserParam('poll.can_view_user_poll_results_own_poll') ||
            (Phpfox::getUserParam('poll.can_view_user_poll_results_other_poll'))
        ) {
            $bShowResults = true;
        }
        $this->template()->assign(array('bShowResults' => $bShowResults));

        if ($aPoll['view_id'] == 1) {
            if ((!Phpfox::getUserParam('poll.poll_can_moderate_polls') && $aPoll['user_id'] != Phpfox::getUserId())) {
                return Phpfox_Error::display(_p('unable_to_view_this_poll'));
            }

            if ($sModerate = $this->request()->get('moderate')) {
                Phpfox::getUserParam('poll.poll_can_moderate_polls', true);
                switch ($sModerate) {
                    case 'approve':
                        if (\Phpfox::getService('poll.process')->moderatePoll($aPoll['poll_id'], 0)) {
                            $this->url()->send('current', array('poll', $aPoll['question_url']),
                                _p('poll_successfully_approved'));
                        }
                        break;
                    default:
                        break;
                }
            }
        }

        // Increment the view counter
        $bUpdateCounter = false;
        if (Phpfox::isModule('track')) {
            if (!$aPoll['poll_is_viewed'] && !Phpfox::getUserBy('is_invisible')) {
                $bUpdateCounter = true;
                Phpfox::getService('track.process')->add('poll', $aPoll['poll_id']);
            } elseif ($aPoll['poll_is_viewed'] && !Phpfox::getUserBy('is_invisible')) {
                if (!setting('track.unique_viewers_counter')) {
                    $bUpdateCounter = true;
                    Phpfox::getService('track.process')->add('poll', $aPoll['poll_id']);
                } else {
                    Phpfox::getService('track.process')->update('poll', $aPoll['poll_id']);
                }
            }
        } else {
            $bUpdateCounter = true;
        }
        if ($bUpdateCounter) {
            \Phpfox::getService('poll.process')->updateView($aPoll['poll_id']);
        }
        // check editing permissions
        $aPoll['bCanEdit'] = \Phpfox::getService('poll')->bCanEdit($aPoll['user_id']);
        $aPoll['bCanDelete'] = \Phpfox::getService('poll')->bCanDelete($aPoll['user_id']);
        $aTitleLabel = [
            'type_id' => 'poll'
        ];

        if ($aPoll['is_featured']) {
            $aTitleLabel['label']['featured'] = [
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'icon_class' => 'diamond'

            ];
        }
        if ($aPoll['is_sponsor']) {
            $aTitleLabel['label']['sponsored'] = [
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'icon_class' => 'sponsor'

            ];
        }
        if (!$aPoll['canVotesWithCloseTime']) {
            $aTitleLabel['label']['closed'] = [
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'icon_class' => 'warning'

            ];
        }
        $aTitleLabel['total_label'] = isset($aTitleLabel['label']) ? count($aTitleLabel['label']) : 0;

        if ($aPoll['view_id'] == 1) {
            $aTitleLabel['label']['pending'] = [
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'icon_class' => 'clock-o'

            ];
            $aPendingItem = [
                'message' => _p('this_poll_is_being_moderated_and_no_votes_can_be_added_until_it_has_been_approved'),
                'actions' => []
            ];
            if ($aPoll['canApprove']) {
                $aPendingItem['actions']['approve'] = [
                    'is_ajax' => true,
                    'label' => _p('approve'),
                    'action' => '$.ajaxCall(\'poll.moderatePoll\',\'iResult=0&amp;iPoll='.$aPoll['poll_id'].'\')'
                ];
            }
            if ($aPoll['canEdit']) {
                $aPendingItem['actions']['edit'] = [
                    'label' => _p('edit'),
                    'action' => $this->url()->makeUrl('poll.add',['id' => $aPoll['poll_id']]),
                ];
            }
            if ($aPoll['canDelete']) {
                $aPendingItem['actions']['delete'] = [
                    'is_confirm' => true,
                    'confirm_message' => _p('are_you_sure_you_want_to_delete_this_poll'),
                    'label' => _p('delete'),
                    'action' => $this->url()->makeUrl('poll',['delete' => $aPoll['poll_id']])
                ];
            }
            $this->template()->assign([
                'aPendingItem' => $aPendingItem
            ]);
        }
        // Define params for "review views" block tracker
        $this->setParam(array(
                'sTrackType' => 'poll',
                'iTrackId' => $aPoll['poll_id'],
                'iTrackUserId' => $aPoll['user_id'],
                'aPoll' => $aPoll
            )
        );

        $this->setParam('aFeed', array(
                'comment_type_id' => 'poll',
                'privacy' => $aPoll['privacy'],
                'comment_privacy' => Phpfox::getUserParam('poll.can_post_comment_on_poll') ? 0 : 3,
                'like_type_id' => 'poll',
                'feed_is_liked' => $aPoll['is_liked'],
                'feed_is_friend' => $aPoll['is_friend'],
                'item_id' => $aPoll['poll_id'],
                'user_id' => $aPoll['user_id'],
                'total_comment' => $aPoll['total_comment'],
                'total_like' => $aPoll['total_like'],
                'feed_link' => $this->url()->permalink('poll', $aPoll['poll_id'], $aPoll['question']),
                'feed_title' => $aPoll['question'],
                'feed_display' => 'view',
                'feed_total_like' => $aPoll['total_like'],
                'report_module' => 'poll',
                'report_phrase' => _p('report_this_poll_lowercase')
            )
        );

        $this->template()->setTitle($aPoll['question'])
            ->setBreadCrumb(_p('polls'), $this->url()->makeUrl('poll'))
            ->setBreadCrumb($aPoll['question'], $this->url()->permalink('poll', $aPoll['poll_id'], $aPoll['question']),
                true)
            ->setMeta('keywords', $this->template()->getKeywords($aPoll['question']))
            ->setMeta('description', _p('full_name_s_poll_from_time_stamp_question', array(
                        'full_name' => $aPoll['full_name'],
                        'time_stamp' => Phpfox::getTime(Phpfox::getParam('core.description_time_stamp'),
                            $aPoll['time_stamp']),
                        'question' => $aPoll['question']
                    )
                )
            )
            ->setMeta('description', Phpfox::getParam('poll.poll_meta_description'))
            ->setMeta('keywords', Phpfox::getParam('poll.poll_meta_keywords'))
            ->setHeader('cache', array(
                    'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                    'jquery/plugin/jquery.scrollTo.js' => 'static_script',
                )
            )->setEditor(array(
                    'load' => 'simple'
                )
            )->assign(array(
                    'bIsViewingPoll' => true,
                    'aPoll' => $aPoll,
                    'sShareDescription' => str_replace(array("\n", "\r", "\r\n"), '', $aPoll['description']),
                    'sAddThisPubId' => setting('core.addthis_pub_id', ''),
                    'aTitleLabel' => $aTitleLabel
                )
            );

        if (!empty($aPoll['image_path'])) {
            $aPoll['image'] = Phpfox::getLib('image.helper')->display(array(
                'server_id' => $aPoll['server_id'],
                'path' => 'poll.url_image',
                'file' => $aPoll['image_path'],
                'suffix' => '',
                'return_url' => true
            ));
            $size_img = @getimagesize($aPoll['image']);
            if (!empty($size_img)) {
                $og_image_width = $size_img[0];
                $og_image_height = $size_img[1];
            } else {
                $og_image_width = 640;
                $og_image_height = 442;
            }
            $this->template()
                ->setMeta('og:image', $aPoll['image'])
                ->setMeta('og:image:width', $og_image_width)
                ->setMeta('og:image:height', $og_image_height);
        }

        if (isset($aPoll['answer']) && is_array($aPoll['answer'])) {
            foreach ($aPoll['answer'] as $aAnswer) {
                $this->template()->setMeta('keywords', $this->template()->getKeywords($aAnswer['answer']));
            }
        }

        Phpfox::getService('poll')->buildMenu();

        (($sPlugin = Phpfox_Plugin::get('poll.component_controller_view_process_end')) ? eval($sPlugin) : false);
        return null;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('poll.component_controller_view_clean')) ? eval($sPlugin) : false);
    }
}