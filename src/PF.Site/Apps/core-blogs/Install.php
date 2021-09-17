<?php

namespace Apps\Core_Blogs;

use Core\App;
use Phpfox;

/**
 * Class Install
 * @package Apps\Core_Blogs
 */
class Install extends App\App
{
    private $_app_phrases = [

    ];

    public $store_id = 1886;

    protected function setId()
    {
        $this->id = 'Core_Blogs';
    }

    protected function setSupportVersion()
    {
        $this->start_support_version = '4.6.0';
        $this->end_support_version = '';
    }

    protected function setAlias()
    {
        $this->alias = 'blog';
    }

    protected function setName()
    {
        $this->name = _p('Blogs');
    }

    protected function setVersion()
    {
        $this->version = '4.6.0';
    }

    protected function setSettings()
    {
        $iIndex = 1;
        $this->settings = [
            'blog_paging_mode' => [
                'var_name' => 'blog_paging_mode',
                'info' => 'Pagination Style',
                'description' => 'Select Pagination Style at Search Page.',
                'type' => 'select',
                'value' => 'loadmore',
                'options' => [
                    'loadmore' => 'Scrolling down to Load More items',
                    'next_prev' => 'Use Next and Prev buttons',
                    'pagination' => 'Use Pagination with page number'
                ],
                'ordering' => $iIndex++,
            ],
            'display_blog_created_in_group' => [
                'var_name' => 'display_blog_created_in_group',
                'info' => 'Display blogs which created in Group to Blogs app',
                'description' => 'Enable to display all public blogs created in Group to Blogs app. Disable to hide them.',
                'type' => 'boolean',
                'value' => '0',
                'ordering' => $iIndex++,
            ],
            'display_blog_created_in_page' => [
                'var_name' => 'display_blog_created_in_page',
                'info' => 'Display blogs which created in Page to Blogs app',
                'description' => 'Enable to display all public blogs created in Page to Blogs app. Disable to hide them.',
                'type' => 'boolean',
                'value' => '0',
                'ordering' => $iIndex++,
            ]
        ];
        if (!Phpfox::getParam('blog.blog_meta_description', null)) {
            $this->settings['blog_meta_description'] = [
                'var_name' => 'blog_meta_description',
                'info' => 'Blog Meta Description',
                'description' => 'Meta description added to pages related to the Blog app. <a target="_bank" href="' . Phpfox::getLib('url')->makeUrl('admincp.language.phrase') . '?q=seo_blog_meta_description">Click here</a> to edit meta description.<span style="float:right;">(SEO) <input style="width:150px;" readonly value="seo_blog_meta_description"></span>',
                'type' => '',
                'value' => "{_p var='seo_blog_meta_description'}",
                'group_id' => 'seo',
                'ordering' => $iIndex++,
            ];
        }

        if (!Phpfox::getParam('blog.blog_meta_keywords', null)) {
            $this->settings['blog_meta_keywords'] = [
                'var_name' => 'blog_meta_keywords',
                'info' => 'Blog Meta Keywords',
                'description' => 'Meta keywords that will be displayed on sections related to the Blog app. <a target="_bank" href="' . Phpfox::getLib('url')->makeUrl('admincp.language.phrase') . '?q=seo_blog_meta_keywords">Click here</a> to edit meta keywords.<span style="float:right;">(SEO) <input style="width:150px;" readonly value="seo_blog_meta_keywords"></span>',
                'type' => '',
                'value' => "{_p var='seo_blog_meta_keywords'}",
                'group_id' => 'seo',
                'ordering' => $iIndex++
            ];
        }

        unset($iIndex);
    }

