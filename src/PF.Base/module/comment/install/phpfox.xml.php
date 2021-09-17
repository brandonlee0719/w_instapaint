<module>
	<data>
		<module_id>comment</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>0</is_menu>
        <menu><![CDATA[a:2:{s:35:"admincp.admin_menu_pending_comments";a:1:{s:3:"url";a:1:{i:0;s:7:"comment";}}s:32:"admincp.admin_menu_spam_comments";a:1:{s:3:"url";a:2:{i:0;s:7:"comment";i:1;s:4:"spam";}}}]]></menu>
		<phrase_var_name>module_comment</phrase_var_name>
		<writable />
	</data>
	<settings>
		<setting group="time_stamps" module_id="comment" is_hidden="0" type="string" var_name="comment_time_stamp" phrase_var_name="setting_comment_time_stamp" ordering="2" version_id="2.0.0alpha1">F j, Y</setting>
		<setting group="" module_id="comment" is_hidden="0" type="integer" var_name="comment_page_limit" phrase_var_name="setting_comment_page_limit" ordering="0" version_id="2.0.0alpha1">10</setting>
		<setting group="spam" module_id="comment" is_hidden="0" type="boolean" var_name="comment_hash_check" phrase_var_name="setting_comment_hash_check" ordering="8" version_id="2.0.0rc1">0</setting>
		<setting group="spam" module_id="comment" is_hidden="0" type="integer" var_name="comments_to_check" phrase_var_name="setting_comments_to_check" ordering="9" version_id="2.0.0rc1">10</setting>
		<setting group="spam" module_id="comment" is_hidden="0" type="string" var_name="total_minutes_to_wait_for_comments" phrase_var_name="setting_total_minutes_to_wait_for_comments" ordering="13" version_id="2.0.0rc1">2</setting>
		<setting group="" module_id="comment" is_hidden="0" type="integer" var_name="total_comments_in_activity_feed" phrase_var_name="setting_total_comments_in_activity_feed" ordering="1" version_id="3.0.0Beta1">2</setting>
		<setting group="" module_id="comment" is_hidden="0" type="boolean" var_name="comment_is_threaded" phrase_var_name="setting_comment_is_threaded" ordering="1" version_id="2.0.0alpha1">0</setting>
		<setting group="" module_id="comment" is_hidden="0" type="integer" var_name="thread_comment_total_display" phrase_var_name="setting_thread_comment_total_display" ordering="1" version_id="3.0.0">3</setting>
		<setting group="spam" module_id="comment" is_hidden="1" type="boolean" var_name="spam_check_comments" phrase_var_name="setting_spam_check_comments" ordering="6" version_id="2.0.0rc1">1</setting>
		<setting group="" module_id="comment" is_hidden="1" type="integer" var_name="total_amount_of_comments_to_load" phrase_var_name="setting_total_amount_of_comments_to_load" ordering="1" version_id="3.0.0Beta1">10</setting>
		<setting group="" module_id="comment" is_hidden="1" type="boolean" var_name="allow_rss_feed_on_comments" phrase_var_name="setting_allow_rss_feed_on_comments" ordering="1" version_id="2.0.0beta5">1</setting>
		<setting group="" module_id="comment" is_hidden="1" type="boolean" var_name="allow_comments_on_profiles" phrase_var_name="setting_allow_comments_on_profiles" ordering="1" version_id="2.0.0rc4">1</setting>
		<setting group="" module_id="comment" is_hidden="1" type="boolean" var_name="newest_comment_on_top" phrase_var_name="setting_newest_comment_on_top" ordering="98" version_id="4.6.0">0</setting>
	</settings>
	<hooks>
		<hook module_id="comment" hook_type="component" module="comment" call_name="comment.component_block_rating_process" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="comment" hook_type="component" module="comment" call_name="comment.component_block_rating_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="comment" hook_type="component" module="comment" call_name="comment.component_block_display_process_start" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="comment" hook_type="component" module="comment" call_name="comment.component_block_display_process_validation" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="comment" hook_type="component" module="comment" call_name="comment.component_block_display_process_middle" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="comment" hook_type="component" module="comment" call_name="comment.component_block_display_process_end" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="comment" hook_type="component" module="comment" call_name="comment.component_block_view_process" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="comment" hook_type="component" module="comment" call_name="comment.component_block_view_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="comment" hook_type="component" module="comment" call_name="comment.component_ajax_ajax_add_start" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="comment" hook_type="component" module="comment" call_name="comment.component_ajax_ajax_add_passed" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="comment" hook_type="service" module="comment" call_name="comment.service_callback__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="comment" hook_type="service" module="comment" call_name="comment.service_comment_getquote_start" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="comment" hook_type="service" module="comment" call_name="comment.service_comment_getquote_end" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="comment" hook_type="service" module="comment" call_name="comment.service_comment_getcommentforedit" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="comment" hook_type="service" module="comment" call_name="comment.service_comment_hasaccess_start" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="comment" hook_type="service" module="comment" call_name="comment.service_comment_hasaccess_end" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="comment" hook_type="service" module="comment" call_name="comment.service_comment___call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="comment" hook_type="service" module="comment" call_name="comment.service_process_add" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="comment" hook_type="service" module="comment" call_name="comment.service_process_deleteinline" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="comment" hook_type="service" module="comment" call_name="comment.service_process_delete" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="comment" hook_type="service" module="comment" call_name="comment.service_process___call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="comment" hook_type="template" module="comment" call_name="comment.template_block_display_add_comment" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="comment" hook_type="template" module="comment" call_name="comment.template_block_display_textarea_start" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="comment" hook_type="template" module="comment" call_name="comment.template_block_display_textarea_end" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="comment" hook_type="component" module="comment" call_name="comment.component_block_mini_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="comment" hook_type="component" module="comment" call_name="comment.component_block_moderate_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="comment" hook_type="controller" module="comment" call_name="comment.component_controller_admincp_spam_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="comment" hook_type="controller" module="comment" call_name="comment.component_controller_moderate_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="comment" hook_type="controller" module="comment" call_name="comment.component_controller_view_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="comment" hook_type="controller" module="comment" call_name="comment.component_controller_rss_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="comment" hook_type="service" module="comment" call_name="comment.service_comment_get__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="comment" hook_type="service" module="comment" call_name="comment.service_comment_get__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="comment" hook_type="service" module="comment" call_name="comment.service_comment_getforrss__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="comment" hook_type="service" module="comment" call_name="comment.service_comment_getforrss__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="comment" hook_type="service" module="comment" call_name="comment.component_service_callback_getredirectrequest__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="comment" hook_type="service" module="comment" call_name="comment.component_service_callback_getnotificationsettings__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="comment" hook_type="service" module="comment" call_name="comment.component_service_callback_ondeleteuser__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="comment" hook_type="service" module="comment" call_name="comment.component_service_callback_updatecounterlist__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="comment" hook_type="service" module="comment" call_name="comment.component_service_callback_updatecounter__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="comment" hook_type="service" module="comment" call_name="comment.component_service_callback_updatecounter__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="comment" hook_type="component" module="comment" call_name="comment.component_ajax_get_quote" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="comment" hook_type="component" module="comment" call_name="comment.component_ajax_get_text" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="comment" hook_type="template" module="comment" call_name="comment.template_block_display_add_comment_define" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="comment" hook_type="service" module="comment" call_name="comment.service_process_add_end" added="1286546859" version_id="2.0.7" />
		<hook module_id="comment" hook_type="service" module="comment" call_name="comment.service_comment_get_count_query" added="1286546859" version_id="2.0.7" />
		<hook module_id="comment" hook_type="service" module="comment" call_name="comment.service_comment_get_query" added="1286546859" version_id="2.0.7" />
		<hook module_id="comment" hook_type="component" module="comment" call_name="comment.component_ajax_browse" added="1286546859" version_id="2.0.7" />
		<hook module_id="comment" hook_type="component" module="comment" call_name="comment.component_block_view_process_template_load" added="1290072896" version_id="2.0.7" />
		<hook module_id="comment" hook_type="component" module="comment" call_name="comment.component_block_comment_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="comment" hook_type="service" module="comment" call_name="comment.service_process_notify_1" added="1335951260" version_id="3.2.0" />
		<hook module_id="comment" hook_type="component" module="comment" call_name="comment.component_block_share_clean" added="1339076699" version_id="3.3.0beta1" />
		<hook module_id="comment" hook_type="service" module="comment" call_name="comment.service_comment_massmail__1" added="1361180401" version_id="3.5.0rc1" />
		<hook module_id="comment" hook_type="service" module="comment" call_name="comment.service_comment_massmail__0" added="1363075699" version_id="3.5.0" />
	</hooks>
	<components>
		<component module_id="comment" component="view" m_connection="" module="comment" is_controller="0" is_block="1" is_active="1" />
		<component module_id="comment" component="rating" m_connection="" module="comment" is_controller="0" is_block="1" is_active="1" />
		<component module_id="comment" component="ajax" m_connection="" module="comment" is_controller="0" is_block="0" is_active="1" />
	</components>
	<user_group_settings>
		<setting is_admin_setting="0" module_id="comment" type="boolean" admin="1" user="0" guest="0" staff="1" module="comment" ordering="0">edit_own_comment</setting>
		<setting is_admin_setting="0" module_id="comment" type="boolean" admin="1" user="0" guest="0" staff="1" module="comment" ordering="0">edit_user_comment</setting>
		<setting is_admin_setting="0" module_id="comment" type="boolean" admin="1" user="1" guest="0" staff="1" module="comment" ordering="0">delete_own_comment</setting>
		<setting is_admin_setting="0" module_id="comment" type="boolean" admin="1" user="0" guest="0" staff="1" module="comment" ordering="0">delete_user_comment</setting>
		<setting is_admin_setting="0" module_id="comment" type="integer" admin="1" user="1" guest="1" staff="1" module="comment" ordering="0">points_comment</setting>
		<setting is_admin_setting="0" module_id="comment" type="boolean" admin="1" user="1" guest="0" staff="1" module="comment" ordering="0">can_post_comments</setting>
		<setting is_admin_setting="0" module_id="comment" type="integer" admin="0" user="0" guest="1" staff="0" module="comment" ordering="0">comment_post_flood_control</setting>
		<setting is_admin_setting="0" module_id="comment" type="boolean" admin="1" user="0" guest="0" staff="1" module="comment" ordering="0">can_moderate_comments</setting>
		<setting is_admin_setting="0" module_id="comment" type="boolean" admin="1" user="1" guest="0" staff="1" module="comment" ordering="0">can_delete_comments_posted_on_own_profile</setting>
		<setting is_admin_setting="0" module_id="comment" type="boolean" admin="1" user="1" guest="0" staff="1" module="comment" ordering="0">can_comment_on_own_profile</setting>
		<setting is_admin_setting="0" module_id="comment" type="boolean" admin="0" user="0" guest="0" staff="0" module="comment" ordering="0">approve_all_comments</setting>
		<setting is_admin_setting="0" module_id="comment" type="boolean" admin="0" user="0" guest="0" staff="0" module="comment" ordering="0">can_delete_comment_on_own_item</setting>
	</user_group_settings>
	<tables><![CDATA[a:4:{s:14:"phpfox_comment";a:3:{s:7:"COLUMNS";a:19:{s:10:"comment_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:9:"parent_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"type_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"item_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:13:"owner_user_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"update_time";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:11:"update_user";a:4:{i:0;s:9:"VCHAR:100";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:6:"rating";a:4:{i:0;s:8:"VCHAR:10";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"ip_address";a:4:{i:0;s:8:"VCHAR:15";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:6:"author";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:12:"author_email";a:4:{i:0;s:9:"VCHAR:100";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"author_url";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:7:"view_id";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:11:"child_total";a:4:{i:0;s:5:"USINT";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"total_like";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"total_dislike";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"feed_table";a:4:{i:0;s:8:"VCHAR:10";i:1;s:4:"feed";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:10:"comment_id";s:4:"KEYS";a:6:{s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"user_id";i:1;s:7:"view_id";}}s:13:"owner_user_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:13:"owner_user_id";i:1;s:7:"view_id";}}s:7:"type_id";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:7:"type_id";i:1;s:7:"item_id";i:2;s:7:"view_id";}}s:9:"parent_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:9:"parent_id";i:1;s:7:"view_id";}}s:11:"parent_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:4:{i:0;s:9:"parent_id";i:1;s:7:"type_id";i:2;s:7:"item_id";i:3;s:7:"view_id";}}s:7:"view_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"view_id";}}}s:19:"phpfox_comment_hash";a:2:{s:7:"COLUMNS";a:3:{s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"item_hash";a:4:{i:0;s:7:"CHAR:32";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:4:"KEYS";a:1:{s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:7:"user_id";i:1;s:9:"item_hash";i:2;s:10:"time_stamp";}}}}s:21:"phpfox_comment_rating";a:2:{s:7:"COLUMNS";a:5:{s:10:"comment_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:6:"rating";a:4:{i:0;s:7:"VCHAR:2";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"ip_address";a:4:{i:0;s:8:"VCHAR:15";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:4:"KEYS";a:1:{s:10:"comment_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:10:"comment_id";i:1;s:7:"user_id";}}}}s:19:"phpfox_comment_text";a:2:{s:7:"COLUMNS";a:3:{s:10:"comment_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:4:"text";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:11:"text_parsed";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:4:"KEYS";a:1:{s:10:"comment_id";a:2:{i:0;s:5:"INDEX";i:1;s:10:"comment_id";}}}}]]></tables>
</module>