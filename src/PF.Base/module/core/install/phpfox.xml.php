<module>
	<data>
		<module_id>core</module_id>
		<product_id>phpfox</product_id>
		<is_core>1</is_core>
		<is_active>1</is_active>
		<is_menu>0</is_menu>
		<menu />
		<phrase_var_name />
		<writable><![CDATA[a:8:{i:0;s:11:"file/cache/";i:1;s:10:"file/gzip/";i:2;s:9:"file/log/";i:3;s:12:"file/static/";i:4;s:13:"file/session/";i:5;s:19:"file/pic/watermark/";i:6;s:14:"file/pic/icon/";i:7;s:14:"file/settings/";}]]></writable>
	</data>
	<menus>
		<menu module_id="core" parent_var_name="" m_connection="main" var_name="menu_home" ordering="1" url_value="" version_id="2.0.0alpha1" disallow_access="" module="core" mobile_icon="dashboard" />
		<menu module_id="core" parent_var_name="" m_connection="main_right" var_name="menu_admincp" ordering="1" url_value="admincp" version_id="2.0.0alpha1" disallow_access="" module="core" />
		<menu module_id="core" parent_var_name="" m_connection="" var_name="menu_log_out" ordering="10" url_value="user.logout" version_id="2.0.0alpha1" disallow_access="a:1:{i:0;s:1:&quot;3&quot;;}" module="core" />
		<menu module_id="core" parent_var_name="" m_connection="footer" var_name="menu_privacy" ordering="12" url_value="policy" version_id="2.0.0alpha1" disallow_access="" module="core" />
	</menus>
	<setting_groups>
		<name module_id="core" version_id="2.0.0alpha1" var_name="setting_group_cookie">cookie</name>
		<name module_id="core" version_id="2.0.0alpha1" var_name="setting_group_general">general</name>
		<name module_id="core" version_id="2.0.0alpha1" var_name="setting_group_server_settings">server_settings</name>
		<name module_id="core" version_id="2.0.0alpha1" var_name="setting_group_mail">mail</name>
		<name module_id="core" version_id="2.0.0beta4" var_name="setting_group_spam">spam</name>
		<name module_id="core" version_id="2.0.0beta4" var_name="setting_group_site_offlineonline">site_offline_online</name>
		<name module_id="core" version_id="2.0.0rc1" var_name="setting_group_registration">registration</name>
		<name module_id="core" version_id="3.6.0rc1" var_name="setting_group_security">security</name>
		<name module_id="core" version_id="4.4.0" var_name="setting_group_time_stamps">time_stamps</name>
		<name module_id="core" version_id="4.5.0" var_name="setting_group_seo">seo</name>
		<name module_id="core" version_id="4.6.0" var_name="setting_group_label_ssl">ssl</name>
	</setting_groups>
	<settings>
		<setting group="server_settings" module_id="core" is_hidden="0" type="boolean" var_name="use_gzip" phrase_var_name="setting_use_gzip" ordering="3" version_id="2.0.0alpha1">1</setting>
		<setting group="time_stamps" module_id="core" is_hidden="0" type="string" var_name="global_update_time" phrase_var_name="setting_global_update_time" ordering="1" version_id="2.0.0alpha1">F j, Y</setting>
		<setting group="general" module_id="core" is_hidden="0" type="string" var_name="title_delim" phrase_var_name="setting_title_delim" ordering="4" version_id="2.0.0alpha1"><![CDATA[&raquo;]]></setting>
		<setting group="general" module_id="core" is_hidden="0" type="string" var_name="site_title" phrase_var_name="setting_site_title" ordering="1" version_id="2.0.0alpha1">SiteName</setting>
		<setting group="server_settings" module_id="core" is_hidden="0" type="integer" var_name="ip_check" phrase_var_name="setting_ip_check" ordering="5" version_id="2.0.0alpha1">1</setting>
		<setting group="time_stamps" module_id="core" is_hidden="0" type="string" var_name="profile_time_stamps" phrase_var_name="setting_profile_time_stamps" ordering="8" version_id="2.0.0alpha1">F j, Y</setting>
		<setting group="cookie" module_id="core" is_hidden="0" type="string" var_name="session_prefix" phrase_var_name="setting_session_prefix" ordering="0" version_id="2.0.0alpha1">core</setting>
		<setting group="general" module_id="core" is_hidden="0" type="large_string" var_name="keywords" phrase_var_name="setting_keywords" ordering="5" version_id="2.0.0alpha1">social networking</setting>
		<setting group="general" module_id="core" is_hidden="0" type="large_string" var_name="description" phrase_var_name="setting_description" ordering="6" version_id="2.0.0alpha1">Some information about your site...</setting>
		<setting group="cookie" module_id="core" is_hidden="0" type="string" var_name="cookie_path" phrase_var_name="setting_cookie_path" ordering="0" version_id="2.0.0alpha1">/</setting>
		<setting group="cookie" module_id="core" is_hidden="0" type="string" var_name="cookie_domain" phrase_var_name="setting_cookie_domain" ordering="0" version_id="2.0.0alpha1" />
		<setting group="" module_id="core" is_hidden="0" type="string" var_name="unzip_path" phrase_var_name="setting_unzip_path" ordering="0" version_id="2.0.0alpha1">/usr/bin/unzip</setting>
		<setting group="" module_id="core" is_hidden="0" type="string" var_name="tar_path" phrase_var_name="setting_tar_path" ordering="0" version_id="2.0.0alpha1">/bin/tar</setting>
		<setting group="server_settings" module_id="core" is_hidden="0" type="drop" var_name="csrf_protection_level" phrase_var_name="setting_csrf_protection_level" ordering="4" version_id="2.0.0alpha1"><![CDATA[a:2:{s:7:"default";s:6:"medium";s:6:"values";a:3:{i:0;s:6:"medium";i:1;s:4:"high";i:2;s:3:"low";}}]]></setting>
		<setting group="server_settings" module_id="core" is_hidden="0" type="boolean" var_name="build_file_dir" phrase_var_name="setting_build_file_dir" ordering="5" version_id="2.0.0alpha1">1</setting>
		<setting group="server_settings" module_id="core" is_hidden="0" type="string" var_name="build_format" phrase_var_name="setting_build_format" ordering="6" version_id="2.0.0alpha1">Y/m</setting>
		<setting group="" module_id="core" is_hidden="0" type="string" var_name="zip_path" phrase_var_name="setting_zip_path" ordering="1" version_id="2.0.0alpha1">/usr/bin/zip</setting>
		<setting group="general" module_id="core" is_hidden="0" type="string" var_name="site_copyright" phrase_var_name="setting_site_copyright" ordering="3" version_id="2.0.0alpha1"><![CDATA[SiteName &copy;]]></setting>
		<setting group="time_stamps" module_id="core" is_hidden="0" type="string" var_name="default_time_zone_offset" phrase_var_name="setting_default_time_zone_offset" ordering="0" version_id="2.0.0alpha1">0</setting>
		<setting group="mail" module_id="core" is_hidden="0" type="drop" var_name="method" phrase_var_name="setting_method" ordering="1" version_id="2.0.0alpha1"><![CDATA[a:2:{s:7:"default";s:4:"mail";s:6:"values";a:2:{i:0;s:4:"mail";i:1;s:4:"smtp";}}]]></setting>
		<setting group="mail" module_id="core" is_hidden="0" type="string" var_name="mailsmtphost" phrase_var_name="setting_mailsmtphost" ordering="5" version_id="2.0.0alpha1" />
		<setting group="mail" module_id="core" is_hidden="0" type="boolean" var_name="mail_smtp_authentication" phrase_var_name="setting_mail_smtp_authentication" ordering="6" version_id="2.0.0alpha1">0</setting>
		<setting group="mail" module_id="core" is_hidden="0" type="string" var_name="mail_smtp_username" phrase_var_name="setting_mail_smtp_username" ordering="7" version_id="2.0.0alpha1" />
		<setting group="mail" module_id="core" is_hidden="0" type="password" var_name="mail_smtp_password" phrase_var_name="setting_mail_smtp_password" ordering="8" version_id="2.0.0alpha1" />
        <setting group="mail" module_id="core" is_hidden="0" type="drop" var_name="mail_smtp_secure" phrase_var_name="setting_mail_smtp_secure" ordering="9" version_id="4.5.3"><![CDATA[a:2:{s:7:"default";s:4:"none";s:6:"values";a:3:{i:0;s:4:"none";i:1;s:3:"ssl";i:2;s:3:"tls";}}]]></setting>
		<setting group="mail" module_id="core" is_hidden="0" type="string" var_name="mail_from_name" phrase_var_name="setting_mail_from_name" ordering="2" version_id="2.0.0alpha1">null</setting>
		<setting group="mail" module_id="core" is_hidden="0" type="string" var_name="email_from_email" phrase_var_name="setting_email_from_email" ordering="3" version_id="2.0.0alpha1" />
		<setting group="mail" module_id="core" is_hidden="0" type="large_string" var_name="mail_signature" phrase_var_name="setting_mail_signature" ordering="4" version_id="2.0.0alpha1">Kind Regards,
