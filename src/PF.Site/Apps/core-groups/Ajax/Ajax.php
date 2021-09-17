<?php

namespace Apps\PHPfox_Groups\Ajax;

use Phpfox;
use Phpfox_Ajax;
use Phpfox_Error;
use Phpfox_Image;
use Phpfox_Image_Helper;
use Phpfox_Plugin;

/**
 * Class Ajax
 *
 * @package Apps\PHPfox_Groups\Ajax
 */
class Ajax extends Phpfox_Ajax
{
    public function request()
    {
        Phpfox::getBlock('groups.category');
    }

    public function add()
    {
        Phpfox::isUser(true);

        if (($iId = Phpfox::getService('groups.process')->add($this->get('val')))) {
            $aPage = Phpfox::getService('groups')->getPage($iId);
            $this->call('window.location.href = \'' . \Phpfox_Url::instance()->makeUrl('groups.add',
                    array('id' => $aPage['page_id'], 'new' => '1')) . '\';');
        } else {
            $this->error(false);
            $sError = Phpfox_Error::get();
            $sError = implode('<br />', $sError);
            $this->call('$("#add_group_error_messages").show(); $("#add_group_error_messages").html("' . $sError . '");')
                ->call('$Core.Groups.resetSubmit();');
        }
    }

    public function removeLogo()
    {
        if (($aPage = Phpfox::getService('groups.process')->removeLogo($this->get('page_id'))) !== false) {
            $this->call('window.location.href = \'' . $aPage['link'] . '\';');
        }
    }

    public function deleteWidget()
    {
        if (Phpfox::getService('groups.process')->deleteWidget($this->get('widget_id'))) {
            $this->reload();
        }
    }

    public function widget()
    {
        $this->setTitle(_p('Widgets'));
        Phpfox::getComponent('groups.widget', [], 'controller');

        (($sPlugin = Phpfox_Plugin::get('groups.component_ajax_widget')) ? eval($sPlugin) : false);

        echo '<script type="text/javascript">$Core.loadInit();</script>';
    }

    public function addFeedComment()
    {
        Phpfox::isUser(true);

        $aVals = (array)$this->get('val');
        $iCustomPageId = isset($_REQUEST['custom_pages_post_as_page']) ? $_REQUEST['custom_pages_post_as_page'] : 0;
        if (($iCustomPageId && $iCustomPageId != $aVals['callback_item_id']) || !Phpfox::getService('groups')->hasPerm($aVals['callback_item_id'],
                'groups.share_updates')) {
            $this->alert(_p('You do not have permission to add comments'));
            $this->call('$Core.activityFeedProcess(false);');

            return;
        }

        if (Phpfox::getLib('parse.format')->isEmpty($aVals['user_status'])) {
            $this->alert(_p('add_some_text_to_share'));
            $this->call('$Core.activityFeedProcess(false);');

            return;
        }

        $aPage = Phpfox::getService('groups')->getPage($aVals['callback_item_id']);

        if (!isset($aPage['page_id'])) {
            $this->alert(_p('Unable to find the page you are trying to comment on.'));
            $this->call('$Core.activityFeedProcess(false);');

            return;
        }

        $sLink = Phpfox::getService('groups')->getUrl($aPage['page_id'], $aPage['title'], $aPage['vanity_url']);
        $aCallback = array(
            'module' => 'groups',
            'table_prefix' => 'pages_',
            'link' => $sLink,
            'email_user_id' => $aPage['user_id'],
            'subject' => _p('{{ full_name }} wrote a comment on your group "{{ title }}".',
                array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $aPage['title'])),
            'message' => _p('{{ full_name }} wrote a comment on your group "<a href="{{ link }}">{{ title }}</a>". To see the comment thread, follow the link below: <a href="{{ link }}">{{ link }}</a>',
                array('full_name' => Phpfox::getUserBy('full_name'), 'link' => $sLink, 'title' => $aPage['title'])),
            'notification' => null,
            'feed_id' => 'groups_comment',
            'item_id' => $aPage['page_id'],
            'mail_translated' => true,
            'add_to_main_feed' => true,
            'add_tag' => true
        );

        $aVals['parent_user_id'] = $aVals['callback_item_id'];

