<?php

namespace Apps\PHPfox_Groups\Service;

use Phpfox;
use Phpfox_Pages_Facade;

/**
 * Class Facade
 *
 * @package Apps\PHPfox_Groups\Service
 */
class Facade extends Phpfox_Pages_Facade
{
    private $_phrases = [
        'unable_to_find_the_page_you_are_looking_for' => 'Unable to find the group you are looking for.',
        'unable_to_add_a_widget_to_this_page' => 'Unable to add a widget to this group.',
        'provide_a_title_for_your_widget' => 'Provide a title for your widget.',
        'provide_content_for_your_widget' => 'Provide content for your widget.',
        'provide_a_menu_title_for_your_widget' => 'Provide a menu title for your widget.',
        'provide_a_url_title_for_your_widget' => 'Provide a URL title for your widget.',
        'you_cannot_use_this_url_for_your_widget' => 'You cannot use this URL for your widget.',
        'page_name_cannot_be_empty' => 'Group name cannot be empty.',
        'full_name_invited_you_to_the_page_title' => '{{ full_name }} invited you to the group "{{ title }}".',
        'to_view_this_page_click_the_link_below_a_href_link_link_a' => 'To view this group click the link below:\r\n<a href=\"{link}\">{link}<\/a>',
        'full_name_sent_you_a_page_invitation' => '{{ full_name }} sent you a group invitation.',
        'invitations_sent_out' => 'Invitations sent out.',
        'that_title_is_not_allowed' => 'That title is not allowed',
        'unable_to_find_the_page' => 'Unable to find the group.',
        'unable_to_moderate_this_page' => 'Unable to moderate this group.',
        'unable_to_find_the_page_you_are_trying_to_login_to' => 'Unable to find the group you are trying to login to.',
        'unable_to_log_in_as_this_page' => 'Unable to log in as this group.',
        'unable_to_find_the_page_you_are_trying_to_delete' => 'Unable to find the group you are trying to delete.',
        'you_are_unable_to_delete_this_page' => 'You are unable to delete this group.',
        'unable_to_find_the_page_you_are_trying_to_approve' => 'Unable to find the group you are trying to approve.',
        'page_title_approved' => 'Group "{{ title }}" approved!',
        'your_page_title_has_been_approved' => 'Your group "{{ title }}" has been approved. To view this group follow the link below: <a href="{{ link }}">{{ link }}</a>',
        'user_is_not_an_admin' => 'User is not an admin',
        'the_photo_does_not_belong_to_this_page' => 'The photo does not belong to this group',
        'unable_to_delete_this_widget' => 'Unable to delete this widget.',
        'pending_memberships' => 'Pending Memberships',
        'home' => 'Home',
        'unable_to_find_the_page_you_are_trying_to_edit' => 'Unable to find the group you are trying to edit.',
        'you_are_unable_to_edit_this_page' => 'You are unable to edit this group.',
        'info' => 'Info',

    ];

    private $_userGroupSettings = [
        'can_moderate_pages' => 'pf_group_moderate',
        'approve_pages' => 'pf_group_approve_groups',
        'max_upload_size_pages' => 'pf_group_max_upload_size',
        'can_edit_all_pages' => 'can_edit_all_groups',
        'can_delete_all_pages' => 'can_delete_all_groups',
        'can_approve_pages' => 'can_approve_groups'
    ];

    public function getItems()
    {
        return \Phpfox::getService('groups');
    }

    public function getCategory()
    {
        return Phpfox::getService('groups.category');
    }

    public function getProcess()
    {
        return Phpfox::getService('groups.process');
    }

    public function getType()
    {
        return Phpfox::getService('groups.type');
    }

    public function getBrowse()
    {
        return Phpfox::getService('groups.browse');
    }

    public function getCallback()
    {
        return Phpfox::getService('groups.callback');
    }

    public function getApi()
    {
        return Phpfox::getService('groups.api');
    }

    public function getItemType()
    {
        return 'groups';
    }

    public function getItemTypeId()
    {
        return 1;
    }

    public function getPhrase($name, $params = [])
    {
        if (empty($params)) {
            return _p((isset($this->_phrases[$name]) ? $this->_phrases[$name] : $name));
        }

        return _p((isset($this->_phrases[$name]) ? $this->_phrases[$name] : $name), $params);
    }

    public function getUserParam($name)
    {
        return user((isset($this->_userGroupSettings[$name]) ? $this->_userGroupSettings[$name] : $name));
    }
}
