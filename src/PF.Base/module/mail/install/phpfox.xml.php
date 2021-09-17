<module>
	<data>
		<module_id>mail</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>0</is_menu>
		<menu />
		<phrase_var_name>module_mail</phrase_var_name>
		<writable />
	</data>
	<menus>
		<menu module_id="mail" parent_var_name="" m_connection="mail" var_name="menu_compose" ordering="2" url_value="mail.compose" version_id="2.0.0alpha1" disallow_access="" module="mail" />
		<menu module_id="mail" parent_var_name="" m_connection="mobile" var_name="menu_mail_mail_532c28d5412dd75bf975fb951c740a30" ordering="119" url_value="mail" version_id="3.1.0rc1" disallow_access="" module="mail" mobile_icon="small_mail.png" />
	</menus>
	<settings>
		<setting group="time_stamps" module_id="mail" is_hidden="0" type="string" var_name="mail_time_stamp" phrase_var_name="setting_mail_time_stamp" ordering="9" version_id="2.0.0alpha1">M j, g:i a</setting>
		<setting group="" module_id="mail" is_hidden="0" type="boolean" var_name="show_core_mail_folders_item_count" phrase_var_name="setting_show_core_mail_folders_item_count" ordering="1" version_id="2.0.0alpha1">0</setting>
		<setting group="" module_id="mail" is_hidden="0" type="boolean" var_name="enable_mail_box_warning" phrase_var_name="setting_enable_mail_box_warning" ordering="1" version_id="2.0.0beta5">1</setting>
		<setting group="" module_id="mail" is_hidden="0" type="boolean" var_name="enable_cron_delete_old_mail" phrase_var_name="setting_enable_cron_delete_old_mail" ordering="1" version_id="2.0.0beta5">1</setting>
		<setting group="" module_id="mail" is_hidden="0" type="integer" var_name="cron_delete_messages_delay" phrase_var_name="setting_cron_delete_messages_delay" ordering="2" version_id="2.0.0beta5">30</setting>
		<setting group="" module_id="mail" is_hidden="0" type="integer" var_name="message_age_to_delete" phrase_var_name="setting_message_age_to_delete" ordering="1" version_id="2.0.0beta5">20</setting>
		<setting group="" module_id="mail" is_hidden="0" type="boolean" var_name="delete_sent_when_account_cancel" phrase_var_name="setting_delete_sent_when_account_cancel" ordering="1" version_id="2.0.0beta5">1</setting>
		<setting group="spam" module_id="mail" is_hidden="0" type="boolean" var_name="mail_hash_check" phrase_var_name="setting_mail_hash_check" ordering="2" version_id="2.0.0rc1">0</setting>
		<setting group="spam" module_id="mail" is_hidden="0" type="integer" var_name="total_mail_messages_to_check" phrase_var_name="setting_total_mail_messages_to_check" ordering="10" version_id="2.0.0rc1">10</setting>
		<setting group="spam" module_id="mail" is_hidden="0" type="integer" var_name="total_minutes_to_wait_for_pm" phrase_var_name="setting_total_minutes_to_wait_for_pm" ordering="14" version_id="2.0.0rc1">2</setting>
		<setting group="" module_id="mail" is_hidden="0" type="boolean" var_name="show_preview_message" phrase_var_name="setting_show_preview_message" ordering="1" version_id="2.0.0rc3">1</setting>
		<setting group="" module_id="mail" is_hidden="0" type="boolean" var_name="disallow_select_of_recipients" phrase_var_name="setting_disallow_select_of_recipients" ordering="1" version_id="2.0.7">0</setting>
		<setting group="" module_id="mail" is_hidden="0" type="boolean" var_name="update_message_notification_preview" phrase_var_name="setting_update_message_notification_preview" ordering="1" version_id="3.1.0beta1">1</setting>
		<setting group="spam" module_id="mail" is_hidden="1" type="boolean" var_name="spam_check_messages" phrase_var_name="setting_spam_check_messages" ordering="7" version_id="2.0.0rc1">1</setting>
		<setting group="" module_id="mail" is_hidden="1" type="boolean" var_name="threaded_mail_conversation" phrase_var_name="setting_threaded_mail_conversation" ordering="1" version_id="3.2.0beta1">1</setting>
	</settings>
	<hooks>
		<hook module_id="mail" hook_type="component" module="mail" call_name="mail.component_block_folder_process" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="mail" hook_type="component" module="mail" call_name="mail.component_block_folder_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="mail" hook_type="component" module="mail" call_name="mail.component_block_box_edit_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="mail" hook_type="controller" module="mail" call_name="mail.component_controller_box_index_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="mail" hook_type="controller" module="mail" call_name="mail.component_controller_index_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="mail" hook_type="controller" module="mail" call_name="mail.component_controller_sentbox_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="mail" hook_type="controller" module="mail" call_name="mail.component_controller_view_process_start" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="mail" hook_type="controller" module="mail" call_name="mail.component_controller_view_process_validation" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="mail" hook_type="controller" module="mail" call_name="mail.component_controller_view_process_end" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="mail" hook_type="controller" module="mail" call_name="mail.component_controller_view_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="mail" hook_type="controller" module="mail" call_name="mail.component_controller_compose_controller_to" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="mail" hook_type="controller" module="mail" call_name="mail.component_controller_compose_controller_validation" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="mail" hook_type="controller" module="mail" call_name="mail.component_controller_compose_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="mail" hook_type="controller" module="mail" call_name="mail.component_controller_trash_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="mail" hook_type="service" module="mail" call_name="mail.service_folder_folder__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="mail" hook_type="service" module="mail" call_name="mail.service_folder_process_move" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="mail" hook_type="service" module="mail" call_name="mail.service_folder_process_add" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="mail" hook_type="service" module="mail" call_name="mail.service_folder_process_update" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="mail" hook_type="service" module="mail" call_name="mail.service_folder_process_delete" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="mail" hook_type="service" module="mail" call_name="mail.service_folder_process__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="mail" hook_type="service" module="mail" call_name="mail.service_mail_get" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="mail" hook_type="service" module="mail" call_name="mail.service_mail_getmail" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="mail" hook_type="service" module="mail" call_name="mail.service_mail__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="mail" hook_type="service" module="mail" call_name="mail.service_process_add" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="mail" hook_type="service" module="mail" call_name="mail.service_process_toggleview" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="mail" hook_type="service" module="mail" call_name="mail.service_process_delete" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="mail" hook_type="service" module="mail" call_name="mail.service_process_deletetrash" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="mail" hook_type="service" module="mail" call_name="mail.service_process_undelete" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="mail" hook_type="service" module="mail" call_name="mail.service_process__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="mail" hook_type="service" module="mail" call_name="mail.service_callback__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="mail" hook_type="service" module="mail" call_name="mail.service_process_cronDeleteMessages_start" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="mail" hook_type="service" module="mail" call_name="mail.service_process_cronDeleteMessages_end" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="mail" hook_type="controller" module="mail" call_name="mail.component_controller_admincp_view_clean" added="1261572640" version_id="2.0.0" />
		<hook module_id="mail" hook_type="controller" module="mail" call_name="mail.component_controller_compose_mobile_clean" added="1267629983" version_id="2.0.4" />
		<hook module_id="mail" hook_type="controller" module="mail" call_name="mail.component_controller_index_mobile_clean" added="1267629983" version_id="2.0.4" />
		<hook module_id="mail" hook_type="controller" module="mail" call_name="mail.component_controller_view_mobile_clean" added="1267629983" version_id="2.0.4" />
		<hook module_id="mail" hook_type="component" module="mail" call_name="mail.component_ajax_compose" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="mail" hook_type="component" module="mail" call_name="mail.component_block_box_add_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="mail" hook_type="component" module="mail" call_name="mail.component_block_box_select_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="mail" hook_type="service" module="mail" call_name="mail.service_api__call" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="mail" hook_type="service" module="mail" call_name="mail.service_mail_canmessageuser_1" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="mail" hook_type="service" module="mail" call_name="mail.service_process_add_1" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="mail" hook_type="template" module="mail" call_name="mail.template_controller_compose_ajax_onsubmit" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="mail" hook_type="controller" module="mail" call_name="mail.component_controller_thread_clean" added="1334069444" version_id="3.2.0beta1" />
		<hook module_id="mail" hook_type="service" module="mail" call_name="mail.service_process_add_2" added="1384774431" version_id="3.7.3" />
	</hooks>
	<components>
		<component module_id="mail" component="compose" m_connection="mail.compose" module="mail" is_controller="1" is_block="0" is_active="1" />
		<component module_id="mail" component="folder" m_connection="" module="mail" is_controller="0" is_block="1" is_active="1" />
		<component module_id="mail" component="ajax" m_connection="" module="mail" is_controller="0" is_block="0" is_active="1" />
		<component module_id="mail" component="box.edit" m_connection="" module="mail" is_controller="0" is_block="1" is_active="1" />
		<component module_id="mail" component="index" m_connection="mail.index" module="mail" is_controller="1" is_block="0" is_active="1" />
		<component module_id="mail" component="view" m_connection="mail.view" module="mail" is_controller="1" is_block="0" is_active="1" />
		<component module_id="mail" component="sentbox" m_connection="mail.sentbox" module="mail" is_controller="1" is_block="0" is_active="1" />
		<component module_id="mail" component="box.index" m_connection="mail.box.index" module="mail" is_controller="1" is_block="0" is_active="1" />
		<component module_id="mail" component="trash" m_connection="mail.trash" module="mail" is_controller="1" is_block="0" is_active="1" />
	</components>
	<crons>
		<cron module_id="mail" type_id="3" every="30"><![CDATA[Phpfox::getService('mail.process')->cronDeleteMessages();]]></cron>
	</crons>
	<user_group_settings>
		<setting is_admin_setting="0" module_id="mail" type="integer" admin="10" user="10" guest="0" staff="10" module="mail" ordering="0">total_folders</setting>
		<setting is_admin_setting="0" module_id="mail" type="boolean" admin="1" user="1" guest="0" staff="1" module="mail" ordering="0">can_compose_message</setting>
		<setting is_admin_setting="0" module_id="mail" type="boolean" admin="1" user="1" guest="0" staff="1" module="mail" ordering="0">can_add_folders</setting>
		<setting is_admin_setting="0" module_id="mail" type="boolean" admin="1" user="0" guest="0" staff="0" module="mail" ordering="0">show_core_mail_folders_item_count</setting>
		<setting is_admin_setting="0" module_id="mail" type="boolean" admin="1" user="1" guest="0" staff="1" module="mail" ordering="0">can_add_attachment_on_mail</setting>
		<setting is_admin_setting="0" module_id="mail" type="boolean" admin="false" user="true" guest="true" staff="false" module="mail" ordering="0">restrict_message_to_friends</setting>
        <setting is_admin_setting="0" module_id="mail" type="boolean" admin="true" user="true" guest="false" staff="true" module="mail" ordering="0">can_message_self</setting>
		<setting is_admin_setting="0" module_id="mail" type="integer" admin="0" user="80" guest="01" staff="90" module="mail" ordering="0">mail_box_warning</setting>
		<setting is_admin_setting="0" module_id="mail" type="boolean" admin="true" user="false" guest="false" staff="false" module="mail" ordering="0">can_read_private_messages</setting>
		<setting is_admin_setting="0" module_id="mail" type="boolean" admin="true" user="false" guest="false" staff="false" module="mail" ordering="0">can_delete_others_messages</setting>
		<setting is_admin_setting="0" module_id="mail" type="boolean" admin="0" user="0" guest="0" staff="0" module="mail" ordering="0">enable_captcha_on_mail</setting>
		<setting is_admin_setting="0" module_id="mail" type="integer" admin="0" user="50" guest="1" staff="100" module="mail" ordering="0">send_message_to_max_users_each_time</setting>
	</user_group_settings>
	<tables><![CDATA[a:8:{s:11:"phpfox_mail";a:3:{s:7:"COLUMNS";a:15:{s:7:"mail_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:9:"parent_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"mass_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"subject";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:7:"preview";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:13:"owner_user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:15:"owner_folder_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"owner_type_id";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:14:"viewer_user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:16:"viewer_folder_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:14:"viewer_type_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"viewer_is_new";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:12:"time_updated";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:16:"total_attachment";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:7:"mail_id";s:4:"KEYS";a:4:{s:13:"owner_user_id";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:13:"owner_user_id";i:1;s:15:"owner_folder_id";i:2;s:13:"owner_type_id";}}s:14:"viewer_user_id";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:14:"viewer_user_id";i:1;s:16:"viewer_folder_id";i:2;s:14:"viewer_type_id";}}s:15:"owner_user_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:13:"owner_user_id";i:1;s:14:"viewer_user_id";}}s:7:"mail_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"mail_id";i:1;s:13:"owner_user_id";}}}}s:18:"phpfox_mail_folder";a:3:{s:7:"COLUMNS";a:3:{s:9:"folder_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:4:"name";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:9:"folder_id";s:4:"KEYS";a:3:{s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"user_id";}s:4:"name";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:4:"name";i:1;s:7:"user_id";}}s:9:"folder_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:9:"folder_id";i:1;s:4:"name";}}}}s:16:"phpfox_mail_hash";a:2:{s:7:"COLUMNS";a:3:{s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"item_hash";a:4:{i:0;s:7:"CHAR:32";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:4:"KEYS";a:1:{s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:7:"user_id";i:1;s:9:"item_hash";i:2;s:10:"time_stamp";}}}}s:16:"phpfox_mail_text";a:2:{s:7:"COLUMNS";a:3:{s:7:"mail_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:4:"text";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:11:"text_parsed";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:4:"KEYS";a:1:{s:7:"mail_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"mail_id";}}}s:18:"phpfox_mail_thread";a:3:{s:7:"COLUMNS";a:4:{s:9:"thread_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"hash_id";a:4:{i:0;s:7:"CHAR:32";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"last_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:9:"thread_id";s:4:"KEYS";a:1:{s:7:"last_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"last_id";}}}s:26:"phpfox_mail_thread_forward";a:3:{s:7:"COLUMNS";a:3:{s:10:"forward_id";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:10:"message_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"copy_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:10:"forward_id";s:4:"KEYS";a:2:{s:10:"message_id";a:2:{i:0;s:5:"INDEX";i:1;s:10:"message_id";}s:7:"copy_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"copy_id";}}}s:23:"phpfox_mail_thread_text";a:3:{s:7:"COLUMNS";a:8:{s:10:"message_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:9:"thread_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:4:"text";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:16:"total_attachment";a:4:{i:0;s:7:"UINT:11";i:1;s:1:"0";i:2;s:0:"";i:3;s:3:"YES";}s:9:"is_mobile";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:11:"has_forward";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:10:"message_id";s:4:"KEYS";a:2:{s:9:"thread_id";a:2:{i:0;s:5:"INDEX";i:1;s:9:"thread_id";}s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"user_id";}}}s:23:"phpfox_mail_thread_user";a:2:{s:7:"COLUMNS";a:6:{s:9:"thread_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"is_read";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"is_archive";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"is_sent";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:14:"is_sent_update";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:4:"KEYS";a:6:{s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"user_id";}s:9:"thread_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:9:"thread_id";i:1;s:7:"user_id";}}s:9:"user_id_3";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"user_id";i:1;s:7:"is_sent";}}s:9:"user_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:7:"user_id";i:1;s:10:"is_archive";i:2;s:14:"is_sent_update";}}s:9:"user_id_4";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:7:"user_id";i:1;s:10:"is_archive";i:2;s:7:"is_sent";}}s:9:"user_id_5";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"user_id";i:1;s:10:"is_archive";}}}}}]]></tables>
</module>