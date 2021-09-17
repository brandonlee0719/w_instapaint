<?php
namespace Apps\Core_Photos;

use Core\App;
use Phpfox_Url;
use Phpfox;

/**
 * Class Install
 * @author  phpFox
 * @version 4.5.3
 * @package Apps\core_photos
 */
class Install extends App\App
{
    private $_app_phrases = [

    ];

    public $store_id = 1887;

    protected function setId()
    {
        $this->id = 'Core_Photos';
    }

    protected function setAlias()
    {
        $this->alias = 'photo';
    }

    protected function setName()
    {
        $this->name = _p('Photos');
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
        $iIndex = 1;
        $this->settings = [
            'photo_paging_mode' => [
                'var_name' => 'photo_paging_mode',
                'info'     => 'Pagination Style',
                'description' => 'Select Pagination Style at Search Page.',
                'type'  => 'select',
                'value' => 'loadmore',
                'options'=> [
                    'loadmore'=>'Scrolling down to Load More items',
                    'next_prev'=>'Use Next and Prev buttons',
                    'pagination'=>'Use Pagination with page number'
                ],
                'ordering' => $iIndex++,
            ],
            'allow_photo_category_selection' => [
                'var_name' => 'allow_photo_category_selection',
                'info'     => 'Allow Selection of Categories',
                'description' => 'Enable this feature to give users the option to select categories directly while uploading photos.',
                'type'  => 'boolean',
                'value' => 1,
                'ordering' => $iIndex++,
            ],
            'photo_upload_process' => [
                'var_name' => 'photo_upload_process',
                'info'     => 'Edit Photos After Upload',
                'description' => 'Enable this option if you want users to edit the batch of photos they had just recently updated.',
                'type'  => 'boolean',
                'value' => 1,
                'ordering' => $iIndex++,
            ],
            'ajax_refresh_on_featured_photos' => [
                'var_name' => 'ajax_refresh_on_featured_photos',
                'info'     => 'AJAX Refresh Featured Photos',
                'description' => 'With this option enabled photos within the "Featured Photo" block will refresh.',
                'type'  => 'boolean',
                'value' => 0,
                'ordering' => $iIndex++,
            ],
            'display_profile_photo_within_gallery' => [
                'var_name' => 'display_profile_photo_within_gallery',
                'info'     => 'Display User Profile Photos within Gallery',
                'description' => 'Disable this feature if you do not want to display user profile photos within the photo gallery.',
                'type'  => 'boolean',
                'value' => 0,
                'ordering' => $iIndex++,
            ],
            'display_cover_photo_within_gallery' => [
                'var_name' => 'display_cover_photo_within_gallery',
                'info'     => 'Display User Cover Photos within Gallery',
                'description' => 'Disable this feature if you do not want to display user cover photos within the photo gallery.',
                'type'  => 'boolean',
                'value' => 0,
                'ordering' => $iIndex++,
            ],
            'display_photo_album_created_in_group' => [
                'var_name' => 'display_photo_album_created_in_group',
                'info'     => 'Display photos/albums which created in Group to the Photo app',
                'description' => 'Enable to display all public photos/albums to the both Photos/Albums page in group detail and in Photo app. Disable to display photos/albums created by an users to the both Photos/Albums page in group detail and My Photos/Albums page of this user in Photo app and nobody can see these photos/albums in Photo app but owner. <br/><b>Notice:</b> This setting will be applied for all types of groups, include secret groups.',
                'type'  => 'boolean',
                'value' => '0',
                'ordering' => $iIndex++,
            ],
            'display_photo_album_created_in_page' => [
                'var_name' => 'display_photo_album_created_in_page',
                'info'     => 'Display photos/albums which created in Page to the Photo app',
                'description' => 'Enable to display all public photos/albums to the both Photos/Albums page in page detail and in Photo app. Disable to display photos/albums created by an users to the both Photos/Albums page in page detail and My Photos/Albums page of this user in Photo app and nobody can see these photos/albums in Photo app but owner.',
                'type'  => 'boolean',
                'value' => '0',
                'ordering' => $iIndex++,
            ],
            'enabled_watermark_on_photos' => [
                'var_name' => 'enabled_watermark_on_photos',
                'info'     => 'Watermark Photos',
                'description' => 'Enable this option to watermark photos. <b>Notice:</b> The setting <a href="'.Phpfox_Url::instance()->makeUrl('admincp.setting.edit').'?module-id=core#watermark_option">Image Watermark</a> must be enabled.',
                'type'  => 'boolean',
                'value' => 0,
                'ordering' => $iIndex++,
            ],
            'display_timeline_photo_within_gallery' => [
                'var_name' => 'display_timeline_photo_within_gallery',
                'info'     => 'Display User Timeline Photos within Gallery',
                'description' => 'Disable this feature if you do not want to display user timeline photos within the photo gallery.',
                'type'  => 'boolean',
                'value' => 0,
                'ordering' => $iIndex++,
            ],
        ];
        if (!Phpfox::getParam('photo.photo_meta_description', null)) {
            $this->settings['photo_meta_description'] = [
                'var_name' => 'photo_meta_description',
                'info' => 'Photo Meta Description',
                'description' => 'Meta description added to pages related to the Photo app. <a target="_bank" href="' . Phpfox_Url::instance()->makeUrl('admincp.language.phrase') . '?q=seo_photo_meta_description">Click here</a> to edit meta description.<span style="float:right;">(SEO) <input style="width:150px;" readonly value="seo_photo_meta_description"></span>',
                'type' => '',
                'value' => '{_p var=\'seo_photo_meta_description\'}',
                'group_id' => 'seo',
                'ordering' => $iIndex++,
            ];
        }
        if (!Phpfox::getParam('photo.photo_meta_keywords', null)) {
            $this->settings['photo_meta_keywords'] = [
                'var_name' => 'photo_meta_keywords',
                'info' => 'Photo Meta Keywords',
                'description' => 'Meta keywords that will be displayed on sections related to the Photo app. <a target="_bank" href="' . Phpfox_Url::instance()->makeUrl('admincp.language.phrase') . '?q=seo_photo_meta_keywords">Click here</a> to edit meta keywords.<span style="float:right;">(SEO) <input style="width:150px;" readonly value="seo_photo_meta_keywords"></span>',
                'type' => '',
                'value' => '{_p var=\'seo_photo_meta_keywords\'}',
                'group_id' => 'seo',
                'ordering' => $iIndex++,
            ];
        }
        unset($iIndex);
    }

