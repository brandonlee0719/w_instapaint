<?php
/**
 * @param Phpfox_Installer $Installer
 */
return function(Phpfox_Installer $Installer) {
    $value = Phpfox::getParam('user.disable_username_on_sign_up') ? 'full_name' : 'both';
    $arr = [
        'default' => $value,
        'values' =>
            [
                0 => 'full_name',
                1 => 'username',
                2 => 'both',
            ]
    ];
    $Installer->db->update(':setting',[
        'value_actual' => serialize($arr),
        'value_default' => 'a:2:{s:7:"default";s:9:"full_name";s:6:"values";a:3:{i:0;s:9:"full_name";i:1;s:8:"username";i:2;s:4:"both";}}',
        'type_id' => 'drop'
    ],[
        'var_name' => 'disable_username_on_sign_up',
        'module_id' => 'user'
    ]);

    $Installer->db->update(':block',[
        'is_active' => 1
    ],[
        'm_connection' => 'groups.view',
        'module_id' => 'groups',
        'component' => 'menu'
    ]);
    //update menu of newsletter
    $Installer->db->update(':module',array('menu' => 'a:2:{s:39:"newsletter.admin_menu_create_newsletter";a:1:{s:3:"url";a:2:{i:0;s:10:"newsletter";i:1;s:3:"add";}}s:40:"newsletter.admin_menu_manage_newsletters";a:1:{s:3:"url";a:1:{i:0;s:10:"newsletter";}}}'), 'module_id="newsletter"');
    
    $Installer->db->query("ALTER TABLE " . Phpfox::getT("blog_category") . " ADD is_active TINYINT(1) NOT NULL DEFAULT '1' AFTER used;");

    $Installer->db->query("ALTER TABLE " . Phpfox::getT("blog_category") . " ADD ordering INT(10) NOT NULL AFTER is_active;");
    
    $Installer->db->query("ALTER TABLE " . Phpfox::getT("photo_category") . " ADD is_active TINYINT(1) NOT NULL DEFAULT '1' AFTER used;");
    
    $Installer->db->query("ALTER TABLE " . Phpfox::getT('music_genre') . " ADD `user_id` INT(10) NOT NULL DEFAULT '0' AFTER `name`, ADD `added` INT(10) NOT NULL AFTER `user_id`, ADD `used` INT(10) NOT NULL AFTER `added`, ADD `is_active` INT(1) NOT NULL DEFAULT '1' AFTER `used`, ADD `ordering` INT(10) NOT NULL AFTER `is_active`;");

    $Installer->db->query("ALTER TABLE " . Phpfox::getT("photo_feed") . " ADD feed_table VARCHAR(255) NOT NULL DEFAULT 'feed';");

    $Installer->db->query("ALTER TABLE " . Phpfox::getT("like") . " ADD feed_table VARCHAR(255) NOT NULL DEFAULT 'feed';");
    $Installer->db->query("ALTER TABLE " . Phpfox::getT("comment") . " ADD feed_table VARCHAR(255) NOT NULL DEFAULT 'feed';");
    $oFix = new \Core\Fix();
    $oFix->fixAppsLikeData('PHPfox_Videos');
    $oFix->fixAppsCommentData('PHPfox_Videos');
    
    //Remove setting
    $Installer->db->delete(':setting','module_id="comment" AND var_name="load_delayed_comments_items"');
    $Installer->db->delete(':setting','module_id="language" AND var_name="cache_phrases"');
    $Installer->db->delete(':setting','module_id="friend" AND var_name="allow_blocked_user_to_friend_request"');
    
    //Remove block
    $Installer->db->delete(':block', 'module_id="feed" AND component="time"');

    //Remove block in page
    $Installer->db->delete(':block', 'module_id=\'report\' AND component=\'profile\' AND m_connection=\'profile.index\'');
    $Installer->db->delete(':block', 'module_id=\'friend\' AND component=\'remove\' AND m_connection=\'profile.index\'');
    
    //remove component
    $Installer->db->delete(':component', 'module_id="feed" AND component="time"');
    
    //Remove module facebook
    $Installer->db->delete(':module', 'module_id="facebook" AND phrase_var_name="module_facebook"');
    
    //Update database
    $Installer->db->query("ALTER TABLE ` " . Phpfox::getT('ad') . "` CHANGE `name` `name` VARCHAR(511) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;");

    $Installer->db->update(':feed',['parent_module_id' => 'PHPfox_Videos'], ['parent_module_id' => 'v']);

    $Installer->db->update(':setting', ['is_hidden' => 0], 'module_id="core" AND var_name="allow_html"');

    //phpfox_module add more field

    $Installer->db->query("ALTER TABLE " . Phpfox::getT("module") . " ADD version VARCHAR(32) NULL DEFAULT '4.5.0';");
    $Installer->db->query("ALTER TABLE " . Phpfox::getT("module") . " ADD author VARCHAR(255) NULL DEFAULT 'n/a';");
    $Installer->db->query("ALTER TABLE " . Phpfox::getT("module") . " ADD vendor VARCHAR(255) NULL DEFAULT '';");
    $Installer->db->query("ALTER TABLE " . Phpfox::getT("module") . " ADD description TEXT NULL DEFAULT NULL;");
    $Installer->db->query("ALTER TABLE " . Phpfox::getT("module") . " ADD apps_icon VARCHAR(255) NULL DEFAULT '';");

    //update table feed_share
    $Installer->db->update(':feed_share', ['title' => "{_p var='photo'}", 'description' => "{_p var='say_something_about_this_photo'}"], ['title' => "{phrase var='photo.photo'}", 'module_id' => 'photo']);

    //Update track data - Ad
    $aAds = $Installer->db->select('*')
        ->from(':ad_track')
        ->executeRows();
    foreach ($aAds as $aAd) {
        $Installer->db->insert(':track', [
            'type_id' => 'ad',
            'item_id' => $aAd['ad_id'],
            'user_id' => $aAd['user_id'],
            'ip_address' => $aAd['ip_address'],
            'time_stamp' => $aAd['time_stamp']
        ]);
    }
    //Blog
    $aAds = $Installer->db->select('*')
        ->from(':blog_track')
        ->executeRows();
    foreach ($aAds as $aAd) {
        $Installer->db->insert(':track', [
            'type_id' => 'blog',
            'item_id' => $aAd['item_id'],
            'user_id' => $aAd['user_id'],
            'ip_address' => '',
            'time_stamp' => $aAd['time_stamp']
        ]);
    }

    //update friend module
    $Installer->db->update(':module', [
        'phrase_var_name' => 'module_friend'
    ],[
        'module_id' => "friend"
    ]);
    //update friend track
    $Installer->db->update(':module', [
        'phrase_var_name' => 'module_track'
    ],[
        'module_id' => "track"
    ]);
    //update friend track phrase
    $Installer->db->update(':module', [
        'phrase_var_name' => 'module_captcha_phrase'
    ],[
        'module_id' => "captcha"
    ]);

    //update friend track blog
    $Installer->db->update(':module', [
        'phrase_var_name' => 'module_blog_phrase'
    ],[
        'module_id' => "blog"
    ]);

    $Installer->db->changeField(Phpfox::getT('setting'), 'product_id', ['type' => 'VCHAR:75']);
};