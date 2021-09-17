<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\Core_Events\Ajax;

use Phpfox;
use Phpfox_Cache;
use Phpfox_Database;
use Phpfox_Pager;

defined('PHPFOX') or exit('NO DICE!');

class Ajax extends \Phpfox_Ajax
{
    public function loadMiniForm()
    {
        Phpfox::getBlock('event.mini');

        $sContent = $this->getContent(false);
        $sContent = str_replace(array("\n", "\t"), '', $sContent);

        $this->html('.block_event_sub_holder', $sContent);
        $this->call('$Core.loadInit();');
    }

    public function deleteImage()
    {
        Phpfox::isUser(true);

        if (Phpfox::getService('event.process')->deleteImage($this->get('id'))) {

        }
    }

    public function addRsvp()
    {
        Phpfox::isUser(true);
        $iEventId = $this->get('id');
        if (Phpfox::getService('event.process')->addRsvp($iEventId, $this->get('rsvp'), Phpfox::getUserId())) {
            if ($this->get('rsvp') == 3) {
                $sRsvpMessage = _p('not_attending');
            } elseif ($this->get('rsvp') == 2) {
                $sRsvpMessage = _p('maybe_attending');
            } elseif ($this->get('rsvp') == 1) {
                $sRsvpMessage = _p('attending');
            } else {
                $sRsvpMessage = '';
            }

            if ($this->get('inline')) {
                $this->html('#js_event_rsvp_' . $iEventId, $sRsvpMessage);
                $this->hide('#js_event_rsvp_invite_image_' . $iEventId);
            } else {
                $this->html('#js_event_rsvp_update', _p('done'), '.fadeOut(5000);')
                    ->html('#js_event_rsvp_' . $iEventId, $sRsvpMessage)
                    ->call('$(\'#js_event_rsvp_button\').find(\'input:first\').attr(\'disabled\', false);')
                    ->call('tb_remove();');

                $this->call('$.ajaxCall(\'event.listGuests\', \'&rsvp=' . $this->get('rsvp') . '&id=' . $iEventId . '' . ($this->get('module') ? '&module=' . $this->get('module') . '&item=' . $this->get('item') . '' : '') . '\');')
                    ->call('$Behavior.event_ajax_1 = function(){ $(\'#js_block_border_event_list .menu:first ul li\').removeClass(\'active\'); $(\'#js_block_border_event_list .menu:first ul li a\').each(function() { var aParts = explode(\'rsvp=\', this.href); var aParts2 = explode(\'&\', aParts[1]); if (aParts2[0] == ' . $this->get('rsvp') . ') {  $(this).parent().addClass(\'active\'); } }); };');
            }
            $this->template()->
            assign([
                'iEventId' => $iEventId,
                'iAttendingCnt' => Phpfox::getService('event')->getTotalRsvp($iEventId, 1),
                'iMaybeCnt' => Phpfox::getService('event')->getTotalRsvp($iEventId, 2),
                'iAwaitingCnt' => Phpfox::getService('event')->getTotalRsvp($iEventId, 0),
            ])->getTemplate('event.block.guests-list-tab');
            $this->call('if($(\'#js_guests_list_detail\')){$(\'#js_guests_list_detail\').html(\'' . $this->getContent(false) . '\');$Core.loadInit();}');
        }
    }

    public function listGuests()
    {
        Phpfox::getBlock('event.list');

        $this->html('#js_event_item_holder', $this->getContent(false));
        $this->call('$Core.loadInit();');
    }

    public function browseList()
    {
        Phpfox::getBlock('event.browse');

        if ((int)$this->get('page') > 0) {
            $this->html('#js_event_browse_guest_list', $this->getContent(false));
        } else {
            $this->setTitle(_p('guest_list'));
        }
    }

    public function deleteGuest()
    {
        if (Phpfox::getService('event.process')->deleteGuest($this->get('id'))) {

        }
    }

    public function delete()
    {
        $iId = $this->get('id');
        $bIsDetail = $this->get('is_detail', 0);
        if ($sParentReturn = Phpfox::getService('event.process')->delete($iId)) {
            Phpfox::addMessage(_p('successfully_deleted_event'));
        }
        else {
            $this->alert(_p('you_do_not_have_sufficient_permission_to_delete_this_event'));
            return false;
        }

        if (!$bIsDetail) {
            $this->call('window.location.reload();');
        } else {
            if (is_bool($sParentReturn)) {
                $sUrl = Phpfox::getLib('url')->makeUrl('event');
            } else {
                $sUrl = $sParentReturn;
            }
            $this->call('window.location.href = "' . $sUrl . '";');
        }
    }