        if (isset($aVals['user_status']) && ($iId = Phpfox::getService('feed.process')->callback($aCallback)->addComment($aVals))) {
            if (!isset($aVals['feed_id'])) {
                $aTaggedUsers = Phpfox::getLib('parse.output')->mentionsRegex($aVals['user_status']);
                foreach ($aTaggedUsers as $oUser) {
                    Phpfox::isModule('notification') && Phpfox::getService('notification.process')->add('groups_post_tag',
                        $iId, $oUser->id);
                }
                \Phpfox_Database::instance()->updateCounter('pages', 'total_comment', 'page_id', $aPage['page_id']);

                defined('PHPFOX_PAGES_ADD_COMMENT') || define('PHPFOX_PAGES_ADD_COMMENT', 1);
                Phpfox::getService('feed')->callback($aCallback)->processAjax($iId);
            } else {
                $this->call("$('#js_item_feed_$aVals[feed_id]').find('div.activity_feed_content_status').text('$aVals[user_status]');");
                $this->call('tb_remove();');
                $this->call('setTimeout(function(){$Core.resetActivityFeedForm();$Core.loadInit();}, 500);');
            }
        } else {
            $this->call('$Core.activityFeedProcess(false);');
        }
    }

    public function changeUrl()
    {
        Phpfox::isUser(true);

        if (($aPage = Phpfox::getService('groups')->getForEdit($this->get('id')))) {
            $aVals = $this->get('val');

            $sNewTitle = Phpfox::getLib('parse.input')->cleanTitle($aVals['vanity_url']);

            if (Phpfox::getLib('parse.input')->allowTitle($sNewTitle,
                _p('Group name not allowed. Please select another name.'))) {
                if (Phpfox::getService('groups.process')->updateTitle($this->get('id'), $sNewTitle)) {
                    $this->alert(_p('Successfully updated your group URL.'), _p('URL Updated!'), 300, 150, true);
                }
            }
            $sUrl = Phpfox::getService('groups')->getUrl($aPage['page_id']);
            $this->call('$(".page_section_menu_link").attr("href", "' . $sUrl . '");');
        }

        $this->call('$Core.processForm(\'#js_groups_vanity_url_button\', true);');
    }

    public function signup()
    {
        Phpfox::isUser(true);
        if (Phpfox::getService('groups.process')->register($this->get('page_id'))) {
            $this->alert(_p('Successfully registered for this group. Your membership is pending an admins approval. As soon as your membership has been approved you will be notified.'));
        }
    }

    public function moderation()
    {
        Phpfox::isUser(true);
        $sAction = $this->get('action');

        if (Phpfox::getService('groups.process')->moderation($this->get('item_moderate'), $this->get('action'))) {
            foreach ((array)$this->get('item_moderate') as $iId) {
                $this->remove('#js_pages_user_entry_' . $iId);
            }

            $this->updateCount();
            switch ($sAction) {
                case 'delete':
                    $sMessage = _p('Successfully deleted user(s).');
                    break;
                case 'approve':
                    $sMessage = _p('Successfully approved user(s).');
                    break;
                default:
                    $sMessage = _p('Successfully moderated user(s).');
                    break;
            }
            $this->alert($sMessage, _p('Moderation'), 300, 150, true);
        }

        $this->hide('.moderation_process');
        $this->call('setTimeout(function() {location.reload();}, 3000);');
    }

    public function logBackUser()
    {
        $this->error(false);
        Phpfox::isUser(true);
        $aUser = Phpfox::getService('groups')->getLastLogin();
        list ($bPass,) = Phpfox::getService('user.auth')->login($aUser['email'], $this->get('password'), true,
            $sType = 'email');
        if ($bPass) {
            Phpfox::getService('groups.process')->clearLogin($aUser['user_id']);

            $this->call('window.location.href = \'' . \Phpfox_Url::instance()->makeUrl('') . '\';');
        } else {
            $this->html('#js_error_pages_login_user',
                '<div class="error_message">' . implode('<br />', \Phpfox_Error::get()) . '</div>');
        }
    }

    public function pageModeration()
    {
        Phpfox::isUser(true);

        switch ($this->get('action')) {
            case 'approve':
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('groups.process')->approve($iId);
                }
                Phpfox::addMessage(_p('Group(s) successfully approved.'));
                $this->call('window.location.reload();');
                break;
            case 'delete':
                foreach ((array)$this->get('item_moderate') as $iId) {
                    Phpfox::getService('groups.process')->delete($iId);
                }
                Phpfox::addMessage(_p('Group(s) successfully deleted.'));
                $this->call('window.location.reload();');
                break;
            default:
                $sMessage = '';
                $this->updateCount();
                $this->alert($sMessage, _p('Moderation'), 300, 150, true);
                $this->hide('.moderation_process');
                break;
        }
    }

    public function approve()
    {
        if (Phpfox::getService('groups.process')->approve($this->get('page_id'))) {
            $this->alert(_p('Group has been approved.'), _p('Group Approved'), 300, 100, true);
            $this->hide('#js_item_bar_approve_image');
            $this->hide('.js_moderation_off');
            $this->show('.js_moderation_on');
        }
    }

    public function updateActivity()
    {
        Phpfox::getService('groups.process')->updateActivity($this->get('id'), $this->get('active'), $this->get('sub'));
    }

    public function categoryOrdering()
    {
        Phpfox::isAdmin(true);
        $aVals = $this->get('val');
        Phpfox::getService('core.process')->updateOrdering(array(
                'table' => 'pages_type',
                'key' => 'type_id',
                'values' => $aVals['ordering']
            )
        );

        Phpfox::getLib('cache')->remove();
    }

    public function categorySubOrdering()
    {
        Phpfox::isAdmin(true);
        $aVals = $this->get('val');
        Phpfox::getService('core.process')->updateOrdering(array(
                'table' => 'pages_category',
                'key' => 'category_id',
                'values' => $aVals['ordering']
            )
        );

        Phpfox::getLib('cache')->remove();
    }


    public function setCoverPhoto()
    {
        $iPageId = $this->get('page_id');
        $iPhotoId = $this->get('photo_id');

        if (Phpfox::getService('groups.process')->setCoverPhoto($iPageId, $iPhotoId)) {
            $this->call('window.location.href = "' . Phpfox::permalink('groups', $this->get('page_id'),
                    '') . 'coverupdate_1";');

        }
    }

    public function repositionCoverPhoto()
    {
        Phpfox::getService('groups.process')->updateCoverPosition($this->get('id'), $this->get('position'));
        Phpfox::addMessage(_p('position_set_correctly'));
        $this->reload();
    }

    public function updateCoverPosition()
    {
        if (Phpfox::getService('groups.process')->updateCoverPosition($this->get('page_id'), $this->get('position'))) {
            $this->call('window.location.href = "' . Phpfox::permalink('groups', $this->get('page_id'), '') . '";');
            Phpfox::addMessage(_p('Position set correctly.'));
        }
    }

    public function removeCoverPhoto()
    {
        if (Phpfox::getService('groups.process')->removeCoverPhoto($this->get('page_id'))) {
            $this->call('window.location.href=window.location.href;');
        }
    }

    public function cropme()
    {
        Phpfox::getBlock('groups.cropme');
        $this->call('<script>$Behavior.crop_groups_image_photo();</script>');
    }

    public function processCropme()
    {
        $aVals = $this->get('val');
        $aPage = Phpfox::getService('groups')->getForEdit($aVals['page_id']);
        if (!Phpfox::getService('groups')->isAdmin($aPage)) {
            return;
        }
        //Process crop image
        if (isset($aVals['crop-data']) && !empty($aVals['crop-data'])) {
            $sTempPath = PHPFOX_DIR_CACHE . md5('pages_avatar' . $aVals['page_id']) . '.png';
            list(, $data) = explode(';', $aVals['crop-data']);
            list(, $data) = explode(',', $data);
            $data = base64_decode($data);
            file_put_contents($sTempPath, $data);
            $oImage = Phpfox_Image::instance();
            $aSize = Phpfox::getService('groups')->getPhotoPicSizes();
            foreach ($aSize as $iSize) {
                if (Phpfox::getParam('core.keep_non_square_images')) {
                    $oImage->createThumbnail(sprintf($sTempPath, ''),
                        Phpfox::getParam('pages.dir_image') . sprintf($aPage['image_path'], "_$iSize"), $iSize, $iSize,
                        false);
                }
                $oImage->createThumbnail(sprintf($sTempPath, ''),
                    Phpfox::getParam('pages.dir_image') . sprintf($aPage['image_path'], '_' . $iSize . '_square'),
                    $iSize, $iSize, false);
            }

            // update user photo for page
            $iUserId = Phpfox::getService('pages')->getUserId($aVals['page_id']);
            define('PHPFOX_PAGES_IS_IN_UPDATE', true);
            Phpfox::getService('user.process')->uploadImage($iUserId, true, $sTempPath);

            // delete temporary image
            register_shutdown_function(function () use ($sTempPath) {
                @unlink($sTempPath);
            });

            /**
             * ++ Note: we do not replace original image for future editing thumbnail
             */
        }
        //End crop image
        $sImagePath = Phpfox_Image_Helper::instance()->display([
            'server_id' => $aPage['image_server_id'],
            'path' => 'pages.url_image',
            'file' => $aPage['image_path'],
            'suffix' => '_120',
            'max_width' => '120',
            'max_height' => '120',
            'thickbox' => true,
            'time_stamp' => true,
            'return_url' => true
        ]);

        $this->call('$("#js_current_image_wrapper span").css("background-image", \'url("' . $sImagePath . '")\');');
        $this->call("tb_remove();");
    }

    public function deleteCategory()
    {
        $this->setTitle(_p('delete_category'));
        Phpfox::getBlock('groups.delete-category');
    }

    public function deleteCategoryImage()
    {
        Phpfox::getService('groups.type')->deleteImage($this->get('type_id'));
        $this->call('$(".category-image").remove();');
        $this->softNotice(_p('delete_category_image_successfully'));
    }

    public function addGroup()
    {
        Phpfox::getBlock('groups.add-group', ['type_id' => $this->get('type_id')]);
    }

    public function orderWidget()
    {
        $aOrdering = $this->get('ordering');

        if (empty($aOrdering)) {
            return;
        }

        foreach ($aOrdering as $iWidgetId => $iOrder) {
            Phpfox::getService('groups')->updateWidgetOrder($iWidgetId, $iOrder);
        }
    }

    public function removeMember()
    {
        $iGroupId = $this->get('group_id');
        $iUserId = $this->get('user_id');

        if (!$iGroupId || !$iUserId) {
            return;
        }

        Phpfox::getService('like.process')->delete('groups', $iGroupId, $iUserId);
        $this->fadeOut("#groups-member-$iUserId")
            ->call('$Core.Groups.updateCounter("#all-members-count");');
    }

    public function approvePendingRequest()
    {
        $iSignUpId = $this->get('sign_up');
        $iUserId = $this->get('user_id');

        if (!$iSignUpId) {
            return;
        }

        Phpfox::getService('groups.process')->moderation([$iSignUpId], 'approve');
        $this->fadeOut("#groups-member-$iUserId");
    }

    public function removePendingRequest()
    {
        $iSignUpId = $this->get('sign_up');
        $iUserId = $this->get('user_id');

        if (!$iSignUpId) {
            return;
        }

        Phpfox::getService('groups.process')->moderation([$iSignUpId], '');
        $this->fadeOut("#groups-member-$iUserId")
            ->call('$Core.Groups.updateCounter("#pending-members-count");');
    }

    public function removeAdmin()
    {
        $iGroupId = $this->get('group_id');
        $iUserId = $this->get('user_id');

        if (!$iGroupId || !$iUserId) {
            return;
        }

        Phpfox::getService('groups.process')->removeAdmin($iGroupId, $iUserId);
        $this->fadeOut("#groups-member-$iUserId")
            ->call('$Core.Groups.updateCounter("#admin-members-count");');
    }

    public function getMembers()
    {
        $sContainer = $this->get('container');
        Phpfox::getBlock('groups.search-member', [
            'tab' => $this->get('tab'),
            'container' => $sContainer,
            'group_id' => $this->get('group_id'),
            'search' => $this->get('search')
        ]);
        $this->html("$sContainer", $this->getContent(false));
        if ($this->get('search')) {
            $this->call('$Core.Groups.searchingDone(true);');
        }
        $this->call('$Core.loadInit();');
    }

    public function memberModeration()
    {
        $sAction = $this->get('action');
        $aUserId = $this->get('item_moderate');
        $iPageId = $this->get('page_id');

        switch ($sAction) {
            case 'delete':
                foreach ($aUserId as $iUserId) {
                    Phpfox::getService('like.process')->delete('groups', $iPageId, $iUserId);
                }
                break;
        }

        $this->call('window.location.reload();');
    }
}