    protected function setUserGroupSettings()
    {
        $this->user_group_settings = [
            'can_post_comment_on_blog' => [
                'var_name' => 'can_post_comment_on_blog',
                'info' => 'Can post comments on blogs?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
            ],
            'can_approve_blogs' => [
                'var_name' => 'can_approve_blogs',
                'info' => 'Can approve blogs?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
            ],
            'approve_blogs' => [
                'var_name' => 'approve_blogs',
                'info' => 'Approve blogs before they are publicly displayed?',
                'description' => '',
                'type' => 'boolean',
                'value' => 0,
            ],
            'can_feature_blog' => [
                'var_name' => 'can_feature_blog',
                'info' => 'Can feature a blog?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
            ],
            'can_sponsor_blog' => [
                'var_name' => 'can_sponsor_blog',
                'info' => 'Can members of this user group mark a blog as Sponsor without paying fee?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
            ],
            'can_purchase_sponsor' => [
                'var_name' => 'can_purchase_sponsor',
                'info' => 'Can members of this user group purchase a sponsored ad space for their items?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
            ],
            'blog_sponsor_price' => [
                'var_name' => 'blog_sponsor_price',
                'info' => 'How much is the sponsor space worth? This works in a CPM basis.',
                'description' => '',
                'type' => 'currency'
            ],
            'auto_publish_sponsored_item' => [
                'var_name' => 'auto_publish_sponsored_item',
                'info' => 'Auto publish sponsored item?',
                'description' => 'After the user has purchased a sponsored space, should the item be published right away? 
If set to No, the admin will have to approve each new purchased sponsored item space before it is shown in the site.',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
            ],
            'flood_control_blog' => [
                'var_name' => 'flood_control_blog',
                'info' => 'How many minutes should a user wait before they can submit another blog?<br><br>Note: Setting it to "0" (without quotes) is default and users will not have to wait.',
                'description' => '',
                'type' => 'integer',
                'value' => 0,
            ],
            'view_blogs' => [
                'var_name' => 'view_blogs',
                'info' => 'Can view blogs.',
                'description' => '',
                'type' => 'boolean',
                'value' => 1,
            ],
            'edit_own_blog' => [
                'var_name' => 'edit_own_blog',
                'info' => 'Can edit their own blogs?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
            ],
            'edit_user_blog' => [
                'var_name' => 'edit_user_blog',
                'info' => 'Can edit all blogs?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
            ],
            'delete_own_blog' => [
                'var_name' => 'delete_own_blog',
                'info' => 'Can delete their own blogs?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
            ],
            'delete_user_blog' => [
                'var_name' => 'delete_user_blog',
                'info' => 'Can delete all blogs?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
            ],
            'add_new_blog' => [
                'var_name' => 'add_new_blog',
                'info' => 'Can add a new blog?',
                'description' => '',
                'type' => 'boolean',
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
            ],
            'total_blogs_displays' => [
                'var_name' => 'total_blogs_displays',
                'info' => 'Define how many blogs a user can view at once when browsing the public blog section?',
                'description' => '',
                'type' => 'array',
                'value' => [
                    '1' => [12, 24, 36],
                    '2' => [12, 24, 36],
                    '3' => [12, 24, 36],
                    '4' => [12, 24, 36],
                    '5' => [12, 24, 36]
                ],
            ],
            'points_blog' => [
                'var_name' => 'points_blog',
                'info' => 'Activity points',
                'description' => 'Specify how many points the user will receive when adding a new blog.',
                'type' => 'integer',
                'value' => 1,
            ],
            'blog_photo_max_upload_size' => [
                'var_name' => 'blog_photo_max_upload_size',
                'info' => 'Photo max upload size',
                'description' => 'Max file size for photo of blog upload in kilobits (kb). <br> (1000 kb = 1 mb) <br> For unlimited add "0" without quotes.',
                'type' => 'integer',
                'value' => 5000,
            ]
        ];
    }

    protected function setComponent()
    {
        $this->component = [
            'block' => [
                'add-category-list' => '',
                'categories' => '',
                'featured' => '',
                'sponsored' => '',
                'top' => '',
                'new' => ''
            ],
            'controller' => [
                'index' => 'blog.index',
                'view' => 'blog.view',
                'add' => 'blog.add',
            ]
        ];
    }

    protected function setComponentBlock()
    {
        $this->component_block = [
            'Categories' => [
                'type_id' => '0',
                'm_connection' => 'blog.index',
                'component' => 'categories',
                'location' => '1',
                'is_active' => '1',
                'ordering' => '1',
            ],
            'Featured Blogs' => [
                'type_id' => '0',
                'm_connection' => 'blog.index',
                'component' => 'featured',
                'location' => '3',
                'is_active' => '1',
                'ordering' => '1',
            ],
            'Sponsored Blogs' => [
                'type_id' => '0',
                'm_connection' => 'blog.index',
                'component' => 'sponsored',
                'location' => '3',
                'is_active' => '1',
                'ordering' => '2',
            ],
            'Top Bloggers' => [
                'type_id' => '0',
                'm_connection' => 'blog.index',
                'component' => 'top',
                'location' => '3',
                'is_active' => '1',
                'ordering' => '3',
            ],
            'Popular Topics' => [
                'type_id' => '0',
                'm_connection' => 'blog.index',
                'component' => 'cloud',
                'module_id' => 'tag',
                'location' => '3',
                'is_active' => '1',
                'ordering' => '4',
            ],
            'Suggestion' => [
                'type_id' => '0',
                'm_connection' => 'blog.view',
                'component' => 'related',
                'location' => '3',
                'is_active' => '1',
                'ordering' => '2',
            ]
        ];
    }

    protected function setPhrase()
    {
        $this->addPhrases($this->_app_phrases);
    }

    protected function setOthers()
    {
        $this->admincp_route = "/blog/admincp";
        $this->admincp_menu = [
            _p('Categories') => "#"
        ];
        $this->admincp_action_menu = [
            '/admincp/blog/add' => _p('New Category')
        ];
        $this->menu = [
            "phrase_var_name" => "menu_blogs",
            "url" => "blog",
            "icon" => "pencil-square"
        ];
        $this->_publisher = 'phpFox';
        $this->_publisher_url = 'http://store.phpfox.com/';
        $this->_apps_dir = "core-blogs";
        $this->_writable_dirs = [
            'PF.Base/file/pic/blog/'
        ];

        $this->database = [
            'Blog',
            'Blog_Category',
            'Blog_Category_Data',
            'Blog_Text'
        ];
        $this->_admin_cp_menu_ajax = false;
    }
}
