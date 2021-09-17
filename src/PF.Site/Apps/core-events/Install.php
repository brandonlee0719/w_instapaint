<?php
namespace Apps\Core_Events;

use Core\App;

/**
 * Class Install
 * @author  phpFox
 * @version 4.6.0
 * @package Apps\Core_Events
 */
class Install extends App\App
{
    private $_app_phrases = [

    ];

    public $store_id = 2005;

    protected function setId()
    {
        $this->id = 'Core_Events';
    }

    protected function setAlias()
    {
        $this->alias = 'event';
    }

    protected function setName()
    {
        $this->name = _p('Events');
    }

    protected function setVersion()
    {
        $this->version = '4.6.0';
    }

    protected function setSupportVersion()
    {
        $this->start_support_version = '4.6.0';

    }

    protected function setSettings()
    {
        $this->settings = [
            'event_paging_mode' => [
                'var_name' => 'event_paging_mode',
                'info' => 'Pagination Style',
                'description' => 'Select Pagination Style at Search Page',
                'type' => 'select',
                'value' => 'loadmore',
                'options' => [
                    'loadmore' => 'Scrolling down to Load More items',
                    'next_prev' => 'Use Next and Pre buttons',
                    'pagination' => 'Use Pagination with page number'
                ],
                'ordering' => 1
            ],
            'event_default_sort_time' => [
                'var_name' => 'event_default_sort_time',
                'info' => 'Default time to sort events',
                'description' => 'Select default time time to sort events in listing events page (Except Pending page, My page and Profile page) and some blocks',
                'type' => 'select',
                'value' => 'ongoing',
                'options' => [
                    'all-time' => 'All Time',
                    'this-month' => 'This Month',
                    'this-week' => 'This week',
                    'today' => 'Today',
                    'upcoming' => 'Upcoming',
                    'ongoing' => 'Ongoing'
                ],
                'ordering' => 2
            ],
            'event_meta_description' => [
                'var_name' => 'event_meta_description',
                'info' => 'Events Meta Description',
                'description' => 'Meta description added to pages related to the Events app. <a target="_bank" href="' . \Phpfox_Url::instance()->makeUrl('admincp.language.phrase') . '?q=seo_event_meta_description">Click here</a> to edit meta description.<span style="float:right;">(SEO) <input style="width:150px;" readonly value="seo_event_meta_description"></span>',
                'type' => '',
                'value' => '{_p var=\'seo_event_meta_description\'}',
                'group_id' => 'seo',
                'ordering' => 3,
            ],
            'event_meta_keywords' => [
                'var_name' => 'event_meta_keywords',
                'info' => 'Events Meta Keywords',
                'description' => 'Meta keywords that will be displayed on sections related to the Events app. <a target="_bank" href="' . \Phpfox_Url::instance()->makeUrl('admincp.language.phrase') . '?q=seo_event_meta_keywords">Click here</a> to edit meta keywords.<span style="float:right;">(SEO) <input style="width:150px;" readonly value="seo_event_meta_keywords"></span>',
                'type' => '',
                'value' => '{_p var=\'seo_event_meta_keywords\'}',
                'group_id' => 'seo',
                'ordering' => 4,
            ],
            'event_basic_information_time' => [
                'var_name' => 'event_basic_information_time',
                'info' => 'Event Basic Information Time Stamp',
                'description' => 'This is the time stamp that is used when viewing an event.',
                'type' => 'string',
                'value' => 'l, F j, Y g:i a',
                'ordering' => 5,
            ],
            'event_display_event_created_in_group' => [
                'var_name' => 'event_display_event_created_in_group',
                'info' => 'Display events which created in Group to the All Events page at the Events app',
                'description' => 'Enable to display all public events to the both Events page in group detail and All Events page in Events app. Disable to display events created by an users to the both Events page in group detail and My Events page of this user in Events app and nobody can see these events in Events app but owner.',
                'type' => 'boolean',
                'value' => 0,
                'ordering' => 6,
            ],
            'event_display_event_created_in_page' => [
                'var_name' => 'event_display_event_created_in_page',
                'info' => 'Display events which created in Page to the All Events page at the Events app',
                'description' => 'Enable to display all public events to the both Events page in page detail and All Events page in Events app. Disable to display events created by an users to the both Events page in page detail and My Events page of this user in Events app and nobody can see these events in Events app but owner.',
                'type' => 'boolean',
                'value' => 0,
                'ordering' => 7,
            ],
        ];
    }

