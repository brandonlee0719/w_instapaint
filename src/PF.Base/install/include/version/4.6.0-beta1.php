<?php

return function (Phpfox_Installer $Installer) {
    $Installer->db->delete(':component',
        "component='upload' AND m_connection='photo.upload' AND module_id='photo'");

    $Installer->db->delete(':menu', "url_value='ad.add' AND module_id='ad'");

    $Installer->db->insert(':setting', [
        'group_id'        => 'registration',
        'module_id'       => 'user',
        'product_id'      => 'phpfox',
        'is_hidden'       => '0',
        'version_id'      => '4.6.0',
        'type_id'         => 'boolean',
        'var_name'        => 'signup_repeat_password',
        'phrase_var_name' => 'setting_signup_repeat_password',
        'value_actual'    => '0',
        'value_default'   => '0',
        'ordering'        => '6',
    ]);

    $Installer->db->insert(':setting', [
        'group_id'        => null,
        'module_id'       => 'link',
        'product_id'      => 'phpfox',
        'is_hidden'       => '0',
        'version_id'      => '4.6.0',
        'type_id'         => 'string',
        'var_name'        => 'youtube_data_api_key',
        'phrase_var_name' => 'setting_youtube_data_api_key',
        'value_actual'    => '',
        'value_default'   => '',
        'ordering'        => '96',
    ]);
    $Installer->db->insert(':setting', [
        'group_id'        => null,
        'module_id'       => 'core',
        'product_id'      => 'phpfox',
        'is_hidden'       => '0',
        'version_id'      => '4.6.0',
        'type_id'         => 'boolean',
        'var_name'        => 'auto_detect_language',
        'phrase_var_name' => 'setting_auto_detect_language',
        'value_actual'    => '1',
        'value_default'   => '1',
        'ordering'        => '97',
    ]);

    $Installer->db->insert(':setting', [
        'group_id'        => null,
        'module_id'       => 'comment',
        'product_id'      => 'phpfox',
        'is_hidden'       => '0',
        'version_id'      => '4.6.0',
        'type_id'         => 'boolean',
        'var_name'        => 'newest_comment_on_top',
        'phrase_var_name' => 'setting_newest_comment_on_top',
        'value_actual'    => '0',
        'value_default'   => '0',
        'ordering'        => '98',
    ]);

    $Installer->db->insert(':setting', [
        'group_id'        => null,
        'module_id'       => 'core',
        'product_id'      => 'phpfox',
        'is_hidden'       => '0',
        'version_id'      => '4.6.0',
        'type_id'         => 'integer',
        'var_name'        => 'auto_clear_cache',
        'phrase_var_name' => 'setting_auto_clear_cache',
        'value_actual'    => '0',
        'value_default'   => '0',
        'ordering'        => '98',
    ]);


    $Installer->db->delete(':setting', 'var_name = "clickatell_username" AND module_id="core"');
    $Installer->db->delete(':setting', 'var_name = "clickatell_password" AND module_id="core"');
    $Installer->db->delete(':setting', 'var_name = "clickatell_app_id" AND module_id="core"');

    $Installer->db->update(':user_group_setting',['is_hidden' => 1], 'name = "can_add_tags_on_blogs" AND module_id="tag"');

    //Tag type: 1 HashTag | 0 Tag
    $Installer->db->addField([
        'table' => Phpfox::getT('tag'),
        'field' => 'tag_type',
        'type' => 'TINT:1',
        'default' => '1',
        'after' => 'tag_url',
        'null' => true
    ]);
    //Duplicate tag

    $iMaxId = (int)$Installer->db
        ->select('count(*)')
        ->from(':tag')
        ->execute('getSlaveField');

    if($iMaxId < 50000){ // clone in single transaction
        $sql = strtr('insert ignore into `phpfox_tag` (`item_id`, `category_id`,`user_id`,`tag_type`, `tag_text`, `tag_url`, `added`) 
Select `item_id`, `category_id`,  `user_id`,0 as `tag_type`, `tag_text`, `tag_url`, `added`
from phpfox_tag where tag_type =1;', ['phpfox_tag' => Phpfox::getT('tag')]);

        $Installer->db->query($sql);
    }else{
        \Phpfox_Queue::instance()->addJob('pages_generate_missing_thumbnails', []);
    }

    //Currency format:
    $Installer->db->addField([
        'table' => Phpfox::getT('currency'),
        'field' => 'format',
        'type' => 'VCHAR:100',
        'default' => '\'{0} #,###.00 {1}\'',
        'after' => 'phrase_var',
        'null' => false
    ]);

    // Remove settings
    $Installer->db->delete(':setting', 'module_id="core" AND var_name="force_secure_site"');
    $Installer->db->delete(':setting', 'module_id="core" AND var_name="number_of_items_on_main_menu"');
    $Installer->db->delete(':setting', 'module_id="user" AND var_name="can_be_invisible"');

    // Update settings
    $Installer->db->update(':setting', [
        "is_hidden" => 0
    ], 'module_id="core" AND var_name="session_prefix"');
    $Installer->db->update(':setting', [
        "is_hidden" => 0
    ], 'module_id="user" AND var_name="multi_step_registration_form"');
    $Installer->db->update(':setting', [
        "is_hidden" => 0
    ], 'module_id="core" AND var_name="cookie_path"');
    $Installer->db->update(':setting', [
        "is_hidden" => 0
    ], 'module_id="core" AND var_name="cookie_domain"');
    $Installer->db->update(':setting', [
        "is_hidden" => 1
    ], 'module_id="core" AND var_name="categories_to_show_at_first"');

    $Installer->db->update(':setting', [
        "group_id" => "ssl"
    ], 'module_id="core" AND var_name="force_https_secure_pages"');
    $Installer->db->update(':setting', [
        "group_id" => "ssl"
    ], 'module_id="core" AND var_name="use_secure_image_display"');

    // add column location location_latlng, location_name to :feed_comment
    $Installer->db->addField([
        'table' => Phpfox::getT('feed_comment'),
        'field' => 'location_latlng',
        'type' => 'VCHAR:100',
        'default' => 'NULL'
    ]);
    $Installer->db->addField([
        'table' => Phpfox::getT('feed_comment'),
        'field' => 'location_name',
        'type' => 'VCHAR:255',
        'default' => 'NULL'
    ]);
    //Increase character from module_id
    $Installer->_db()->query("ALTER TABLE `" . Phpfox::getT('user_group_setting') . "` CHANGE `product_id` `product_id` VARCHAR(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'phpfox';");

    // remove block `remove friend` from `profile.index`
    $Installer->db->delete(':block', [
        'product_id' => 'phpfox',
        'm_connection' => 'profile.index',
        'module_id' => 'friend',
        'component' => 'remove'
    ]);


    $Installer->db->addField([
        'table' => Phpfox::getT('language'),
        'field' => 'version',
        'type' => 'VCHAR:12',
        'default' => '"4.0.1"'
    ]);
    $Installer->db->addField([
        'table' => Phpfox::getT('language'),
        'field' => 'store_id',
        'type' => 'UINT:10',
        'default' => '0'
    ]);

    $Installer->db->insert(':component',[
        'component'=>'fe-site-stat',
        'm_connection'=>'',
        'module_id'=>'core',
        'product_id'=>'phpfox',
        'is_controller'=>0,
        'is_block'=>1,
        'is_active'=>1,
    ]);

    $Installer->db->insert(':block',[
        'title'=>'Site Statistics',
        'type_id'=>0,
        'm_connection'=>'core.index-member',
        'module_id'=>'core',
        'product_id'=>'phpfox',
        'component'=>'fe-site-stat',
        'location'=>1,
        'is_active'=>1,
        'ordering'=>3,
    ]);

    $Installer->db->update(':user_group_setting', ['is_admin_setting' => '1'], ['name' => 'can_stay_logged_in']);
    $Installer->db->update(':user_group_setting', ['is_admin_setting' => '1'], ['name' => 'user_is_banned']);
    $Installer->db->update(':user_group_setting', ['is_hidden' => '1'], ['name' => 'force_cropping_tool_for_photos']);

    // notification add column `is_read`
    $Installer->db->addField([
        'table' => Phpfox::getT('notification'),
        'field' => 'is_read',
        'type' => 'TINT:1',
        'default' => '0'
    ]);
    $Installer->db->update(':notification', ['is_read' => 1],'1');
};
