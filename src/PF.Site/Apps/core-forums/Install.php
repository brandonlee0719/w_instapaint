<?php
namespace Apps\Core_Forums;

use Core\App;
use Phpfox;

/**
 * Class Install
 * @author  phpFox
 * @version 4.6.0
 * @package Apps\Core_Forums
 */
class Install extends App\App
{
    private $_app_phrases = [

    ];

    public $store_id = 2006;

    protected function setId()
    {
        $this->id = 'Core_Forums';
    }

    protected function setAlias()
    {
        $this->alias = 'forum';
    }

    protected function setName()
    {
        $this->name = _p('Forums');
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
            'keep_active_posts' => [
                'var_name' => 'keep_active_posts',
                'info' => 'Active Posts',
                'description' => 'Define how long we should keep posts active in minutes.<br/>Note that if a post passes this limit it will be displayed on the site normally, however if a post is active there will be some form of letting the user know that they have not viewed the specific thread or forum. Depending on the theme you are using this is usually identified by images and formating the title of the thread or forum to be bold.',
                'type' => 'integer',
                'value' => 60,
                'ordering' => 1,
            ],
            'forum_time_stamp' => [
                'var_name' => 'forum_time_stamp',
                'info' => 'Forum Time Stamp',
                'description' => 'Default forum time stamp',
                'type' => 'string',
                'value' => 'M j, g:i a',
                'ordering' => 2,
            ],
            'total_posts_per_thread' => [
                'var_name' => 'total_posts_per_thread',
                'info' => 'Show posts per thread at first time',
                'description' => '',
                'type' => 'integer',
                'value' => 15,
                'ordering' => 4,
            ],
            'total_forum_tags_display' => [
                'var_name' => 'total_forum_tags_display',
                'info' => 'Total Tag Display',
                'description' => 'Define how many tags should be displayed within the tag cloud for the forum.',
                'type' => 'integer',
                'value' => 100,
                'ordering' => 5,
            ],
            'rss_feed_on_each_forum' => [
                'var_name' => 'rss_feed_on_each_forum',
                'info' => 'RSS Feed within Forums',
                'description' => 'Set to <b>True</b> to enable RSS feeds for each forum.',
                'type' => 'boolean',
                'value' => 1,
                'ordering' => 6,
            ],
            'enable_rss_on_threads' => [
                'var_name' => 'enable_rss_on_threads',
                'info' => 'RSS Feed on Threads',
                'description' => 'Set to <b>True</b> to enable RSS feeds on threads.',
                'type' => 'boolean',
                'value' => 1,
                'ordering' => 7,
            ],
            'enable_thanks_on_posts' => [
                'var_name' => 'enable_thanks_on_posts',
                'info' => 'Enable "Thanks" on posts',
                'description' => 'Set to <b>Yes</b> to enable "Thanks" on posts.<br/><b>Note:</b> If you enable "Thanks" on posts, the feature "Like" on posts will be disable.',
                'type' => 'boolean',
                'value' => 0,
                'ordering' => 8,
            ],
            'forum_meta_description' => [
                'var_name' => 'forum_meta_description',
                'info' => 'Forum Meta Description',
                'description' => 'Meta description added to pages related to the Forum app. <a target="_bank" href="' . \Phpfox_Url::instance()->makeUrl('admincp.language.phrase') . '?q=forum_meta_description">Click here</a> to edit meta description.<span style="float:right;">(SEO) <input style="width:150px;" readonly value="forum_meta_description"></span>',
                'type' => '',
                'value' => '{_p var=\'seo_forum_meta_description\'}',
                'group_id' => 'seo',
                'ordering' => 9,
            ],
            'forum_meta_keywords' => [
                'var_name' => 'forum_meta_keywords',
                'info' => 'Forum Meta Keywords',
                'description' => 'Meta keywords that will be displayed on sections related to the Forum app. <a target="_bank" href="' . \Phpfox_Url::instance()->makeUrl('admincp.language.phrase') . '?q=forum_meta_keywords">Click here</a> to edit meta keywords.<span style="float:right;">(SEO) <input style="width:150px;" readonly value="forum_meta_keywords"></span>',
                'type' => '',
                'value' => '{_p var=\'seo_forum_meta_keywords\'}',
                'group_id' => 'seo',
                'ordering' => 10,
            ],
            'forum_paging_mode' => [
                'var_name' => 'forum_paging_mode',
                'info' => 'Pagination Style',
                'description' => 'Select Pagination Style at Search Page.',
                'type' => 'select',
                'value' => 'loadmore',
                'options' => [
                    'loadmore' => 'Scrolling down to Load More items',
                    'next_prev' => 'Use Next and Pre buttons',
                    'pagination' => 'Use Pagination with page number'
                ],
                'ordering' => 11
            ],
            'default_search_type' => [
                'var_name' => 'default_search_type',
                'info' => 'Default option to search in main forum page',
                'description' => '',
                'type' => 'select',
                'value' => 'threads',
                'options' => [
                    'threads' => 'Show Threads',
                    'posts' => 'Show Posts',
                ],
                'ordering' => 12
            ],
        ];
    }

    protected function setUserGroupSettings()
    {
        $this->user_group_settings = [
            'can_stick_thread' => [
                'var_name' => 'can_stick_thread',
                'info' => 'Can stick a forum thread?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 1,
            ],
            'can_close_a_thread' => [
                'var_name' => 'can_close_a_thread',
                'info' => 'Can close a thread?',
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
            'can_post_announcement' => [
                'var_name' => 'can_post_announcement',
                'info' => 'Can post an announcement?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 3,
            ],
            'can_delete_own_post' => [
                'var_name' => 'can_delete_own_post',
                'info' => 'Can delete their own post?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 4,
            ],
            'can_delete_other_posts' => [
                'var_name' => 'can_delete_other_posts',
                'info' => 'Can delete all posts?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
                'ordering' => 5,
            ],
            'can_add_new_forum' => [
                'var_name' => 'can_add_new_forum',
                'info' => 'Can add a new public forum?',
                'description' => 'Notice: this setting only apply for members who have permission to go to AdminCP',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 6,
            ],
            'can_edit_forum' => [
                'var_name' => 'can_edit_forum',
                'info' => 'Can edit a public forum?',
                'description' => 'Notice: this setting only apply for members who have permission to go to AdminCP',
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
            'can_manage_forum_moderators' => [
                'var_name' => 'can_manage_forum_moderators',
                'info' => 'Can manage forum moderators? ',
                'description' => 'Notice: Includes adding, editing and deleting forum moderators. This setting only apply for members who have permission to go to AdminCP',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
                'ordering' => 8,
            ],
            'can_delete_forum' => [
                'var_name' => 'can_delete_forum',
                'info' => 'Can delete a public forum?',
                'description' => 'Notice: this setting only apply for members who have permission to go to AdminCP',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
                'ordering' => 9,
            ],
            'can_edit_own_post' => [
                'var_name' => 'can_edit_own_post',
                'info' => 'Can edit own forum post?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 10,
            ],
            'can_edit_other_posts' => [
                'var_name' => 'can_edit_other_posts',
                'info' => 'Can edit all forum posts?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 11,
            ],
            'can_move_forum_thread' => [
                'var_name' => 'can_move_forum_thread',
                'info' => 'Can move forum threads?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 12,
            ],
            'can_copy_forum_thread' => [
                'var_name' => 'can_copy_forum_thread',
                'info' => 'Can copy forum threads?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 13,
            ],
            'can_merge_forum_threads' => [
                'var_name' => 'can_merge_forum_threads',
                'info' => 'Can merge forum threads?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 14,
            ],
            'can_reply_to_own_thread' => [
                'var_name' => 'can_reply_to_own_thread',
                'info' => 'Can reply to own thread?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 15,
            ],
            'can_reply_on_other_threads' => [
                'var_name' => 'can_reply_on_other_threads',
                'info' => 'Can reply on all threads?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 16,
            ],
            'can_add_new_thread' => [
                'var_name' => 'can_add_new_thread',
                'info' => 'Can post a new thread?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 17,
            ],
            'can_add_forum_attachments' => [
                'var_name' => 'can_add_forum_attachments',
                'info' => 'Can add attachments to posts?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 18,
            ],
            'enable_captcha_on_posting' => [
                'var_name' => 'enable_captcha_on_posting',
                'info' => 'Enable Captcha protection when posting within the forums?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '0',
                    '2' => '0',
                    '3' => '1',
                    '4' => '0',
                    '5' => '0'
                ],
                'ordering' => 20,
            ],
            'forum_thread_flood_control' => [
                'var_name' => 'forum_thread_flood_control',
                'info' => 'Define how many minutes this user group should wait before they can post a new thread.',
                'description' => 'Note: Set to 0 if there should be no limit.',
                'type' => 'integer',
                'value' => [
                    '1' => '0',
                    '2' => '1',
                    '3' => '50',
                    '4' => '0',
                    '5' => '0'
                ],
                'ordering' => 21,
            ],
            'forum_post_flood_control' => [
                'var_name' => 'forum_post_flood_control',
                'info' => 'Define how many minutes this user group should wait before they can post a new reply to a thread.',
                'description' => 'Note: Set to 0 if there should be no limit.',
                'type' => 'integer',
                'value' => [
                    '1' => '0',
                    '2' => '1',
                    '3' => '50',
                    '4' => '0',
                    '5' => '0'
                ],
                'ordering' => 22,
            ],
            'points_forum' => [
                'var_name' => 'points_forum',
                'info' => 'Points received when adding a thread/post within the forum.',
                'description' => '',
                'type' => 'integer',
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 23,
            ],
            'can_view_forum' => [
                'var_name' => 'can_view_forum',
                'info' => 'Can browse and view the forum module?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '1',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 24,
            ],
            'can_sponsor_thread' => [
                'var_name' => 'can_sponsor_thread',
                'info' => 'Can members of this user group mark a thread as sponsored?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
                'ordering' => 25,
            ],
            'can_purchase_sponsor' => [
                'var_name' => 'can_purchase_sponsor',
                'info' => 'Can members of this user group purchase a sponsored ad space?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '0',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
                'ordering' => 26,
            ],
            'forum_thread_sponsor_price' => [
                'var_name' => 'forum_thread_sponsor_price',
                'info' => 'How much is the sponsor space worth for threads? This works in a CPM basis.',
                'description' => '',
                'type' => 'currency',
                'value' => ['USD' => 0,],
                'ordering' => 27,
            ],
            'auto_publish_sponsored_thread' => [
                'var_name' => 'auto_publish_sponsored_item',
                'info' => 'Auto publish sponsored thread?',
                'description' => 'After the user has purchased a sponsored space, should the thread be published right away? 
If set to false, the admin will have to approve each new purchased sponsored thread before it is shown in the site.',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
                'ordering' => 28,
            ],
            'approve_forum_thread' => [
                'var_name' => 'approve_forum_thread',
                'info' => 'Approve threads before they are displayed publicly?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '0',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
                'ordering' => 29,
            ],
            'can_approve_forum_thread' => [
                'var_name' => 'can_approve_forum_thread',
                'info' => 'Can approve forum threads?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 30,
            ],
            'approve_forum_post' => [
                'var_name' => 'approve_forum_post',
                'info' => 'Approve forum posts before they are displayed publicly?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '0',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
                'ordering' => 31,
            ],
            'can_approve_forum_post' => [
                'var_name' => 'can_approve_forum_post',
                'info' => 'Can approve forum posts?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 32,
            ],
            'can_thank_on_forum_posts' => [
                'var_name' => 'can_thank_on_forum_posts',
                'info' => 'Can give "thanks" on forum posts?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 33,
            ],
            'can_delete_thanks_by_other_users' => [
                'var_name' => 'can_delete_thanks_by_other_users',
                'info' => 'Can delete all "thanks"?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 34,
            ],
            'can_manage_forum_permissions' => [
                'var_name' => 'can_manage_forum_permissions',
                'info' => 'Can manage forum permissions?',
                'description' => 'Notice: this setting only apply for members who have permission to go to AdminCP',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 35,
            ],
            'can_add_poll_to_forum_thread' => [
                'var_name' => 'can_add_poll_to_forum_thread',
                'info' => 'Can attach polls to forum threads?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 36,
            ],
        ];
    }

    protected function setComponent()
    {
        $this->component = [
            'block' => [
                'recent-post' => '',
                'recent-thread' => '',
                'sponsored' => ''
            ],
            'controller' => [
                'index' => 'forum.index',
                'forum' => 'forum.forum',
            ]
        ];
    }

    protected function setComponentBlock()
    {
        $this->component_block = [
            'Recent Posts' => [
                'type_id' => '0',
                'm_connection' => 'forum.forum',
                'component' => 'recent-post',
                'location' => '3',
                'is_active' => '1',
                'ordering' => '1',
            ],
            'Recent Threads' => [
                'type_id' => '0',
                'm_connection' => 'forum.index',
                'component' => 'recent-thread',
                'location' => '3',
                'is_active' => '1',
                'ordering' => '1',
            ],
            'Sponsored Threads' => [
                'type_id' => '0',
                'm_connection' => 'forum.index',
                'component' => 'sponsored',
                'location' => '3',
                'is_active' => '1',
                'ordering' => '2',
            ],
            'Trends' => [
                'type_id' => '0',
                'm_connection' => 'forum.index',
                'component' => 'cloud',
                'module_id' => 'tag',
                'location' => '3',
                'is_active' => '1',
                'ordering' => '3',
            ],
        ];
    }

    protected function setPhrase()
    {
        $this->phrase = $this->_app_phrases;
    }

    protected function setOthers()
    {
        $this->admincp_route = '/forum/admincp';
        $this->admincp_menu = [
            _p('manage_forums') => '#'
        ];
        if (Phpfox::getUserParam('forum.can_add_new_forum')) {
            $this->admincp_action_menu = [
                '/admincp/forum/add' => _p('add_forum')
            ];
        }
        $this->map = [];
        $this->menu = [
            'phrase_var_name' => 'menu_forum',
            'url' => 'forum',
            'icon' => 'comments'
        ];
        $this->database = [
            'Forum',
            'Forum_Access',
            'Forum_Announcement',
            'Forum_Moderator',
            'Forum_Moderator_Access',
            'Forum_Post',
            'Forum_Post_Text',
            'Forum_Subscribe',
            'Forum_Thank',
            'Forum_Thread'
        ];
        $this->_apps_dir = 'core-forums';
        $this->_admin_cp_menu_ajax = false;
        $this->_publisher = 'phpFox';
        $this->_publisher_url = 'http://store.phpfox.com/';
    }
}