<?php

namespace Apps\PHPfox_Videos;

use Core\App;
use Core\App\Install\Setting;
use Phpfox_Url;

class Install extends App\App
{
    private $_app_phrases = [

    ];

    public $store_id = 1819;

    protected function setId()
    {
        $this->id = 'PHPfox_Videos';
    }

    /**
     * Set start and end support version of your App.
     * @example   $this->start_support_version = 4.2.0
     * @example   $this->end_support_version = 4.5.0
     * @see       list of our verson at PF.Base/install/include/installer.class.php ($_aVersions)
     * @important You DO NOT ALLOW to set current version of phpFox for start_support_version and end_support_version. We will reject of app if you use current version of phpFox for these variable. These variables help clients know their sites is work with your app or not.
     */
    protected function setSupportVersion()
    {
        $this->start_support_version = '4.5.2';
    }

    protected function setAlias()
    {
        $this->alias = 'v';
    }

    protected function setName()
    {
        $this->name = _p('Videos');
    }

    protected function setVersion()
    {
        $this->version = '4.6.0';
    }

    protected function setSettings()
    {
        $this->settings = [
            'pf_video_paging_mode' => [
                'var_name' => 'pf_video_paging_mode',
                'info' => 'Pagination Style',
                'description' => 'Select Pagination Style at Search Page.',
                'type' => 'select',
                'value' => 'loadmore',
                'options' => [
                    'loadmore' => 'Scrolling down to Load More items',
                    'next_prev' => 'Use Next and Prev buttons',
                    'pagination' => 'Use Pagination with page number'
                ],
                'ordering' => 1,
            ],
            'pf_video_meta_description' => [
                'var_name' => 'pf_v_meta_description',
                'info' => 'Video Meta Description',
                'description' => 'Meta description added to pages related to the Video app. <a target="_bank" href="' . \Phpfox_Url::instance()->makeUrl('admincp.language.phrase') . '?q=seo_video_meta_description">Click here</a> to edit meta description.<span style="float:right;">(SEO) <input style="width:150px;" readonly value="seo_video_meta_description"></span>',
                'type' => '',
                'value' => "{_p var='seo_video_meta_description'}",
                'group_id' => 'seo',
                'js_variable' => true
            ],
            'pf_video_meta_keywords' => [
                'var_name' => 'pf_v_meta_keywords',
                'info' => 'Video Meta Keywords',
                'description' => 'Meta keywords that will be displayed on sections related to the Video app. <a target="_bank" href="' . \Phpfox_Url::instance()->makeUrl('admincp.language.phrase') . '?q=seo_video_meta_keywords">Click here</a> to edit meta keywords.<span style="float:right;">(SEO) <input style="width:150px;" readonly value="seo_video_meta_keywords"></span>',
                'type' => '',
                'value' => "{_p var='seo_video_meta_keywords'}",
                'group_id' => 'seo',
                'js_variable' => true
            ],
            'pf_video_display_video_created_in_group' => [
                'var_name' => 'pf_display_video_created_in_group',
                'info' => 'Display videos which created in Group to the All Videos page at the Video app',
                'description' => 'Enable to display all public videos to the both Video page in group detail and All Videos page in Video app. Disable to display videos created by an users to the both Video page in group detail and My Videos page of this user in Video app and nobody can see these videos in Video app but owner. <br/><i><b>Notice:</b> This setting will be applied for all types of groups, include secret groups.</i>',
                'type' => Setting\Site::TYPE_RADIO,
                'value' => '0',
                'js_variable' => true
            ],
            'pf_video_display_video_created_in_page' => [
                'var_name' => 'pf_display_video_created_in_page',
                'info' => 'Display videos which created in Page to the All Videos page at the Video app',
                'description' => ' Enable to display all public videos to the both Video page in page detail and All Videos page in Video app. Disable to display videos created by an users to the both Video page in page detail and My Videos page of this user in Video app and nobody can see these videos in Video app but owner.',
                'type' => Setting\Site::TYPE_RADIO,
                'value' => '0',
                'js_variable' => true
            ],
            'pf_video_support_upload_video' => [
                'var_name' => 'pf_support_upload_video',
                'info' => 'Enable Uploading of Videos',
                'description' => 'Enable this option if you would like to give users the ability to upload videos from their computer. <br/><i><b>Notice:</b> This feature requires that FFMPEG and (ZenCoder/Amazon S3) be installed. Once you attempt to enable this feature the script will attempt to verify if the server has all the required scripts installed.</i>',
                'type' => Setting\Site::TYPE_RADIO,
                'value' => '1',
                'js_variable' => true
            ],
            'pf_video_method_upload' => [
                'var_name' => 'pf_video_method_upload',
                'info' => 'Uploading Method',
                'description' => 'Select which method to encode your videos.',
                'type' => Setting\Site::TYPE_SELECT,
                'value' => '1',
                'options' => ['0' => 'FFMPEG', '1' => 'Zencoder + S3'],
                'js_variable' => true
            ],
            'pf_video_key' => [
                'var_name' => 'pf_video_key',
                'info' => 'Zencoder API Key',
            ],
            'pf_video_s3_key' => [
                'var_name' => 'pf_video_s3_key',
                'info' => 'Amazon S3 Access Key',
            ],
            'pf_video_s3_secret' => [
                'var_name' => 'pf_video_s3_secret',
                'info' => 'Amazon S3 Secret',
            ],
            'pf_video_s3_bucket' => [
                'var_name' => 'pf_video_s3_bucket',
                'info' => 'Amazon S3 Bucket',
            ],
            'pf_video_s3_url' => [
                'var_name' => 'pf_video_s3_url',
                'info' => 'Provide the S3, CloudFront or Custom URL',
                'js_variable' => true
            ],
            'pf_video_ffmpeg_path' => [
                'var_name' => 'pf_video_ffmpeg_path',
                'info' => 'Path to FFMPEG',
                'description' => 'Please enter the path then <a href="' . Phpfox_Url::instance()->makeUrl('admincp.v.utilities') . '">click here</a> to check FFMPEG version and supported video formats.',
            ]
        ];
    }

