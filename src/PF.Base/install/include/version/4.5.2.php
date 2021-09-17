<?php
return function (Phpfox_Installer $Installer) {
    $Installer->db->delete(':user_group_setting', ['module_id' => 'feed', 'name' => 'can_remove_feeds_from_dashboard']);
    $Installer->db->delete(':user_group_setting', ['module_id' => 'feed', 'name' => 'can_remove_feeds_from_profile']);
    $Installer->db->changeField(Phpfox::getT('country'), 'name', ['type' => 'VCHAR:255']);
    $Installer->db->update(':language_phrase', [
        'text' => '<title>Friends Only Community</title><info>By enabling this option certain sections (eg. Blogs, Photos etc...), will by default only show items from the member and his or her friends list.</info>'
    ], 'var_name="setting_friends_only_community" AND language_id="en"');
    $Installer->db->update(':language_phrase', [
        'text' => "<title>Top Bloggers Cache Time</title><info>Define how long we should keep the cache for the <b>Top Bloggers</b> by minutes.\r\n\r\nNote this setting will have not affect if the setting <setting>cache_top_bloggers</setting> is disabled.</info>",
        'text_default' => "<title>Top Bloggers Cache Time</title><info>Define how long we should keep the cache for the <b>Top Bloggers</b> by minutes.\r\n\r\nNote this setting will have not affect if the setting <setting>cache_top_bloggers</setting> is disabled.</info>"
    ], 'var_name="setting_cache_top_bloggers_limit" AND language_id="en"');
    $Installer->db->update(':custom_field', ['is_search' => 1], 'phrase_var_name = "user.custom_about_me"');
    $Installer->db->addField([
        'table' => Phpfox::getT('egift'),
        'field' => 'server_id',
        'type' => 'TINT:3',
        'default' => '0',
    ]);
    $Installer->db->update(':user_group_custom', ['default_value' => 0],
        'user_group_id = 3 AND module_id = "app_PHPfox_Videos" AND name = "pf_video_share"');
    $Installer->db->update(':user_group_custom', ['default_value' => 0],
        'user_group_id = 5 AND module_id = "app_PHPfox_Videos" AND name = "pf_video_share"');
    $Installer->db->update(':language_phrase', [
        'text' => "Your video is being uploaded and will be converted to the appropriate format to complete the process. Please don't close this window. Take a moment to add a caption or share you think for your video.",
        'text_default' => "Your video is being uploaded and will be converted to the appropriate format to complete the process. Please don't close this window. Take a moment to add a caption or share you think for your video."
    ], 'var_name="pf_video_uploading_message" AND language_id="en"');
    $Installer->db->update(':language_phrase', [
        'text' => "<title>Number items of main menu</title><info>Number items of main menu. Minimum is 5, it will be 5 if you put smaller</info>",
        'text_default' => "<title>Number items of main menu</title><info>Number items of main menu. Minimum is 5, it will be 5 if you put smaller</info>"
    ], 'var_name="setting_number_of_items_on_main_menu" AND language_id="en"');
    //Fix upgrade from V3. Setting allow_cdn must be true
    $Installer->db->update(':setting', [
        'value_actual' => 1,
        'value_default' => 1,
    ],'var_name="allow_cdn" AND module_id="core"');
    $Installer->db->changeField(':user_delete_feedback', 'reasons_given', ['type' => 'TEXT']);
    $Installer->db->update(':setting', [
        "is_hidden" => 1
    ], 'module_id="core" AND var_name="session_prefix"');
    $Installer->db->update(':setting', [
        "is_hidden" => 1
    ], 'module_id="core" AND var_name="cookie_path"');
    $Installer->db->update(':setting', [
        "is_hidden" => 1
    ], 'module_id="core" AND var_name="cookie_domain"');
    $Installer->db->update(':language_phrase', [
        'text' => "How many questions is the least a Quiz (created by members of this user group) can have. Minimum value is 1.",
        'text_default' => "How many questions is the least a Quiz (created by members of this user group) can have. Minimum value is 1."
    ], 'var_name="user_setting_min_questions" AND language_id="en"');
    $Installer->db->update(':language_phrase', [
        'text' => "How many answers (minimum) can a question in a quiz have? Minimum value is 2.",
        'text_default' => "How many answers (minimum) can a question in a quiz have? Minimum value is 2."
    ], 'var_name="user_setting_min_answers" AND language_id="en"');
    $Installer->db->update(':language_phrase', [
        'text' => "Can members of this user group approve polls?",
        'text_default' => "Can members of this user group approve polls?"
    ], 'var_name="user_setting_poll_can_moderate_polls" AND language_id="en"');
    $Installer->db->update(':cache', [
        'cache_data' => '"app_' . md5('Music') . '"'
    ], 'file_name="pf_video_category" AND cache_data="\"Music\""');
    $Installer->db->update(':cache', [
        'cache_data' => '"app_' . md5('Comedy') . '"'
    ], 'file_name="pf_video_category" AND cache_data="\"Comedy\""');
    $Installer->db->update(':cache', [
        'cache_data' => '"app_' . md5('Film & Entertainment') . '"'
    ], 'file_name="pf_video_category" AND cache_data="\"Film & Entertainment\""');
    $Installer->db->update(':cache', [
        'cache_data' => '"app_' . md5('Gaming') . '"'
    ], 'file_name="pf_video_category" AND cache_data="\"Gaming\""');
    $Installer->db->update(':language_phrase', [
        'text' => "<title>Allow HTML</title><info>Set this to <b>True</b> if you would like to allow HTML on your site. Note that even with this setting enabled by default we only allow certain HTML tags we feel that will not harm your site.<\/info>",
        'text_default' => "<title>Allow HTML</title><info>Set this to <b>True<\/b> if you would like to allow HTML on your site. Note that even with this setting enabled by default we only allow certain HTML tags we feel that will not harm your site.<\/info>"
    ], 'var_name="setting_allow_html" AND language_id="en"');
    $Installer->db->update(':language_phrase', [
        'text' => "<title>Language Package Helper</title><info>If enabled this option will add brackets surrounding a phrase, which can be used to identify which phrases have not been added into the core language package. Hard coded phrases will not have brackets.\r\n\r\nIf a phrase is hard coded in the source the site will be unable to translate that specific phrase.\r\n\r\nIt is best to use this feature during development or creating of a new language package.\r\n\r\nExample of how a phrase will look once this setting is enabled:\r\n[quote]\r\n{This is a sample}\r\n[/quote]</info>",
        'text_default' => "<title>Language Package Helper</title><info>If enabled this option will add brackets surrounding a phrase, which can be used to identify which phrases have not been added into the core language package. Hard coded phrases will not have brackets.\r\n\r\nIf a phrase is hard coded in the source the site will be unable to translate that specific phrase.\r\n\r\nIt is best to use this feature during development or creating of a new language package.\r\n\r\nExample of how a phrase will look once this setting is enabled:\r\n[quote]\r\n{This is a sample}\r\n[/quote]</info>"
    ], 'var_name="setting_lang_pack_helper" AND language_id="en"');
    $Installer->db->update(':language_phrase', [
        'text' => "Maximum file size of songs uploaded. (MB)",
        'text_default' => "Maximum file size of songs uploaded. (MB)"
    ], 'var_name="user_setting_music_max_file_size" AND language_id="en"');
    $Installer->db->update(':language_phrase', [
        'text' => "{total_play} play(s)",
        'text_default' => "{total_play} play(s)"
    ], 'var_name="total_play_plays" AND language_id="en"');
    $Installer->db->update(':language_phrase', [
        'text' => "{total_play} play(s)",
        'text_default' => "{total_play} play(s)"
    ], 'var_name="total_play_plays" AND language_id="en"');
    $Installer->db->update(':language_phrase', [
        'text' => "{total_play} play(s)",
        'text_default' => "{total_play} play(s)"
    ], 'var_name="total_play_plays" AND language_id="en"');
    $Installer->db->update(':language_phrase', [
        'text' => "{total_track} track(s)",
        'text_default' => "{total_track} track(s)"
    ], 'var_name="total_track_tracks" AND language_id="en"');
    $Installer->db->update(':language_phrase', [
        'text' => "{total} play(s)",
        'text_default' => "{total} play(s)"
    ], 'var_name="total_plays" AND language_id="en"');
    $Installer->db->update(':language_phrase', [
        'text' => "{total} track(s)",
        'text_default' => "{total} track(s)"
    ], 'var_name="total_tracks" AND language_id="en"');
    $Installer->db->update(':language_phrase', [
        'text' => "Page URL is invalid.",
        'text_default' => "Page URL is invalid."
    ], 'var_name="invalid_title" AND language_id="en"');
    $Installer->db->update(':language_phrase', [
        'text' => "<title>Enable Check-In</title><info>If enabled users will be able to choose their location when posting a status update.\r\n\r\nThis setting also allows pages to define their location. Pages with a location defined will show up in the list of establishments when the user is posting a status update.\r\n\r\nFor this to work you need to have entered the Google API Key (<setting>google_api_key</setting></info>)",
        'text_default' => "<title>Enable Check-In</title><info>If enabled users will be able to choose their location when posting a status update.\r\n\r\nThis setting also allows pages to define their location. Pages with a location defined will show up in the list of establishments when the user is posting a status update.\r\n\r\nFor this to work you need to have entered the Google API Key (<setting>google_api_key</setting></info>)"
    ], 'var_name="setting_enable_check_in" AND language_id="en"');

    // Update block title
    $Installer->db->update(':block', ['title' => 'AdminCP Notes'], 'module_id="core" AND component="note"');
    $Installer->db->update(':block', ['title' => 'Active Admins'], 'module_id="core" AND component="active-admin"');
    $Installer->db->update(':block', ['title' => 'Corporate News & Updates'], 'module_id="core" AND component="news"');
    $Installer->db->update(':block', ['title' => 'Site Statistics'], 'module_id="core" AND component="site-stat"');
    $Installer->db->update(':block', ['title' => 'Latest Admin Logins'], 'module_id="core" AND component="latest-admin-login"');
    $Installer->db->update(':block', ['title' => 'Announcement'], 'module_id="announcement" AND component="index"');
    $Installer->db->update(':block', ['title' => 'RSPV'], 'module_id="event" AND component="rsvp"');
    $Installer->db->update(':block', ['title' => 'Category'], 'module_id="event" AND component="category"');
    $Installer->db->update(':block', ['title' => 'Sponsored'], 'module_id="event" AND component="sponsored"');
    $Installer->db->update(':block', ['title' => 'Activity Feed'], 'module_id="feed" AND component="display" AND m_connection="profile.index"');
    $Installer->db->update(':block', ['title' => 'More From Seller'], 'm_connection="marketplace.view" AND module_id="marketplace" AND component="my"');
    $Installer->db->update(':block', ['title' => 'Category'], 'm_connection="marketplace.index" AND module_id="marketplace" AND component="category"');
    $Installer->db->update(':block', ['title' => 'Sponsored'], 'm_connection="marketplace.index" AND module_id="marketplace" AND component="sponsored"');
    $Installer->db->update(':block', ['title' => 'Genres'], 'm_connection="music.index" AND module_id="music" AND component="list"');
    $Installer->db->update(':block', ['title' => 'Genres'], 'm_connection="music.browse.song" AND module_id="music" AND component="list"');
    $Installer->db->update(':block', ['title' => 'Widgets'], 'm_connection="pages.view" AND module_id="pages" AND component="widget"');
    $Installer->db->update(':block', ['title' => 'Recently Taken By'], 'm_connection="quiz.view" AND module_id="quiz" AND component="stat"');
    $Installer->db->update(':block', ['title' => 'Report User'], 'm_connection="profile.index" AND module_id="report" AND component="profile"');
    $Installer->db->update(':block', ['title' => 'Find Friends'], 'm_connection="user.browse" AND module_id="user" AND component="filter"');
    $Installer->db->update(':block', ['title' => 'Featured Members'], 'm_connection="user.browse" AND module_id="user" AND component="featured"');

    $Installer->db->delete(':component', 'module_id="photo" AND component="parent"');
    $Installer->db->delete(':block', 'm_connection="group.view" AND module_id="photo" AND component="parent"');
    $Installer->db->delete(':component', "component='upload' AND m_connection='photo.upload' AND module_id='photo'");
    $Installer->db->delete(':block', 'component="upload" AND m_connection="photo.upload" AND module_id="photo"');

    // support share link with location
    if (!$Installer->db->isField(':link', 'location_latlng')) {
        $Installer->db->query('ALTER TABLE  `' . Phpfox::getT('link') . '` ADD  `location_latlng` VARCHAR( 100 ) NULL DEFAULT NULL');
    }
    if (!$Installer->db->isField(':link', 'location_name')) {
        $Installer->db->query('ALTER TABLE  `' . Phpfox::getT('link') . '` ADD  `location_name` VARCHAR( 255 ) NULL DEFAULT NULL');
    }

    $Installer->db->query("ALTER TABLE `" . Phpfox::getT('pages') . "` CHANGE `cover_photo_position` `cover_photo_position` VARCHAR(5) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;");

    $Installer->db->delete(':user_group_setting', 'module_id="mail" AND name="mail_box_limit"');
    $Installer->db->delete(':user_group_setting', 'module_id="mail" AND name="override_mail_box_limit"');
    $Installer->db->delete(':user_group_setting', 'module_id="mail" AND name="override_restrict_message_to_friends"');

    $Installer->db->insert(':setting', [
        'group_id' => null,
        'module_id' => 'core',
        'product_id' => 'phpfox',
        'is_hidden' => '0',
        'version_id' => '4.6.0',
        'type_id' => 'boolean',
        'var_name' => 'use_secure_image_display',
        'phrase_var_name' => 'setting_use_secure_image_display',
        'value_actual' => '0',
        'value_default' => '0',
        'ordering' => '0'
    ]);

    $Installer->db->update(':language_phrase', [
        'text' => "<title>Minimum Length for Username</title><info>Minimum Length for Username. Leave empty will be 0</info>",
        'text_default' => "<title>Minimum Length for Username</title><info>Minimum Length for Username. Leave empty will be 0</info>"
    ], 'var_name="setting_min_length_for_username" AND text=text_default');

    $Installer->db->update(':language_phrase', [
        'text' => "<title>Maximum Length for Username</title><info>Maximum Length for Username. Leave empty will be 0</info>",
        'text_default' => "<title>Maximum Length for Username</title><info>Maximum Length for Username. Leave empty will be 0</info>"
    ], 'var_name="setting_max_length_for_username" AND text=text_default');

    // v4 does not support music.tracklist anymore
    $Installer->db->delete(':component', 'module_id=\'music\' AND component=\'tracklist\'');
    $Installer->db->delete(':block', 'module_id=\'music\' AND component=\'tracklist\'');
};