    protected function setUserGroupSettings()
    {
        $this->user_group_settings = [
            'can_edit_own_event' => [
                'var_name' => 'can_edit_own_event',
                'info' => 'Can edit own event?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
                'ordering' => 1,
            ],
            'can_edit_other_event' => [
                'var_name' => 'can_edit_other_event',
                'info' => 'Can edit all events?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 2,
            ],
            'can_delete_own_event' => [
                'var_name' => 'can_delete_own_event',
                'info' => 'Can delete own event?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
                'ordering' => 3,
            ],
            'can_delete_other_event' => [
                'var_name' => 'can_delete_other_event',
                'info' => 'Can delete all events?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 4,
            ],
            'max_upload_size_event' => [
                'var_name' => 'max_upload_size_event',
                'info' => 'Max file size for event photos in kilobits (kb). (1000 kb = 1 mb) ',
                'description' => 'For unlimited add "0" without quotes.',
                'type' => 'integer',
                'value' => [
                    '1' => '5000',
                    '2' => '5000',
                    '3' => '5000',
                    '4' => '5000',
                    '5' => '5000'
                ],
                'ordering' => 5,
            ],
            'can_post_comment_on_event' => [
                'var_name' => 'can_post_comment_on_event',
                'info' => 'Can post comments on events?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
                'ordering' => 6,
            ],
            'can_approve_events' => [
                'var_name' => 'can_approve_events',
                'info' => 'Can approve events?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 7,
            ],
            'can_feature_events' => [
                'var_name' => 'can_feature_events',
                'info' => 'Can feature events?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 8,
            ],
            'event_must_be_approved' => [
                'var_name' => 'event_must_be_approved',
                'info' => 'Events must be approved first before they are displayed publicly?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '0',
                    '2' => '0',
                    '3' => '1',
                    '4' => '0',
                    '5' => '0'
                ],
                'ordering' => 9,
            ],
            'total_mass_emails_per_hour' => [
                'var_name' => 'total_mass_emails_per_hour',
                'info' => 'Define how long this user group must wait until they are allowed to send out another mass email.',
                'description' => '',
                'type' => 'integer',
                'value' => [
                    '1' => '0',
                    '2' => '60',
                    '3' => '60',
                    '4' => '0',
                    '5' => '60'
                ],
                'ordering' => 10,
            ],
            'can_mass_mail_own_members' => [
                'var_name' => 'can_mass_mail_own_members',
                'info' => 'Can mass email own event guests?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
                'ordering' => 11,
            ],
            'can_access_event' => [
                'var_name' => 'can_access_event',
                'info' => 'Can browse and view the event module?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '1',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 12,
            ],
            'can_create_event' => [
                'var_name' => 'can_create_event',
                'info' => 'Can create an event?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 13,
            ],
            'can_sponsor_event' => [
                'var_name' => 'can_sponsor_event',
                'info' => 'Can members of this user group mark a event as Sponsor without paying fee?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
                'ordering' => 14,
            ],
            'can_purchase_sponsor' => [
                'var_name' => 'can_purchase_sponsor',
                'info' => 'Can members of this user group purchase a sponsored ad space for their items?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '0',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
                'ordering' => 15,
            ],
            'event_sponsor_price' => [
                'var_name' => 'event_sponsor_price',
                'info' => 'How much is the sponsor space worth for events? This works in a CPM basis.',
                'description' => '',
                'type' => 'currency',
                'value' => ['USD' => 0,],
                'ordering' => 16,
            ],
            'auto_publish_sponsored_item' => [
                'var_name' => 'auto_publish_sponsored_item',
                'info' => 'Auto publish sponsored item?',
                'description' => 'After the user has purchased a sponsored space, should the item be published right away? 
If set to false, the admin will have to approve each new purchased sponsored item space before it is shown in the site.',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
                'ordering' => 17,
            ],
            'flood_control_events' => [
                'var_name' => 'flood_control_events',
                'info' => 'How many minutes should a user wait before they can create another event? ',
                'description' => 'Note: Setting it to "0" (without quotes) is default and users will not have to wait.',
                'type' => 'integer',
                'value' => [
                    '1' => '0',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
                'ordering' => 18,
            ],
            'points_event' => [
                'var_name' => 'points_event',
                'info' => 'How many points does the user get when they add a new event?',
                'description' => '',
                'type' => 'integer',
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 19,
            ],
        ];
    }