    protected function setUserGroupSettings()
    {
        $this->user_group_settings = [
            'pf_video_share' => [
                'var_name' => 'pf_video_share',
                'info' => 'Can share/upload a video?',
                'type' => Setting\Groups::TYPE_RADIO,
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'options' => Setting\Groups::$OPTION_YES_NO
            ],
            'pf_video_file_size' => [
                'var_name' => 'pf_video_file_size',
                'info' => 'Maximum file size of video uploaded (MB)?',
                'type' => Setting\Groups::TYPE_TEXT,
                'value' => '10'
            ],
            'pf_video_max_file_size_photo_upload' => [
                'var_name' => 'pf_video_max_file_size_photo_upload',
                'info' => 'Maximum file size of photo uploaded (KB)?',
                'type' => Setting\Groups::TYPE_TEXT,
                'value' => '5000'
            ],
            'pf_video_view' => [
                'var_name' => 'pf_video_view',
                'info' => 'Can browse and view videos?',
                'type' => 'input:radio',
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '1',
                    '4' => '1',
                    '5' => '0'
                ],
                'options' => Setting\Groups::$OPTION_YES_NO
            ],
            'pf_video_comment' => [
                'var_name' => 'pf_video_comment',
                'info' => 'Can add a comment on a video?',
                'type' => Setting\Groups::TYPE_RADIO,
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'options' => Setting\Groups::$OPTION_YES_NO
            ],
            'pf_video_edit_own_video' => [
                'var_name' => 'pf_edit_own_video',
                'info' => 'Can edit own videos?',
                'type' => Setting\Groups::TYPE_RADIO,
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'options' => Setting\Groups::$OPTION_YES_NO
            ],
            'pf_video_edit_all_video' => [
                'var_name' => 'pf_edit_all_video',
                'info' => 'Can edit all videos?',
                'type' => Setting\Groups::TYPE_RADIO,
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
                'options' => Setting\Groups::$OPTION_YES_NO
            ],
            'pf_video_delete_own_video' => [
                'var_name' => 'pf_delete_own_video',
                'info' => 'Can delete own videos?',
                'type' => Setting\Groups::TYPE_RADIO,
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'options' => Setting\Groups::$OPTION_YES_NO
            ],
            'pf_video_delete_all_video' => [
                'var_name' => 'pf_delete_all_video',
                'info' => 'Can delete all videos?',
                'type' => Setting\Groups::TYPE_RADIO,
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
                'options' => Setting\Groups::$OPTION_YES_NO
            ],
            'can_sponsor_v' => [
                'var_name' => 'pf_video_can_sponsor',
                'info' => 'Can members of this user group mark a video as Sponsor without paying fee?',
                'type' => Setting\Groups::TYPE_RADIO,
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
                'options' => Setting\Groups::$OPTION_YES_NO
            ],
            'can_purchase_sponsor' => [
                'var_name' => 'pf_video_can_purchase_sponsor',
                'info' => 'Can members of this user group purchase a sponsored ad space for their items?',
                'type' => Setting\Groups::TYPE_RADIO,
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
                'options' => Setting\Groups::$OPTION_YES_NO
            ],
            'v_sponsor_price' => [
                'var_name' => 'pf_sponsor_price',
                'info' => 'How much is the sponsor space worth for videos? This works in a CPM basis.',
                'type' => Setting\Groups::TYPE_CURRENCY
            ],
            'auto_publish_sponsored_item' => [
                'var_name' => 'pf_purchase_sponsored_ad_space_have_to_approve',
                'info' => 'After the user has purchased a sponsored space, should the item be published right away? 
If set to No, the admin will have to approve each new purchased sponsored item space before it is shown in the site.',
                'type' => Setting\Groups::TYPE_RADIO,
                'value' => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
                'options' => Setting\Groups::$OPTION_YES_NO
            ],
            'points_v' => [
                'var_name' => 'pf_video_activity_point',
                'info' => 'How many activity points should a user receive for sharing a video?',
                'type' => 'input:text',
                'value' => '1'
            ],
            'pf_video_feature' => [
                'var_name' => 'pf_video_feature',
                'info' => 'Can feature videos?',
                'type' => Setting\Groups::TYPE_RADIO,
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
                'options' => Setting\Groups::$OPTION_YES_NO
            ],
            'pf_video_approve' => [
                'var_name' => 'pf_video_approve',
                'info' => 'Can approve videos?',
                'type' => Setting\Groups::TYPE_RADIO,
                'value' => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
                'options' => Setting\Groups::$OPTION_YES_NO
            ],
            'pf_video_approve_before_publicly' => [ // need approve?
                'var_name' => 'pf_approve_before_publicly',
                'info' => 'Approve videos before they are publicly displayed?',
                'type' => Setting\Groups::TYPE_RADIO,
                'value' => [
                    '1' => '0',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'options' => Setting\Groups::$OPTION_YES_NO
            ]
        ];
    }

