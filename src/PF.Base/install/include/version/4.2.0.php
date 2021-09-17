<?php

return function(Phpfox_Installer $Installer) {
    $Installer->db->query("UPDATE " . Phpfox::getT('setting') . " SET is_hidden = 0 WHERE module_id = 'photo' AND var_name='display_profile_photo_within_gallery';");
    $Installer->db->query("UPDATE " . Phpfox::getT('setting') . " SET group_id = 'server_settings' WHERE module_id = 'core' AND var_name='default_time_zone_offset';");
    $Installer->db->query("DELETE FROM " . Phpfox::getT('setting') . " WHERE module_id = 'feed' AND var_name='group_duplicate_feeds';");

    $columns =  Phpfox::getLib('database.support')->getColumns(Phpfox::getT('photo_album'));
    if (!array_key_exists('cover_id', $columns))
        $Installer->db->query("ALTER TABLE " . Phpfox::getT('photo_album') . " ADD cover_id INT UNSIGNED NOT NULL DEFAULT '0' AFTER profile_id");

    $Installer->db->query("UPDATE " . Phpfox::getT('setting') . " SET group_id = 'ip_infodb' WHERE module_id = 'core' AND var_name='ip_infodb_api_key';");

    $Installer->db->update(':setting',[
        'group_id' => 'seo',
        'is_hidden' => 0
    ], [
       'module_id' => 'profile',
        'var_name' => 'profile_seo_for_meta_title'
    ]);

    $Installer->db->update(':setting',[
        'group_id' => 'seo',
        'is_hidden' => 0
    ], [
       'module_id' => 'blog',
        'var_name' => 'blog_meta_description'
    ]);
    $Installer->db->update(':setting',[
        'group_id' => 'seo',
        'is_hidden' => 0
    ], [
       'module_id' => 'blog',
        'var_name' => 'blog_meta_keywords'
    ]);
    $Installer->db->update(':setting',[
        'group_id' => 'seo',
        'is_hidden' => 0
    ], [
       'module_id' => 'core',
        'var_name' => 'meta_description_limit'
    ]);

    $Installer->db->update(':setting',[
        'group_id' => 'seo',
        'is_hidden' => 0
    ], [
       'module_id' => 'core',
        'var_name' => 'meta_keyword_limit'
    ]);

    $Installer->db->update(':setting',[
        'group_id' => 'seo',
        'is_hidden' => 0
    ], [
       'module_id' => 'core',
        'var_name' => 'meta_description_profile'
    ]);

    $Installer->db->update(':setting',[
        'group_id' => 'seo',
        'is_hidden' => 0
    ], [
       'module_id' => 'friend',
        'var_name' => 'friend_meta_keywords'
    ]);

    $Installer->db->update(':setting',[
        'group_id' => 'seo',
        'is_hidden' => 0
    ], [
       'module_id' => 'photo',
        'var_name' => 'photo_meta_description'
    ]);

    $Installer->db->update(':setting',[
        'group_id' => 'seo',
        'is_hidden' => 0
    ], [
       'module_id' => 'photo',
        'var_name' => 'photo_meta_keywords'
    ]);

    $Installer->db->update(':setting',[
        'group_id' => 'seo',
        'is_hidden' => 0
    ], [
       'module_id' => 'poll',
        'var_name' => 'poll_meta_description'
    ]);

    $Installer->db->update(':setting',[
        'group_id' => 'seo',
        'is_hidden' => 0
    ], [
       'module_id' => 'poll',
        'var_name' => 'poll_meta_keywords'
    ]);

    $Installer->db->update(':setting',[
        'group_id' => 'seo',
        'is_hidden' => 0
    ], [
       'module_id' => 'quiz',
        'var_name' => 'quiz_meta_keywords'
    ]);

    $Installer->db->update(':setting',[
        'group_id' => 'seo',
        'is_hidden' => 0
    ], [
       'module_id' => 'quiz',
        'var_name' => 'quiz_meta_description'
    ]);

    $Installer->db->delete(':language_phrase','module_id="feed" AND var_name="method_deprecated_since_2_dot1_dot0beta1"');

};