    protected function setComponent()
    {
        $this->component = [
            'block' => [
                'attending' => '',
                'category' => '',
                'menu' => '',
                'rsvp' => '',
                'profile' => '',
                'info' => '',
                'sponsored' => '',
                'list' => '',
                'invite' => '',
                'featured' => '',
                'suggestion' => ''
            ],
            'controller' => [
                'view' => 'event.view',
                'index' => 'event.index',
                'profile' => 'event.profile'
            ]
        ];
    }

    protected function setComponentBlock()
    {
        $this->component_block = [
            'Category' => [
                'type_id' => '0',
                'm_connection' => 'event.index',
                'component' => 'category',
                'location' => '1',
                'is_active' => '1',
                'ordering' => '1',
            ],
            'Birthday' => [
                'type_id' => '0',
                'm_connection' => 'event.index',
                'module_id' => 'friend',
                'component' => 'birthday',
                'location' => '3',
                'is_active' => '1',
                'ordering' => '1',
            ],
            'Invite' => [
                'type_id' => '0',
                'm_connection' => 'event.index',
                'component' => 'invite',
                'location' => '3',
                'is_active' => '1',
                'ordering' => '2',
            ],
            'Featured' => [
                'type_id' => '0',
                'm_connection' => 'event.index',
                'component' => 'featured',
                'location' => '3',
                'is_active' => '1',
                'ordering' => '3',
            ],
            'Sponsored' => [
                'type_id' => '0',
                'm_connection' => 'event.index',
                'component' => 'sponsored',
                'location' => '3',
                'is_active' => '1',
                'ordering' => '4',
            ],
            'Event Information' => [
                'type_id' => '0',
                'm_connection' => 'event.view',
                'component' => 'info',
                'location' => '4',
                'is_active' => '1',
                'ordering' => '1',
            ],
            'Activity Feed' => [
                'type_id' => '0',
                'm_connection' => 'event.view',
                'module_id' => 'feed',
                'component' => 'display',
                'location' => '4',
                'is_active' => '1',
                'ordering' => '3',
            ],
            'Suggestion Events' => [
                'type_id' => '0',
                'm_connection' => 'event.view',
                'component' => 'suggestion',
                'location' => '3',
                'is_active' => '1',
                'ordering' => '2',
            ],
        ];
    }

    protected function setPhrase()
    {
        $this->phrase = $this->_app_phrases;
    }

    protected function setOthers()
    {
        $this->admincp_route = '/event/admincp';
        $this->admincp_menu = [
            'Manage Categories' => '#'
        ];
        $this->admincp_action_menu = [
            '/admincp/event/add' => 'Add New Category'
        ];
        $this->map = [];
        $this->menu = [
            'phrase_var_name' => 'menu_event',
            'url' => 'event',
            'icon' => 'calendar'
        ];
        $this->database = [
            'Event',
            'Event_Category',
            'Event_Category_Data',
            'Event_Feed',
            'Event_Feed_Comment',
            'Event_Invite',
            'Event_Text'
        ];
        $this->_writable_dirs = [
            'PF.Base/file/pic/event/'
        ];
        $this->_apps_dir = 'core-events';
        $this->_admin_cp_menu_ajax = false;
        $this->_publisher = 'phpFox';
        $this->_publisher_url = 'http://store.phpfox.com/';
    }
}