    protected function setComponent()
    {
        $this->component = [
            'block' => [
                'category' => '',
                'featured' => '',
                'sponsored' => '',
                'suggested' => '',
            ],
            'controller' => [
                'index' => 'v.index',
                'play' => 'v.play'
            ]
        ];
    }

    protected function setComponentBlock()
    {
        $this->component_block = [
            'Categories' => [
                'type_id' => '0',
                'm_connection' => 'v.index',
                'component' => 'category',
                'location' => '1',
                'is_active' => '1',
                'ordering' => '1',
            ],
            'Featured Videos' => [
                'type_id' => '0',
                'm_connection' => 'v.index',
                'component' => 'featured',
                'location' => '3',
                'is_active' => '1',
                'ordering' => '1',
            ],
            'Sponsored Videos' => [
                'type_id' => '0',
                'm_connection' => 'v.index',
                'component' => 'sponsored',
                'location' => '3',
                'is_active' => '1',
                'ordering' => '2',
            ],
            'Suggested Videos' => [
                'type_id' => '0',
                'm_connection' => 'v.play',
                'component' => 'suggested',
                'location' => '3',
                'is_active' => '1',
                'ordering' => '1',
            ]
        ];
    }

    protected function setPhrase()
    {
        $this->addPhrases($this->_app_phrases);
    }

    protected function setOthers()
    {
        $this->notifications = [
            'video_ready' => [
                'message' => 'your_video_is_ready',
                'url' => '/video/play/:id',
                'icon' => 'fa-video-camera'
            ]
        ];
        $this->admincp_route = '/v/admincp';
        $this->admincp_menu = [
            'Categories' => '#',
            'FFMPEG Video Utilities' => 'v.utilities',
            'Convert Old Videos' => 'v.convert'
        ];
        $this->admincp_help = 'https://docs.phpfox.com/display/FOX4MAN/Setting+Up+the+Video+App';
        $this->admincp_action_menu = [
            '/admincp/v/add-category' => 'New Category'
        ];
        $this->map = [];
        $this->menu = [
            'phrase_var_name' => 'menu_videos',
            'url' => 'v',
            'icon' => 'video-camera'
        ];
        $this->database = [
            'Video',
            'Video_Category',
            'Video_Category_Data',
            'Video_Text',
            'Video_Embed',
        ];

        $this->_admin_cp_menu_ajax = false;
        $this->_publisher = 'phpFox';
        $this->_publisher_url = 'http://store.phpfox.com/';
        $this->_apps_dir = 'core-videos';
    }
}