    public function rsvp()
    {
        Phpfox::getBlock('event.rsvp');
    }

    public function feature()
    {
        if (Phpfox::getService('event.process')->feature($this->get('event_id'), $this->get('type'))) {
            if ($this->get('type') == '1') {
                $this->alert(_p('event_successfully_featured'));
            } else {
                $this->alert(_p('event_successfully_unfeatured'));
            }
        }
    }

    public function sponsor()
    {
        if (!Phpfox::isModule('ad')) {
            return $this->alert('your_request_is_invalid');
        }
        if (Phpfox::getService('event.process')->sponsor($this->get('event_id'), $this->get('type'))) {
            $aEvent = Phpfox::getService('event')->getForEdit($this->get('event_id'), true);
            if ($this->get('type') == '1') {
                Phpfox::getService('ad.process')->addSponsor(array(
                    'module' => 'event',
                    'item_id' => $this->get('event_id'),
                    'name' => _p('default_campaign_custom_name', ['module' => _p('event'), 'name' => $aEvent['title']])
                ));
                $this->call('$("#js_event_unsponsor_' . $this->get('event_id') . '").show();');
                $this->call('$("#js_event_sponsor_' . $this->get('event_id') . '").hide();');
                //$this->addClass('#js_event_item_holder_'.$this->get('event_id'), 'row_sponsored');
                $this->show('#js_sponsor_phrase_' . $this->get('event_id'));
                $this->alert(_p('event_successfully_sponsored'));
            } else {
                Phpfox::getService('ad.process')->deleteAdminSponsor('event', $this->get('event_id'));
                $this->call('$("#js_event_unsponsor_' . $this->get('event_id') . '").hide();');
                $this->call('$("#js_event_sponsor_' . $this->get('event_id') . '").show();');
                //$this->removeClass('#js_event_item_holder_'.$this->get('event_id'), 'row_sponsored');
                $this->hide('#js_sponsor_phrase_' . $this->get('event_id'));
                $this->alert(_p('event_successfully_un_sponsored'));
            }
            Phpfox_Cache::instance()->remove();
        }
    }

    public function approve()
    {
        $iId = $this->get('event_id');
        if (Phpfox::getUserParam('event.can_approve_events', true) && Phpfox::getService('event.process')->approve($iId)) {
            $this->call('var oSubsectionCountItem = $(\'.sub_section_menu .pending\'); if ($(oSubsectionCountItem).length > 0) { var iSubsectionCount = parseInt(oSubsectionCountItem.html()); if (iSubsectionCount > 1) { oSubsectionCountItem.html(parseInt(iSubsectionCount - 1)); } else { $(\'.sub_section_menu .pending\').parents(\'li\').remove(); } }');
            $this->alert(_p('event_has_been_approved'), _p('event_approved'), 300, 100, true);
            $this->hide('.js_moderation_off');
            $this->show('.js_moderation_on');
            $this->call('window.location.reload();');
        }
    }

    public function moderation()
    {
        Phpfox::isUser(true);

        switch ($this->get('action')) {
            case 'approve':
                Phpfox::getUserParam('event.can_approve_events', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('event.process')->approve($iId);
                }
                $sMessage = _p('event_s_successfully_approved');
                break;
            case 'delete':
                foreach ((array)$this->get('item_moderate') as $iId) {
                    if (!Phpfox::getService('event')->isAdminOfParentItem($iId)) {
                        Phpfox::getUserParam('event.can_delete_other_event', true);
                    }
                    Phpfox::getService('event.process')->delete($iId);
                    $this->call('$("#js_event_item_holder_' . $iId . '").parent().prev().remove();');
                    $this->call('$("#js_event_item_holder_' . $iId . '").parent().remove();');
                }
                $sMessage = _p('event_s_successfully_deleted');
                break;
            case 'feature':
                Phpfox::getUserParam('event.can_feature_events', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    \Phpfox::getService('event.process')->feature($iId, 1);
                }
                $sMessage = _p('event_s_successfully_featured');
                break;
            case 'un-feature':
                Phpfox::getUserParam('event.can_feature_events', true);
                foreach ((array)$this->get('item_moderate') as $iId) {
                    \Phpfox::getService('event.process')->feature($iId, 0);
                }
                $sMessage = _p('event_s_successfully_unfeatured');
                break;
            default:
                $sMessage = '';
                break;
        }

        Phpfox::addMessage($sMessage);
        $this->call('window.location.reload();');
    }

