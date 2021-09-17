<?php
return function (Phpfox_Installer $Installer) {

    $Installer->db->addField([
        'table' => Phpfox::getT('user_group_setting'),
        'field' => 'option_values',
        'type'  => 'MEDIUMTEXT',
        'null'  => true,
    ]);

    $Installer->db->query('update `' . Phpfox::getT('setting')
        . '` set module_id = product_id where module_id is NULL;');

    $Installer->db->query('ALTER TABLE `phpfox_block` ADD `params` MEDIUMTEXT NULL AFTER `version_id`;');

    $aUpdatePhrases = [
        'initial_fee_recurring_fee_annually' => "{currency_symbol}{initial_fee} for the first year and then {currency_symbol}{recurring_fee} annually.",
        'initial_fee_then_cost_per_month' => "{currency_symbol}{initial_fee} for the first month and then {currency_symbol}{recurring_fee} monthly",
        'no_initial_then_cost_per_month' => "Free for the first month, then {currency_symbol}{recurring_fee} monthly",
        'initial_fee_then_cost_per_quarter' => "{currency_symbol}{initial_fee} for the first quarter and then {currency_symbol}{recurring_fee} quarterly.",
        'no_initial_then_cost_per_quarter' => "Free for the first quarter, then {currency_symbol}{recurring_fee} quarterly",
        'initial_fee_then_cost_biannually' => "{currency_symbol}{initial_fee} for the first biannual and then {currency_symbol}{recurring_fee} biannually",
        'no_initial_then_cost_biannually' => "Free for the first biannual, then {currency_symbol}{recurring_fee} biannually.",
        'initial_fee_then_cost_yearly' => "{currency_symbol}{initial_fee} for the first year and then {currency_symbol}{recurring_fee} yearly.",
        'no_initial_then_cost_yearly' => "Free for the first year, then {currency_symbol}{recurring_fee} yearly.",
        "user_setting_maximum_image_width_keeps_in_server" => "Maximum image width keeps in server (in pixel). If image width user upload higher than this value will reduce to this value.",
        "user_setting_max_upload_size_pages" => "Max file size for photos upload in kilobytes (kb).\r\n(1000 kb = 1 mb)\r\nFor unlimited add \"0\" without quotes.",
        "upload_failed_your_file_size_is_larger_then_our_limit_file_size" => "Upload failed. Your file ({size}) is larger than our limit: {file_size}",
        "user_setting_can_delete_other_photo_albums" => "Can delete all photo albums?",
        "user_setting_can_edit_other_photo_albums" => "Can edit all photo albums?",
        "user_setting_can_delete_other_photos" => "Can delete all photos?",
        "user_setting_can_edit_other_photo" => "Can edit all photos?",
        "user_setting_can_edit_other_event" => "Can edit all events?",
        "user_setting_can_delete_other_event" => "Can delete all events?",
        "user_setting_captcha_on_comment" => "Enable CAPTCHA challenge when a user adds a comment?",
        "setting_use_secure_image_display" => "<title>Secure Image Display<\/title><info>You should turn this setting on if SSL has been set up for your site. By this way, instead of loading all external images directly, your own server will get them then return to the browser to by pass the SSL issues.<\/info>",
        "setting_display_profile_photo_within_gallery" => "<title>Display User Profile Photos within Gallery</title><info>Disable this feature if you do not want to display profile photos within the photo gallery.</info>",
        "setting_logout_after_change_email_if_verify" => "<title>Logout After Changing Email<\/title><info>If users must verify their email address (<setting>verify_email_at_signup</setting>), when they change their email address should they be logged out so they need to verify right away?\r\n\r\nIf you set this to no they will be able to use the site until they sign out, after that they will need to verify their email address.<\/info>",
        "pf_core_cache_driver_description" => "<div class=\"alert alert-warning\">If the driver cannot work based on their server/settings, it always falls back to the flat file system. More information about how to set up cache drivers can be found <a target=\"_blank\" href=\"https://docs.phpfox.com/display/FOX4MAN/Cache+Options\">here</a></div>",
        "default_icon_to_represent_this_language_package_br_advised_size_is_max_16_pixels_width_height" => "Default icon to represent this language package."
    ];

    Phpfox::getService('language.phrase.process')->updatePhrases($aUpdatePhrases);

    // remove user group setting can_view_pirvate_events
    $Installer->db->delete(':user_group_setting', [
        'module_id' => 'event',
        'product_id' => 'phpfox',
        'name' => 'can_view_pirvate_events'
    ]);

    // update user's avatar from Facebook
    $aUsers = $Installer->db
        ->select('user_id, user_image')
        ->from(':user')
        ->where('user_image LIKE \'{"fb":"%\'')
        ->executeRows();

    foreach ($aUsers as $aUser) {
        $aFb = explode(':', $aUser['user_image']);
        if (count($aFb) != 2) {
            continue;
        }
        $iFbId = str_replace(['}', '"'], '', $aFb[1]);
        // add job
        Phpfox_Queue::instance()->addJob('core_get_facebook_images', [
            'iFbId' => $iFbId,
            'iUserId' => $aUser['user_id']
        ], null, 3600);
    }

    // add is_cover_photo col
    $Installer->db->addField([
        'table'   => Phpfox::getT('photo'),
        'field'   => 'is_cover_photo',
        'type'    => 'TINT:1',
        'default' => '0'
    ]);
    // update profile, cover photos flag
    $aProfileAlbums = $Installer->db->select('album_id')->from(':photo_album')->where('profile_id > 0')->executeRows();
    foreach ($aProfileAlbums as $aProfileAlbum) {
        $Installer->db->update(':photo', ['is_cover' => 0], ['album_id' => $aProfileAlbum['album_id']]);
    }
    $aCoverAlbums = $Installer->db->select('album_id')->from(':photo_album')->where('cover_id > 0')->executeRows();
    foreach ($aCoverAlbums as $aCoverAlbum) {
        $Installer->db->update(':photo', ['is_profile_photo' => 0, 'is_cover_photo' => 1], ['album_id' => $aCoverAlbum['album_id']]);
    }

    $Installer->db->update(':setting', [
        'is_hidden' => 1,
        'value_actual' => 0,
        'value_default' => 0,
    ], 'var_name = "force_404_check" AND module_id="core"');

    $Installer->db->delete(':setting', 'var_name = "notification_browse_messages" AND module_id="notification"');

    // remove old action menu "add a new image" on photo.index
    $Installer->db->delete(':menu', [
        'm_connection' => 'photo.index',
        'module_id'    => 'photo',
        'product_id'   => 'phpfox',
        'url_value'    => 'photo.add'
    ]);
    $Installer->db->delete(':menu', [
        'm_connection' => 'photo.albums',
        'module_id'    => 'photo',
        'product_id'   => 'phpfox',
        'url_value'    => 'photo.add'
    ]);

    // remove old action menu "add new poll" on poll.index
    $Installer->db->delete(':menu', [
        'm_connection' => 'poll.index',
        'module_id'    => 'poll',
        'product_id'   => 'phpfox',
        'url_value'    => 'poll.add'
    ]);

    // remove old action menu "add new listing" on marketplace.index
    $Installer->db->delete(':menu', [
        'm_connection' => 'marketplace.index',
        'module_id'    => 'marketplace',
        'product_id'   => 'phpfox',
        'url_value'    => 'marketplace.add'
    ]);

    // remove old action menu "create a list" on friend.index
    $Installer->db->delete(':menu', [
        'm_connection' => 'friend.index',
        'module_id'    => 'core',
        'product_id'   => 'phpfox',
        'url_value'    => '#friend-add-list'
    ]);

    // promotion add new column rule
    $Installer->db->addField([
        'table'   => Phpfox::getT('user_promotion'),
        'field'   => '`rule`',
        'type'    => 'TINT:1',
        'null'    => false,
        'default' => '0',
        'after'   => '`total_day`'
    ]);

    // update menu for comment admincp
    $Installer->db->update(':module', [
        'menu' => 'a:2:{s:35:"admincp.admin_menu_pending_comments";a:1:{s:3:"url";a:1:{i:0;s:7:"comment";}}s:32:"admincp.admin_menu_spam_comments";a:1:{s:3:"url";a:2:{i:0;s:7:"comment";i:1;s:4:"spam";}}}'
    ], ['module_id' => 'comment']);

    // Groups App: update user group setting for activity point
     $Installer->db->update(':user_group_setting', ['name' => 'points_groups'], ['name' => 'pf_group_points_groups']);

     // Support link's check-in
    if (!$Installer->db->isField(':link', 'location_latlng')) {
        $Installer->db->addField([
            'table' => Phpfox::getT('link'),
            'field' => 'location_latlng',
            'type' => 'VCHAR:100',
            'null' => true,
            'default' => 'NULL'
        ]);
    }
    if (!$Installer->db->isField(':link', 'location_name')) {
        $Installer->db->addField([
            'table' => Phpfox::getT('link'),
            'field' => 'location_name',
            'type' => 'VCHAR:255',
            'null' => true,
            'default' => 'NULL'
        ]);
    }

    // Pages App: add image column of category table
    if (!db()->isField(':pages_type', 'image_server_id')) {
        db()->addField([
            'table'   => \Phpfox::getT('pages_type'),
            'field'   => 'image_server_id',
                'type'    => 'INT:4',
            'default' => 0,
            'after'   => 'name'
        ]);
    }
    if (!db()->isField(':pages_type', 'image_path')) {
        db()->addField([
            'table' => \Phpfox::getT('pages_type'),
            'field' => 'image_path',
            'type'  => 'VCHAR:200',
            'null'  => true,
            'after' => 'name'
        ]);
    }

    $Installer->db->changeField(':music_genre', 'user_id', [
        'type' => 'INT:10',
        'null' => false,
        'default' => 0
    ]);
    $Installer->db->changeField(':music_genre', 'added', [
        'type' => 'INT:10',
        'null' => false,
        'default' => 0
    ]);
    $Installer->db->changeField(':music_genre', 'used', [
        'type' => 'INT:10',
        'null' => false,
        'default' => 0
    ]);
    $Installer->db->changeField(':music_genre', 'ordering', [
        'type' => 'INT:10',
        'null' => false,
        'default' => 0
    ]);

    // add new field server id on user_spam table
    if (!$Installer->db->isField(':user_spam', 'server_id')) {
        $Installer->db->addField([
            'table'   => Phpfox::getT('user_spam'),
            'field'   => 'server_id',
            'type'    => 'TINT:3',
            'null'    => false,
            'default' => 0,
            'after'   => 'image_path'
        ]);
    }

    $Installer->db->update(':ad_sponsor', ['module_id' => 'music_song'], ['module_id' => 'music-song']);
    $Installer->db->update(':ad_sponsor', ['module_id' => 'music_album'], ['module_id' => 'music-album']);
    $Installer->db->update(':ad_sponsor', ['module_id' => 'forum_thread'], ['module_id' => 'forum-thread']);

    //Remove un-sue groups
    $Installer->db->update(':setting', [
        'group_id' => ''
    ], 'group_id="ftp"');
    $Installer->db->update(':setting', [
        'group_id' => ''
    ], 'group_id="ftp_account"');
    $Installer->db->update(':setting', [
        'group_id' => ''
    ], 'group_id="ip_infodb"');

    // add new field server id on language table
    if (!$Installer->db->isField(':language', 'server_id')) {
        $Installer->db->addField([
            'table'   => Phpfox::getT('language'),
            'field'   => 'server_id',
            'type'    => 'TINT:3',
            'null'    => false,
            'default' => 0,
            'after'   => 'flag_id'
        ]);
    }
};