<?php
    return function(Phpfox_Installer $Installer) {
        //Clean settings
        //Module Ad
        $Installer->db->delete(':setting','module_id="ad" AND var_name="ad_cache_limit"');
        $Installer->db->delete(':setting','module_id="ad" AND var_name="ad_ajax_refresh"');
        $Installer->db->delete(':setting','module_id="ad" AND var_name="ad_ajax_refresh_time"');
        $Installer->db->delete(':setting','module_id="ad" AND var_name="how_many_ads_per_location"');
        $Installer->db->delete(':setting','module_id="ad" AND var_name="multi_ad"');

        //Module AdminCP
        $Installer->db->delete(':setting','module_id="admincp" AND var_name="admincp"');
        $Installer->db->delete(':setting','module_id="admincp" AND var_name="cache_time_stamp"');

        //Module blog
        $Installer->db->delete(':setting', 'module_id="blog" AND var_name="length_in_index"');

        //End clean settings

        //update settings
        $Installer->db->update(':setting',['is_hidden' => 0], 'module_id="user" AND var_name="hide_main_menu"');
        $Installer->db->update(':setting',['is_hidden' => 0], 'module_id="tag" AND var_name="enable_hashtag_support"');

        $sTable = Phpfox::getT('user_twofactor_token');
        $Installer->db->query("CREATE TABLE IF NOT EXISTS`{$sTable}` (`email` varchar(100) NOT NULL DEFAULT '', `token_data` text, PRIMARY KEY (`email`)
) ENGINE=InnoDB;");

        Phpfox::getService('admincp.menu.process')->add(
            array(
                'module_id' => "core",
                'product_id' => "phpfox",
                'parent_var_name' => "",
                'm_connection' => "footer",
                'var_name' => "menu_privacy",
                'ordering' => "12",
                'url_value' => "policy",
                'version_id' => "2.0.0alpha1",
                'disallow_access' => "",
                'module' => "core"
            ),
            false,
            true,
            false
        );

        Phpfox::getService('admincp.menu.process')->add(
            array(
                'module_id' => "page",
                'product_id' => "phpfox",
                'parent_var_name' => "",
                'm_connection' => "footer",
                'var_name' => "menu_terms",
                'ordering' => "14",
                'url_value' => "terms",
                'version_id' => "2.0.0alpha1",
                'disallow_access' => "",
                'module' => "page"
            ),
            false,
            true,
            false
        );
        //Remove User group setting

        //Module blog
        $Installer->db->delete(':user_group_setting', 'module_id="blog" AND name="can_password_protect_blogs"');
        $Installer->db->delete(':user_group_setting', 'module_id="blog" AND name="can_view_password_protected_blog"');
        $Installer->db->delete(':user_group_setting', 'module_id="blog" AND name="can_use_editor_on_blog"');
        $Installer->db->delete(':user_group_setting', 'module_id="blog" AND name="can_view_private_blogs"');
        $Installer->db->delete(':user_group_setting', 'module_id="blog" AND name="can_delete_own_blog_category"');
        $Installer->db->delete(':user_group_setting', 'module_id="blog" AND name="blog_add_categories"');
        $Installer->db->delete(':user_group_setting', 'module_id="blog" AND name="can_delete_other_blog_category"');

        //Module share
        $Installer->db->delete(':user_group_setting', 'module_id="share" AND name="can_send_emails"');
        $Installer->db->delete(':user_group_setting', 'module_id="share" AND name="total_emails_per_round"');
        $Installer->db->delete(':user_group_setting', 'module_id="share" AND name="emails_per_hour"');

        //Module photo
        $Installer->db->delete(':user_group_setting', 'module_id="photo" AND name="can_view_hidden_photos"');
        $Installer->db->delete(':user_group_setting', 'module_id="photo" AND name="can_control_comments_on_photos"');
        $Installer->db->delete(':user_group_setting', 'module_id="photo" AND name="can_add_to_rating_module"');
        $Installer->db->delete(':user_group_setting', 'module_id="photo" AND name="max_photo_display_limit"');
        $Installer->db->delete(':user_group_setting', 'module_id="photo" AND name="default_photo_display_limit"');

        //Module forum
        $Installer->db->delete(':user_group_setting', 'module_id="forum" AND name="can_multi_quote_forum"');
        //End remove user group setting

        $Installer->db->update(':language_phrase',[
            'module_id' => 'language'
        ], [
            'module_id' => '_app'
        ]);
        $Installer->db->update(':setting',[
            'is_hidden' => '0'
        ], "module_id= 'photo' AND
            var_name ='ajax_refresh_on_featured_photos'
        ");

        //Convert App pages to normal pages
        $Installer->db->update(':pages', ['app_id' => 0], 'app_id=1');

        //Prevent list file
        if (!file_exists(PHPFOX_DIR . 'file/index.html')) {
            file_put_contents(PHPFOX_DIR . 'file/.htaccess','Options -Indexes');
            touch(PHPFOX_DIR . 'file/index.html');
        }
    }
?>
