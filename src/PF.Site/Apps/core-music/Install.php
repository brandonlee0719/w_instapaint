<?php
namespace Apps\Core_Music;

use Core\App;


/**
 * Class Install
 * @package Apps\Core_Music
 */
class Install extends App\App
{
    private $_app_phrases = [

    ];
    
    public $store_id = 1838;

    protected function setId()
    {
        $this->id = 'Core_Music';
    }

    protected function setAlias()
    {
        $this->alias = 'music';
    }

    protected function setName()
    {
        $this->name = _p('Music');
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
            'music_paging_mode' => [
                'var_name' => 'music_paging_mode',
                'info'     => 'Pagination Style',
                'description' => 'Select Pagination Style at Search Page.',
                'type'  => 'select',
                'value' => 'loadmore',
                'options'=> [
                    'loadmore'=>'Scrolling down to Load More items',
                    'next_prev'=>'Use Next and Pre buttons',
                    'pagination'=>'Use Pagination with page number'
                ],
                'ordering' => 1
            ],
            'music_display_music_created_in_group' => [
                'var_name' => 'music_display_music_created_in_group',
                'info'     => 'Display music which created in Group to the Music app',
                'description' => 'Enable to display all public music created in Group to the Music app. Disable to hide them.',
                'type'  => 'boolean',
                'value' => 0,
                'ordering' => 2,
            ],
            'music_display_music_created_in_page' => [
                'var_name' => 'music_display_music_created_in_page',
                'info'     => 'Display music which created in Page to the Music app',
                'description' => 'Enable to display all public music created in Page to the Music app. Disable to hide them.',
                'type'  => 'boolean',
                'value' => 0,
                'ordering' => 3,
            ],
            'music_meta_description' => [
                'var_name' => 'music_meta_description',
                'info' => 'Music Meta Description',
                'description' => 'Meta description added to pages related to the Music app. <a target="_bank" href="' . \Phpfox_Url::instance()->makeUrl('admincp.language.phrase') . '?q=seo_music_meta_description">Click here</a> to edit meta description.<span style="float:right;">(SEO) <input style="width:150px;" readonly value="seo_music_meta_description"></span>',
                'type' => '',
                'value' => '{_p var=\'seo_music_meta_description\'}',
                'group_id' => 'seo',
                'ordering' => 4,
            ],
            'music_meta_keywords' => [
                'var_name' => 'music_meta_keywords',
                'info' => 'Music Meta Keywords',
                'description' => 'Meta keywords that will be displayed on sections related to the Music app. <a target="_bank" href="' . \Phpfox_Url::instance()->makeUrl('admincp.language.phrase') . '?q=seo_music_meta_keywords">Click here</a> to edit meta keywords.<span style="float:right;">(SEO) <input style="width:150px;" readonly value="seo_music_meta_keywords"></span>',
                'type' => '',
                'value' => '{_p var=\'seo_music_meta_keywords\'}',
                'group_id' => 'seo',
                'ordering' => 5,
            ]
        ];
    }

    protected function setUserGroupSettings()
    {
        $this->user_group_settings = [
            'can_upload_music_public' => [
                'var_name' => 'can_upload_music_public',
                'info' => 'Can upload music?',
                'description' => 'Notice: This will allow this user group the right to upload songs to the public music section.',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
                'ordering' => 1,
            ],
            'can_add_comment_on_music_album' => [
                'var_name' => 'can_add_comment_on_music_album',
                'info' => 'Can add comments on music albums?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
                'ordering' => 2,
            ],
            'can_add_comment_on_music_song' => [
                'var_name' => 'can_add_comment_on_music_song',
                'info' => 'Can add a comment on a song?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
                'ordering' => 3,
            ],
            'can_add_music_album' => [
                'var_name' => 'can_add_music_album',
                'info' => 'Can add new album?',
                'description' => 'Notice: This will allow this user group the right to add albums to the public music section.',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
                'ordering' => 4,
            ],
            'can_edit_other_music_albums' => [
                'var_name' => 'can_edit_other_music_albums',
                'info' => 'Can edit all albums?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 5,
            ],
            'can_edit_own_albums' => [
                'var_name' => 'can_edit_own_albums',
                'info' => 'Can edit own albums?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
                'ordering' => 6,
            ],
            'can_edit_other_song' => [
                'var_name' => 'can_edit_other_song',
                'info' => 'Can edit all songs?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 7,
            ],
            'can_edit_own_song' => [
                'var_name' => 'can_edit_own_song',
                'info' => 'Can edit own songs?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
                'ordering' => 8,
            ],
            'can_delete_own_track' => [
                'var_name' => 'can_delete_own_track',
                'info' => 'Can delete own songs?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
                'ordering' => 9,
            ],
            'can_delete_other_tracks' => [
                'var_name' => 'can_delete_other_tracks',
                'info' => 'Can delete all songs?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
                'ordering' => 10,
            ],
            'can_delete_own_music_album' => [
                'var_name' => 'can_delete_own_music_album',
                'info' => 'Can delete own albums?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
                'ordering' => 11,
            ],
            'can_delete_other_music_albums' => [
                'var_name' => 'can_delete_other_music_albums',
                'info' => 'Can delete all albums?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 12,
            ],
            'music_max_file_size' => [
                'var_name' => 'music_max_file_size',
                'info'     => 'Maximum file size of songs uploaded',
                'description' => 'Max file size for songs upload in megabyte (MB). (1MB = 1000kb) 
For unlimited add "0" without quotes.',
                'type'     => 'integer',
                'value'    => [
                    '1' => '10',
                    '2' => '10',
                    '3' => '10',
                    '4' => '10',
                    '5' => '10'
                ],
                'ordering' => 13,
            ],
            'can_feature_songs' => [
                'var_name' => 'can_feature_songs',
                'info' => 'Can feature songs?',
                'description' => '',
                'type' => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 14,
            ],
            'can_approve_songs' => [
                'var_name' => 'can_approve_songs',
                'info' => 'Can approve songs?',
                'description' => '',
                'type' => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 15,
            ],
            'music_song_approval' => [
                'var_name' => 'music_song_approval',
                'info' => 'Songs must be approved first?',
                'description' => '',
                'type' => 'boolean',
                'value'    => [
                    '1' => '0',
                    '2' => '0',
                    '3' => '1',
                    '4' => '0',
                    '5' => '1'
                ],
                'ordering' => 16,
            ],
            'can_feature_music_albums' => [
                'var_name' => 'can_feature_music_albums',
                'info' => 'Can feature albums?',
                'description' => '',
                'type' => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 17,
            ],
            'can_access_music' => [
                'var_name' => 'can_access_music',
                'info' => 'Can browse and view the music app?',
                'description' => '',
                'type' => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '1',
                    '4' => '1',
                    '5' => '0'
                ],
                'ordering' => 18,
            ],
            'can_sponsor_song' => [
                'var_name' => 'can_sponsor_song',
                'info'     => 'Can members of this user group mark a song as Sponsor without paying fee?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
                'ordering' => 19,
            ],
            'can_purchase_sponsor_song' => [
                'var_name' => 'can_purchase_sponsor_song',
                'info'     => 'Can members of this user group purchase a sponsored ad space for their songs?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
                'ordering' => 20,
            ],
            'music_song_sponsor_price' => [
                'var_name' => 'music_song_sponsor_price',
                'info'     => 'How much is the sponsor space worth for songs? This works in a CPM basis.',
                'description' => '',
                'type' => 'currency',
                'value'=> ['USD' =>0, ],
                'ordering' => 21,
            ],
            'auto_publish_sponsored_song' => [
                'var_name' => 'auto_publish_sponsored_song',
                'info'     => 'Auto publish sponsored song?',
                'description' => 'After the user has purchased a sponsored space, should the song be published right away? 
If set to No, the admin will have to approve each new purchased sponsored song space before it is shown in the site.',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
                'ordering' => 22,
            ],
            'can_sponsor_album' => [
                'var_name' => 'can_sponsor_album',
                'info'     => 'Can members of this user group mark an album as Sponsor without paying fee?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
                'ordering' => 23,
            ],
            'can_purchase_sponsor_album' => [
                'var_name' => 'can_purchase_sponsor_album',
                'info'     => 'Can members of this user group purchase a sponsored ad space for their albums?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
                'ordering' => 24,
            ],
            'music_album_sponsor_price' => [
                'var_name' => 'music_album_sponsor_price',
                'info'     => 'How much is the sponsor space worth for albums? This works in a CPM basis.',
                'description' => '',
                'type' => 'currency',
                'value'=> ['USD' =>0, ],
                'ordering' => 25,
            ],
            'auto_publish_sponsored_album' => [
                'var_name' => 'auto_publish_sponsored_album',
                'info'     => 'Auto publish sponsored album?',
                'description' => 'After the user has purchased a sponsored space, should the album be published right away? 
If set to No, the admin will have to approve each new purchased sponsored album space before it is shown in the site.',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '0',
                    '3' => '0',
                    '4' => '0',
                    '5' => '0'
                ],
                'ordering' => 26,
            ],
            'points_music_song' => [
                'var_name' => 'points_music_song',
                'info'     => 'Activity points',
                'description' => 'How many activity points should a user receive for uploading a song?',
                'type'     => 'integer',
                'value'    => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
                'ordering' => 27,
            ],
            'max_songs_per_upload'  => [
                'var_name' => 'max_songs_per_upload',
                'info'     => 'Maximum number of songs per upload',
                'description' => 'Define the maximum number of songs a user can upload each time they use the upload form. 
Notice: This setting does not control how many songs a user can upload in total, just how many they can upload each time they use the upload form to upload new songs.',
                'type'     => 'integer',
                'value'    => [
                    '1' => '10',
                    '2' => '10',
                    '3' => '0',
                    '4' => '10',
                    '5' => '10'
                ],
                'ordering' => 28
            ],
            'can_download_songs' => [
                'var_name' => 'can_download_songs',
                'info'     => 'Can download songs?',
                'description' => '',
                'type'     => 'boolean',
                'value'    => [
                    '1' => '1',
                    '2' => '1',
                    '3' => '0',
                    '4' => '1',
                    '5' => '1'
                ],
                'ordering' => 29,
            ],
        ];
    }

    protected function setComponent()
    {
        $this->component = [
            'block' => [
                'song' => '',
                'list' => '',
                'genre' => '',
                'sponsored-song' => '',
                'sponsored-album' => '',
                'featured' => '',
                'featured-album' => '',
                'new-album' => '',
                'photo' => '',
                'track' => '',
                'suggestion' => '',
                'related-album' => ''
                ],
            'controller' => [
                'index' => 'music.index',
                'view' => 'music.view',
                'view-album' => 'music.view-album',
                'browse' => 'music.browse',
                'browse.song' => 'music.browse.song',
                'browse.album' => 'music.browse.album',
                'album' => 'music.album',
                'profile' => 'music.profile'
            ]
        ];
    }

    protected function setComponentBlock()
    {
        $this->component_block = [
            'Sponsored Songs' => [
                'type_id'       => '0',
                'm_connection'  => 'music.index',
                'component'     => 'sponsored-song',
                'location'      => '3',
                'is_active'     => '1',
                'ordering'      => '2',
            ],
            'Genres Index' => [
                'type_id'       => '0',
                'm_connection'  => 'music.index',
                'component'     => 'list',
                'location'      => '1',
                'is_active'     => '1',
                'ordering'      => '2',
            ],
            'Genres Browse Song' => [
                'type_id'       => '0',
                'm_connection'  => 'music.browse.song',
                'component'     => 'list',
                'location'      => '1',
                'is_active'     => '1',
                'ordering'      => '1',
            ],
            'Genres View' => [
                'type_id'       => '0',
                'm_connection'  => 'music.view',
                'component'     => 'list',
                'location'      => '1',
                'is_active'     => '1',
                'ordering'      => '1',
            ],
            'New Albums' => [
                'type_id'       => '0',
                'm_connection'  => 'music.index',
                'component'     => 'new-album',
                'location'      => '3',
                'is_active'     => '1',
                'ordering'      => '1',
            ],
            'Featured Songs' => [
                'type_id'       => '0',
                'm_connection'  => 'music.index',
                'component'     => 'featured',
                'location'      => '3',
                'is_active'     => '1',
                'ordering'      => '3',
            ],
            'Suggestion' => [
                'type_id'       => '0',
                'm_connection'  => 'music.view',
                'component'     => 'suggestion',
                'location'      => '3',
                'is_active'     => '1',
                'ordering'      => '1',
            ],
            'Manage Tracks for Albums' => [
                'type_id'       => '0',
                'm_connection'  => 'music.album',
                'component'     => 'track',
                'location'      => '3',
                'is_active'     => '1',
                'ordering'      => '2',
            ],
            'Sponsored Albums' => [
                'type_id'       => '0',
                'm_connection'  => 'music.browse.album',
                'component'     => 'sponsored-album',
                'location'      => '3',
                'is_active'     => '1',
                'ordering'      => '1',
            ],
            'Featured Albums' => [
                'type_id'       => '0',
                'm_connection'  => 'music.browse.album',
                'component'     => 'featured-album',
                'location'      => '3',
                'is_active'     => '1',
                'ordering'      => '2',
            ],
            'Album Tracklist' => [
                'type_id'       => '0',
                'm_connection'  => 'music.view-album',
                'component'     => 'track',
                'location'      => '3',
                'is_active'     => '1',
                'ordering'      => '1',
            ],
            'Related Albums' => [
                'type_id'       => '0',
                'm_connection'  => 'music.view-album',
                'component'     => 'related-album',
                'location'      => '3',
                'is_active'     => '1',
                'ordering'      => '2',
            ],
            'Latest Songs' => [
                'type_id'       => '0',
                'm_connection'  => 'profile.index',
                'component'     => 'song',
                'location'      => '3',
                'is_active'     => '1',
                'ordering'      => '2',
            ],
        ];
    }

    protected function setPhrase()
    {
        $this->phrase = $this->_app_phrases;
    }

    protected function setOthers()
    {
        $this->admincp_route = '/music/admincp';
        $this->admincp_menu = [
            'Manage Genres' => '#',
        ];
        $this->admincp_action_menu = [
            '/admincp/music/add' => 'Add Genre'
        ];
        $this->map = [];
        $this->menu = [
            'phrase_var_name' => 'menu_music',
            'url'  => 'music',
            'icon' => 'music'
        ];
        $this->database = [
            'Music_Album',
            'Music_Album_Text',
            'Music_Genre',
            'Music_Profile',
            'Music_Song',
            'Music_Genre_Data',
            'Music_Feed'
        ];
        $this->_writable_dirs = [
            'PF.Base/file/music/',
            'PF.Base/file/pic/music/'
        ];
        $this->_apps_dir = 'core-music';
        $this->_admin_cp_menu_ajax = false;
        $this->_publisher = 'phpFox';
        $this->_publisher_url = 'http://store.phpfox.com/';
    }
}