    public function massEmail()
    {
        $iPage = $this->get('page', 1);
        $sSubject = $this->get('subject');
        $sText = $this->get('text');

        if ($iPage == 1 && !Phpfox::getService('event')->canSendEmails($this->get('id'))) {
            $this->hide('#js_event_mass_mail_li');
            $this->alert(_p('you_are_unable_to_send_out_any_mass_emails_at_the_moment'));

            return;
        }

        if (empty($sSubject) || empty($sText)) {
            $this->hide('#js_event_mass_mail_li');
            $this->alert(_p('fill_in_both_a_subject_and_text_for_your_mass_email'));

            return;
        }

        $iCnt = Phpfox::getService('event.process')->massEmail($this->get('id'), $iPage, $this->get('subject'),
            $this->get('text'));

        if ($iCnt === false) {
            $this->hide('#js_event_mass_mail_li');
            $this->alert(_p('you_are_unable_to_send_a_mass_email_for_this_event'));

            return;
        }

        Phpfox_Pager::instance()->set(array(
            'ajax' => 'event.massEmail',
            'page' => $iPage,
            'size' => 20,
            'count' => $iCnt
        ));

        if ($iPage < Phpfox_Pager::instance()->getLastPage()) {
            $this->call('$.ajaxCall(\'event.massEmail\', \'id=' . $this->get('id') . '&page=' . ($iPage + 1) . '&subject=' . $this->get('subject') . '&text=' . $this->get('text') . '\');');

            $this->html('#js_event_mass_mail_send', _p('email_progress_page_total',
                array('page' => $iPage, 'total' => Phpfox_Pager::instance()->getLastPage())));
        } else {
            if (!Phpfox::getService('event')->canSendEmails($this->get('id'), true)) {
                $this->hide('#js_send_email')
                    ->show('#js_send_email_fail')
                    ->html('#js_time_left', Phpfox::getTime(Phpfox::getParam('mail.mail_time_stamp'),
                        Phpfox::getService('event')->getTimeLeft($this->get('id'))));
            }

            $this->hide('#js_event_mass_mail_li');
            $this->alert(_p('done'));
        }
    }

    public function removeInvite()
    {
        Phpfox::getService('event.process')->removeInvite($this->get('id'));
    }

    public function addFeedComment()
    {
        Phpfox::isUser(true);

        $aVals = (array)$this->get('val');

        if (Phpfox::getLib('parse.format')->isEmpty($aVals['user_status'])) {
            $this->alert(_p('add_some_text_to_share'));
            $this->call('$Core.activityFeedProcess(false);');
            return;
        }

        $aEvent = Phpfox::getService('event')->getForEdit($aVals['callback_item_id'], true);

        if (!isset($aEvent['event_id'])) {
            $this->alert(_p('unable_to_find_the_event_you_are_trying_to_comment_on'));
            $this->call('$Core.activityFeedProcess(false);');
            return;
        }

        $sLink = Phpfox::permalink('event', $aEvent['event_id'], $aEvent['title']);
        $aCallback = array(
            'module' => 'event',
            'table_prefix' => 'event_',
            'link' => $sLink,
            'email_user_id' => $aEvent['user_id'],
            'subject' => _p('full_name_wrote_a_comment_on_your_event_title',
                array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aEvent['title'])),
            'message' => _p('full_name_wrote_a_comment_on_your_event_message',
                array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aEvent['title'])),
            'notification' => 'event_comment',
            'feed_id' => 'event_comment',
            'item_id' => $aEvent['event_id']
        );

        $aVals['parent_user_id'] = $aVals['callback_item_id'];

        if (isset($aVals['user_status']) && ($iId = Phpfox::getService('feed.process')->callback($aCallback)->addComment($aVals))) {
            if (isset($aVals['feed_id'])) {
                $this->call("$('#js_item_feed_$aVals[feed_id]').find('div.activity_feed_content_status').text('$aVals[user_status]');");
                $this->call('tb_remove();');
                $this->call('setTimeout(function(){$Core.resetActivityFeedForm();$Core.loadInit();}, 500);');
            } else {
                db()->updateCounter('event', 'total_comment', 'event_id', $aEvent['event_id']);
                Phpfox::getService('feed')->callback($aCallback)->processAjax($iId);
            }
        } else {
            $this->call('$Core.activityFeedProcess(false);');
        }
    }

    //Category
    public function categoryOrdering()
    {
        Phpfox::isAdmin(true);
        $aVals = $this->get('val');
        Phpfox::getService('core.process')->updateOrdering([
            'table' => 'event_category',
            'key' => 'category_id',
            'values' => $aVals['ordering']
        ]);
        Phpfox::getLib('cache')->remove();
    }

    public function toggleActiveCategory()
    {
        $iCategoryId = $this->get('id');
        $iActive = $this->get('active');
        Phpfox::getService('event.category.process')->toggleActiveCategory($iCategoryId, $iActive);
    }
}