    protected function setUserGroupSettings()
    {
        $this->user_group_settings = [
            'can_upload_photos' => [
                'var_name' => 'can_upload_photos',
                'info'     => 'Can upload photos?',
                'description' => '',
                'type'     => 'input:radio',
                'value'    => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
                'ordering' => 1
            ],
            'max_images_per_upload'  => [
                'var_name' => 'max_images_per_upload',
                'info'     => 'Maximum number of images per upload',
                'description' => 'Define the maximum number of images a user can upload each time they use the upload form. Leave to 0 for no images 
Notice: This setting does not control how many images a user can upload in total, just how many they can upload each time they use the upload form to upload new images.',
                'type'     => 'integer',
                'value'    => [
                    '1' => '10',
                    '2' => '10',
                    '3' => '0',
                    '4' => '10',
                    '5' => '10'
                ],
                'ordering' => 2
            ],
            'points_photo' => [
                'var_name' => 'points_photo',
                'info'     => 'Activity points',
                'description' => 'How many activity points should a user receive for uploading a new image.',
                'type'     => 'integer',
                'value'    => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
                'ordering' => 3
            ],
            'can_create_photo_album' => [
                'var_name' => 'can_create_photo_album',
                'info'     => 'Can create a new photo album?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
                'ordering' => 4
            ],
            'can_use_privacy_settings' => [
                'var_name' => 'can_use_privacy_settings',
                'info'     => 'Can use privacy settings when creating an album?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
                'ordering' => 5
            ],
            'max_number_of_albums' => [
                'var_name' => 'max_number_of_albums',
                'info'     => 'Maximum number of albums',
                'description' => 'Define the total number of photo albums a user within this user group can create. 
Notice: Leave this empty will allow them to create an unlimited amount of photo albums. Setting this value to 0 will not allow them the ability to create photo albums.',
                'type'     => 'string',
                'value'    => [
                    '1' => '',
                    '2' => '20',
                    '3' => '0',
                    '4' => '30',
                    '5' => '20'
                ],
                'ordering' => 6
            ],
            'can_view_photos' => [
                'var_name' => 'can_view_photos',
                'info'     => 'Can browse and view the photo module?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '1',
                    '4' => '1',
                    '5' => '1'
                ],
                'ordering' => 7
            ],
            'can_search_for_photos' => [
                'var_name' => 'can_search_for_photos',
                'info'     => 'Can search for photos?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
                'ordering' => 8
            ],
            'can_download_user_photos' => [
                'var_name' => 'can_download_user_photos',
                'info'     => 'Can download other users photos?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
                'ordering' => 9
            ],
            'can_post_on_photos' => [
                'var_name' => 'can_post_on_photos',
                'info'     => 'Can post comments on photos?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
                'ordering' => 10
            ],
            'can_post_on_albums' => [
                'var_name' => 'can_post_on_albums',
                'info'     => 'Can post comments on albums?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
                'ordering' => 11
            ],
            'can_add_mature_images' => [
                'var_name' => 'can_add_mature_images',
                'info'     => 'Can add mature images with warnings?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
                'ordering' => 12
            ],
            'photo_mature_age_limit' => [
                'var_name' => 'photo_mature_age_limit',
                'info'     => 'Photo mature age limit?',
                'description' => 'Note: The age you define will 
- not allow users with younger the ability to view mature photos (strict)
- display warning to users with younger while viewing mature photos (warning)',
                'type'     => 'integer',
                'value'    => [
                    '1' => '18',
                    '2' => '18',
                    '3' => '18',
                    '4' => '18',
                    '5' => '18'
                ],
                'ordering' => 13
            ],
            'total_photos_displays' => [
                'var_name' => 'total_photos_displays',
                'info'     => 'Define how many images a user can view at once when browsing the public photo section?',
                'description' => '',
                'type'     => 'array',
                'value'    => [
                    '1' => [20,40,60],
                    '2' => [20,40,60],
                    '3' => [20,40,60],
                    '4' => [20,40,60],
                    '5' => [20,40,60]
                ],
                'ordering' => 14
            ],
            'can_edit_own_photo' => [
                'var_name' => 'can_edit_own_photo',
                'info'     => 'Can edit own photo?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
                'ordering' => 15
            ],
            'can_edit_other_photo' => [
                'var_name' => 'can_edit_other_photo',
                'info'     => 'Can edit all photos?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 16
            ],
            'can_delete_own_photo' => [
                'var_name' => 'can_delete_own_photo',
                'info'     => 'Can delete own photos?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
                'ordering' => 17
            ],
            'can_delete_other_photos' => [
                'var_name' => 'can_delete_other_photos',
                'info'     => 'Can delete all photos?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 18
            ],
            'can_edit_own_photo_album' => [
                'var_name' => 'can_edit_own_photo_album',
                'info'     => 'Can edit own photo albums?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
                'ordering' => 19
            ],
            'can_edit_other_photo_albums' => [
                'var_name' => 'can_edit_other_photo_albums',
                'info'     => 'Can edit all photo albums?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 20
            ],
            'can_delete_own_photo_album' => [
                'var_name' => 'can_delete_own_photo_album',
                'info'     => 'Can delete own photo albums?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
                'ordering' => 21
            ],
            'can_delete_other_photo_albums' => [
                'var_name' => 'can_delete_other_photo_albums',
                'info'     => 'Can delete all photo albums?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 22
            ],
            'photo_must_be_approved' => [
                'var_name' => 'photo_must_be_approved',
                'info'     => 'Photo must be approved?',
                'description' => 'Set this to True if photos uploaded must be approved before they are visible to the public.',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '0',
                    '2' => '0',
                    '3' => '1',
                    '4' => '0',
                    '5' => '0'
                ],
                'ordering' => 23
            ],
            'can_approve_photos' => [
                'var_name' => 'can_approve_photos',
                'info'     => 'Can approve photos that require moderation?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 24
            ],
            'can_feature_photo' => [
                'var_name' => 'can_feature_photo',
                'info'     => 'Can feature a photo?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 25
            ],
            'can_tag_own_photo' => [
                'var_name' => 'can_tag_own_photo',
                'info'     => 'Can tag own photo?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
                'ordering' => 26
            ],
            'can_tag_other_photos' => [
                'var_name' => 'can_tag_other_photos',
                'info'     => 'Can tag all photos?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
                'ordering' => 27
            ],
            'how_many_tags_on_own_photo' => [
                'var_name' => 'how_many_tags_on_own_photo',
                'info'     => 'How many times can a user tag their own photo?. Set 0 won\'t allow users to tag on their own photos',
                'description' => '',
                'type'     => 'integer',
                'value'    => [
                    '1' => '40',
                    '2' => '40',
                    '3' => '0',
                    '4' => '40',
                    '5' => '40'
                ],
                'ordering' => 28
            ],
            'how_many_tags_on_other_photo' => [
                'var_name' => 'how_many_tags_on_other_photo',
                'info'     => 'How many times can this user tag photos added by other users?. Set 0 won\'t allow users to tag on photos added by other users',
                'description' => '',
                'type'     => 'integer',
                'value'    => [
                    '1' => '4',
                    '2' => '4',
                    '3' => '0',
                    '4' => '4',
                    '5' => '4'
                ],
                'ordering' => 29
            ],
            'photo_max_upload_size' => [
                'var_name' => 'photo_max_upload_size',
                'info'     => 'Max file size for photos upload',
                'description' => 'Max file size for photos upload in kilobits (kb). (1000 kb = 1 mb) 
For unlimited add "0" without quotes.',
                'type'     => 'integer',
                'value'    => [
                    '1' => '5000',
                    '2' => '5000',
                    '3' => '5000',
                    '4' => '5000',
                    '5' => '5000'
                ],
                'ordering' => 30
            ],
            'can_sponsor_photo' => [
                'var_name' => 'can_sponsor_photo',
                'info'     => 'Can members of this user group mark a photo as Sponsor without paying fee?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
                'ordering' => 31
            ],
            'can_purchase_sponsor' => [
                'var_name' => 'can_purchase_sponsor',
                'info'     => 'Can members of this user group purchase a sponsored ad space?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
                'ordering' => 32
            ],
            'photo_sponsor_price' => [
                'var_name' => 'photo_sponsor_price',
                'info'     => 'How much is the sponsor space worth? This works in a CPM basis.',
                'description' => '',
                'type' => 'currency'
            ],
            'auto_publish_sponsored_item' => [
                'var_name' => 'auto_publish_sponsored_item',
                'info'     => 'Auto publish sponsored item?',
                'description' => 'After the user has purchased a sponsored space, should the item be published right away? 
If set to No, the admin will have to approve each new purchased sponsored item space before it is shown in the site.',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
                'ordering' => 33
            ],
            'can_view_photo_albums' => [
                'var_name' => 'can_view_photo_albums',
                'info'     => 'Can view photo albums?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '1',
                    '4' => '1',
                    '5' => '1'
                ],
                'ordering' => 34
            ],
            'flood_control_photos' => [
                'var_name' => 'flood_control_photos',
                'info'     => 'Flood control photos',
                'description' => 'How many minutes should a user wait before they can upload another batch of photos? 
Note: Setting it to "0" (without quotes) is default and users will not have to wait.',
                'type'     => 'integer',
                'value'    => [
                    '1' => '0',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
                'ordering' => 35
            ],
            'refresh_featured_photo' => [
                'var_name' => 'refresh_featured_photo',
                'info'     => 'How many minutes or seconds the script should wait until it refreshes the feature photo?',
                'description' => 'Define how many minutes or seconds the script should wait until it refreshes the feature photo. <br/>
Notice: To add X minutes here are some examples: <br/>
1 min <br/>
2 min <br/>
30 min <br/>
If you would like to define it in seconds here are some examples: <br/>
20 sec <br/>
30 sec <br/>
90 sec',
                'type'     => 'input:text',
                'value'    => [
                    '1' => '1 min',
                    '2' => '1 min',
                    '3' => '1 min',
                    '4' => '1 min',
                    '5' => '1 min'
                ],
                'ordering' => 36
            ],
            'maximum_image_width_keeps_in_server' => [
                'var_name' => 'maximum_image_width_keeps_in_server',
                'info'     => 'Maximum image width keeps in server (in pixel)',
                'description' => 'If image width user upload higher than this value will crop to this value.',
                'type'     => 'integer',
                'value'    => [
                    '1' => '1500',
                    '2' => '1200',
                    '3' => '1200',
                    '4' => '1500',
                    '5' => '1200'
                ],
                'ordering' => 37
            ]
        ];
    }

    protected function setComponent()
    {
        $this->component = [
            'block' => [
                'category' =>  '',
                'featured' =>  '',
                'detail' =>  '',
                'menu' =>  '',
                'stream' => '',
                'menu-album' => '',
                'profile' => '',
                'sponsored' => '',
                'album-tag' => '',
                'my-photo' => '',
            ],
            'controller' => [
                'index' =>  'photo.index',
                'view' =>  'photo.view',
                'profile' =>  'photo.profile',
                'album' =>  'photo.album',
                'add' =>  'photo.add',
                'albums' =>  'photo.albums'
            ]
        ];
    }

    protected function setComponentBlock()
    {
        $this->component_block = [
            'Categories' => [
                'type_id'       => '0',
                'm_connection'  => 'photo.index',
                'component'     => 'category',
                'location'      => '1',
                'is_active'     => '1',
                'ordering'      => '1',
            ],
            'Featured Photos' => [
                'type_id'       => '0',
                'm_connection'  => 'photo.index',
                'component'     => 'featured',
                'location'      => '3',
                'is_active'     => '1',
                'ordering'      => '1',
            ],
            'Sponsored Photos' => [
                'type_id'       => '0',
                'm_connection'  => 'photo.index',
                'component'     => 'sponsored',
                'location'      => '3',
                'is_active'     => '1',
                'ordering'      => '2',
            ],
            'Viewing Photo' => [
                'type_id'       => '0',
                'm_connection'  => 'photo.view',
                'component'     => 'stream',
                'location'      => '7',
                'is_active'     => '1',
                'ordering'      => '1',
            ],
            'In This Album' => [
                'type_id'       => '0',
                'm_connection'  => 'photo.album',
                'component'     => 'album-tag',
                'location'      => '3',
                'is_active'     => '1',
                'ordering'      => '2',
            ],
            'Photos' => [
                'type_id'       => '0',
                'm_connection'  => 'profile.index',
                'component'     => 'my-photo',
                'location'      => '3',
                'is_active'     => '1',
                'ordering'      => '1',
            ]
        ];
    }

    protected function setPhrase()
    {
        $this->phrase = $this->_app_phrases;
    }

    protected function setOthers()
    {
        $this->admincp_route = '/photo/admincp';
        $this->admincp_menu = [
            _p('Categories') => '#',
        ];
        $this->admincp_action_menu = [
            '/admincp/photo/add' => _p('New Category')
        ];
        $this->map = [];
        $this->menu = [
            'phrase_var_name' => 'menu_photos',
            'url'  => 'photo',
            'icon' => 'photo'
        ];
        $this->database = [
            'Photo',
            'Photo_Category',
            'Photo_Category_Data',
            'Photo_Info',
            'Photo_Feed',
            'Photo_Tag',
            'Photo_Album',
            'Photo_Album_Info'
        ];
        $this->_apps_dir = 'core-photos';
        $this->_admin_cp_menu_ajax = false;
        $this->_publisher = 'phpFox';
        $this->_publisher_url = 'http://store.phpfox.com/';
        $this->_writable_dirs = [
            'PF.Base/file/pic/photo/'
        ];
    }
}