Site Name</setting>
        <setting group="mail" module_id="core" is_hidden="0" type="boolean" var_name="mail_queue" phrase_var_name="setting_mail_queue" ordering="5" version_id="4.5.3">0</setting>
		<setting group="server_settings" module_id="core" is_hidden="0" type="boolean" var_name="log_site_activity" phrase_var_name="setting_log_site_activity" ordering="7" version_id="2.0.0alpha1">0</setting>
		<setting group="server_settings" module_id="core" is_hidden="0" type="boolean" var_name="cache_js_css" phrase_var_name="setting_cache_js_css" ordering="8" version_id="2.0.0alpha1">0</setting>
		<setting group="" module_id="core" is_hidden="0" type="boolean" var_name="cache_plugins" phrase_var_name="setting_cache_plugins" ordering="1" version_id="2.0.0alpha1">1</setting>
		<setting group="site_offline_online" module_id="core" is_hidden="0" type="boolean" var_name="site_is_offline" phrase_var_name="setting_site_is_offline" ordering="1" version_id="2.0.0beta4">0</setting>
		<setting group="site_offline_online" module_id="core" is_hidden="0" type="large_string" var_name="site_offline_message" phrase_var_name="setting_site_offline_message" ordering="2" version_id="2.0.0beta4" />
		<setting group="" module_id="core" is_hidden="0" type="boolean" var_name="cache_site_stats" phrase_var_name="setting_cache_site_stats" ordering="1" version_id="2.0.0beta4">1</setting>
		<setting group="time_stamps" module_id="core" is_hidden="0" type="boolean" var_name="identify_dst" phrase_var_name="setting_identify_dst" ordering="9" version_id="2.0.0beta5">1</setting>
		<setting group="" module_id="core" is_hidden="0" type="drop" var_name="watermark_option" phrase_var_name="setting_watermark_option" ordering="1" version_id="2.0.0rc1"><![CDATA[a:2:{s:7:"default";s:4:"none";s:6:"values";a:3:{i:0;s:4:"none";i:1;s:5:"image";i:2;s:4:"text";}}]]></setting>
		<setting group="" module_id="core" is_hidden="0" type="integer" var_name="watermark_opacity" phrase_var_name="setting_watermark_opacity" ordering="3" version_id="2.0.0rc1">100</setting>
		<setting group="" module_id="core" is_hidden="0" type="drop" var_name="watermark_image_position" phrase_var_name="setting_watermark_image_position" ordering="4" version_id="2.0.0rc1"><![CDATA[a:2:{s:7:"default";s:12:"bottom_right";s:6:"values";a:4:{i:0;s:12:"bottom_right";i:1;s:11:"bottom_left";i:2;s:8:"top_left";i:3;s:9:"top_right";}}]]></setting>
		<setting group="" module_id="core" is_hidden="0" type="string" var_name="image_text" phrase_var_name="setting_image_text" ordering="6" version_id="2.0.0rc1">www.yoursite.com</setting>
		<setting group="registration" module_id="core" is_hidden="0" type="boolean" var_name="registration_enable_dob" phrase_var_name="setting_registration_enable_dob" ordering="13" version_id="2.0.0rc1">0</setting>
		<setting group="registration" module_id="core" is_hidden="0" type="boolean" var_name="registration_enable_gender" phrase_var_name="setting_registration_enable_gender" ordering="14" version_id="2.0.0rc1">0</setting>
		<setting group="registration" module_id="core" is_hidden="0" type="boolean" var_name="registration_enable_location" phrase_var_name="setting_registration_enable_location" ordering="15" version_id="2.0.0rc1">0</setting>
		<setting group="registration" module_id="core" is_hidden="0" type="boolean" var_name="registration_enable_timezone" phrase_var_name="setting_registration_enable_timezone" ordering="16" version_id="2.0.0rc1">0</setting>
		<setting group="spam" module_id="core" is_hidden="0" type="boolean" var_name="enable_spam_check" phrase_var_name="setting_enable_spam_check" ordering="3" version_id="2.0.0rc1">0</setting>
		<setting group="spam" module_id="core" is_hidden="0" type="integer" var_name="auto_deny_items" phrase_var_name="setting_auto_deny_items" ordering="11" version_id="2.0.0rc1">10</setting>
		<setting group="spam" module_id="core" is_hidden="0" type="integer" var_name="auto_ban_spammer" phrase_var_name="setting_auto_ban_spammer" ordering="15" version_id="2.0.0rc1">0</setting>
		<setting group="spam" module_id="core" is_hidden="0" type="boolean" var_name="warn_on_external_links" phrase_var_name="setting_warn_on_external_links" ordering="4" version_id="2.0.0rc1">0</setting>
		<setting group="spam" module_id="core" is_hidden="0" type="boolean" var_name="disable_all_external_urls" phrase_var_name="setting_disable_all_external_urls" ordering="16" version_id="2.0.0rc1">0</setting>
		<setting group="spam" module_id="core" is_hidden="0" type="large_string" var_name="url_spam_white_list" phrase_var_name="setting_url_spam_white_list" ordering="17" version_id="2.0.0rc1">*.yahoo.com, *.google.*</setting>
		<setting group="spam" module_id="core" is_hidden="0" type="boolean" var_name="disable_all_external_emails" phrase_var_name="setting_disable_all_external_emails" ordering="18" version_id="2.0.0rc1">0</setting>
		<setting group="spam" module_id="core" is_hidden="0" type="large_string" var_name="email_white_list" phrase_var_name="setting_email_white_list" ordering="19" version_id="2.0.0rc1">*@yahoo.com, *@google.com</setting>
		<setting group="" module_id="core" is_hidden="0" type="boolean" var_name="redirect_guest_on_same_page" phrase_var_name="setting_redirect_guest_on_same_page" ordering="1" version_id="2.0.0rc1">1</setting>
		<setting group="time_stamps" module_id="core" is_hidden="0" type="string" var_name="extended_global_time_stamp" phrase_var_name="setting_extended_global_time_stamp" ordering="10" version_id="2.0.0rc2">M j, g:i a</setting>
		<setting group="" module_id="core" is_hidden="0" type="boolean" var_name="can_move_on_a_y_and_x_axis" phrase_var_name="setting_can_move_on_a_y_and_x_axis" ordering="1" version_id="2.0.0rc4">0</setting>
		<setting group="mail" module_id="core" is_hidden="0" type="integer" var_name="mail_smtp_port" phrase_var_name="setting_mail_smtp_port" ordering="9" version_id="2.0.0rc9">25</setting>
		<setting group="" module_id="core" is_hidden="0" type="string" var_name="conver_time_to_string" phrase_var_name="setting_conver_time_to_string" ordering="11" version_id="2.0.0rc10">g:i a</setting>
		<setting group="" module_id="core" is_hidden="1" type="integer" var_name="categories_to_show_at_first" phrase_var_name="setting_categories_to_show_at_first" ordering="1" version_id="2.0.0rc12">5</setting>
		<setting group="general" module_id="core" is_hidden="0" type="string" var_name="global_site_title" phrase_var_name="setting_global_site_title" ordering="2" version_id="2.0.0">Social Networking Community</setting>
		<setting group="" module_id="core" is_hidden="0" type="string" var_name="exchange_rate_api_key" phrase_var_name="setting_exchange_rate_api_key" ordering="1" version_id="2.0.5" />
		<setting group="ssl" module_id="core" is_hidden="0" type="boolean" var_name="force_https_secure_pages" phrase_var_name="setting_force_https_secure_pages" ordering="10" version_id="2.0.5dev1">0</setting>
		<setting group="registration" module_id="core" is_hidden="0" type="array" var_name="global_genders" phrase_var_name="setting_global_genders" ordering="1" version_id="2.0.5dev2"><![CDATA[s:112:"array(
  0 => '1|core.his|profile.male|core.himself',
  1 => '2|core.her|profile.female|core.herself|female',
);";]]></setting>
		<setting group="" module_id="core" is_hidden="0" type="string" var_name="ip_infodb_api_key" phrase_var_name="setting_ip_infodb_api_key" ordering="1" version_id="2.0.7" />
		<setting group="general" module_id="core" is_hidden="0" type="boolean" var_name="friends_only_community" phrase_var_name="setting_friends_only_community" ordering="10" version_id="2.1.0Beta1">0</setting>
		<setting group="server_settings" module_id="core" is_hidden="0" type="boolean" var_name="site_wide_ajax_browsing" phrase_var_name="setting_site_wide_ajax_browsing" ordering="1" version_id="2.1.0Beta1">0</setting>
		<setting group="time_stamps" module_id="core" is_hidden="0" type="drop" var_name="date_field_order" phrase_var_name="setting_date_field_order" ordering="15" version_id="3.0.0Beta1"><![CDATA[a:2:{s:7:"default";s:3:"MDY";s:6:"values";a:3:{i:0;s:3:"MDY";i:1;s:3:"DMY";i:2;s:3:"YMD";}}]]></setting>
		<setting group="time_stamps" module_id="core" is_hidden="0" type="boolean" var_name="use_jquery_datepicker" phrase_var_name="setting_use_jquery_datepicker" ordering="16" version_id="3.0.0Beta1">1</setting>
		<setting group="server_settings" module_id="core" is_hidden="0" type="boolean" var_name="disable_hash_bang_support" phrase_var_name="setting_disable_hash_bang_support" ordering="14" version_id="3.0.0beta3">0</setting>
		<setting group="general" module_id="core" is_hidden="0" type="string" var_name="official_launch_of_site" phrase_var_name="setting_official_launch_of_site" ordering="17" version_id="3.2.0beta1">1/1/2012</setting>
		<setting group="" module_id="core" is_hidden="0" type="string" var_name="rackspace_url" phrase_var_name="setting_rackspace_url" ordering="14" version_id="3.3.0beta1" />
		<setting group="" module_id="core" is_hidden="0" type="boolean" var_name="keep_files_in_server" phrase_var_name="setting_keep_files_in_server" ordering="15" version_id="3.5.0beta1">1</setting>
		<setting group="" module_id="core" is_hidden="0" type="string" var_name="google_api_key" phrase_var_name="setting_google_api_key" ordering="1" version_id="3.5.0beta1" />
		<setting group="" module_id="core" is_hidden="0" type="boolean" var_name="defer_loading_user_images" phrase_var_name="setting_defer_loading_user_images" ordering="1" version_id="3.6.0rc1">0</setting>
		<setting group="" module_id="core" is_hidden="0" type="boolean" var_name="keep_non_square_images" phrase_var_name="setting_keep_non_square_images" ordering="3" version_id="3.6.0rc1">1</setting>
		<setting group="" module_id="core" is_hidden="0" type="array" var_name="controllers_to_load_delayed" phrase_var_name="setting_controllers_to_load_delayed" ordering="4" version_id="3.6.0rc1"><![CDATA[s:8:"array();";]]></setting>
		<setting group="" module_id="core" is_hidden="0" type="boolean" var_name="super_cache_system" phrase_var_name="setting_super_cache_system" ordering="5" version_id="3.6.0rc1">0</setting>
		<setting group="" module_id="core" is_hidden="0" type="boolean" var_name="store_only_users_in_session" phrase_var_name="setting_store_only_users_in_session" ordering="6" version_id="3.6.0rc1">0</setting>
		<setting group="" module_id="core" is_hidden="1" type="boolean" var_name="force_404_check" phrase_var_name="setting_force_404_check" ordering="7" version_id="3.6.0rc1">0</setting>
		<setting group="security" module_id="core" is_hidden="0" type="boolean" var_name="use_custom_cookie_names" phrase_var_name="setting_use_custom_cookie_names" ordering="2" version_id="3.6.0rc1">0</setting>
		<setting group="security" module_id="core" is_hidden="0" type="string" var_name="custom_cookie_names_hash" phrase_var_name="setting_custom_cookie_names_hash" ordering="3" version_id="3.6.0rc1">s6ks763s5h3)s</setting>
		<setting group="security" module_id="core" is_hidden="0" type="string" var_name="protect_admincp_with_ips" phrase_var_name="setting_protect_admincp_with_ips" ordering="4" version_id="3.6.0rc1" />
		<setting group="security" module_id="core" is_hidden="0" type="boolean" var_name="auth_user_via_session" phrase_var_name="setting_auth_user_via_session" ordering="5" version_id="3.6.0rc1">0</setting>
		<setting group="" module_id="core" is_hidden="0" type="boolean" var_name="include_ip_sub_id_hash" phrase_var_name="setting_include_ip_sub_id_hash" ordering="8" version_id="3.6.0rc1">0</setting>
		<setting group="security" module_id="core" is_hidden="0" type="string" var_name="id_hash_salt" phrase_var_name="setting_id_hash_salt" ordering="6" version_id="3.6.0rc1">iysduyt623rts</setting>
		<setting group="registration" module_id="core" is_hidden="0" type="boolean" var_name="city_in_registration" phrase_var_name="setting_city_in_registration" ordering="17" version_id="3.7.0beta1">0</setting>
		<setting group="" module_id="core" is_hidden="0" type="string" var_name="watermark_image" phrase_var_name="setting_watermark_image" ordering="2" version_id="2.0.0rc1">watermark%s.png</setting>
		<setting group="" module_id="core" is_hidden="0" type="drop" var_name="upload_method" phrase_var_name="file_upload_method" ordering="1" version_id="4.2.0"><![CDATA[a:2:{s:7:"default";s:3:"ftp";s:6:"values";a:3:{i:1;s:11:"file_system";i:2;s:3:"ftp";i:3;s:8:"sftp_ssh";}}]]></setting>
		<setting group="" module_id="core" is_hidden="0" type="string" var_name="ftp_host_name" phrase_var_name="ftp_host_name" ordering="2" version_id="4.2.0">localhost</setting>
		<setting group="" module_id="core" is_hidden="0" type="string" var_name="ftp_port" phrase_var_name="ftp_port" ordering="3" version_id="4.2.0">21</setting>
		<setting group="" module_id="core" is_hidden="0" type="string" var_name="ftp_user_name" phrase_var_name="ftp_user_name" ordering="4" version_id="4.2.0">username</setting>
		<setting group="" module_id="core" is_hidden="0" type="string" var_name="ftp_password" phrase_var_name="ftp_password" ordering="5" version_id="4.2.0">password</setting>
		<setting group="registration" module_id="core" is_hidden="0" type="boolean" var_name="registration_sms_enable" phrase_var_name="setting_registration_sms_enable" ordering="18" version_id="4.3.0">0</setting>
		<setting group="registration" module_id="core" is_hidden="0" type="drop" var_name="registration_sms_service" phrase_var_name="setting_registration_sms_service" ordering="19" version_id="4.3.0"><![CDATA[a:2:{s:7:"default";s:5:"nexmo";s:6:"values";a:3:{i:0;s:5:"nexmo";i:1;s:6:"twilio";i:2;s:10:"clickatell";}}]]></setting>
		<setting group="registration" module_id="core" is_hidden="0" type="string" var_name="nexmo_api_key" phrase_var_name="setting_nexmo_api_key" ordering="20" version_id="4.3.0" />
		<setting group="registration" module_id="core" is_hidden="0" type="string" var_name="nexmo_api_secret" phrase_var_name="setting_nexmo_api_secret" ordering="21" version_id="4.3.0" />
		<setting group="registration" module_id="core" is_hidden="0" type="string" var_name="twilio_account_id" phrase_var_name="setting_twilio_account_id" ordering="22" version_id="4.3.0" />
		<setting group="registration" module_id="core" is_hidden="0" type="string" var_name="twilio_auth_token" phrase_var_name="setting_twilio_auth_token" ordering="23" version_id="4.3.0" />
		<setting group="registration" module_id="core" is_hidden="0" type="string" var_name="twilio_phone_number" phrase_var_name="setting_twilio_phone_number" ordering="24" version_id="4.3.0" />
		<setting group="registration" module_id="core" is_hidden="0" type="string" var_name="clickatell_api_key" phrase_var_name="setting_clickatell_api_key" ordering="25" version_id="4.6.0" />
		<setting group="registration" module_id="core" is_hidden="0" type="string" var_name="nexmo_phone_number" phrase_var_name="setting_nexmo_phone_number" ordering="28" version_id="4.3.0" />
		<setting group="" module_id="core" is_hidden="1" type="boolean" var_name="allow_cdn" phrase_var_name="setting_allow_cdn" ordering="1" version_id="2.0.5">1</setting>
		<setting group="" module_id="core" is_hidden="1" type="boolean" var_name="push_jscss_to_cdn" phrase_var_name="setting_push_jscss_to_cdn" ordering="16" version_id="3.6.0rc1">0</setting>
		<setting group="" module_id="core" is_hidden="0" type="boolean" var_name="allow_html" phrase_var_name="setting_allow_html" ordering="0" version_id="2.0.0alpha1">0</setting>
		<setting group="" module_id="core" is_hidden="1" type="large_string" var_name="allowed_html" phrase_var_name="setting_allowed_html" ordering="0" version_id="2.0.0alpha1"><![CDATA[<p><br><br /><strong><em><u><ul><li><font><ol><img><div><span><blockquote><strike><sub><sup><h1><h2><h3><h4><h5><h6><a><b><i><hr><tt><s><center><big><abbr><pre><small><object><embed><param><code>]]></setting>
		<setting group="" module_id="core" is_hidden="1" type="integer" var_name="crop_seo_url" phrase_var_name="setting_crop_seo_url" ordering="6" version_id="2.0.0alpha1">75</setting>
		<setting group="" module_id="core" is_hidden="1" type="integer" var_name="meta_description_limit" phrase_var_name="setting_meta_description_limit" ordering="1" version_id="2.0.0alpha1">500</setting>
		<setting group="" module_id="core" is_hidden="1" type="integer" var_name="meta_keyword_limit" phrase_var_name="setting_meta_keyword_limit" ordering="2" version_id="2.0.0alpha1">900</setting>
		<setting group="" module_id="core" is_hidden="1" type="string" var_name="description_time_stamp" phrase_var_name="setting_description_time_stamp" ordering="3" version_id="2.0.0alpha1">F j, Y</setting>
		<setting group="mail" module_id="core" is_hidden="1" type="boolean" var_name="use_dnscheck" phrase_var_name="setting_use_dnscheck" ordering="8" version_id="2.0.0alpha1">0</setting>
		<setting group="" module_id="core" is_hidden="1" type="integer" var_name="shorten_parsed_url_links" phrase_var_name="setting_shorten_parsed_url_links" ordering="2" version_id="2.0.0alpha3">50</setting>
		<setting group="spam" module_id="core" is_hidden="1" type="string" var_name="akismet_url" phrase_var_name="setting_akismet_url" ordering="1" version_id="2.0.0rc1" />
		<setting group="spam" module_id="core" is_hidden="1" type="string" var_name="akismet_password" phrase_var_name="setting_akismet_password" ordering="2" version_id="2.0.0rc1" />
		<setting group="seo" module_id="core" is_hidden="0" type="large_string" var_name="meta_description_profile" phrase_var_name="setting_meta_description_profile" ordering="8" version_id="2.0.0rc1">Site Name gives people the power to share and makes the world more open and connected.</setting>
		<setting group="" module_id="core" is_hidden="1" type="large_string" var_name="words_remove_in_keywords" phrase_var_name="setting_words_remove_in_keywords" ordering="4" version_id="2.0.0rc1">and, i, in</setting>
		<setting group="general" module_id="core" is_hidden="1" type="boolean" var_name="section_privacy_item_browsing" phrase_var_name="setting_section_privacy_item_browsing" ordering="9" version_id="3.0.0Beta1">1</setting>
		<setting group="" module_id="core" is_hidden="1" type="boolean" var_name="no_follow_on_external_links" phrase_var_name="setting_no_follow_on_external_links" ordering="7" version_id="3.3.0beta1">0</setting>
		<setting group="" module_id="core" is_hidden="1" type="integer" var_name="activity_feed_line_breaks" phrase_var_name="setting_activity_feed_line_breaks" ordering="7" version_id="3.5.0">0</setting>
		<setting group="" module_id="core" is_hidden="1" type="boolean" var_name="include_site_title_all_pages" phrase_var_name="setting_include_site_title_all_pages" ordering="8" version_id="3.6.0rc1">0</setting>
		<setting group="" module_id="core" is_hidden="1" type="string" var_name="ftp_host" phrase_var_name="setting_host" ordering="2" version_id="2.0.0alpha1" />
		<setting group="" module_id="core" is_hidden="1" type="string" var_name="ftp_username" phrase_var_name="setting_username" ordering="3" version_id="2.0.0alpha1" />
		<setting group="" module_id="core" is_hidden="1" type="boolean" var_name="ftp_enabled" phrase_var_name="setting_ftp_enabled" ordering="1" version_id="2.0.0alpha1">0</setting>
		<setting group="" module_id="core" is_hidden="1" type="string" var_name="ftp_dir_path" phrase_var_name="setting_ftp_dir_path" ordering="5" version_id="2.0.0rc1" />
		<setting group="" module_id="core" is_hidden="1" type="integer" var_name="banned_user_group_id" phrase_var_name="setting_banned_user_group_id" ordering="1" version_id="2.0.0rc1">0</setting>
		<setting group="" module_id="core" is_hidden="1" type="string" var_name="image_text_hex" phrase_var_name="setting_image_text_hex" ordering="5" version_id="2.0.0rc1">000000</setting>
		<setting group="" module_id="core" is_hidden="1" type="boolean" var_name="enabled_edit_area" phrase_var_name="setting_enabled_edit_area" ordering="5" version_id="2.0.7">0</setting>
		etting
		<setting group="server_settings" module_id="core" is_hidden="1" type="string" var_name="jquery_google_cdn_version" phrase_var_name="setting_jquery_google_cdn_version" ordering="12" version_id="2.1.0Beta1">1.4.4</setting>
		<setting group="server_settings" module_id="core" is_hidden="1" type="string" var_name="jquery_ui_google_cdn_version" phrase_var_name="setting_jquery_ui_google_cdn_version" ordering="13" version_id="2.1.0Beta1">1.8.7</setting>
		<setting group="general" module_id="core" is_hidden="1" type="boolean" var_name="display_required" phrase_var_name="setting_display_required" ordering="12" version_id="2.0.0alpha1">1</setting>
		<setting group="general" module_id="core" is_hidden="1" type="string" var_name="required_symbol" phrase_var_name="setting_required_symbol" ordering="14" version_id="2.0.0alpha1"><![CDATA[&#42;]]></setting>
		<setting group="general" module_id="core" is_hidden="1" type="boolean" var_name="is_personal_site" phrase_var_name="setting_is_personal_site" ordering="1" version_id="2.0.0alpha1">0</setting>
		<setting group="" module_id="core" is_hidden="1" type="boolean" var_name="branding" phrase_var_name="setting_branding" ordering="0" version_id="2.0.0alpha1">0</setting>
		<setting group="" module_id="core" is_hidden="1" type="string" var_name="default_lang_id" phrase_var_name="setting_default_lang_id" ordering="0" version_id="2.0.0alpha1">en</setting>
		<setting group="" module_id="core" is_hidden="1" type="string" var_name="default_theme_name" phrase_var_name="setting_default_theme_name" ordering="0" version_id="2.0.0alpha1">default</setting>
		<setting group="" module_id="core" is_hidden="1" type="string" var_name="default_style_name" phrase_var_name="setting_default_style_name" ordering="0" version_id="2.0.0alpha1">default</setting>
		<setting group="" module_id="core" is_hidden="1" type="string" var_name="module_forum" phrase_var_name="setting_module_forum" ordering="0" version_id="2.0.0alpha1">forum</setting>
		<setting group="" module_id="core" is_hidden="1" type="string" var_name="module_core" phrase_var_name="setting_module_core" ordering="0" version_id="2.0.0alpha1">core</setting>
		<setting group="" module_id="core" is_hidden="1" type="large_string" var_name="global_admincp_note" phrase_var_name="setting_global_admincp_note" ordering="1" version_id="2.0.0rc1">Save your notes here...</setting>
		<setting group="" module_id="core" is_hidden="1" type="string" var_name="phpfox_version" phrase_var_name="setting_phpfox_version" ordering="1" version_id="2.0.0rc1">2.0.0rc2</setting>
		<setting group="" module_id="core" is_hidden="1" type="string" var_name="theme_session_prefix" phrase_var_name="setting_theme_session_prefix" ordering="1" version_id="2.0.0rc3">486256453</setting>
		<setting group="" module_id="core" is_hidden="1" type="integer" var_name="css_edit_id" phrase_var_name="setting_css_edit_id" ordering="1" version_id="2.0.2">1</setting>
		<setting group="" module_id="core" is_hidden="1" type="integer" var_name="phpfox_total_users_online_mark" phrase_var_name="setting_phpfox_total_users_online_mark" ordering="1" version_id="2.0.7" />
		<setting group="" module_id="core" is_hidden="1" type="string" var_name="phpfox_total_users_online_history" phrase_var_name="setting_phpfox_total_users_online_history" ordering="1" version_id="2.0.7" />
		<setting group="" module_id="core" is_hidden="1" type="boolean" var_name="phpfox_is_hosted" phrase_var_name="setting_phpfox_is_hosted" ordering="1" version_id="2.0.7">0</setting>
		<setting group="" module_id="core" is_hidden="1" type="string" var_name="phpfox_max_users_online" phrase_var_name="setting_phpfox_max_users_online" ordering="1" version_id="2.0.7">0</setting>
        <setting group="general" module_id="core" is_hidden="0" type="boolean" var_name="turn_off_full_ajax_mode" phrase_var_name="setting_turn_off_full_ajax_mode" ordering="1" version_id="4.4.0">false</setting>
		<setting group="seo" module_id="core" is_hidden="0" type="string" var_name="google_plus_page_url" phrase_var_name="setting_google_plus_page_url" ordering="1" version_id="4.5.0" />
        <setting group="general" module_id="core" is_hidden="0" type="boolean" var_name="show_addthis_section" phrase_var_name="setting_show_addthis_section" ordering="1" version_id="4.6.0">1</setting>
        <setting group="" module_id="core" is_hidden="0" type="string" var_name="addthis_pub_id" phrase_var_name="setting_addthis_pub_id" ordering="1" version_id="4.5.2" />
        <setting group="" module_id="core" is_hidden="0" type="large_string" var_name="addthis_share_button" phrase_var_name="setting_addthis_share_button" ordering="1" version_id="4.5.2" />
        <setting group="ssl" module_id="core" is_hidden="0" type="boolean" var_name="use_secure_image_display" phrase_var_name="setting_use_secure_image_display" ordering="1" version_id="4.5.2">false</setting>
        <setting group="general" module_id="core" is_hidden="0" type="drop" var_name="paging_mode" phrase_var_name="setting_paging_mode" ordering="1" version_id="4.6.0"><![CDATA[a:2:{s:7:"default";s:8:"loadmore";s:6:"values";a:3:{i:0;s:8:"loadmore";i:1;s:9:"next_prev";i:2;s:10:"pagination";}}]]></setting>
        <setting group="general" module_id="core" is_hidden="0" type="boolean" var_name="search_group_settings" phrase_var_name="setting_search_group_settings" ordering="1" version_id="4.6.0">1</setting>
        <setting group="" module_id="core" is_hidden="0" type="boolean" var_name="auto_detect_language" phrase_var_name="setting_auto_detect_language" ordering="97" version_id="4.6.0">1</setting>
        <setting group="" module_id="core" is_hidden="0" type="integer" var_name="auto_clear_cache" phrase_var_name="setting_auto_clear_cache" ordering="98" version_id="4.6.0">0</setting>
	</settings>
	<blocks>
		<block type_id="0" m_connection="admincp.index" module_id="core" component="note" location="2" is_active="1" ordering="2" disallow_access="" can_move="1">
			<title>AdminCP Notes</title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="admincp.index" module_id="core" component="active-admin" location="1" is_active="1" ordering="2" disallow_access="" can_move="1">
			<title>Active Admins</title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="admincp.index" module_id="core" component="news" location="2" is_active="1" ordering="3" disallow_access="" can_move="1">
			<title>Corporate News And Updates</title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="admincp.index" module_id="core" component="site-stat" location="3" is_active="1" ordering="1" disallow_access="" can_move="1">
			<title>Site Statistics</title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="admincp.index" module_id="core" component="latest-admin-login" location="1" is_active="1" ordering="3" disallow_access="" can_move="1">
			<title>Latest Admin Logins</title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="core.index-member" module_id="core" component="fe-site-stat" location="1" is_active="1" ordering="3" disallow_access="" can_move="1">
			<title>Site Statistics</title>
			<source_code />
			<source_parsed />
		</block>
	</blocks>
	<hooks>
		<hook module_id="core" hook_type="component" module="core" call_name="core.component_message_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="core" hook_type="service" module="core" call_name="core.service_country_country__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="core" hook_type="service" module="core" call_name="core.service_country_process__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="core" hook_type="service" module="core" call_name="core.service_core__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="core" hook_type="library" module="core" call_name="user_genders" added="1242299671" version_id="2.0.0beta2" />
		<hook module_id="core" hook_type="library" module="core" call_name="template_getheader_language" added="1242299671" version_id="2.0.0beta2" />
		<hook module_id="core" hook_type="library" module="core" call_name="template_getheader_setting" added="1242299671" version_id="2.0.0beta2" />
		<hook module_id="core" hook_type="library" module="core" call_name="template_getheader" added="1242299671" version_id="2.0.0beta2" />
		<hook module_id="core" hook_type="library" module="core" call_name="cron_exec" added="1242299671" version_id="2.0.0beta2" />
		<hook module_id="core" hook_type="library" module="core" call_name="cron_construct" added="1242299671" version_id="2.0.0beta2" />
		<hook module_id="core" hook_type="library" module="core" call_name="run" added="1242299671" version_id="2.0.0beta2" />
		<hook module_id="core" hook_type="library" module="core" call_name="validator_construct" added="1242299671" version_id="2.0.0beta2" />
		<hook module_id="core" hook_type="library" module="core" call_name="parse_bbcode_construct" added="1242299671" version_id="2.0.0beta2" />
		<hook module_id="core" hook_type="library" module="core" call_name="spam_methods" added="1242299671" version_id="2.0.0beta2" />
		<hook module_id="core" hook_type="library" module="core" call_name="cron_start" added="1242299671" version_id="2.0.0beta2" />
		<hook module_id="core" hook_type="library" module="core" call_name="cron_end" added="1242299671" version_id="2.0.0beta2" />
		<hook module_id="core" hook_type="library" module="core" call_name="init" added="1242299671" version_id="2.0.0beta2" />
		<hook module_id="core" hook_type="component" module="core" call_name="core.component_block_dashboard_clean" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="core" hook_type="component" module="core" call_name="core.component_block_info_clean" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="core" hook_type="component" module="core" call_name="core.component_block_country_child_clean" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="core" hook_type="component" module="core" call_name="core.component_block_new_setting_clean" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="core" hook_type="component" module="core" call_name="core.component_block_currency_clean" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="core" hook_type="component" module="core" call_name="core.component_block_activity_clean" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="core" hook_type="service" module="core" call_name="core.service_process__call" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="core" hook_type="service" module="core" call_name="core.service_block__call" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="core" hook_type="service" module="core" call_name="core.service_callback__call" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="core" hook_type="service" module="core" call_name="core.service_currency__call" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="core" hook_type="component" module="core" call_name="core.component_block_ftp_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="core" hook_type="component" module="core" call_name="core.component_block_site_stat_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="core" hook_type="component" module="core" call_name="core.component_block_view_admincp_login_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="core" hook_type="component" module="core" call_name="core.component_block_active_admin_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="core" hook_type="component" module="core" call_name="core.component_block_latest_admin_login_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="core" hook_type="component" module="core" call_name="core.component_block_note_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="core" hook_type="component" module="core" call_name="core.component_block_news_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="core" hook_type="controller" module="core" call_name="core.component_controller_admincp_country_import_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="core" hook_type="controller" module="core" call_name="core.component_controller_admincp_country_index_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="core" hook_type="controller" module="core" call_name="core.component_controller_admincp_country_child_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="core" hook_type="controller" module="core" call_name="core.component_controller_admincp_country_child_add_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="core" hook_type="controller" module="core" call_name="core.component_controller_admincp_country_add_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="core" hook_type="controller" module="core" call_name="core.component_controller_phpinfo_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="core" hook_type="controller" module="core" call_name="core.component_controller_admincp_ip_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="core" hook_type="controller" module="core" call_name="core.component_controller_admincp_online_guest_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="core" hook_type="controller" module="core" call_name="core.component_controller_admincp_latest_admin_login_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="core" hook_type="controller" module="core" call_name="core.component_controller_system_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="core" hook_type="controller" module="core" call_name="core.component_controller_redirect_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="core" hook_type="controller" module="core" call_name="core.component_controller_offline_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="core" hook_type="service" module="core" call_name="core.service_admincp_process__call" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="core" hook_type="service" module="core" call_name="core.service_admincp_admincp__call" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="core" hook_type="service" module="core" call_name="core.service_country_child_process__call" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="core" hook_type="service" module="core" call_name="core.service_stat_process__call" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="core" hook_type="service" module="core" call_name="core.service_stat_stat__call" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="core" hook_type="service" module="core" call_name="core.service_category_category__call" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="core" hook_type="service" module="core" call_name="core.service_process_addGender_start" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="core" hook_type="service" module="core" call_name="core.service_process_addGender_end" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="core" hook_type="service" module="core" call_name="core.service_load__call" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="core" hook_type="service" module="core" call_name="core.service_system__call" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="core" hook_type="controller" module="core" call_name="core.component_controller_index_visitor_start" added="1259173633" version_id="2.0.0rc9" />
		<hook module_id="core" hook_type="controller" module="core" call_name="core.component_controller_index_member_start" added="1259173633" version_id="2.0.0rc9" />
		<hook module_id="core" hook_type="library" module="core" call_name="locale_contruct__end" added="1260366442" version_id="2.0.0rc11" />
		<hook module_id="core" hook_type="component" module="core" call_name="core.component_block_translate_child_country_clean" added="1260366442" version_id="2.0.0rc11" />
		<hook module_id="core" hook_type="component" module="core" call_name="core.component_block_translate_country_clean" added="1260366442" version_id="2.0.0rc11" />
		<hook module_id="core" hook_type="component" module="core" call_name="core.component_block_holder_clean" added="1261572640" version_id="2.0.0" />
		<hook module_id="core" hook_type="template" module="core" call_name="theme_template_body__end" added="1261572988" version_id="2.0.0" />
		<hook module_id="core" hook_type="library" module="core" call_name="session_remove__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="core" hook_type="service" module="core" call_name="core.service_core_getgenders__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="core" hook_type="library" module="core" call_name="set_defined_controller" added="1263388996" version_id="2.0.2" />
		<hook module_id="core" hook_type="library" module="core" call_name="run_start" added="1263388996" version_id="2.0.2" />
		<hook module_id="core" hook_type="library" module="core" call_name="get_controller" added="1263388996" version_id="2.0.2" />
		<hook module_id="core" hook_type="library" module="core" call_name="check_url_is_array" added="1263388996" version_id="2.0.2" />
		<hook module_id="core" hook_type="library" module="core" call_name="request_get" added="1263388996" version_id="2.0.2" />
		<hook module_id="core" hook_type="library" module="core" call_name="template_gettemplatefile" added="1263388996" version_id="2.0.2" />
		<hook module_id="core" hook_type="library" module="core" call_name="component_pre_process" added="1263389358" version_id="2.0.2" />
		<hook module_id="core" hook_type="library" module="core" call_name="component_post_process" added="1263389358" version_id="2.0.2" />
		<hook module_id="core" hook_type="library" module="core" call_name="set_controller_else_end" added="1264437857" version_id="2.0.3" />
		<hook module_id="core" hook_type="library" module="core" call_name="mail_send_query" added="1266260139" version_id="2.0.4" />
		<hook module_id="core" hook_type="library" module="core" call_name="mail_send_call" added="1266260139" version_id="2.0.4" />
		<hook module_id="core" hook_type="library" module="core" call_name="file_upload_start" added="1266260157" version_id="2.0.4" />
		<hook module_id="core" hook_type="library" module="core" call_name="check_url_is_array_return" added="1267629983" version_id="2.0.4" />
		<hook module_id="core" hook_type="controller" module="core" call_name="core.component_controller_index_visitor_mobile_clean" added="1267629983" version_id="2.0.4" />
		<hook module_id="core" hook_type="controller" module="core" call_name="core.component_controller_index_member_mobile_clean" added="1267629983" version_id="2.0.4" />
		<hook module_id="core" hook_type="library" module="core" call_name="hash_sethash__end" added="1268138234" version_id="2.0.4" />
		<hook module_id="core" hook_type="library" module="core" call_name="validator_check_routine_default" added="1271160844" version_id="2.0.5" />
		<hook module_id="core" hook_type="library" module="core" call_name="phpfox_parse_output_parse__start" added="1271160844" version_id="2.0.5" />
		<hook module_id="core" hook_type="library" module="core" call_name="editor_get" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="core" hook_type="library" module="core" call_name="parse_input_construct" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="core" hook_type="library" module="core" call_name="parse_input__removeevilattributes" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="core" hook_type="service" module="core" call_name="core.service_currency__construct" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="core" hook_type="service" module="core" call_name="core.service_currency_process__call" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="core" hook_type="controller" module="core" call_name="core.component_controller_admincp_currency_add_clean" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="core" hook_type="controller" module="core" call_name="core.component_controller_admincp_currency_index_clean" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="core" hook_type="library" module="core" call_name="module_getcomponent_handle_block_position" added="1276177474" version_id="2.0.5" />
		<hook module_id="core" hook_type="library" module="core" call_name="template_parsefunction_block_end_if" added="1276177474" version_id="2.0.5" />
		<hook module_id="core" hook_type="service" module="core" call_name="core.service_core_getsecurepages" added="1276177474" version_id="2.0.5" />
		<hook module_id="core" hook_type="library" module="core" call_name="ajax__construct" added="1286546859" version_id="2.0.7" />
		<hook module_id="core" hook_type="library" module="core" call_name="ajax_process" added="1286546859" version_id="2.0.7" />
		<hook module_id="core" hook_type="library" module="core" call_name="ajax_html" added="1286546859" version_id="2.0.7" />
		<hook module_id="core" hook_type="library" module="core" call_name="ajax_prepend" added="1286546859" version_id="2.0.7" />
		<hook module_id="core" hook_type="library" module="core" call_name="ajax_append" added="1286546859" version_id="2.0.7" />
		<hook module_id="core" hook_type="library" module="core" call_name="ajax_getcontent" added="1286546859" version_id="2.0.7" />
		<hook module_id="core" hook_type="library" module="core" call_name="ajax_getdata" added="1286546859" version_id="2.0.7" />
		<hook module_id="core" hook_type="library" module="core" call_name="ajax__ajaxsafe" added="1286546859" version_id="2.0.7" />
		<hook module_id="core" hook_type="library" module="core" call_name="module_setcontroller_start" added="1286546859" version_id="2.0.7" />
		<hook module_id="core" hook_type="library" module="core" call_name="module_setcontroller_end" added="1286546859" version_id="2.0.7" />
		<hook module_id="core" hook_type="library" module="core" call_name="module_getcontrollertemplate" added="1286546859" version_id="2.0.7" />
		<hook module_id="core" hook_type="library" module="core" call_name="get_module_blocks" added="1286546859" version_id="2.0.7" />
		<hook module_id="core" hook_type="library" module="core" call_name="module_getcomponent_start" added="1286546859" version_id="2.0.7" />
		<hook module_id="core" hook_type="library" module="core" call_name="module_getcomponent_gettemplate" added="1286546859" version_id="2.0.7" />
		<hook module_id="core" hook_type="library" module="core" call_name="image_helper_display_notfound" added="1286546859" version_id="2.0.7" />
		<hook module_id="core" hook_type="library" module="core" call_name="template_template_setbreadcrump" added="1286546859" version_id="2.0.7" />
		<hook module_id="core" hook_type="library" module="core" call_name="template_gettemplate" added="1286546859" version_id="2.0.7" />
		<hook module_id="core" hook_type="library" module="core" call_name="archive__construct" added="1286546859" version_id="2.0.7" />
		<hook module_id="core" hook_type="library" module="core" call_name="archive_export_set" added="1286546859" version_id="2.0.7" />
		<hook module_id="core" hook_type="library" module="core" call_name="archive_export_download" added="1286546859" version_id="2.0.7" />
		<hook module_id="core" hook_type="library" module="core" call_name="parse_output_fiximagewidth" added="1286546859" version_id="2.0.7" />
		<hook module_id="core" hook_type="library" module="core" call_name="parse_bbcode_preparse_end" added="1286546859" version_id="2.0.7" />
		<hook module_id="core" hook_type="library" module="core" call_name="parse_bbcode_parse_end" added="1286546859" version_id="2.0.7" />
		<hook module_id="core" hook_type="library" module="core" call_name="parse_bbcode_quote_start" added="1286546859" version_id="2.0.7" />
		<hook module_id="core" hook_type="library" module="core" call_name="parse_bbcode_quote_end" added="1286546859" version_id="2.0.7" />
		<hook module_id="core" hook_type="service" module="core" call_name="core.service_currency_getcurrency" added="1286546859" version_id="2.0.7" />
		<hook module_id="core" hook_type="library" module="core" call_name="template_getstaticversion" added="1290072896" version_id="2.0.7" />
		<hook module_id="core" hook_type="library" module="core" call_name="parse_bbcode__image" added="1290072896" version_id="2.0.7" />
		<hook module_id="core" hook_type="library" module="core" call_name="template_gettitle" added="1290094336" version_id="2.0.7" />
		<hook module_id="core" hook_type="library" module="core" call_name="editor_construct" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="core" hook_type="library" module="core" call_name="image_helper_display_start" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="core" hook_type="library" module="core" call_name="parse_bbcode__code1" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="core" hook_type="library" module="core" call_name="parse_bbcode__code2" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="core" hook_type="library" module="core" call_name="parse_bbcode__code3" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="core" hook_type="library" module="core" call_name="url_getdomain_1" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="core" hook_type="library" module="core" call_name="librayr_url__send_switch" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="core" hook_type="component" module="core" call_name="core.component_block__clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="core" hook_type="component" module="core" call_name="core.component_block_category_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="core" hook_type="component" module="core" call_name="core.component_block_moderation_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="core" hook_type="component" module="core" call_name="core.component_block_template_body_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="core" hook_type="component" module="core" call_name="core.component_block_template_breadcrumblist_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="core" hook_type="component" module="core" call_name="core.component_block_template_breadcrumbmenu_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="core" hook_type="component" module="core" call_name="core.component_block_template_contentclass_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="core" hook_type="component" module="core" call_name="core.component_block_template_copyright_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="core" hook_type="component" module="core" call_name="core.component_block_template-footer_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="core" hook_type="component" module="core" call_name="core.component_block_template_holdername_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="core" hook_type="component" module="core" call_name="core.component_block_template_logo_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="core" hook_type="component" module="core" call_name="core.component_block_template_menu_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="core" hook_type="component" module="core" call_name="core.component_block_template_menuaccount_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="core" hook_type="component" module="core" call_name="core.component_block_template-menufooter_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="core" hook_type="component" module="core" call_name="core.component_block_template_menusub_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="core" hook_type="component" module="core" call_name="core.component_block_template_notification_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="core" hook_type="service" module="core" call_name="core.service_redirect_process__call" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="core" hook_type="template" module="core" call_name="core.template_block_comment_border_new" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="core" hook_type="controller" module="core" call_name="core.component_controller_admincp_stat_clean" added="1335951260" version_id="3.2.0" />
		<hook module_id="core" hook_type="library" module="core" call_name="library_template_getstyle_1" added="1358258443" version_id="3.5.0beta1" />
		<hook module_id="core" hook_type="library" module="core" call_name="library_template_getlayoutfile_1" added="1358258443" version_id="3.5.0beta1" />
		<hook module_id="core" hook_type="library" module="core" call_name="template_template_getmenu_1" added="1358258443" version_id="3.5.0beta1" />
		<hook module_id="core" hook_type="library" module="core" call_name="library_module_getmoduleblocks_1" added="1358258443" version_id="3.5.0beta1" />
		<hook module_id="core" hook_type="library" module="core" call_name="library_phpfox_file_file_upload_1" added="1358258443" version_id="3.5.0beta1" />
		<hook module_id="core" hook_type="library" module="core" call_name="library_phpfox_file_file_upload_2" added="1358258443" version_id="3.5.0beta1" />
		<hook module_id="core" hook_type="library" module="core" call_name="library_phpfox_file_file_upload_3" added="1358258443" version_id="3.5.0beta1" />
		<hook module_id="core" hook_type="library" module="core" call_name="library_phpfox_ismobile" added="1358258443" version_id="3.5.0beta1" />
		<hook module_id="core" hook_type="component" module="core" call_name="core.template_block_template_menu_1" added="1358258443" version_id="3.5.0beta1" />
		<hook module_id="core" hook_type="controller" module="core" call_name="core.component_controller_full_clean" added="1358258443" version_id="3.5.0beta1" />
		<hook module_id="core" hook_type="library" module="core" call_name="template_template_getmenu_2" added="1361180401" version_id="3.5.0rc1" />
		<hook module_id="core" hook_type="library" module="core" call_name="library_phpfox_phpfox_getuserid__1" added="1361180401" version_id="3.5.0rc1" />
		<hook module_id="core" hook_type="service" module="core" call_name="core.service_currency_contruct__1" added="1361532353" version_id="3.5.0" />
		<hook module_id="core" hook_type="service" module="core" call_name="core.service_currency_getforedit__1" added="1361532353" version_id="3.5.0" />
		<hook module_id="core" hook_type="service" module="core" call_name="core.service_currency_getforbrowse__1" added="1361532353" version_id="3.5.0" />
		<hook module_id="core" hook_type="library" module="core" call_name="library_phpfox_locale_phrase_not_found" added="1361776392" version_id="3.5.0" />
		<hook module_id="core" hook_type="library" module="core" call_name="library_module_getservice_1" added="1363075699" version_id="3.5.0" />
		<hook module_id="core" hook_type="library" module="core" call_name="library_module_getcomponent_1" added="1363075699" version_id="3.5.0" />
		<hook module_id="core" hook_type="library" module="core" call_name="library_module_getcomponent_2" added="1363075699" version_id="3.5.0" />
		<hook module_id="core" hook_type="library" module="core" call_name="library_template_cache_compile__1" added="1363075699" version_id="3.5.0" />
		<hook module_id="core" hook_type="library" module="core" call_name="library_template_cache_parse__1" added="1363075699" version_id="3.5.0" />
		<hook module_id="core" hook_type="library" module="core" call_name="library_phpfox_getlibclass_1" added="1363075699" version_id="3.5.0" />
		<hook module_id="core" hook_type="library" module="core" call_name="mail_send_call_2" added="1372757268" version_id="3.6.0rc1" />
		<hook module_id="core" hook_type="library" module="core" call_name="mail_send_call_3" added="1372757268" version_id="3.6.0rc1" />
		<hook module_id="core" hook_type="library" module="core" call_name="mail_send_call_4" added="1378372973" version_id="3.7.0rc1" />
		<hook module_id="core" hook_type="library" module="core" call_name="get_master_files" added="1378374384" version_id="3.7.0rc1" />
		<hook module_id="core" hook_type="library" module="core" call_name="get_service_1" added="1378455278" version_id="3.7.0rc1" />
		<hook module_id="core" hook_type="library" module="core" call_name="library_phpfox_getlibclass_0" added="1378455278" version_id="3.7.0rc1" />
		<hook module_id="core" hook_type="library" module="core" call_name="library_phpfox_getlib_0" added="1378455278" version_id="3.7.0rc1" />
		<hook module_id="core" hook_type="library" module="core" call_name="run_get_header_file_1" added="1378455278" version_id="3.7.0rc1" />
		<hook module_id="core" hook_type="library" module="core" call_name="request_is_mobile" added="1384771085" version_id="3.7.3" />
	</hooks>
	<components>
		<component module_id="core" component="index-member" m_connection="core.index-member" module="core" is_controller="1" is_block="0" is_active="1" />
		<component module_id="core" component="index-visitor" m_connection="core.index-visitor" module="core" is_controller="1" is_block="0" is_active="1" />
		<component module_id="core" component="new" m_connection="" module="core" is_controller="0" is_block="1" is_active="1" />
		<component module_id="core" component="dashboard" m_connection="" module="core" is_controller="0" is_block="1" is_active="1" />
		<component module_id="core" component="note" m_connection="" module="core" is_controller="0" is_block="1" is_active="1" />
		<component module_id="core" component="active-admin" m_connection="" module="core" is_controller="0" is_block="1" is_active="1" />
		<component module_id="core" component="news" m_connection="" module="core" is_controller="0" is_block="1" is_active="1" />
		<component module_id="core" component="site-stat" m_connection="" module="core" is_controller="0" is_block="1" is_active="1" />
		<component module_id="core" component="latest-admin-login" m_connection="" module="core" is_controller="0" is_block="1" is_active="1" />
		<component module_id="core" component="fe-site-stat" m_connection="" module="core" is_controller="0" is_block="1" is_active="1" />
	</components>
	<crons>
		<cron module_id="core" type_id="1" every="5"><![CDATA[Phpfox_Queue::instance()->work();]]></cron>
		<cron module_id="user" type_id="2" every="1"><![CDATA[Phpfox::getService('core.temp-file')->clean();]]></cron>
		<cron module_id="core" type_id="2" every="1"><![CDATA[Phpfox::getService('admincp.maintain')->cronRemoveCache();]]></cron>
	</crons>
	<pages>
		<page module_id="core" is_phrase="0" has_bookmark="0" parse_php="1" add_view="0" full_size="1" title="Privacy" title_url="policy" added="1231339063">
			<keyword></keyword>
			<description></description>
			<text><![CDATA[<div class="item_view_content">
<ul>
<li>Coffee</li>
<li>Milk</li>
</ul>

<ol>
<li>Coffee</li>
<li>Milk</li>
</ol>
</div>

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris fringilla justo consectetur velit. Morbi volutpat. Nam et nibh. Sed nec metus vitae libero luctus cursus. Nulla facilisi. Duis at orci ut mauris imperdiet mattis. Integer quam enim, feugiat at, sagittis at, venenatis in, lacus. Phasellus at tellus. Praesent orci justo, malesuada ac, pulvinar sed, iaculis non, leo. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Etiam eget quam. Nunc sed velit. Phasellus quis nisi. In nisi nisi, suscipit ut, lobortis non, vestibulum quis, sapien. Cras nisl. Proin tristique. Duis ac diam nec ante convallis elementum. Quisque eget purus.

Quisque mauris orci, feugiat et, ornare vitae, adipiscing tempus, metus. Nam tincidunt. Donec arcu. Sed augue risus, faucibus eu, laoreet sit amet, interdum eget, odio. Aliquam faucibus libero sed lorem. Nulla erat. Donec sapien dui, rutrum ac, pharetra id, fermentum sed, arcu. Donec elementum vulputate lectus. Nam vitae risus. Suspendisse semper consectetur nulla. Morbi mattis justo a mauris. Nam vel felis ac velit pharetra rhoncus. Praesent faucibus odio tincidunt massa. Etiam adipiscing libero vel erat. Vestibulum arcu. Donec convallis quam non lectus.]]></text>
			<text_parsed><![CDATA[<?php /* Cached: April 24, 2013, 9:45 am */ ?>
<div class="item_view_content">
<ul>
<li>Coffee</li>
<li>Milk</li>
</ul>

<ol>
<li>Coffee</li>
<li>Milk</li>
</ol>
</div>

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris fringilla justo consectetur velit. Morbi volutpat. Nam et nibh. Sed nec metus vitae libero luctus cursus. Nulla facilisi. Duis at orci ut mauris imperdiet mattis. Integer quam enim, feugiat at, sagittis at, venenatis in, lacus. Phasellus at tellus. Praesent orci justo, malesuada ac, pulvinar sed, iaculis non, leo. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Etiam eget quam. Nunc sed velit. Phasellus quis nisi. In nisi nisi, suscipit ut, lobortis non, vestibulum quis, sapien. Cras nisl. Proin tristique. Duis ac diam nec ante convallis elementum. Quisque eget purus.

Quisque mauris orci, feugiat et, ornare vitae, adipiscing tempus, metus. Nam tincidunt. Donec arcu. Sed augue risus, faucibus eu, laoreet sit amet, interdum eget, odio. Aliquam faucibus libero sed lorem. Nulla erat. Donec sapien dui, rutrum ac, pharetra id, fermentum sed, arcu. Donec elementum vulputate lectus. Nam vitae risus. Suspendisse semper consectetur nulla. Morbi mattis justo a mauris. Nam vel felis ac velit pharetra rhoncus. Praesent faucibus odio tincidunt massa. Etiam adipiscing libero vel erat. Vestibulum arcu. Donec convallis quam non lectus.]]></text_parsed>
		</page>
		<page module_id="core" is_phrase="0" has_bookmark="0" parse_php="1" add_view="0" full_size="1" title="Terms of Use" title_url="terms" added="1232964954">
			<keyword></keyword>
			<description></description>
			<text>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque id ipsum nisl. Nam vitae ligula turpis, vel egestas turpis. Curabitur condimentum metus ac ligula pulvinar volutpat. Nullam mollis nulla eu ligula volutpat pellentesque. Pellentesque sit amet nisl metus, et placerat elit. Mauris ac justo est, at malesuada mauris. Etiam auctor pharetra mollis. Vivamus lobortis, sem sit amet porta suscipit, augue libero consectetur justo, a sollicitudin risus eros et est. Vivamus eget lectus tellus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Aenean vel ullamcorper mauris.

                Ut non consequat risus. Phasellus eget ligula vel enim pretium volutpat. Phasellus rutrum porttitor lorem. In accumsan pharetra sapien, in porta augue accumsan a. Fusce pellentesque egestas euismod. Donec porta dapibus urna eu varius. Pellentesque aliquet dapibus turpis, ut sollicitudin elit convallis eget. Curabitur ornare, sapien nec rhoncus bibendum, lacus libero tristique dui, in interdum massa purus ut quam. Morbi est turpis, feugiat non porttitor sed, adipiscing sed ligula.

                Quisque ac tempus ipsum. Praesent tempus convallis enim in suscipit. Nulla eu ipsum nec nisl tempus vestibulum. Fusce rutrum placerat tortor, vel ultricies sem ultrices sit amet. Proin elementum convallis neque eu sodales. Vivamus turpis massa, sodales sed volutpat consequat, feugiat non ante. Phasellus vel blandit nunc. Quisque nec ligula orci. Proin luctus interdum diam eu mattis. Maecenas nec posuere nunc. Duis a purus lacus. Quisque sit amet enim lacus. Praesent molestie, arcu id pharetra sollicitudin, est diam mattis erat, vel volutpat mauris ante nec lorem. In eget posuere sapien.

                Donec felis tellus, adipiscing viverra volutpat vel, luctus sed felis. Morbi ultricies ante in mauris ultrices ullamcorper. Vivamus justo est, suscipit eget convallis quis, dapibus nec lacus. Maecenas vel urna ac lacus adipiscing molestie nec id quam. Aliquam faucibus rutrum nisl, vitae faucibus felis tincidunt eget. Aliquam sit amet varius augue. In elementum sodales sapien id laoreet. Ut mattis laoreet neque, quis tincidunt leo mattis sed.

                Cras lacinia elementum auctor. Proin ante lacus, lobortis viverra tincidunt vitae, ullamcorper in nisi. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Phasellus condimentum gravida lorem, eget lacinia mauris sodales eu. Suspendisse odio orci, congue sit amet elementum sed, vulputate in massa. Ut eget nisl metus, et adipiscing augue. Proin egestas porta arcu vitae feugiat. Donec et lacus tortor. Aliquam ornare velit tempor urna consectetur laoreet. Quisque congue ante vel sem ultricies ullamcorper.

                Duis posuere mauris quis turpis ornare lobortis sed quis leo. Morbi rhoncus lorem ac erat porttitor consectetur. Nullam condimentum libero sit amet sapien hendrerit placerat. Quisque at neque at turpis gravida hendrerit placerat quis neque. Sed dictum ipsum nisi, non placerat nisi. Cras elementum, eros at tempus tristique, lectus mi scelerisque tortor, sit amet pretium odio turpis sed risus. Vestibulum rutrum commodo porta. Phasellus sagittis mattis pretium. Curabitur in auctor libero. Curabitur rutrum dignissim ipsum et scelerisque. Nullam fermentum vehicula lectus eget tristique. Duis lacus nisl, ultricies vel fringilla at, iaculis a risus. Maecenas in eleifend massa.

                Nunc sit amet lorem turpis, sed gravida ante. Duis semper, nunc in condimentum imperdiet, eros tellus scelerisque lectus, vitae viverra justo libero a justo. In magna justo, blandit sed commodo non, porta ac urna. Vivamus in lacus mi, at scelerisque tortor. Nullam velit felis, convallis sit amet ullamcorper ut, consequat ut lacus. Aenean id porta lectus. Maecenas rutrum ante justo. Mauris dapibus adipiscing elementum. Fusce imperdiet neque dignissim ipsum sagittis fermentum. Nulla facilisi.

                Duis convallis tempus felis, eget sodales orci euismod sit amet. Ut at velit ipsum. Donec id nisl at turpis mollis rutrum. Vivamus faucibus, ipsum volutpat lacinia tincidunt, justo dui elementum felis, faucibus bibendum nibh nisi nec nulla. Mauris nisi arcu, dignissim non ultrices quis, ultricies rhoncus leo. Suspendisse varius volutpat odio euismod rhoncus. In ac sem vel nisl convallis varius. Nullam nisi erat, accumsan nec porta vel, blandit at leo. Mauris eu lorem laoreet sem faucibus auctor. Praesent viverra, enim id feugiat tincidunt, eros urna dapibus enim, vel adipiscing eros felis et neque. Nulla eu cursus velit. Ut at tellus nunc, eget feugiat erat. Ut nec magna blandit risus ornare vulputate a eget diam.

                Aenean posuere, purus ac cursus pulvinar, turpis eros condimentum sem, sit amet pulvinar purus lacus ut velit. Nulla tristique vestibulum nisl, a posuere ante tincidunt a. Fusce porta vestibulum felis, in eleifend sem faucibus eu. Duis bibendum suscipit dolor et mollis. Integer eget nulla eu augue mollis sagittis. Nam vel tempus odio. Nulla facilisi. Duis fermentum tortor vitae risus porta cursus. Morbi ultrices luctus lorem vitae pharetra. Integer pulvinar dui sed erat ultricies vehicula. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vestibulum pharetra pharetra ante at pellentesque. Etiam at convallis orci. Duis semper lorem quis ipsum hendrerit et interdum ligula vestibulum.

                Praesent accumsan nulla quis arcu ornare iaculis. Vestibulum in enim at arcu malesuada lacinia non sed neque. Maecenas vel velit sed lectus sagittis porttitor non a turpis. Cras eu tortor quis leo dapibus sagittis sed vehicula mauris. In luctus elementum urna sit amet sodales. Aliquam erat volutpat. Mauris sit amet tincidunt ipsum. Nam arcu lorem, vehicula rutrum dignissim non, pellentesque a arcu. Ut nec leo dui, vitae molestie lorem. Nam nisl tellus, tincidunt mollis facilisis ornare, suscipit vel mauris. Morbi pellentesque ullamcorper augue nec vestibulum. Mauris ac ipsum eget nibh tempus consequat id non ante. Etiam ligula magna, posuere a suscipit et, molestie at erat. Integer mi eros, dignissim non posuere eget, convallis ut magna. Proin vestibulum feugiat eros, id tempor dui rutrum non. Aenean auctor congue dignissim.

                Cras suscipit felis sit amet urna bibendum vel aliquet nibh tincidunt. Fusce eget velit sed diam interdum fringilla. Fusce mauris massa, pharetra quis vehicula eu, condimentum non neque. Mauris non odio metus. Integer nec purus lacus. Donec elit felis, bibendum in ultrices sed, elementum non arcu. In quis libero at turpis semper egestas. Duis dapibus lectus a urna cursus volutpat. Praesent rutrum imperdiet egestas. Mauris pulvinar lacus sed mauris dictum pellentesque. Suspendisse dictum, risus et pellentesque congue, nisi turpis suscipit nisl, non porta velit magna non ipsum. Etiam eget tellus sit amet sem lobortis mattis in ac nisi.

                Donec tristique rhoncus tellus ac pharetra. Nulla pellentesque lorem est, consequat pellentesque erat. Phasellus non nunc a sem egestas pellentesque vitae dapibus augue. Mauris vestibulum, augue ac blandit aliquam, sem justo varius dolor, et condimentum augue magna ac nulla. Ut sed nisl lorem, vel laoreet est. Donec lacus magna, dapibus ac auctor eget, imperdiet non dui. Suspendisse tristique luctus sagittis. Nulla sagittis odio eu felis facilisis suscipit non sed tortor. Nunc nulla sapien, cursus non faucibus et, luctus nec lectus. Integer euismod volutpat dolor suscipit semper. Proin blandit imperdiet tincidunt. Aenean placerat, purus vitae elementum venenatis, nunc libero fringilla mauris, et varius erat ligula eu dolor. Sed purus augue, convallis sit amet bibendum vitae, bibendum et metus. Suspendisse tempus quam ut odio dapibus ac tristique mauris varius. Mauris ut enim vulputate ante viverra interdum. Mauris sed mi ipsum.

                Suspendisse quis risus ut eros luctus rhoncus nec et ligula. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec semper viverra pellentesque. Praesent eros velit, elementum eget feugiat fringilla, interdum eu mauris. Donec nisi ante, lacinia non pellentesque in, accumsan eget mauris. Phasellus suscipit mauris sit amet lectus dapibus dignissim. In sed ullamcorper lectus. Sed sit amet sapien ligula, non venenatis lorem. In turpis ligula, posuere vitae imperdiet eu, aliquet a quam. Nullam accumsan dignissim leo. Cras blandit ultricies pharetra. In eu tortor quis metus cursus placerat. Vestibulum varius euismod nulla, placerat sodales mi viverra id. Aliquam erat volutpat.

                Duis eu quam nec metus consequat malesuada. Sed at lectus nisi. Nunc mauris lectus, commodo a condimentum in, commodo eget mi. Quisque vulputate rutrum purus ut lobortis. Curabitur sagittis ligula non magna iaculis id fermentum ante bibendum. Proin mollis ipsum in massa cursus convallis. Cras eleifend fermentum velit, eget vestibulum diam viverra vitae. Praesent purus diam, iaculis interdum tincidunt non, ultrices id mi.

                Maecenas gravida, dui id varius egestas, nisi purus feugiat enim, quis semper nunc massa et est. Aliquam convallis ante eros, in posuere erat. Nunc ut sagittis lorem. Ut non cursus sapien. Donec nisl tortor, commodo ut commodo sit amet, volutpat et dui. Maecenas laoreet ligula at augue tincidunt pellentesque tincidunt nulla consectetur. Praesent vestibulum, est ut tincidunt dignissim, urna lectus commodo neque, id aliquam leo risus rhoncus leo. Sed ligula lacus, fringilla vitae mattis a, malesuada non purus. Duis id magna quis tortor consectetur vulputate sit amet id justo. Vestibulum fermentum ligula non quam porta a posuere neque rhoncus. In at purus nunc. Integer ornare vestibulum nisl, a elementum nibh tincidunt in. Duis porta nisl nisi. Sed volutpat pulvinar dui in tempus.

                Mauris arcu nisl, sollicitudin ornare scelerisque sit amet, suscipit ut metus. In hac habitasse platea dictumst. Donec porttitor nibh a massa lacinia nec imperdiet lectus eleifend. Quisque ultricies nibh ac sem faucibus mattis. Mauris enim augue, rhoncus et mollis at, congue vitae sapien. Vivamus luctus feugiat euismod. Donec metus libero, tempus vitae posuere non, posuere vitae magna. Sed et nunc orci. Nullam in erat dui. In hac habitasse platea dictumst. Maecenas sollicitudin sapien id augue malesuada porta. Nullam id lorem ac leo feugiat laoreet nec ac orci. In eget nunc enim, quis pretium ante. Nam vestibulum purus ut dolor tristique aliquam.

                Vestibulum in enim nisl. In pretium, diam sed lacinia facilisis, augue felis dictum diam, vitae ullamcorper orci odio a nunc. Morbi porttitor, est a aliquam faucibus, urna nulla consequat augue, pulvinar imperdiet purus justo eu augue. Maecenas porta libero quis nulla euismod et cursus lorem ullamcorper. In nulla neque, eleifend vel porttitor id, consequat a neque. Etiam pretium rhoncus sapien, sit amet bibendum nibh adipiscing in. Cras dapibus orci nec neque vehicula vulputate. Curabitur congue, felis lacinia convallis porta, neque turpis eleifend nunc, at scelerisque nibh est sed leo.

                Ut id nibh vitae augue facilisis convallis. Sed quis augue lacus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. In molestie justo non felis sodales convallis. Maecenas sit amet nisl blandit diam sollicitudin dignissim. Proin imperdiet mattis ante non malesuada. Proin pharetra pharetra justo. Fusce at elit vitae tortor fringilla tincidunt. Nulla ut fringilla mi. Nulla ultrices massa eget odio scelerisque lobortis. Pellentesque a lorem felis. Aenean eget porta urna.

                Vestibulum lacinia bibendum lectus, ultrices tempus nibh aliquam et. Phasellus a convallis erat. Quisque mollis, augue nec tincidunt mollis, purus ipsum ullamcorper lorem, quis auctor tortor metus et lectus. In vitae metus nec diam lacinia euismod. Maecenas vitae vulputate risus. Nullam eget quam vel risus malesuada laoreet. Praesent malesuada justo ac augue porttitor sed accumsan elit congue. Nam eget tellus quis est convallis tempus. Vestibulum sit amet lorem a est hendrerit sodales. Sed nec dictum magna. Duis pretium viverra dolor, in rhoncus diam euismod sed. Etiam vitae felis ac justo tincidunt mattis ac nec risus. Vestibulum varius imperdiet turpis sed facilisis. Nullam arcu dolor, aliquam in consectetur sit amet, scelerisque in turpis.

                Proin consectetur commodo justo, vitae convallis magna laoreet ut. Nulla eget risus eget velit consequat dignissim. Nunc vitae sem turpis. Morbi vestibulum malesuada ante at rhoncus. Ut faucibus lectus ut sapien tincidunt nec facilisis neque interdum. Quisque velit nulla, ornare et semper nec, scelerisque vitae ipsum. Nullam et pretium erat. Aliquam facilisis tincidunt nunc eu placerat. Mauris cursus dui in risus convallis lobortis. Mauris nec tortor lectus, a laoreet massa. Phasellus a erat metus. Etiam nisl nulla, sollicitudin a iaculis ut, tristique quis augue. Suspendisse dui est, ullamcorper id porta ac, aliquam quis erat.

                Nullam venenatis varius laoreet. Donec pellentesque justo at quam facilisis mattis fringilla risus sagittis. Cras dui ante, sollicitudin faucibus lobortis a, interdum vitae augue. Donec dui felis, viverra a semper nec, gravida sed augue. Nulla justo sem, convallis et porta vitae, placerat ac magna. Nullam a turpis in ipsum hendrerit dictum id at erat. Nullam lacinia iaculis risus, a mattis diam hendrerit in. Suspendisse dictum lobortis iaculis. In vulputate lectus a massa gravida venenatis. Aenean porttitor condimentum posuere.

                Nulla facilisi. Praesent vel risus id mauris malesuada vestibulum eget vel ligula. Etiam dapibus ultrices urna, nec auctor turpis aliquam non. Nam erat quam, sagittis nec faucibus in, fringilla vel sapien. Quisque commodo, eros sed elementum rutrum, tortor tortor viverra sem, non egestas augue mauris non libero. Cras tristique tortor et libero vehicula eu venenatis velit pharetra. Donec sagittis ornare libero, et interdum odio volutpat at. Donec non tellus et mauris lacinia pretium vitae in lorem. Nam tellus velit, mollis eget auctor non, luctus a tellus. Suspendisse potenti. Suspendisse posuere metus ipsum.

                Proin faucibus, dolor iaculis volutpat viverra, arcu nisi accumsan turpis, nec ultrices nibh nibh a tellus. Nullam a neque id diam lobortis dictum quis lobortis velit. Nunc congue aliquam facilisis. Ut interdum, tortor ut volutpat rutrum, enim nisi tincidunt tortor, ac venenatis libero tortor in erat. Cras at justo ut felis molestie rutrum. Nam justo nunc, vulputate et sollicitudin sed, vestibulum in neque. Nunc laoreet varius nulla.

                Nullam eu nisi non lorem fringilla luctus eu ac magna. Nulla rutrum ante eget magna fermentum consequat. Suspendisse hendrerit lacus vulputate turpis dictum lacinia. Nam placerat nisl in ante gravida rutrum. Sed sed eros libero, et tincidunt libero. Duis placerat sollicitudin bibendum. Nullam vitae nulla diam, ac luctus nisi. Ut dictum nunc ac purus semper posuere. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque non ipsum nec risus porttitor sodales. Quisque odio dolor, malesuada quis faucibus at, laoreet semper elit. Nullam ac ipsum eu mauris hendrerit pulvinar eu at magna. Mauris eget ante libero, non porttitor arcu.

                Vestibulum accumsan tempus venenatis. Nam massa quam, fermentum vitae aliquam at, vehicula non neque. Phasellus tristique dui at felis euismod porta. Fusce nisl magna, tempor mollis adipiscing sed, convallis at lorem. Suspendisse velit nulla, adipiscing eget gravida ut, sodales ut erat. Nulla facilisi. Etiam et tempus diam. Nam vulputate molestie laoreet.

                Sed nunc nulla, suscipit et fermentum ac, tempor sed magna. Donec libero lorem, tristique sit amet commodo vitae, dictum in felis. Proin sem orci, tempor sit amet adipiscing vitae, blandit blandit neque. Quisque eget massa dui, eget mattis turpis. Proin quis tellus vitae felis laoreet laoreet vitae vitae risus. Cras dictum semper vehicula. Integer vitae libero ante.

                Fusce vitae metus nulla, sed euismod felis. Nulla pulvinar egestas tincidunt. Nam vehicula malesuada urna, ac fringilla purus euismod quis. Curabitur sed metus eu orci rutrum tincidunt. Nam elementum, nibh in suscipit egestas, ligula dolor fringilla diam, et mollis tortor leo eu urna. Aenean egestas mauris ut arcu gravida vel volutpat felis consectetur. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Ut pretium vulputate felis, sit amet varius lectus pellentesque non.

                Nulla facilisi. Nunc ornare tellus non dolor consectetur accumsan. Aenean tincidunt, nibh aliquam interdum bibendum, erat lacus ultrices libero, quis vehicula ligula dolor eget arcu. Sed rhoncus nulla eget justo viverra vitae rutrum nunc porttitor. Proin id neque risus. Maecenas vestibulum purus eget diam ullamcorper vel aliquam lacus imperdiet. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nulla nec libero ac mauris malesuada rutrum sit amet vitae erat. Nam tellus est, tempor ut egestas sit amet, tempor ut lorem. Suspendisse fermentum ornare metus sed tincidunt. Nulla mattis bibendum sapien, ac vehicula lorem mattis in. Maecenas adipiscing elementum consectetur. In vel leo augue, nec tincidunt purus. Sed id gravida massa.

                Curabitur eros enim, feugiat sed eleifend quis, fringilla et metus. Quisque lacinia lacus non lorem commodo sed varius tellus cursus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Mauris egestas consectetur massa, vulputate tincidunt magna vehicula id. Pellentesque eget dolor vel purus semper volutpat eu at nisl. Ut sollicitudin nisi sit amet odio auctor fringilla. Sed eu nisi sed ipsum hendrerit varius. Sed tempus dignissim tincidunt. Curabitur laoreet tortor quis urna tempor fermentum. Ut quis libero vitae nunc cursus hendrerit. Integer et interdum lacus.

                Donec ipsum velit, elementum quis porta ut, condimentum non dolor. Phasellus posuere dignissim leo, quis mollis lectus egestas vel. Phasellus adipiscing faucibus dictum. Vestibulum eu felis eros. Maecenas eu sapien turpis. Quisque volutpat aliquam sem laoreet porta. Nam varius feugiat dolor sit amet ornare. Sed vitae viverra velit. Curabitur non turpis nec augue placerat euismod mattis nec nulla. Morbi aliquet nibh nec leo vehicula vulputate. Maecenas vel lacinia est. Curabitur augue elit, fringilla placerat auctor id, luctus quis eros.

                Vestibulum ultrices iaculis tristique. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam vel enim dui, et consequat magna. In vitae mi eget magna pulvinar pretium. Etiam quis turpis nunc. Nam eget velit eros. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Integer nunc elit, semper vel pellentesque et, faucibus ut metus. Nunc mollis dolor quis sem tempor in dictum est scelerisque. Nunc vel risus nisl, vitae bibendum nulla. Suspendisse tellus erat, dignissim imperdiet ultricies ac, malesuada sit amet nunc.

                Praesent nec turpis iaculis elit adipiscing semper. In feugiat, odio a tempus fermentum, nisl metus fringilla nulla, interdum fringilla felis odio et mauris. Cras consequat leo sed felis interdum eu mattis enim tristique. Suspendisse vel orci sapien, ut varius odio. Curabitur ut arcu elit. Cras purus ligula, sodales sit amet viverra et, iaculis at augue. Praesent vehicula vulputate porta. Pellentesque lectus mi, ornare ut ultricies eu, elementum sed purus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Phasellus laoreet, eros in volutpat fringilla, quam justo suscipit leo, vitae interdum magna nisi vel odio.

                Ut fermentum lorem quis ipsum lobortis bibendum. Pellentesque viverra augue vitae libero euismod volutpat. Nullam ac ipsum eget velit fringilla porta hendrerit nec nisl. Nunc vitae sem enim, quis tempor ipsum. Quisque vel diam eu metus blandit sagittis vitae sed eros. Vestibulum placerat elit et magna pretium dignissim. Integer luctus ligula vel dui mollis id porta purus ornare. Suspendisse eget dui mi. Quisque magna felis, volutpat eu posuere non, blandit id leo. Donec vel nisi nisl, eget lacinia odio. Phasellus sit amet massa vitae nibh congue fringilla sed nec felis.

                Mauris neque dolor, semper id porta vitae, rhoncus in felis. Proin porttitor, dui a auctor sagittis, nulla dolor interdum lacus, quis auctor nisi erat id nunc. Donec feugiat risus eget magna commodo id malesuada eros mollis. Suspendisse enim lacus, ultrices id malesuada nec, fringilla in augue. Ut ultrices dapibus tortor, sit amet dapibus quam condimentum ac. Aenean et nisi in velit luctus tempor. Integer sed risus justo, vel gravida arcu. Duis eleifend massa a nunc molestie tristique ornare nulla iaculis. Praesent vel nisi ante, id consequat diam.

                Nulla facilisi. Duis eros risus, euismod non facilisis et, pharetra eget orci. Praesent ultricies malesuada lectus, semper faucibus nibh ornare eu. Donec condimentum porta mollis. Vivamus sodales aliquet erat sit amet tempor. Nulla non dictum nisl. Suspendisse eleifend metus quis tellus mattis eget vulputate sapien scelerisque. Quisque dui ligula, accumsan cursus imperdiet nec, porttitor sed est.

                Donec purus erat, lobortis a convallis eu, euismod volutpat lacus. Pellentesque bibendum egestas ligula, in commodo est congue a. Cras sagittis viverra est, id tristique leo iaculis nec. Donec posuere sollicitudin scelerisque. Cras eros nulla, varius ut sollicitudin non, interdum at metus. Fusce nisi orci, vestibulum sed vehicula at, ullamcorper eget est. Praesent quis tortor orci, nec malesuada dui. Nullam in ipsum vel mauris gravida cursus ac nec erat.

                Nulla ut mi vitae quam euismod iaculis. Ut dapibus auctor metus, at feugiat tortor tincidunt vitae. Donec ultricies nulla eget diam faucibus ultrices posuere odio malesuada. Aenean porta dolor sed nibh rhoncus placerat. Nam vestibulum mauris in leo dictum nec volutpat lectus facilisis. Integer tristique condimentum purus, id auctor justo tristique quis. Aenean vitae lorem ac mauris suscipit gravida et quis tellus. Morbi ultricies tempus mauris id commodo. Nullam justo ligula, dictum et suscipit vitae, mollis id nunc. Maecenas lacinia, purus eget tempus pharetra, elit eros mattis felis, vel lacinia sapien massa et ligula. Morbi volutpat bibendum quam sed pulvinar. Etiam a odio mi, sit amet volutpat neque.

                Phasellus vel erat non lectus consequat vehicula at a ante. Praesent eu sagittis velit. Fusce eleifend dictum est, eu dapibus velit malesuada non. Nulla at sagittis nisl. Praesent justo tellus, semper eu vehicula vitae, fringilla adipiscing mauris. Praesent enim nulla, cursus eu tincidunt a, venenatis vel sapien. Suspendisse fringilla libero vitae libero eleifend gravida. Sed vitae nisi metus. Proin porttitor porta mattis. Aenean bibendum rhoncus enim rutrum gravida. Proin lobortis sagittis neque in euismod. Mauris ac arcu eu arcu sodales cursus. Sed posuere mauris id metus vulputate tincidunt. Sed sed elit ut est luctus eleifend. Curabitur euismod volutpat mauris, sit amet dapibus ipsum malesuada in.

                Aenean at elit orci. Mauris vel orci a nisl rhoncus lobortis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce eleifend cursus gravida. Morbi semper, felis vitae condimentum adipiscing, enim diam molestie augue, id rutrum nibh nisi nec dui. Quisque tempus feugiat elit vitae pulvinar. Fusce sit amet lorem sed metus tristique ultricies.

                Donec eu mauris elit. Vivamus quis sapien nunc. Nunc ut velit quis magna malesuada gravida non non sapien. Morbi eu neque nibh, ac placerat nulla. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Proin interdum nisi vitae ante ultricies consequat. Lorem ipsum dolor sit amet, consectetur adipiscing elit.

                Vestibulum dictum, nisi vel scelerisque commodo, purus ante accumsan nibh, eget ultrices mauris ipsum eu odio. Nulla facilisi. Etiam ac tincidunt nunc. Praesent sed urna felis. Ut eget tristique tortor. Donec ultrices diam at nisi ultricies placerat. Etiam libero orci, sodales vitae dictum posuere, tempor nec nunc. Praesent pharetra rutrum neque, sed congue tortor pellentesque id. Fusce bibendum turpis non arcu adipiscing ullamcorper non congue libero. In et volutpat felis. Aliquam iaculis dui auctor dolor placerat dapibus. Sed rhoncus erat sed purus tincidunt in mattis massa mattis. Nulla facilisi. Integer viverra rutrum condimentum. Vivamus vitae nibh ac purus dignissim tincidunt et non urna.

                Vivamus pellentesque vehicula magna venenatis interdum. Quisque fringilla quam dui. Curabitur lacinia iaculis nisi, sit amet consectetur arcu porta tempus. Suspendisse vel fringilla eros. Mauris non lacus ac felis cursus vestibulum. Maecenas in orci eget risus bibendum viverra. Nunc ac libero at felis vestibulum pulvinar. Morbi non dui ante, in egestas justo. Cras quis ante venenatis nisi molestie tincidunt a euismod tellus. Suspendisse fermentum consectetur urna vel varius. Proin tempor hendrerit leo, a aliquam velit pellentesque tempus. Nunc nulla eros, ultricies sit amet tincidunt in, ornare sodales felis. Maecenas tincidunt, nisi id lacinia convallis, nibh felis bibendum lectus, at mollis orci lectus nec velit.

                Duis ut quam vel quam consequat hendrerit tincidunt eget metus. Suspendisse vitae molestie elit. Nunc blandit venenatis leo ut vulputate. Pellentesque tempus fringilla porta. Etiam eget mattis felis. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Morbi leo sem, commodo eget semper eget, porttitor id leo. Duis pulvinar pulvinar ultricies. Proin laoreet posuere sagittis. Curabitur lectus libero, fringilla et varius ut, sodales sit amet purus. Cras molestie pulvinar magna in malesuada. Nullam tincidunt enim at tellus tempor dignissim. Aliquam et lacus massa.

                Mauris arcu neque, bibendum vel porttitor eget, hendrerit at metus. Curabitur porttitor, orci a volutpat tristique, turpis nulla pellentesque tortor, vel mattis libero dui nec velit. Proin aliquam enim sed ligula mollis tristique. Aenean eget urna turpis. Etiam volutpat, massa sed tincidunt eleifend, nulla augue lacinia mauris, quis rhoncus libero erat aliquet est. Fusce vel ipsum mattis quam sagittis interdum. Phasellus ac turpis at justo convallis mollis sit amet sed tortor. Donec ac nunc urna, vitae laoreet mauris. Aenean dolor neque, ultricies eget sodales at, bibendum at erat.

                Proin sollicitudin scelerisque orci, at vestibulum magna consectetur et. Pellentesque sit amet ligula dolor. Duis dignissim blandit arcu, a vulputate nisi vehicula at. Sed sit amet velit vel justo pellentesque consequat quis vitae nulla. Nullam ornare tellus nisi. Nunc dignissim magna nisl, sit amet adipiscing turpis. Nulla vel purus vehicula enim dictum aliquam. Donec nisi diam, condimentum eu pharetra nec, ultrices vel lacus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec eget turpis sapien, at adipiscing erat. Curabitur sit amet turpis lacus, non pretium nibh. In hac habitasse platea dictumst. Proin sit amet tellus vel nibh pellentesque lobortis. Vivamus dignissim purus suscipit neque eleifend eget faucibus ipsum fringilla. Aenean nec quam lobortis purus aliquam viverra a eu dui.

                Nulla adipiscing, dui ac varius accumsan, purus lorem aliquet dui, non porttitor libero leo imperdiet mauris. Mauris eleifend porta risus, sit amet pharetra diam luctus quis. Nunc pellentesque blandit massa, vulputate sagittis magna sagittis a. Nulla sagittis euismod odio ut feugiat. Suspendisse non magna ut nisl congue consequat. Maecenas nisi tortor, tincidunt vel fringilla id, dictum vitae augue. Donec cursus nibh in est volutpat posuere. Ut dolor tortor, elementum a sodales in, consectetur scelerisque sem. Mauris non purus neque. Ut dictum ligula id est venenatis a tincidunt enim vulputate. Nullam imperdiet lobortis lectus, ac viverra tellus facilisis et. In lobortis diam lectus.

                Mauris fermentum dapibus urna quis adipiscing. Fusce at lorem lectus. Nullam eu orci justo. Sed orci dolor, congue et suscipit id, elementum sed ante. Phasellus vehicula eros vitae risus interdum pharetra. Nam lectus elit, suscipit in rutrum vel, rhoncus in elit. Nullam dignissim nunc nibh, et aliquet felis. Sed vulputate, ipsum eget facilisis scelerisque, lacus felis feugiat massa, eget interdum nisi sapien eget massa. Vestibulum leo metus, molestie non pulvinar nec, condimentum non diam.

                Quisque sagittis commodo molestie. Donec venenatis ante a purus elementum vestibulum. Maecenas laoreet, tortor non dapibus luctus, odio risus mollis risus, sed luctus diam libero eu felis. Suspendisse potenti. Sed vel quam mi, non placerat neque. In hac habitasse platea dictumst. Integer sed neque a arcu elementum semper. Donec vel velit erat. Nunc posuere, nisl semper pellentesque dignissim, quam purus varius urna, at ultrices dui risus in dolor. Vivamus at dui enim, et imperdiet risus. Vestibulum nisi nisi, faucibus sit amet venenatis vitae, pharetra at dui. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur eu mauris nulla. Nunc diam erat, placerat at commodo porta, faucibus a turpis. Morbi sodales nisi id ante imperdiet adipiscing.

                Fusce metus tellus, luctus placerat vehicula et, fermentum ut sem. Donec ac lorem risus, sit amet lobortis dui. Mauris vel metus ac urna mollis gravida id id justo. Proin id justo felis. Vivamus at vehicula massa. Proin interdum ornare congue. Mauris pellentesque faucibus mi sed vestibulum. Mauris enim sem, vestibulum ut placerat at, rhoncus sit amet metus. Nulla neque dolor, malesuada ac luctus sit amet, imperdiet quis libero. Nunc facilisis erat at turpis vulputate aliquet.

                Sed dignissim convallis interdum. Curabitur ligula ligula, lacinia in accumsan at, pharetra tempor sapien. Quisque quis augue ac magna aliquet fermentum ultrices vitae mauris. Phasellus nec dolor hendrerit dolor vestibulum placerat nec ut lacus. Phasellus mattis suscipit varius. Ut egestas tortor sit amet augue cursus ut volutpat magna scelerisque. Aenean pulvinar, lacus a hendrerit gravida, tellus neque condimentum lorem, eget semper purus massa eget neque. Nam pretium lacus in quam feugiat fringilla aliquet lectus egestas. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Pellentesque elementum consequat feugiat. Quisque id diam justo, eget euismod augue. Integer tristique vestibulum sollicitudin. Quisque eu orci nec magna vestibulum placerat in volutpat odio. Integer in orci non dolor ornare viverra. Nulla facilisi. Fusce volutpat mollis urna, vel ultrices libero porttitor a.</text>
			<text_parsed><![CDATA[<?php /* Cached: August 9, 2012, 7:18 am */ ?>
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque id ipsum nisl. Nam vitae ligula turpis, vel egestas turpis. Curabitur condimentum metus ac ligula pulvinar volutpat. Nullam mollis nulla eu ligula volutpat pellentesque. Pellentesque sit amet nisl metus, et placerat elit. Mauris ac justo est, at malesuada mauris. Etiam auctor pharetra mollis. Vivamus lobortis, sem sit amet porta suscipit, augue libero consectetur justo, a sollicitudin risus eros et est. Vivamus eget lectus tellus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Aenean vel ullamcorper mauris.
<br class="pf_break" />
<br class="pf_break" />Ut non consequat risus. Phasellus eget ligula vel enim pretium volutpat. Phasellus rutrum porttitor lorem. In accumsan pharetra sapien, in porta augue accumsan a. Fusce pellentesque egestas euismod. Donec porta dapibus urna eu varius. Pellentesque aliquet dapibus turpis, ut sollicitudin elit convallis eget. Curabitur ornare, sapien nec rhoncus bibendum, lacus libero tristique dui, in interdum massa purus ut quam. Morbi est turpis, feugiat non porttitor sed, adipiscing sed ligula.
<br class="pf_break" />
<br class="pf_break" />Quisque ac tempus ipsum. Praesent tempus convallis enim in suscipit. Nulla eu ipsum nec nisl tempus vestibulum. Fusce rutrum placerat tortor, vel ultricies sem ultrices sit amet. Proin elementum convallis neque eu sodales. Vivamus turpis massa, sodales sed volutpat consequat, feugiat non ante. Phasellus vel blandit nunc. Quisque nec ligula orci. Proin luctus interdum diam eu mattis. Maecenas nec posuere nunc. Duis a purus lacus. Quisque sit amet enim lacus. Praesent molestie, arcu id pharetra sollicitudin, est diam mattis erat, vel volutpat mauris ante nec lorem. In eget posuere sapien.
<br class="pf_break" />
<br class="pf_break" />Donec felis tellus, adipiscing viverra volutpat vel, luctus sed felis. Morbi ultricies ante in mauris ultrices ullamcorper. Vivamus justo est, suscipit eget convallis quis, dapibus nec lacus. Maecenas vel urna ac lacus adipiscing molestie nec id quam. Aliquam faucibus rutrum nisl, vitae faucibus felis tincidunt eget. Aliquam sit amet varius augue. In elementum sodales sapien id laoreet. Ut mattis laoreet neque, quis tincidunt leo mattis sed.
<br class="pf_break" />
<br class="pf_break" />Cras lacinia elementum auctor. Proin ante lacus, lobortis viverra tincidunt vitae, ullamcorper in nisi. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Phasellus condimentum gravida lorem, eget lacinia mauris sodales eu. Suspendisse odio orci, congue sit amet elementum sed, vulputate in massa. Ut eget nisl metus, et adipiscing augue. Proin egestas porta arcu vitae feugiat. Donec et lacus tortor. Aliquam ornare velit tempor urna consectetur laoreet. Quisque congue ante vel sem ultricies ullamcorper.
<br class="pf_break" />
<br class="pf_break" />Duis posuere mauris quis turpis ornare lobortis sed quis leo. Morbi rhoncus lorem ac erat porttitor consectetur. Nullam condimentum libero sit amet sapien hendrerit placerat. Quisque at neque at turpis gravida hendrerit placerat quis neque. Sed dictum ipsum nisi, non placerat nisi. Cras elementum, eros at tempus tristique, lectus mi scelerisque tortor, sit amet pretium odio turpis sed risus. Vestibulum rutrum commodo porta. Phasellus sagittis mattis pretium. Curabitur in auctor libero. Curabitur rutrum dignissim ipsum et scelerisque. Nullam fermentum vehicula lectus eget tristique. Duis lacus nisl, ultricies vel fringilla at, iaculis a risus. Maecenas in eleifend massa.
<br class="pf_break" />
<br class="pf_break" />Nunc sit amet lorem turpis, sed gravida ante. Duis semper, nunc in condimentum imperdiet, eros tellus scelerisque lectus, vitae viverra justo libero a justo. In magna justo, blandit sed commodo non, porta ac urna. Vivamus in lacus mi, at scelerisque tortor. Nullam velit felis, convallis sit amet ullamcorper ut, consequat ut lacus. Aenean id porta lectus. Maecenas rutrum ante justo. Mauris dapibus adipiscing elementum. Fusce imperdiet neque dignissim ipsum sagittis fermentum. Nulla facilisi.
<br class="pf_break" />
<br class="pf_break" />Duis convallis tempus felis, eget sodales orci euismod sit amet. Ut at velit ipsum. Donec id nisl at turpis mollis rutrum. Vivamus faucibus, ipsum volutpat lacinia tincidunt, justo dui elementum felis, faucibus bibendum nibh nisi nec nulla. Mauris nisi arcu, dignissim non ultrices quis, ultricies rhoncus leo. Suspendisse varius volutpat odio euismod rhoncus. In ac sem vel nisl convallis varius. Nullam nisi erat, accumsan nec porta vel, blandit at leo. Mauris eu lorem laoreet sem faucibus auctor. Praesent viverra, enim id feugiat tincidunt, eros urna dapibus enim, vel adipiscing eros felis et neque. Nulla eu cursus velit. Ut at tellus nunc, eget feugiat erat. Ut nec magna blandit risus ornare vulputate a eget diam.
<br class="pf_break" />
<br class="pf_break" />Aenean posuere, purus ac cursus pulvinar, turpis eros condimentum sem, sit amet pulvinar purus lacus ut velit. Nulla tristique vestibulum nisl, a posuere ante tincidunt a. Fusce porta vestibulum felis, in eleifend sem faucibus eu. Duis bibendum suscipit dolor et mollis. Integer eget nulla eu augue mollis sagittis. Nam vel tempus odio. Nulla facilisi. Duis fermentum tortor vitae risus porta cursus. Morbi ultrices luctus lorem vitae pharetra. Integer pulvinar dui sed erat ultricies vehicula. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Vestibulum pharetra pharetra ante at pellentesque. Etiam at convallis orci. Duis semper lorem quis ipsum hendrerit et interdum ligula vestibulum.
<br class="pf_break" />
<br class="pf_break" />Praesent accumsan nulla quis arcu ornare iaculis. Vestibulum in enim at arcu malesuada lacinia non sed neque. Maecenas vel velit sed lectus sagittis porttitor non a turpis. Cras eu tortor quis leo dapibus sagittis sed vehicula mauris. In luctus elementum urna sit amet sodales. Aliquam erat volutpat. Mauris sit amet tincidunt ipsum. Nam arcu lorem, vehicula rutrum dignissim non, pellentesque a arcu. Ut nec leo dui, vitae molestie lorem. Nam nisl tellus, tincidunt mollis facilisis ornare, suscipit vel mauris. Morbi pellentesque ullamcorper augue nec vestibulum. Mauris ac ipsum eget nibh tempus consequat id non ante. Etiam ligula magna, posuere a suscipit et, molestie at erat. Integer mi eros, dignissim non posuere eget, convallis ut magna. Proin vestibulum feugiat eros, id tempor dui rutrum non. Aenean auctor congue dignissim.
<br class="pf_break" />
<br class="pf_break" />Cras suscipit felis sit amet urna bibendum vel aliquet nibh tincidunt. Fusce eget velit sed diam interdum fringilla. Fusce mauris massa, pharetra quis vehicula eu, condimentum non neque. Mauris non odio metus. Integer nec purus lacus. Donec elit felis, bibendum in ultrices sed, elementum non arcu. In quis libero at turpis semper egestas. Duis dapibus lectus a urna cursus volutpat. Praesent rutrum imperdiet egestas. Mauris pulvinar lacus sed mauris dictum pellentesque. Suspendisse dictum, risus et pellentesque congue, nisi turpis suscipit nisl, non porta velit magna non ipsum. Etiam eget tellus sit amet sem lobortis mattis in ac nisi.
<br class="pf_break" />
<br class="pf_break" />Donec tristique rhoncus tellus ac pharetra. Nulla pellentesque lorem est, consequat pellentesque erat. Phasellus non nunc a sem egestas pellentesque vitae dapibus augue. Mauris vestibulum, augue ac blandit aliquam, sem justo varius dolor, et condimentum augue magna ac nulla. Ut sed nisl lorem, vel laoreet est. Donec lacus magna, dapibus ac auctor eget, imperdiet non dui. Suspendisse tristique luctus sagittis. Nulla sagittis odio eu felis facilisis suscipit non sed tortor. Nunc nulla sapien, cursus non faucibus et, luctus nec lectus. Integer euismod volutpat dolor suscipit semper. Proin blandit imperdiet tincidunt. Aenean placerat, purus vitae elementum venenatis, nunc libero fringilla mauris, et varius erat ligula eu dolor. Sed purus augue, convallis sit amet bibendum vitae, bibendum et metus. Suspendisse tempus quam ut odio dapibus ac tristique mauris varius. Mauris ut enim vulputate ante viverra interdum. Mauris sed mi ipsum.
<br class="pf_break" />
<br class="pf_break" />Suspendisse quis risus ut eros luctus rhoncus nec et ligula. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec semper viverra pellentesque. Praesent eros velit, elementum eget feugiat fringilla, interdum eu mauris. Donec nisi ante, lacinia non pellentesque in, accumsan eget mauris. Phasellus suscipit mauris sit amet lectus dapibus dignissim. In sed ullamcorper lectus. Sed sit amet sapien ligula, non venenatis lorem. In turpis ligula, posuere vitae imperdiet eu, aliquet a quam. Nullam accumsan dignissim leo. Cras blandit ultricies pharetra. In eu tortor quis metus cursus placerat. Vestibulum varius euismod nulla, placerat sodales mi viverra id. Aliquam erat volutpat.
<br class="pf_break" />
<br class="pf_break" />Duis eu quam nec metus consequat malesuada. Sed at lectus nisi. Nunc mauris lectus, commodo a condimentum in, commodo eget mi. Quisque vulputate rutrum purus ut lobortis. Curabitur sagittis ligula non magna iaculis id fermentum ante bibendum. Proin mollis ipsum in massa cursus convallis. Cras eleifend fermentum velit, eget vestibulum diam viverra vitae. Praesent purus diam, iaculis interdum tincidunt non, ultrices id mi.
<br class="pf_break" />
<br class="pf_break" />Maecenas gravida, dui id varius egestas, nisi purus feugiat enim, quis semper nunc massa et est. Aliquam convallis ante eros, in posuere erat. Nunc ut sagittis lorem. Ut non cursus sapien. Donec nisl tortor, commodo ut commodo sit amet, volutpat et dui. Maecenas laoreet ligula at augue tincidunt pellentesque tincidunt nulla consectetur. Praesent vestibulum, est ut tincidunt dignissim, urna lectus commodo neque, id aliquam leo risus rhoncus leo. Sed ligula lacus, fringilla vitae mattis a, malesuada non purus. Duis id magna quis tortor consectetur vulputate sit amet id justo. Vestibulum fermentum ligula non quam porta a posuere neque rhoncus. In at purus nunc. Integer ornare vestibulum nisl, a elementum nibh tincidunt in. Duis porta nisl nisi. Sed volutpat pulvinar dui in tempus.
<br class="pf_break" />
<br class="pf_break" />Mauris arcu nisl, sollicitudin ornare scelerisque sit amet, suscipit ut metus. In hac habitasse platea dictumst. Donec porttitor nibh a massa lacinia nec imperdiet lectus eleifend. Quisque ultricies nibh ac sem faucibus mattis. Mauris enim augue, rhoncus et mollis at, congue vitae sapien. Vivamus luctus feugiat euismod. Donec metus libero, tempus vitae posuere non, posuere vitae magna. Sed et nunc orci. Nullam in erat dui. In hac habitasse platea dictumst. Maecenas sollicitudin sapien id augue malesuada porta. Nullam id lorem ac leo feugiat laoreet nec ac orci. In eget nunc enim, quis pretium ante. Nam vestibulum purus ut dolor tristique aliquam.
<br class="pf_break" />
<br class="pf_break" />Vestibulum in enim nisl. In pretium, diam sed lacinia facilisis, augue felis dictum diam, vitae ullamcorper orci odio a nunc. Morbi porttitor, est a aliquam faucibus, urna nulla consequat augue, pulvinar imperdiet purus justo eu augue. Maecenas porta libero quis nulla euismod et cursus lorem ullamcorper. In nulla neque, eleifend vel porttitor id, consequat a neque. Etiam pretium rhoncus sapien, sit amet bibendum nibh adipiscing in. Cras dapibus orci nec neque vehicula vulputate. Curabitur congue, felis lacinia convallis porta, neque turpis eleifend nunc, at scelerisque nibh est sed leo.
<br class="pf_break" />
<br class="pf_break" />Ut id nibh vitae augue facilisis convallis. Sed quis augue lacus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. In molestie justo non felis sodales convallis. Maecenas sit amet nisl blandit diam sollicitudin dignissim. Proin imperdiet mattis ante non malesuada. Proin pharetra pharetra justo. Fusce at elit vitae tortor fringilla tincidunt. Nulla ut fringilla mi. Nulla ultrices massa eget odio scelerisque lobortis. Pellentesque a lorem felis. Aenean eget porta urna.
<br class="pf_break" />
<br class="pf_break" />Vestibulum lacinia bibendum lectus, ultrices tempus nibh aliquam et. Phasellus a convallis erat. Quisque mollis, augue nec tincidunt mollis, purus ipsum ullamcorper lorem, quis auctor tortor metus et lectus. In vitae metus nec diam lacinia euismod. Maecenas vitae vulputate risus. Nullam eget quam vel risus malesuada laoreet. Praesent malesuada justo ac augue porttitor sed accumsan elit congue. Nam eget tellus quis est convallis tempus. Vestibulum sit amet lorem a est hendrerit sodales. Sed nec dictum magna. Duis pretium viverra dolor, in rhoncus diam euismod sed. Etiam vitae felis ac justo tincidunt mattis ac nec risus. Vestibulum varius imperdiet turpis sed facilisis. Nullam arcu dolor, aliquam in consectetur sit amet, scelerisque in turpis.
<br class="pf_break" />
<br class="pf_break" />Proin consectetur commodo justo, vitae convallis magna laoreet ut. Nulla eget risus eget velit consequat dignissim. Nunc vitae sem turpis. Morbi vestibulum malesuada ante at rhoncus. Ut faucibus lectus ut sapien tincidunt nec facilisis neque interdum. Quisque velit nulla, ornare et semper nec, scelerisque vitae ipsum. Nullam et pretium erat. Aliquam facilisis tincidunt nunc eu placerat. Mauris cursus dui in risus convallis lobortis. Mauris nec tortor lectus, a laoreet massa. Phasellus a erat metus. Etiam nisl nulla, sollicitudin a iaculis ut, tristique quis augue. Suspendisse dui est, ullamcorper id porta ac, aliquam quis erat.
<br class="pf_break" />
<br class="pf_break" />Nullam venenatis varius laoreet. Donec pellentesque justo at quam facilisis mattis fringilla risus sagittis. Cras dui ante, sollicitudin faucibus lobortis a, interdum vitae augue. Donec dui felis, viverra a semper nec, gravida sed augue. Nulla justo sem, convallis et porta vitae, placerat ac magna. Nullam a turpis in ipsum hendrerit dictum id at erat. Nullam lacinia iaculis risus, a mattis diam hendrerit in. Suspendisse dictum lobortis iaculis. In vulputate lectus a massa gravida venenatis. Aenean porttitor condimentum posuere.
<br class="pf_break" />
<br class="pf_break" />Nulla facilisi. Praesent vel risus id mauris malesuada vestibulum eget vel ligula. Etiam dapibus ultrices urna, nec auctor turpis aliquam non. Nam erat quam, sagittis nec faucibus in, fringilla vel sapien. Quisque commodo, eros sed elementum rutrum, tortor tortor viverra sem, non egestas augue mauris non libero. Cras tristique tortor et libero vehicula eu venenatis velit pharetra. Donec sagittis ornare libero, et interdum odio volutpat at. Donec non tellus et mauris lacinia pretium vitae in lorem. Nam tellus velit, mollis eget auctor non, luctus a tellus. Suspendisse potenti. Suspendisse posuere metus ipsum.
<br class="pf_break" />
<br class="pf_break" />Proin faucibus, dolor iaculis volutpat viverra, arcu nisi accumsan turpis, nec ultrices nibh nibh a tellus. Nullam a neque id diam lobortis dictum quis lobortis velit. Nunc congue aliquam facilisis. Ut interdum, tortor ut volutpat rutrum, enim nisi tincidunt tortor, ac venenatis libero tortor in erat. Cras at justo ut felis molestie rutrum. Nam justo nunc, vulputate et sollicitudin sed, vestibulum in neque. Nunc laoreet varius nulla.
<br class="pf_break" />
<br class="pf_break" />Nullam eu nisi non lorem fringilla luctus eu ac magna. Nulla rutrum ante eget magna fermentum consequat. Suspendisse hendrerit lacus vulputate turpis dictum lacinia. Nam placerat nisl in ante gravida rutrum. Sed sed eros libero, et tincidunt libero. Duis placerat sollicitudin bibendum. Nullam vitae nulla diam, ac luctus nisi. Ut dictum nunc ac purus semper posuere. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque non ipsum nec risus porttitor sodales. Quisque odio dolor, malesuada quis faucibus at, laoreet semper elit. Nullam ac ipsum eu mauris hendrerit pulvinar eu at magna. Mauris eget ante libero, non porttitor arcu.
<br class="pf_break" />
<br class="pf_break" />Vestibulum accumsan tempus venenatis. Nam massa quam, fermentum vitae aliquam at, vehicula non neque. Phasellus tristique dui at felis euismod porta. Fusce nisl magna, tempor mollis adipiscing sed, convallis at lorem. Suspendisse velit nulla, adipiscing eget gravida ut, sodales ut erat. Nulla facilisi. Etiam et tempus diam. Nam vulputate molestie laoreet.
<br class="pf_break" />
<br class="pf_break" />Sed nunc nulla, suscipit et fermentum ac, tempor sed magna. Donec libero lorem, tristique sit amet commodo vitae, dictum in felis. Proin sem orci, tempor sit amet adipiscing vitae, blandit blandit neque. Quisque eget massa dui, eget mattis turpis. Proin quis tellus vitae felis laoreet laoreet vitae vitae risus. Cras dictum semper vehicula. Integer vitae libero ante.
<br class="pf_break" />
<br class="pf_break" />Fusce vitae metus nulla, sed euismod felis. Nulla pulvinar egestas tincidunt. Nam vehicula malesuada urna, ac fringilla purus euismod quis. Curabitur sed metus eu orci rutrum tincidunt. Nam elementum, nibh in suscipit egestas, ligula dolor fringilla diam, et mollis tortor leo eu urna. Aenean egestas mauris ut arcu gravida vel volutpat felis consectetur. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Ut pretium vulputate felis, sit amet varius lectus pellentesque non.
<br class="pf_break" />
<br class="pf_break" />Nulla facilisi. Nunc ornare tellus non dolor consectetur accumsan. Aenean tincidunt, nibh aliquam interdum bibendum, erat lacus ultrices libero, quis vehicula ligula dolor eget arcu. Sed rhoncus nulla eget justo viverra vitae rutrum nunc porttitor. Proin id neque risus. Maecenas vestibulum purus eget diam ullamcorper vel aliquam lacus imperdiet. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Nulla nec libero ac mauris malesuada rutrum sit amet vitae erat. Nam tellus est, tempor ut egestas sit amet, tempor ut lorem. Suspendisse fermentum ornare metus sed tincidunt. Nulla mattis bibendum sapien, ac vehicula lorem mattis in. Maecenas adipiscing elementum consectetur. In vel leo augue, nec tincidunt purus. Sed id gravida massa.
<br class="pf_break" />
<br class="pf_break" />Curabitur eros enim, feugiat sed eleifend quis, fringilla et metus. Quisque lacinia lacus non lorem commodo sed varius tellus cursus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Mauris egestas consectetur massa, vulputate tincidunt magna vehicula id. Pellentesque eget dolor vel purus semper volutpat eu at nisl. Ut sollicitudin nisi sit amet odio auctor fringilla. Sed eu nisi sed ipsum hendrerit varius. Sed tempus dignissim tincidunt. Curabitur laoreet tortor quis urna tempor fermentum. Ut quis libero vitae nunc cursus hendrerit. Integer et interdum lacus.
<br class="pf_break" />
<br class="pf_break" />Donec ipsum velit, elementum quis porta ut, condimentum non dolor. Phasellus posuere dignissim leo, quis mollis lectus egestas vel. Phasellus adipiscing faucibus dictum. Vestibulum eu felis eros. Maecenas eu sapien turpis. Quisque volutpat aliquam sem laoreet porta. Nam varius feugiat dolor sit amet ornare. Sed vitae viverra velit. Curabitur non turpis nec augue placerat euismod mattis nec nulla. Morbi aliquet nibh nec leo vehicula vulputate. Maecenas vel lacinia est. Curabitur augue elit, fringilla placerat auctor id, luctus quis eros.
<br class="pf_break" />
<br class="pf_break" />Vestibulum ultrices iaculis tristique. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam vel enim dui, et consequat magna. In vitae mi eget magna pulvinar pretium. Etiam quis turpis nunc. Nam eget velit eros. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Integer nunc elit, semper vel pellentesque et, faucibus ut metus. Nunc mollis dolor quis sem tempor in dictum est scelerisque. Nunc vel risus nisl, vitae bibendum nulla. Suspendisse tellus erat, dignissim imperdiet ultricies ac, malesuada sit amet nunc.
<br class="pf_break" />
<br class="pf_break" />Praesent nec turpis iaculis elit adipiscing semper. In feugiat, odio a tempus fermentum, nisl metus fringilla nulla, interdum fringilla felis odio et mauris. Cras consequat leo sed felis interdum eu mattis enim tristique. Suspendisse vel orci sapien, ut varius odio. Curabitur ut arcu elit. Cras purus ligula, sodales sit amet viverra et, iaculis at augue. Praesent vehicula vulputate porta. Pellentesque lectus mi, ornare ut ultricies eu, elementum sed purus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Phasellus laoreet, eros in volutpat fringilla, quam justo suscipit leo, vitae interdum magna nisi vel odio.
<br class="pf_break" />
<br class="pf_break" />Ut fermentum lorem quis ipsum lobortis bibendum. Pellentesque viverra augue vitae libero euismod volutpat. Nullam ac ipsum eget velit fringilla porta hendrerit nec nisl. Nunc vitae sem enim, quis tempor ipsum. Quisque vel diam eu metus blandit sagittis vitae sed eros. Vestibulum placerat elit et magna pretium dignissim. Integer luctus ligula vel dui mollis id porta purus ornare. Suspendisse eget dui mi. Quisque magna felis, volutpat eu posuere non, blandit id leo. Donec vel nisi nisl, eget lacinia odio. Phasellus sit amet massa vitae nibh congue fringilla sed nec felis.
<br class="pf_break" />
<br class="pf_break" />Mauris neque dolor, semper id porta vitae, rhoncus in felis. Proin porttitor, dui a auctor sagittis, nulla dolor interdum lacus, quis auctor nisi erat id nunc. Donec feugiat risus eget magna commodo id malesuada eros mollis. Suspendisse enim lacus, ultrices id malesuada nec, fringilla in augue. Ut ultrices dapibus tortor, sit amet dapibus quam condimentum ac. Aenean et nisi in velit luctus tempor. Integer sed risus justo, vel gravida arcu. Duis eleifend massa a nunc molestie tristique ornare nulla iaculis. Praesent vel nisi ante, id consequat diam.
<br class="pf_break" />
<br class="pf_break" />Nulla facilisi. Duis eros risus, euismod non facilisis et, pharetra eget orci. Praesent ultricies malesuada lectus, semper faucibus nibh ornare eu. Donec condimentum porta mollis. Vivamus sodales aliquet erat sit amet tempor. Nulla non dictum nisl. Suspendisse eleifend metus quis tellus mattis eget vulputate sapien scelerisque. Quisque dui ligula, accumsan cursus imperdiet nec, porttitor sed est.
<br class="pf_break" />
<br class="pf_break" />Donec purus erat, lobortis a convallis eu, euismod volutpat lacus. Pellentesque bibendum egestas ligula, in commodo est congue a. Cras sagittis viverra est, id tristique leo iaculis nec. Donec posuere sollicitudin scelerisque. Cras eros nulla, varius ut sollicitudin non, interdum at metus. Fusce nisi orci, vestibulum sed vehicula at, ullamcorper eget est. Praesent quis tortor orci, nec malesuada dui. Nullam in ipsum vel mauris gravida cursus ac nec erat.
<br class="pf_break" />
<br class="pf_break" />Nulla ut mi vitae quam euismod iaculis. Ut dapibus auctor metus, at feugiat tortor tincidunt vitae. Donec ultricies nulla eget diam faucibus ultrices posuere odio malesuada. Aenean porta dolor sed nibh rhoncus placerat. Nam vestibulum mauris in leo dictum nec volutpat lectus facilisis. Integer tristique condimentum purus, id auctor justo tristique quis. Aenean vitae lorem ac mauris suscipit gravida et quis tellus. Morbi ultricies tempus mauris id commodo. Nullam justo ligula, dictum et suscipit vitae, mollis id nunc. Maecenas lacinia, purus eget tempus pharetra, elit eros mattis felis, vel lacinia sapien massa et ligula. Morbi volutpat bibendum quam sed pulvinar. Etiam a odio mi, sit amet volutpat neque.
<br class="pf_break" />
<br class="pf_break" />Phasellus vel erat non lectus consequat vehicula at a ante. Praesent eu sagittis velit. Fusce eleifend dictum est, eu dapibus velit malesuada non. Nulla at sagittis nisl. Praesent justo tellus, semper eu vehicula vitae, fringilla adipiscing mauris. Praesent enim nulla, cursus eu tincidunt a, venenatis vel sapien. Suspendisse fringilla libero vitae libero eleifend gravida. Sed vitae nisi metus. Proin porttitor porta mattis. Aenean bibendum rhoncus enim rutrum gravida. Proin lobortis sagittis neque in euismod. Mauris ac arcu eu arcu sodales cursus. Sed posuere mauris id metus vulputate tincidunt. Sed sed elit ut est luctus eleifend. Curabitur euismod volutpat mauris, sit amet dapibus ipsum malesuada in.
<br class="pf_break" />
<br class="pf_break" />Aenean at elit orci. Mauris vel orci a nisl rhoncus lobortis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce eleifend cursus gravida. Morbi semper, felis vitae condimentum adipiscing, enim diam molestie augue, id rutrum nibh nisi nec dui. Quisque tempus feugiat elit vitae pulvinar. Fusce sit amet lorem sed metus tristique ultricies.
<br class="pf_break" />
<br class="pf_break" />Donec eu mauris elit. Vivamus quis sapien nunc. Nunc ut velit quis magna malesuada gravida non non sapien. Morbi eu neque nibh, ac placerat nulla. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Proin interdum nisi vitae ante ultricies consequat. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
<br class="pf_break" />
<br class="pf_break" />Vestibulum dictum, nisi vel scelerisque commodo, purus ante accumsan nibh, eget ultrices mauris ipsum eu odio. Nulla facilisi. Etiam ac tincidunt nunc. Praesent sed urna felis. Ut eget tristique tortor. Donec ultrices diam at nisi ultricies placerat. Etiam libero orci, sodales vitae dictum posuere, tempor nec nunc. Praesent pharetra rutrum neque, sed congue tortor pellentesque id. Fusce bibendum turpis non arcu adipiscing ullamcorper non congue libero. In et volutpat felis. Aliquam iaculis dui auctor dolor placerat dapibus. Sed rhoncus erat sed purus tincidunt in mattis massa mattis. Nulla facilisi. Integer viverra rutrum condimentum. Vivamus vitae nibh ac purus dignissim tincidunt et non urna.
<br class="pf_break" />
<br class="pf_break" />Vivamus pellentesque vehicula magna venenatis interdum. Quisque fringilla quam dui. Curabitur lacinia iaculis nisi, sit amet consectetur arcu porta tempus. Suspendisse vel fringilla eros. Mauris non lacus ac felis cursus vestibulum. Maecenas in orci eget risus bibendum viverra. Nunc ac libero at felis vestibulum pulvinar. Morbi non dui ante, in egestas justo. Cras quis ante venenatis nisi molestie tincidunt a euismod tellus. Suspendisse fermentum consectetur urna vel varius. Proin tempor hendrerit leo, a aliquam velit pellentesque tempus. Nunc nulla eros, ultricies sit amet tincidunt in, ornare sodales felis. Maecenas tincidunt, nisi id lacinia convallis, nibh felis bibendum lectus, at mollis orci lectus nec velit.
<br class="pf_break" />
<br class="pf_break" />Duis ut quam vel quam consequat hendrerit tincidunt eget metus. Suspendisse vitae molestie elit. Nunc blandit venenatis leo ut vulputate. Pellentesque tempus fringilla porta. Etiam eget mattis felis. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Morbi leo sem, commodo eget semper eget, porttitor id leo. Duis pulvinar pulvinar ultricies. Proin laoreet posuere sagittis. Curabitur lectus libero, fringilla et varius ut, sodales sit amet purus. Cras molestie pulvinar magna in malesuada. Nullam tincidunt enim at tellus tempor dignissim. Aliquam et lacus massa.
<br class="pf_break" />
<br class="pf_break" />Mauris arcu neque, bibendum vel porttitor eget, hendrerit at metus. Curabitur porttitor, orci a volutpat tristique, turpis nulla pellentesque tortor, vel mattis libero dui nec velit. Proin aliquam enim sed ligula mollis tristique. Aenean eget urna turpis. Etiam volutpat, massa sed tincidunt eleifend, nulla augue lacinia mauris, quis rhoncus libero erat aliquet est. Fusce vel ipsum mattis quam sagittis interdum. Phasellus ac turpis at justo convallis mollis sit amet sed tortor. Donec ac nunc urna, vitae laoreet mauris. Aenean dolor neque, ultricies eget sodales at, bibendum at erat.
<br class="pf_break" />
<br class="pf_break" />Proin sollicitudin scelerisque orci, at vestibulum magna consectetur et. Pellentesque sit amet ligula dolor. Duis dignissim blandit arcu, a vulputate nisi vehicula at. Sed sit amet velit vel justo pellentesque consequat quis vitae nulla. Nullam ornare tellus nisi. Nunc dignissim magna nisl, sit amet adipiscing turpis. Nulla vel purus vehicula enim dictum aliquam. Donec nisi diam, condimentum eu pharetra nec, ultrices vel lacus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec eget turpis sapien, at adipiscing erat. Curabitur sit amet turpis lacus, non pretium nibh. In hac habitasse platea dictumst. Proin sit amet tellus vel nibh pellentesque lobortis. Vivamus dignissim purus suscipit neque eleifend eget faucibus ipsum fringilla. Aenean nec quam lobortis purus aliquam viverra a eu dui.
<br class="pf_break" />
<br class="pf_break" />Nulla adipiscing, dui ac varius accumsan, purus lorem aliquet dui, non porttitor libero leo imperdiet mauris. Mauris eleifend porta risus, sit amet pharetra diam luctus quis. Nunc pellentesque blandit massa, vulputate sagittis magna sagittis a. Nulla sagittis euismod odio ut feugiat. Suspendisse non magna ut nisl congue consequat. Maecenas nisi tortor, tincidunt vel fringilla id, dictum vitae augue. Donec cursus nibh in est volutpat posuere. Ut dolor tortor, elementum a sodales in, consectetur scelerisque sem. Mauris non purus neque. Ut dictum ligula id est venenatis a tincidunt enim vulputate. Nullam imperdiet lobortis lectus, ac viverra tellus facilisis et. In lobortis diam lectus.
<br class="pf_break" />
<br class="pf_break" />Mauris fermentum dapibus urna quis adipiscing. Fusce at lorem lectus. Nullam eu orci justo. Sed orci dolor, congue et suscipit id, elementum sed ante. Phasellus vehicula eros vitae risus interdum pharetra. Nam lectus elit, suscipit in rutrum vel, rhoncus in elit. Nullam dignissim nunc nibh, et aliquet felis. Sed vulputate, ipsum eget facilisis scelerisque, lacus felis feugiat massa, eget interdum nisi sapien eget massa. Vestibulum leo metus, molestie non pulvinar nec, condimentum non diam.
<br class="pf_break" />
<br class="pf_break" />Quisque sagittis commodo molestie. Donec venenatis ante a purus elementum vestibulum. Maecenas laoreet, tortor non dapibus luctus, odio risus mollis risus, sed luctus diam libero eu felis. Suspendisse potenti. Sed vel quam mi, non placerat neque. In hac habitasse platea dictumst. Integer sed neque a arcu elementum semper. Donec vel velit erat. Nunc posuere, nisl semper pellentesque dignissim, quam purus varius urna, at ultrices dui risus in dolor. Vivamus at dui enim, et imperdiet risus. Vestibulum nisi nisi, faucibus sit amet venenatis vitae, pharetra at dui. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur eu mauris nulla. Nunc diam erat, placerat at commodo porta, faucibus a turpis. Morbi sodales nisi id ante imperdiet adipiscing.
<br class="pf_break" />
<br class="pf_break" />Fusce metus tellus, luctus placerat vehicula et, fermentum ut sem. Donec ac lorem risus, sit amet lobortis dui. Mauris vel metus ac urna mollis gravida id id justo. Proin id justo felis. Vivamus at vehicula massa. Proin interdum ornare congue. Mauris pellentesque faucibus mi sed vestibulum. Mauris enim sem, vestibulum ut placerat at, rhoncus sit amet metus. Nulla neque dolor, malesuada ac luctus sit amet, imperdiet quis libero. Nunc facilisis erat at turpis vulputate aliquet.
<br class="pf_break" />
<br class="pf_break" />Sed dignissim convallis interdum. Curabitur ligula ligula, lacinia in accumsan at, pharetra tempor sapien. Quisque quis augue ac magna aliquet fermentum ultrices vitae mauris. Phasellus nec dolor hendrerit dolor vestibulum placerat nec ut lacus. Phasellus mattis suscipit varius. Ut egestas tortor sit amet augue cursus ut volutpat magna scelerisque. Aenean pulvinar, lacus a hendrerit gravida, tellus neque condimentum lorem, eget semper purus massa eget neque. Nam pretium lacus in quam feugiat fringilla aliquet lectus egestas. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Pellentesque elementum consequat feugiat. Quisque id diam justo, eget euismod augue. Integer tristique vestibulum sollicitudin. Quisque eu orci nec magna vestibulum placerat in volutpat odio. Integer in orci non dolor ornare viverra. Nulla facilisi. Fusce volutpat mollis urna, vel ultrices libero porttitor a.]]></text_parsed>
		</page>
	</pages>
	<reports>
		<report module_id="core">abuse content</report>
		<report module_id="core">training the com</report>
	</reports>
	<user_delete>
		<option module_id="core" phrase_var="core.user_cancellation_9" />
		<option module_id="core" phrase_var="core.user_cancellation_10" />
		<option module_id="core" phrase_var="core.user_cancellation_11" />
		<option module_id="core" phrase_var="core.user_cancellation_12" />
		<option module_id="core" phrase_var="core.user_cancellation_13" />
		<option module_id="core" phrase_var="core.user_cancellation_14" />
	</user_delete>
	<user_group_settings>
		<setting is_admin_setting="0" module_id="core" type="boolean" admin="1" user="1" guest="1" staff="1" module="core" ordering="0">can_view_update_info</setting>
		<setting is_admin_setting="0" module_id="core" type="boolean" admin="1" user="0" guest="0" staff="1" module="core" ordering="0">can_view_private_items</setting>
		<setting is_admin_setting="0" module_id="core" type="boolean" admin="1" user="0" guest="0" staff="0" module="core" ordering="0">can_add_new_setting</setting>
		<setting is_admin_setting="0" module_id="core" type="boolean" admin="1" user="0" guest="0" staff="1" module="core" ordering="0">can_view_site_offline</setting>
		<setting is_admin_setting="1" module_id="core" type="boolean" admin="0" user="0" guest="0" staff="0" module="core" ordering="0">user_is_banned</setting>
		<setting is_admin_setting="0" module_id="core" type="boolean" admin="1" user="0" guest="0" staff="0" module="core" ordering="0">is_spam_free</setting>
		<setting is_admin_setting="0" module_id="core" type="boolean" admin="1" user="0" guest="0" staff="0" module="core" ordering="0">can_view_news_updates</setting>
		<setting is_admin_setting="0" module_id="core" type="boolean" admin="true" user="false" guest="false" staff="false" module="core" ordering="0">can_gift_points</setting>
	</user_group_settings>
	<tables><![CDATA[a:29:{s:20:"phpfox_admincp_login";a:2:{s:7:"COLUMNS";a:6:{s:8:"login_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:9:"is_failed";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"ip_address";a:4:{i:0;s:8:"VCHAR:15";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"cache_data";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:11:"PRIMARY_KEY";s:8:"login_id";}s:12:"phpfox_block";a:3:{s:7:"COLUMNS";a:14:{s:8:"block_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:5:"title";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:7:"type_id";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:12:"m_connection";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:9:"module_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"product_id";a:4:{i:0;s:8:"VCHAR:25";i:1;s:6:"phpfox";i:2;s:0:"";i:3;s:2:"NO";}s:9:"component";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:8:"location";a:4:{i:0;s:9:"VCHAR:255";i:1;s:1:"1";i:2;s:0:"";i:3;s:2:"NO";}s:9:"is_active";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"ordering";a:4:{i:0;s:4:"UINT";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:15:"disallow_access";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:8:"can_move";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"version_id";a:4:{i:0;s:8:"VCHAR:50";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:6:"params";a:4:{i:0;s:5:"MTEXT";i:1;s:0:"";i:2;s:0:"";i:3;s:3:"YES";}}s:11:"PRIMARY_KEY";s:8:"block_id";s:4:"KEYS";a:5:{s:9:"is_active";a:2:{i:0;s:5:"INDEX";i:1;s:9:"is_active";}s:10:"product_id";a:2:{i:0;s:5:"INDEX";i:1;s:10:"product_id";}s:12:"m_connection";a:2:{i:0;s:5:"INDEX";i:1;s:12:"m_connection";}s:14:"m_connection_2";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:12:"m_connection";i:1;s:9:"is_active";}}s:9:"module_id";a:2:{i:0;s:5:"INDEX";i:1;s:9:"module_id";}}}s:18:"phpfox_block_order";a:3:{s:7:"COLUMNS";a:4:{s:8:"order_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:8:"style_id";a:4:{i:0;s:5:"USINT";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"block_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"ordering";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:8:"order_id";s:4:"KEYS";a:1:{s:8:"style_id";a:2:{i:0;s:5:"INDEX";i:1;s:8:"style_id";}}}s:19:"phpfox_block_source";a:2:{s:7:"COLUMNS";a:3:{s:8:"block_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"source_code";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:13:"source_parsed";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:4:"KEYS";a:1:{s:8:"block_id";a:2:{i:0;s:5:"INDEX";i:1;s:8:"block_id";}}}s:12:"phpfox_cache";a:2:{s:7:"COLUMNS";a:6:{s:8:"cache_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:9:"file_name";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"cache_data";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:9:"data_size";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"type_id";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:8:"cache_id";}s:16:"phpfox_component";a:3:{s:7:"COLUMNS";a:8:{s:12:"component_id";a:4:{i:0;s:4:"UINT";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:9:"component";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:12:"m_connection";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:9:"module_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"product_id";a:4:{i:0;s:8:"VCHAR:75";i:1;s:6:"phpfox";i:2;s:0:"";i:3;s:2:"NO";}s:13:"is_controller";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"is_block";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"is_active";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:12:"component_id";s:4:"KEYS";a:2:{s:9:"component";a:2:{i:0;s:5:"INDEX";i:1;s:9:"component";}s:9:"is_active";a:2:{i:0;s:5:"INDEX";i:1;s:9:"is_active";}}}s:24:"phpfox_component_setting";a:2:{s:7:"COLUMNS";a:3:{s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"var_name";a:4:{i:0;s:9:"VCHAR:150";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"user_value";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:4:"KEYS";a:2:{s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"user_id";}s:9:"user_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"user_id";i:1;s:8:"var_name";}}}}s:14:"phpfox_country";a:2:{s:7:"COLUMNS";a:4:{s:11:"country_iso";a:4:{i:0;s:6:"CHAR:2";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:4:"name";a:4:{i:0;s:9:"VCHAR:255";i:1;s:0:"";i:2;s:0:"";i:3;s:2:"NO";}s:8:"ordering";a:4:{i:0;s:5:"USINT";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:15:"phrase_var_name";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:11:"PRIMARY_KEY";s:11:"country_iso";}s:20:"phpfox_country_child";a:3:{s:7:"COLUMNS";a:5:{s:8:"child_id";a:4:{i:0;s:4:"UINT";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:11:"country_iso";a:4:{i:0;s:6:"CHAR:2";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:4:"name";a:4:{i:0;s:9:"VCHAR:200";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"ordering";a:4:{i:0;s:5:"USINT";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:15:"phrase_var_name";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:11:"PRIMARY_KEY";s:8:"child_id";s:4:"KEYS";a:1:{s:11:"country_iso";a:2:{i:0;s:5:"INDEX";i:1;s:11:"country_iso";}}}s:11:"phpfox_cron";a:3:{s:7:"COLUMNS";a:9:{s:7:"cron_id";a:4:{i:0;s:4:"UINT";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:9:"module_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"product_id";a:4:{i:0;s:8:"VCHAR:25";i:1;s:6:"phpfox";i:2;s:0:"";i:3;s:2:"NO";}s:8:"next_run";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"last_run";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"type_id";a:4:{i:0;s:6:"TINT:3";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:5:"every";a:4:{i:0;s:6:"TINT:3";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"is_active";a:4:{i:0;s:6:"TINT:3";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"php_code";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:7:"cron_id";s:4:"KEYS";a:1:{s:9:"is_active";a:2:{i:0;s:5:"INDEX";i:1;s:9:"is_active";}}}s:15:"phpfox_cron_log";a:2:{s:7:"COLUMNS";a:3:{s:6:"log_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"cron_id";a:4:{i:0;s:4:"UINT";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:6:"log_id";}s:15:"phpfox_currency";a:2:{s:7:"COLUMNS";a:6:{s:11:"currency_id";a:4:{i:0;s:7:"VCHAR:3";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:6:"symbol";a:4:{i:0;s:8:"VCHAR:15";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"phrase_var";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"ordering";a:4:{i:0;s:4:"UINT";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"is_default";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"is_active";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:4:"KEYS";a:2:{s:11:"currency_id";a:2:{i:0;s:6:"UNIQUE";i:1;s:11:"currency_id";}s:9:"is_active";a:2:{i:0;s:5:"INDEX";i:1;s:9:"is_active";}}}s:11:"phpfox_menu";a:3:{s:7:"COLUMNS";a:13:{s:7:"menu_id";a:4:{i:0;s:4:"UINT";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:9:"parent_id";a:4:{i:0;s:4:"UINT";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"page_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:12:"m_connection";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:9:"module_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"product_id";a:4:{i:0;s:8:"VCHAR:25";i:1;s:6:"phpfox";i:2;s:0:"";i:3;s:2:"NO";}s:8:"var_name";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"is_active";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"ordering";a:4:{i:0;s:4:"UINT";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"url_value";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:15:"disallow_access";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"version_id";a:4:{i:0;s:8:"VCHAR:50";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:11:"mobile_icon";a:4:{i:0;s:8:"VCHAR:50";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:11:"PRIMARY_KEY";s:7:"menu_id";s:4:"KEYS";a:5:{s:10:"product_id";a:2:{i:0;s:5:"INDEX";i:1;s:10:"product_id";}s:9:"url_value";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:9:"url_value";i:1;s:9:"module_id";}}s:7:"page_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"page_id";}s:12:"m_connection";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:12:"m_connection";i:1;s:9:"is_active";}}s:9:"parent_id";a:2:{i:0;s:5:"INDEX";i:1;s:9:"parent_id";}}}s:13:"phpfox_module";a:2:{s:7:"COLUMNS";a:12:{s:9:"module_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"product_id";a:4:{i:0;s:8:"VCHAR:25";i:1;s:6:"phpfox";i:2;s:0:"";i:3;s:2:"NO";}s:7:"is_core";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"is_active";a:4:{i:0;s:6:"TINT:1";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"is_menu";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"1";i:2;s:0:"";i:3;s:2:"NO";}s:4:"menu";a:4:{i:0;s:4:"TEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:15:"phrase_var_name";a:4:{i:0;s:9:"VCHAR:250";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:7:"version";a:4:{i:0;s:8:"VCHAR:32";i:1;s:5:"4.0.1";i:2;s:0:"";i:3;s:2:"NO";}s:6:"author";a:4:{i:0;s:9:"VCHAR:255";i:1;s:3:"n/a";i:2;s:0:"";i:3;s:2:"NO";}s:6:"vendor";a:4:{i:0;s:9:"VCHAR:255";i:1;s:0:"";i:2;s:0:"";i:3;s:2:"NO";}s:11:"description";a:4:{i:0;s:4:"TEXT";i:1;s:0:"";i:2;s:0:"";i:3;s:3:"YES";}s:9:"apps_icon";a:4:{i:0;s:9:"VCHAR:255";i:1;s:0:"";i:2;s:0:"";i:3;s:3:"YES";}}s:4:"KEYS";a:4:{s:9:"is_active";a:2:{i:0;s:5:"INDEX";i:1;s:9:"is_active";}s:7:"is_menu";a:2:{i:0;s:5:"INDEX";i:1;s:7:"is_menu";}s:9:"module_id";a:2:{i:0;s:5:"INDEX";i:1;s:9:"module_id";}s:16:"module_is_active";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:9:"module_id";i:1;s:9:"is_active";}}}}s:23:"phpfox_password_request";a:2:{s:7:"COLUMNS";a:3:{s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"request_id";a:4:{i:0;s:7:"CHAR:32";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:4:"KEYS";a:2:{s:10:"request_id";a:2:{i:0;s:5:"INDEX";i:1;s:10:"request_id";}s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"user_id";}}}s:13:"phpfox_plugin";a:3:{s:7:"COLUMNS";a:8:{s:9:"plugin_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:9:"module_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"product_id";a:4:{i:0;s:8:"VCHAR:25";i:1;s:6:"phpfox";i:2;s:0:"";i:3;s:2:"NO";}s:9:"call_name";a:4:{i:0;s:9:"VCHAR:250";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:5:"title";a:4:{i:0;s:9:"VCHAR:250";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"php_code";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"is_active";a:4:{i:0;s:6:"TINT:1";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"ordering";a:4:{i:0;s:6:"TINT:3";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:9:"plugin_id";s:4:"KEYS";a:1:{s:9:"is_active";a:2:{i:0;s:5:"INDEX";i:1;s:9:"is_active";}}}s:18:"phpfox_plugin_hook";a:3:{s:7:"COLUMNS";a:8:{s:7:"hook_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:9:"hook_type";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"module_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"product_id";a:4:{i:0;s:8:"VCHAR:25";i:1;s:6:"phpfox";i:2;s:0:"";i:3;s:2:"NO";}s:9:"call_name";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:5:"added";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"version_id";a:4:{i:0;s:8:"VCHAR:50";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:9:"is_active";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:7:"hook_id";s:4:"KEYS";a:2:{s:10:"product_id";a:2:{i:0;s:5:"INDEX";i:1;s:10:"product_id";}s:9:"call_name";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:9:"call_name";i:1;s:9:"is_active";}}}}s:14:"phpfox_product";a:2:{s:7:"COLUMNS";a:12:{s:10:"product_id";a:4:{i:0;s:8:"VCHAR:25";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"is_core";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:5:"title";a:4:{i:0;s:8:"VCHAR:50";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"description";a:4:{i:0;s:9:"VCHAR:250";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:7:"version";a:4:{i:0;s:8:"VCHAR:25";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:14:"latest_version";a:4:{i:0;s:8:"VCHAR:25";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"last_check";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"is_active";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:3:"url";a:4:{i:0;s:9:"VCHAR:250";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:17:"url_version_check";a:4:{i:0;s:9:"VCHAR:250";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:4:"icon";a:4:{i:0;s:9:"VCHAR:250";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:6:"vendor";a:4:{i:0;s:9:"VCHAR:250";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:4:"KEYS";a:4:{s:9:"is_active";a:2:{i:0;s:5:"INDEX";i:1;s:9:"is_active";}s:5:"title";a:2:{i:0;s:5:"INDEX";i:1;s:5:"title";}s:14:"product_active";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:10:"product_id";i:1;s:9:"is_active";}}s:10:"product_id";a:2:{i:0;s:5:"INDEX";i:1;s:10:"product_id";}}}s:25:"phpfox_product_dependency";a:3:{s:7:"COLUMNS";a:6:{s:13:"dependency_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:10:"product_id";a:4:{i:0;s:8:"VCHAR:25";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"type_id";a:4:{i:0;s:8:"VCHAR:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"check_id";a:4:{i:0;s:8:"VCHAR:25";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:16:"dependency_start";a:4:{i:0;s:8:"VCHAR:25";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:14:"dependency_end";a:4:{i:0;s:8:"VCHAR:25";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:11:"PRIMARY_KEY";s:13:"dependency_id";s:4:"KEYS";a:1:{s:10:"product_id";a:2:{i:0;s:5:"INDEX";i:1;s:10:"product_id";}}}s:22:"phpfox_product_install";a:3:{s:7:"COLUMNS";a:5:{s:10:"install_id";a:4:{i:0;s:4:"UINT";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:10:"product_id";a:4:{i:0;s:8:"VCHAR:25";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"version";a:4:{i:0;s:8:"VCHAR:25";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:12:"install_code";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:14:"uninstall_code";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:11:"PRIMARY_KEY";s:10:"install_id";s:4:"KEYS";a:1:{s:10:"product_id";a:2:{i:0;s:5:"INDEX";i:1;s:10:"product_id";}}}s:14:"phpfox_rewrite";a:2:{s:7:"COLUMNS";a:3:{s:10:"rewrite_id";a:4:{i:0;s:4:"UINT";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:3:"url";a:4:{i:0;s:9:"VCHAR:250";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"replacement";a:4:{i:0;s:9:"VCHAR:250";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:10:"rewrite_id";}s:13:"phpfox_search";a:3:{s:7:"COLUMNS";a:6:{s:9:"search_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:12:"search_query";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:12:"search_array";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"search_ids";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:9:"search_id";s:4:"KEYS";a:1:{s:9:"search_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:9:"search_id";i:1;s:7:"user_id";}}}}s:15:"phpfox_seo_meta";a:2:{s:7:"COLUMNS";a:5:{s:7:"meta_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"type_id";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:3:"url";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"content";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:7:"meta_id";}s:19:"phpfox_seo_nofollow";a:2:{s:7:"COLUMNS";a:3:{s:11:"nofollow_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:3:"url";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:11:"nofollow_id";}s:14:"phpfox_setting";a:3:{s:7:"COLUMNS";a:12:{s:10:"setting_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:8:"group_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:9:"module_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"product_id";a:4:{i:0;s:8:"VCHAR:75";i:1;s:6:"phpfox";i:2;s:0:"";i:3;s:2:"NO";}s:9:"is_hidden";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"version_id";a:4:{i:0;s:8:"VCHAR:50";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:7:"type_id";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"var_name";a:4:{i:0;s:9:"VCHAR:100";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:15:"phrase_var_name";a:4:{i:0;s:9:"VCHAR:250";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:12:"value_actual";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:13:"value_default";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:8:"ordering";a:4:{i:0;s:6:"TINT:3";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:10:"setting_id";s:4:"KEYS";a:5:{s:8:"var_name";a:2:{i:0;s:5:"INDEX";i:1;s:8:"var_name";}s:8:"group_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:8:"group_id";i:1;s:9:"is_hidden";}}s:9:"module_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:9:"module_id";i:1;s:9:"is_hidden";}}s:10:"product_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:10:"product_id";i:1;s:9:"is_hidden";}}s:9:"is_hidden";a:2:{i:0;s:5:"INDEX";i:1;s:9:"is_hidden";}}}s:20:"phpfox_setting_group";a:2:{s:7:"COLUMNS";a:5:{s:8:"group_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"module_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"product_id";a:4:{i:0;s:8:"VCHAR:25";i:1;s:6:"phpfox";i:2;s:0:"";i:3;s:2:"NO";}s:10:"version_id";a:4:{i:0;s:8:"VCHAR:50";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:8:"var_name";a:4:{i:0;s:9:"VCHAR:250";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:4:"KEYS";a:2:{s:8:"var_name";a:2:{i:0;s:5:"INDEX";i:1;s:8:"var_name";}s:8:"group_id";a:2:{i:0;s:5:"INDEX";i:1;s:8:"group_id";}}}s:16:"phpfox_site_stat";a:3:{s:7:"COLUMNS";a:9:{s:7:"stat_id";a:4:{i:0;s:5:"USINT";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:9:"module_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"product_id";a:4:{i:0;s:8:"VCHAR:25";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"phrase_var";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"php_code";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"stat_link";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"stat_image";a:4:{i:0;s:8:"VCHAR:20";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"is_active";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"ordering";a:4:{i:0;s:4:"UINT";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:7:"stat_id";s:4:"KEYS";a:1:{s:9:"is_active";a:2:{i:0;s:5:"INDEX";i:1;s:9:"is_active";}}}s:14:"phpfox_version";a:2:{s:7:"COLUMNS";a:2:{s:10:"version_id";a:4:{i:0;s:8:"VCHAR:50";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:8:"ordering";a:4:{i:0;s:4:"UINT";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:4:"KEYS";a:1:{s:10:"version_id";a:2:{i:0;s:5:"INDEX";i:1;s:10:"version_id";}}}s:15:"phpfox_cron_job";a:2:{s:7:"COLUMNS";a:6:{s:2:"id";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:10:"queue_name";a:4:{i:0;s:8:"VCHAR:50";i:1;s:7:"default";i:2;s:0:"";i:3;s:2:"NO";}s:4:"data";a:4:{i:0;s:4:"TEXT";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"expire_time";a:4:{i:0;s:7:"UINT:11";i:1;s:3:"600";i:2;s:0:"";i:3;s:2:"NO";}s:10:"is_running";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"last_run";a:4:{i:0;s:7:"UINT:11";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:2:"id";}}]]></tables>
	<install><![CDATA[		
		$aRows = array(
			array(
				'currency_id' => 'USD',
				'symbol' => '&#36;'	,
				'phrase_var' => 'core.u_s_dollars',
				'ordering' => '1',
				'is_default' => '1',
				'is_active' => '1'
			),	
			array(
				'currency_id' => 'EUR',
				'symbol' => '&#8364;'	,
				'phrase_var' => 'core.euros',
				'ordering' => '2',
				'is_default' => '0',
				'is_active' => '1'
			),
			array(
				'currency_id' => 'GBP',
				'symbol' => '&#163;',
				'phrase_var' => 'core.pounds_sterling',
				'ordering' => '3',
				'is_default' => '0',
				'is_active' => '1'
			)
		);	
		foreach ($aRows as $aRow)
		{
			$aInsert = array();
			foreach ($aRow as $sKey => $sValue)
			{
				$aInsert[$sKey] = $sValue;
			}
			$this->database()->insert(Phpfox::getT('currency'), $aInsert);
		}		
	
		$aRows = array(
			array(
				'url' => 'user/login',
				'replacement' => 'login'	
			),	
			array(
				'url' => 'user/logout',
				'replacement' => 'logout'
			)			
		);
		foreach ($aRows as $aRow)
		{
			$aInsert = array();
			foreach ($aRow as $sKey => $sValue)
			{
				$aInsert[$sKey] = $sValue;
			}
			$this->database()->insert(Phpfox::getT('rewrite'), $aInsert);
		}
		$aRows = array(
			array(
				'product_id' => 'phpfox',
				'title' => 'Core',
				'description' => '',
				'version' => '',
				'is_active' => '1',
				'url' => '',
				'url_version_check' => ''		
			)
		);
		foreach ($aRows as $aRow)
		{
			$aInsert = array();
			foreach ($aRow as $sKey => $sValue)
			{
				$aInsert[$sKey] = $sValue;
			}
			$this->database()->insert(Phpfox::getT('product'), $aInsert);
		}		
		
		$aCountryChildren = array (
				  'US' => 
				  array (
				    0 => 'Alabama',
				    1 => 'Alaska',
				    2 => 'American Samoa',
				    3 => 'Arizona',
				    4 => 'Arkansas',
				    5 => 'California',
				    6 => 'Colorado',
				    7 => 'Connecticut',
				    8 => 'Delaware',
				    9 => 'District Of Columbia',
				    10 => 'Federated States Of Micronesia',
				    11 => 'Florida',
				    12 => 'Georgia',
				    13 => 'Guam',
				    14 => 'Hawaii',
				    15 => 'Idaho',
				    16 => 'Illinois',
				    17 => 'Indiana',
				    18 => 'Iowa',
				    19 => 'Kansas',
				    20 => 'Kentucky',
				    21 => 'Louisiana',
				    22 => 'Maine',
				    23 => 'Marshall Islands',
				    24 => 'Maryland',
				    25 => 'Massachusetts',
				    26 => 'Michigan',
				    27 => 'Minnesota',
				    28 => 'Mississippi',
				    29 => 'Missouri',
				    30 => 'Montana',
				    31 => 'Nebraska',
				    32 => 'Nevada',
				    33 => 'New Hampshire',
				    34 => 'New Jersey',
				    35 => 'New Mexico',
				    36 => 'New York',
				    37 => 'North Carolina',
				    38 => 'North Dakota',
				    39 => 'Northern Mariana Islands',
				    40 => 'Ohio',
				    41 => 'Oklahoma',
				    42 => 'Oregon',
				    43 => 'Palau',
				    44 => 'Pennsylvania',
				    45 => 'Puerto Rico',
				    46 => 'Rhode Island',
				    47 => 'South Carolina',
				    48 => 'South Dakota',
				    49 => 'Tennessee',
				    50 => 'Texas',
				    51 => 'Utah',
				    52 => 'Vermont',
				    53 => 'Virgin Islands',
				    54 => 'Virginia',
				    55 => 'Washington',
				    56 => 'West Virginia',
				    57 => 'Wisconsin',
				    58 => 'Wyoming',
				  ),
				  'SE' => 
				  array (
				    0 => 'Blekinge',
				    1 => 'Bohusl&#228;n',
				    2 => 'Dalarna',
				    3 => 'Dalsland',
				    4 => 'Gotland',
				    5 => 'G&#228;strikland',
				    6 => 'Halland',
				    7 => 'H&#228;lsingland',
				    8 => 'H&#228;rjedalen',
				    9 => 'J&#228;mtland',
				    10 => 'Lappland',
				    11 => 'Medelpad',
				    12 => 'Norrbotten',
				    13 => 'N&#228;rke',
				    14 => 'Sk&#229;ne',
				    15 => 'Sm&#229;land',
				    16 => 'S&#246;dermanland',
				    17 => 'Uppland',
				    18 => 'V&#228;rmland',
				    19 => 'V&#228;stmanland',
				    20 => 'V&#228;sterbotten',
				    21 => 'V&#228;sterg&#246;tland',
				    22 => '&#197;ngermanland',
				    23 => '&#214;land',
				    24 => '&#214;sterg&#246;tland',
				  )
		);	
		
		foreach ($aCountryChildren as $sIso => $aChilds)
		{
			foreach ($aChilds as $sChild)
			{
				$this->database()->insert(Phpfox::getT('country_child'), array('country_iso' => $sIso, 'name' => $sChild));
			}
		}

		/* Remove the attribute Unsigned from feed table*/
		$this->database()->changeField(Phpfox::getT('feed'), 'feed_reference', ['type' => 'INT:10', 'null' => false, 'default' => '0']);
	]]></install>
</module>