<?php
    return function(Phpfox_Installer $Installer) {
        //Update user group setting
        $Installer->db->update(':user_group_setting',[
            'default_admin' => 'true',
            'default_user' => 'true',
            'default_staff' => 'true',
        ],[
            'name' => 'link_to_remove_friend_on_profile',
            'module_id' => 'friend'
        ]);
        $iRemoveFriendSettingId = $Installer->db->select('setting_id')
            ->from(':user_group_setting')
            ->where('name="link_to_remove_friend_on_profile" and module_id="friend"')
            ->execute('getSlaveField');
        $Installer->db->delete(':user_setting', 'setting_id=' . (int) $iRemoveFriendSettingId);
        //Remove menu in admincp
        $Installer->db->update(':module',array('menu' => ''), 'module_id="share"');
        //Add more menu on photo
        $Installer->db->update(':module',array('menu' => 'a:2:{s:33:"photo.admin_menu_add_new_category";a:1:{s:3:"url";a:1:{i:0;s:9:"photo.add";}}s:27:"photo.admin_menu_categories";a:1:{s:3:"url";a:1:{i:0;s:5:"photo";}}}'), 'module_id="photo"');

        //Remove setting
        $Installer->db->delete(':setting','module_id="comment" AND var_name="total_child_comments"');
        $Installer->db->delete(':setting','module_id="feed" AND var_name="comment_feed_cutoff"');
        $Installer->db->delete(':setting','module_id="feed" AND var_name="force_timeline"');
        $Installer->db->delete(':setting','module_id="feed" AND var_name="can_add_past_dates"');
        $Installer->db->delete(':setting','module_id="feed" AND var_name="feed_time_layout"');
        $Installer->db->delete(':setting','module_id="feed" AND var_name="integrate_comments_into_feeds"');
        $Installer->db->delete(':setting','module_id="feed" AND var_name="allow_rating_of_feeds"');
        $Installer->db->delete(':setting','module_id="feed" AND var_name="enable_like_system"');
        $Installer->db->delete(':setting','module_id="feed" AND var_name="display_feeds_from"');
        $Installer->db->delete(':setting','module_id="feed" AND var_name="force_ajax_on_load"');
        $Installer->db->delete(':setting','module_id="photo" AND var_name="protect_photos_from_public"');
        $Installer->db->delete(':setting','module_id="photo" AND var_name="rating_total_photos_cache"');
        $Installer->db->delete(':setting','module_id="photo" AND var_name="photo_battle_image_cache"');
        $Installer->db->delete(':setting','module_id="photo" AND var_name="auto_crop_photo"');
        $Installer->db->delete(':setting','module_id="photo" AND var_name="view_photos_in_theater_mode"');
        $Installer->db->delete(':setting','module_id="photo" AND var_name="enable_photo_battle"');
        $Installer->db->delete(':setting','module_id="photo" AND var_name="enable_mass_uploader"');
        $Installer->db->delete(':setting','module_id="photo" AND var_name="rating_randomize_photos"');
        $Installer->db->delete(':setting','module_id="photo" AND var_name="pre_load_header_view"');
        $Installer->db->delete(':setting','module_id="photo" AND var_name="how_many_categories_to_show_in_title"');
        $Installer->db->delete(':setting','module_id="share" AND var_name="enable_social_bookmarking"');
        $Installer->db->delete(':setting','module_id="share" AND var_name="share_facebook_like"');
        $Installer->db->delete(':setting','module_id="share" AND var_name="share_twitter_link"');
        $Installer->db->delete(':setting','module_id="share" AND var_name="share_google_plus_one"');
        $Installer->db->delete(':setting','module_id="share" AND var_name="twitter_consumer_key"');
        $Installer->db->delete(':setting','module_id="share" AND var_name="twitter_consumer_secret"');
        $Installer->db->delete(':setting','module_id="share" AND var_name="share_on_twitter"');
        $Installer->db->delete(':setting','module_id="blog" AND var_name="show_drafts_count"');
        $Installer->db->delete(':setting','module_id="blog" AND var_name="blog_display_user_post_count"');
        $Installer->db->delete(':setting','module_id="blog" AND var_name="blog_cache_minutes"');
        $Installer->db->delete(':setting','module_id="blog" AND var_name="total_pages_to_cache_blog"');
        $Installer->db->delete(':setting','module_id="blog" AND var_name="digg_integration"');
        $Installer->db->delete(':setting','module_id="poll" AND var_name="poll_max_image_pic_size"');
        $Installer->db->delete(':setting','module_id="poll" AND var_name="polls_to_show"');
        $Installer->db->delete(':setting','module_id="poll" AND var_name="show_x_users_who_took_poll"');
        $Installer->db->delete(':setting','module_id="" AND var_name="allow_dislike"');
        $Installer->db->delete(':setting','module_id="forum" AND var_name="total_forum_post_preview"');
        $Installer->db->delete(':setting','module_id="forum" AND var_name="global_forum_timezone"');
        $Installer->db->delete(':setting','module_id="captcha" AND var_name="recaptcha"');
        $Installer->db->delete(':setting','module_id="captcha" AND var_name="recaptcha_header"');
        $Installer->db->delete(':setting','module_id="contact" AND var_name="is_email_required"');
        $Installer->db->delete(':setting','module_id="facebook" AND var_name="facebook_like_group"');
        $Installer->db->delete(':setting','module_id="facebook" AND var_name="facebook_like_video"');
        $Installer->db->delete(':setting','module_id="profile" AND var_name="can_drag_drop_blocks_on_profile"');
        $Installer->db->delete(':setting','module_id="profile" AND var_name="can_rate_on_users_profile"');
        $Installer->db->delete(':setting','module_id="profile" AND var_name="profile_default_landing_page"');
        $Installer->db->delete(':setting','module_id="profile" AND var_name="allow_user_select_landing"');
        $Installer->db->delete(':setting','module_id="profile" AND var_name="cache_blocks_design"');
        $Installer->db->delete(':setting','module_id="profile" AND var_name="display_submenu_for_photo"');
        $Installer->db->delete(':setting','module_id="profile" AND var_name="ajax_profile_tab"');
        $Installer->db->delete(':setting','module_id="attachment" AND var_name="attachment_enable_mass_uploader"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="gzip_level"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="log_missing_images"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="wysiwyg"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="admin_debug_mode"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="replace_url_with_links"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="default_music_player"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="site_offline_no_template"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="site_stat_update_time"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="display_site_stats"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="enable_getid3_check"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="resize_images"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="global_welcome_time_stamp"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="resize_embed_video"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="footer_watch_time_stamp"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="footer_bar_tool_tip_time_stamp"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="amazon_access_key"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="amazon_secret_key"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="amazon_bucket"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="amazon_bucket_created"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="cdn_cname"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="cdn_amazon_https"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="allow_html_in_activity_feed"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="display_older_ie_error"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="disable_ie_warning"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="cdn_service"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="enable_amazon_expire_urls"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="amazon_s3_expire_url_timeout"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="rackspace_username"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="rackspace_key"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="rackspace_container"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="defer_loading_images"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="enable_html_purifier"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="check_body_for_text"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="check_body_regex"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="check_body_offline_message"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="check_body_header"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="html_purifier_allowed_html"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="html_purifier_allowed_iframes"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="xhtml_valid"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="include_master_files"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="use_md5_for_file_names"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="enable_footer_bar"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="no_more_ie6"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="footer_bar_site_name"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="item_view_area"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="default_style_id"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="phpfox_cdn_js_css_enable"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="phpfox_cdn_js_css_url"');
        $Installer->db->delete(':setting','module_id="music" AND var_name="music_user_group_id"');
        $Installer->db->delete(':setting','module_id="music" AND var_name="music_index_controller"');
        $Installer->db->delete(':setting','module_id="music" AND var_name="music_release_date_time_stamp"');
        $Installer->db->delete(':setting','module_id="friend" AND var_name="friend_user_feed_display_limit"');
        $Installer->db->delete(':setting','module_id="friend" AND var_name="cache_mutual_friends"');
        $Installer->db->delete(':setting','module_id="mail" AND var_name="display_total_mail_count"');
        $Installer->db->delete(':setting','module_id="quiz" AND var_name="quiz_max_image_pic_size"');
        $Installer->db->delete(':setting','module_id="request" AND var_name="display_request_box_on_empty"');
        $Installer->db->delete(':setting','module_id="user" AND var_name="login_module"');
        $Installer->db->delete(':setting','module_id="user" AND var_name="randomize_featured_members"');
        $Installer->db->delete(':setting','module_id="user" AND var_name="remove_users_hidden_age"');
        $Installer->db->delete(':setting','module_id="core" AND var_name="cron"');

        //Update setting
        $Installer->db->update(':setting',[
            'group_id' => 'seo',
            'is_hidden' => 0
        ], [
            'module_id' => 'core',
            'var_name' => 'meta_description_profile'
        ]);

        //Remove User group setting
        $Installer->db->delete(':user_group_setting', 'module_id="newsletter" AND name="can_create_newsletter"');
        $Installer->db->delete(':user_group_setting', 'module_id="feed" AND name="can_view_feed"');
        $Installer->db->delete(':user_group_setting', 'module_id="pages" AND name="can_design_pages"');
        $Installer->db->delete(':user_group_setting', 'module_id="photo" AND name="can_view_private_photos"');
        $Installer->db->delete(':user_group_setting', 'module_id="blog" AND name="search_blogs"');
        $Installer->db->delete(':user_group_setting', 'module_id="blog" AND name="blog_category_limit"');
        $Installer->db->delete(':user_group_setting', 'module_id="blog" AND name="show_drafts_count"');
        $Installer->db->delete(':user_group_setting', 'module_id="comment" AND name="can_vote_on_comments"');
        $Installer->db->delete(':user_group_setting', 'module_id="event" AND name="can_view_gmap"');
        $Installer->db->delete(':user_group_setting', 'module_id="event" AND name="can_add_gmap"');
        $Installer->db->delete(':user_group_setting', 'module_id="profile" AND name="can_custom_design_own_profile"');
        $Installer->db->delete(':user_group_setting', 'module_id="attachment" AND name="can_attach_on_bulletin"');
        $Installer->db->delete(':user_group_setting', 'module_id="core" AND name="can_view_twitter_updates"');
        $Installer->db->delete(':user_group_setting', 'module_id="core" AND name="can_design_dnd"');
        $Installer->db->delete(':user_group_setting', 'module_id="core" AND name="can_remove_friends_from_profile"');
        $Installer->db->delete(':user_group_setting', 'module_id="core" AND name="can_remove_friends_from_dashboard"');
        $Installer->db->delete(':user_group_setting', 'module_id="mail" AND name="allow_delete_every_message"');
        $Installer->db->delete(':user_group_setting', 'module_id="privacy" AND name="can_set_allow_list_on_blogs"');
        $Installer->db->delete(':user_group_setting', 'module_id="tag" AND name="edit_own_tags"');
        $Installer->db->delete(':user_group_setting', 'module_id="tag" AND name="edit_user_tags"');
        $Installer->db->delete(':user_group_setting', 'module_id="theme" AND name="can_view_theme_sample"');

        //Update menu
        $Installer->db->update(':menu', [
            'module_id' => 'pages',
            'var_name' => 'module_pages'
        ], 'm_connection="main" AND url_value="pages"');

        //Remove component
        $Installer->db->delete(':component', 'module_id="blog" AND component="display-options"');
        $Installer->db->delete(':component', 'module_id="blog" AND component="menu"');
        $Installer->db->delete(':component', 'module_id="photo" AND component="filter"');
        $Installer->db->delete(':component', 'module_id="photo" AND component="stat"');
        $Installer->db->delete(':component', 'module_id="photo" AND component="battle"');
        $Installer->db->delete(':component', 'module_id="photo" AND component="rate"');
        $Installer->db->delete(':component', 'module_id="marketplace" AND component="filter"');
        $Installer->db->delete(':component', 'module_id="marketplace" AND component="image"');
        $Installer->db->delete(':component', 'module_id="marketplace" AND component="price"');
        $Installer->db->delete(':component', 'module_id="forum" AND component="forums"');
        $Installer->db->delete(':component', 'module_id="forum" AND component="parent"');
        $Installer->db->delete(':component', 'module_id="forum" AND component="stat"');
        $Installer->db->delete(':component', 'module_id="forum" AND component="timezone"');
        $Installer->db->delete(':component', 'module_id="event" AND component="filter"');
        $Installer->db->delete(':component', 'module_id="event" AND component="image"');
        $Installer->db->delete(':component', 'module_id="event" AND component="map"');
        $Installer->db->delete(':component', 'module_id="event" AND component="parent"');
        $Installer->db->delete(':component', 'module_id="core" AND component="quick-find"');
        $Installer->db->delete(':component', 'module_id="core" AND component="stat"');
        $Installer->db->delete(':component', 'module_id="core" AND component="twitter"');
        $Installer->db->delete(':component', 'module_id="core" AND component="welcome"');
        $Installer->db->delete(':component', 'module_id="music" AND component="album"');
        $Installer->db->delete(':component', 'module_id="music" AND component="album-info"');
        $Installer->db->delete(':component', 'module_id="music" AND component="filter"');
        $Installer->db->delete(':component', 'module_id="music" AND component="info"');
        $Installer->db->delete(':component', 'module_id="music" AND component="latest"');
        $Installer->db->delete(':component', 'module_id="music" AND component="menu"');
        $Installer->db->delete(':component', 'module_id="music" AND component="menu-album"');
        $Installer->db->delete(':component', 'module_id="music" AND component="photo"');
        $Installer->db->delete(':component', 'module_id="music" AND component="photo-album"');
        $Installer->db->delete(':component', 'module_id="music" AND component="profile"');
        $Installer->db->delete(':component', 'module_id="music" AND component="top"');
        $Installer->db->delete(':component', 'module_id="comment" AND component="display"');
        $Installer->db->delete(':component', 'module_id="friend" AND component="birthday-profile"');
        $Installer->db->delete(':component', 'module_id="friend" AND component="menu"');
        $Installer->db->delete(':component', 'module_id="friend" AND component="list.edit"');
        $Installer->db->delete(':component', 'module_id="friend" AND component="top"');
        $Installer->db->delete(':component', 'module_id="log" AND component="users"');
        $Installer->db->delete(':component', 'module_id="profile" AND component="header"');
        $Installer->db->delete(':component', 'module_id="profile" AND component="menu"');
        $Installer->db->delete(':component', 'module_id="profile" AND component="panel"');
        $Installer->db->delete(':component', 'module_id="request" AND component="feed"');

        //Remove block
        $Installer->db->delete(':block', 'module_id="blog" AND component="display-options"');
        $Installer->db->delete(':block', 'module_id="blog" AND component="menu"');
        $Installer->db->delete(':block', 'module_id="photo" AND component="filter"');
        $Installer->db->delete(':block', 'module_id="photo" AND component="stat"');
        $Installer->db->delete(':block', 'module_id="photo" AND component="battle"');
        $Installer->db->delete(':block', 'module_id="photo" AND component="rate"');
        $Installer->db->delete(':block', 'module_id="feed" AND m_connection="core.index-visitor" AND component="display"');
        $Installer->db->delete(':block', 'module_id="marketplace" AND component="filter"');
        $Installer->db->delete(':block', 'module_id="marketplace" AND component="image"');
        $Installer->db->delete(':block', 'module_id="marketplace" AND component="price"');
        $Installer->db->delete(':block', 'module_id="forum" AND component="forums"');
        $Installer->db->delete(':block', 'module_id="forum" AND component="parent"');
        $Installer->db->delete(':block', 'module_id="forum" AND component="stat"');
        $Installer->db->delete(':block', 'module_id="forum" AND component="timezone"');
        $Installer->db->delete(':block', 'module_id="event" AND component="filter"');
        $Installer->db->delete(':block', 'module_id="event" AND component="image"');
        $Installer->db->delete(':block', 'module_id="event" AND component="map"');
        $Installer->db->delete(':block', 'module_id="event" AND component="parent"');
        $Installer->db->delete(':block', 'module_id="core" AND component="quick-find"');
        $Installer->db->delete(':block', 'module_id="core" AND component="stat"');
        $Installer->db->delete(':block', 'module_id="core" AND component="twitter"');
        $Installer->db->delete(':block', 'module_id="core" AND component="welcome"');
        $Installer->db->delete(':block', 'module_id="music" AND component="album"');
        $Installer->db->delete(':block', 'module_id="music" AND component="album-info"');
        $Installer->db->delete(':block', 'module_id="music" AND component="filter"');
        $Installer->db->delete(':block', 'module_id="music" AND component="info"');
        $Installer->db->delete(':block', 'module_id="music" AND component="latest"');
        $Installer->db->delete(':block', 'module_id="music" AND component="menu"');
        $Installer->db->delete(':block', 'module_id="music" AND component="menu-album"');
        $Installer->db->delete(':block', 'module_id="music" AND component="photo"');
        $Installer->db->delete(':block', 'module_id="music" AND component="photo-album"');
        $Installer->db->delete(':block', 'module_id="music" AND component="profile"');
        $Installer->db->delete(':block', 'module_id="music" AND component="top"');
        $Installer->db->delete(':block', 'module_id="comment" AND component="display"');
        $Installer->db->delete(':block', 'module_id="friend" AND component="birthday-profile"');
        $Installer->db->delete(':block', 'module_id="friend" AND component="menu"');
        $Installer->db->delete(':block', 'module_id="friend" AND component="list.edit"');
        $Installer->db->delete(':block', 'module_id="friend" AND component="top"');
        $Installer->db->delete(':block', 'module_id="log" AND component="users"');
        $Installer->db->delete(':block', 'module_id="profile" AND component="header"');
        $Installer->db->delete(':block', 'module_id="profile" AND component="menu"');
        $Installer->db->delete(':block', 'module_id="profile" AND component="panel"');
        $Installer->db->delete(':block', 'module_id="request" AND component="feed"');

        //Add block
        $iCnt = $Installer->db->select('COUNT(*)')
            ->from(':block')
            ->where('m_connection="blog.index" AND module_id="blog" AND component="top"')
            ->count();
        if ($iCnt == 0 || empty($iCnt)){
            $Installer->db->insert(':block', [
                'type_id' => '0',
                'm_connection' => 'blog.index',
                'module_id' => 'blog',
                'component' => 'top',
                'location' => '3',
                'is_active' => '1',
                'ordering' => '1',
                'disallow_access' => '',
                'can_move' => '0',
                'title' => 'Top Bloggers'
            ]);
        }

        $iCnt = $Installer->db->select('COUNT(*)')
            ->from(':block')
            ->where('m_connection="photo.album" AND module_id="photo" AND component="album-tag"')
            ->count();
        if ($iCnt == 0 || empty($iCnt)){
            $Installer->db->insert(':block', [
                'type_id' => '0',
                'm_connection' => 'photo.album',
                'module_id' => 'photo',
                'component' => 'album-tag',
                'location' => '3',
                'is_active' => '1',
                'ordering' => '2',
                'disallow_access' => '',
                'can_move' => '0',
                'title' => 'In This Album'
            ]);
        }

        $iCnt = $Installer->db->select('COUNT(*)')
            ->from(':block')
            ->where('m_connection="music.browse.album" AND module_id="music" AND component="featured-album"')
            ->count();
        if ($iCnt == 0 || empty($iCnt)){
            $Installer->db->insert(':block', [
                'type_id' => '0',
                'm_connection' => 'music.browse.album',
                'module_id' => 'music',
                'component' => 'featured-album',
                'location' => '3',
                'is_active' => '1',
                'ordering' => '4',
                'disallow_access' => '',
                'can_move' => '0',
                'title' => 'Featured Albums'
            ]);
        }

        $iCnt = $Installer->db->select('COUNT(*)')
            ->from(':block')
            ->where('m_connection="music.browse.album" AND module_id="music" AND component="sponsored-album"')
            ->count();
        if ($iCnt == 0 || empty($iCnt)){
            $Installer->db->insert(':block', [
                'type_id' => '0',
                'm_connection' => 'music.browse.album',
                'module_id' => 'music',
                'component' => 'sponsored-album',
                'location' => '3',
                'is_active' => '1',
                'ordering' => '3',
                'disallow_access' => '',
                'can_move' => '0',
                'title' => 'Sponsored Albums'
            ]);
        }

        $iCnt = $Installer->db->select('COUNT(*)')
            ->from(':block')
            ->where('m_connection="marketplace.view" AND module_id="marketplace" AND component="my"')
            ->count();
        if ($iCnt == 0 || empty($iCnt)){
            $Installer->db->insert(':block', [
                'type_id' => '0',
                'm_connection' => 'marketplace.view',
                'module_id' => 'marketplace',
                'component' => 'my',
                'location' => '3',
                'is_active' => '1',
                'ordering' => '1',
                'disallow_access' => '',
                'can_move' => '0'
            ]);
        }

	    $Installer->db->query('ALTER TABLE ' . Phpfox::getT('pages_feed') . ' ADD content TEXT NULL DEFAULT NULL AFTER time_update');
	    $Installer->db->query('ALTER TABLE ' . Phpfox::getT('pages_feed') . ' ADD total_view INT UNSIGNED NOT NULL DEFAULT "0" AFTER content');

        $Installer->db->query('ALTER TABLE ' . Phpfox::getT('feed') . ' DROP INDEX privacy_5');
        $Installer->db->query('ALTER TABLE ' . Phpfox::getT('feed') . ' DROP INDEX time_stamp');
        $Installer->db->query('ALTER TABLE ' . Phpfox::getT('feed') . ' DROP INDEX privacy');

        $Installer->db->query('ALTER TABLE ' . Phpfox::getT('friend') . ' DROP INDEX is_page');
        $Installer->db->query('ALTER TABLE ' . Phpfox::getT('friend') . ' DROP INDEX is_page_2');
        $Installer->db->query('ALTER TABLE ' . Phpfox::getT('friend') . ' DROP INDEX top_friend');
        $Installer->db->query('ALTER TABLE ' . Phpfox::getT('friend') . ' DROP INDEX friend_id');

        $Installer->db->query('ALTER TABLE ' . Phpfox::getT('forum_post') . ' DROP INDEX thread_id_2');
        $Installer->db->query('ALTER TABLE ' . Phpfox::getT('forum_moderator') . ' DROP INDEX forum_id_2');
        $Installer->db->query('ALTER TABLE ' . Phpfox::getT('forum_subscribe') . ' DROP INDEX thread_id_2');
        $Installer->db->query('ALTER TABLE ' . Phpfox::getT('forum_thank') . ' DROP INDEX post_id_2');

        $Installer->db->query('ALTER TABLE ' . Phpfox::getT('event_invite') . ' DROP INDEX rsvp_id');
        $Installer->db->query('ALTER TABLE ' . Phpfox::getT('event_invite') . ' DROP INDEX event_id_3');
        $Installer->db->query('ALTER TABLE ' . Phpfox::getT('event') . ' DROP INDEX view_id_4');
        $Installer->db->query('ALTER TABLE ' . Phpfox::getT('event') . ' DROP INDEX view_id_3');

        $Installer->db->query('ALTER TABLE ' . Phpfox::getT('music_song') . ' DROP INDEX view_id_7');
        $Installer->db->query('ALTER TABLE ' . Phpfox::getT('music_song') . ' DROP INDEX view_id_3');

        $Installer->db->query('ALTER TABLE ' . Phpfox::getT('poll_design') . ' DROP INDEX background');
        $Installer->db->query('ALTER TABLE ' . Phpfox::getT('poll') . ' DROP INDEX item_id_3');

        $Installer->db->query('ALTER TABLE ' . Phpfox::getT('quiz') . ' DROP INDEX view_id');

        $Installer->db->query('ALTER TABLE ' . Phpfox::getT('pages') . ' ADD INDEX `type_id` (`type_id`, `time_stamp`)');

        $Installer->db->query('ALTER TABLE ' . Phpfox::getT('marketplace') . ' DROP INDEX view_id');
        $Installer->db->query('ALTER TABLE ' . Phpfox::getT('marketplace') . ' ADD INDEX `view_id` (`view_id`, `privacy`, `is_sponsor`, `time_stamp`)');

        $Installer->db->query('ALTER TABLE ' . Phpfox::getT('event') . ' ADD INDEX `start_time` (`start_time`,`view_id`)');

        // remove phpfox_log_session indexes
        $Installer->db->query('ALTER TABLE ' . Phpfox::getT('log_session') . ' DROP INDEX specific_forum');
        $Installer->db->query('ALTER TABLE ' . Phpfox::getT('log_session') . ' DROP INDEX general_forum');
        $Installer->db->query('ALTER TABLE ' . Phpfox::getT('log_session') . ' DROP INDEX user_id');
        $Installer->db->query('ALTER TABLE ' . Phpfox::getT('log_session') . ' DROP INDEX user_id_3');
        $Installer->db->query('ALTER TABLE ' . Phpfox::getT('log_session') . ' DROP INDEX log_session');
        $Installer->db->query('ALTER TABLE ' . Phpfox::getT('log_session') . ' DROP INDEX captcha_hash');

        // Groups
        $Installer->db->query("ALTER TABLE " . Phpfox::getT('pages') . " ADD item_type TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'");
        $Installer->db->query("ALTER TABLE " . Phpfox::getT('pages_type') . " ADD item_type TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER is_active");
        $Installer->db->query("ALTER TABLE " . Phpfox::getT('user_space') . " ADD space_groups INT(10) UNSIGNED NOT NULL DEFAULT '0'");
        $Installer->db->query("ALTER TABLE " . Phpfox::getT('user_field') . " ADD total_groups INT(10) UNSIGNED NOT NULL DEFAULT '0'");
        $Installer->db->query("ALTER TABLE " . Phpfox::getT('user_activity') . " ADD activity_groups INT(10) UNSIGNED NOT NULL DEFAULT '0'");

        $groups = array(
            6 => [
                'name' => 'Sports',
                'item_type' => 1
            ],
            7 => [
                'name' => 'Food',
                'item_type' => 1
            ],
            8 => [
                'name' => 'Travel',
                'item_type' => 1
            ],
            9 => [
                'name' => 'Photography',
                'item_type' => 1
            ]
        );

        $iCnt = 0;
        foreach ($groups as $aCategory) {
            $iCnt++;
            $iInsertId = $Installer->db->insert(Phpfox::getT('pages_type'), array(
                    'is_active' => '1',
                    'item_type' => (isset($aCategory['item_type']) ? $aCategory['item_type'] : 0),
                    'name' => $aCategory['name'],
                    'time_stamp' => PHPFOX_TIME,
                    'ordering' => $iCnt
                )
            );
        }

        //process for app phpFox videos
        $json = Core\Lib::appInit('PHPfox_Videos');
        if (isset($json->alias)) {
            //Check Alias is exist
            $iCnt = db()->select('COUNT(*)')
                ->from(':module')
                ->where('module_id = "' . $json->alias . '"' )
                ->execute('getSlaveField');
            if (!$iCnt) {
                $aInsert = [
                    'module_id' => $json->alias,
                    'product_id' => 'phpfox',
                    'is_core' => '0',
                    'is_active' => '1',
                    'is_menu' => '0',
                    'menu' => '',
                    'phrase_var_name' => 'module_apps'
                ];
                db()->insert(':module', $aInsert);
            }
        }
        $Installer->db->delete(':module', 'module_id="emoticon" AND phrase_var_name="module_emoticon"');

        //add order for blog categories
        $Installer->db->query("ALTER TABLE " . Phpfox::getT('blog_category') . " ADD ordering INT(11) UNSIGNED NOT NULL DEFAULT '0'");
    }
?>