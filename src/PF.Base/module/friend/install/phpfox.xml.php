<module>
	<data>
		<module_id>friend</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>0</is_menu>
		<menu />
		<phrase_var_name>module_friend</phrase_var_name>
		<writable />
	</data>
	<menus>
		<menu module_id="friend" parent_var_name="" m_connection="profile" var_name="menu_friend_friends" ordering="16" url_value="profile.friend" version_id="2.0.0alpha1" disallow_access="" module="friend" />
		<menu module_id="friend" parent_var_name="" m_connection="mobile" var_name="menu_friend_friends_532c28d5412dd75bf975fb951c740a30" ordering="118" url_value="friend" version_id="3.1.0rc1" disallow_access="" module="friend" mobile_icon="small_friends.png" />
	</menus>
	<settings>
		<setting group="" module_id="friend" is_hidden="0" type="integer" var_name="total_requests_display" phrase_var_name="setting_total_requests_display" ordering="1" version_id="2.0.0alpha1">10</setting>
		<setting group="" module_id="friend" is_hidden="0" type="integer" var_name="friend_display_limit" phrase_var_name="setting_friend_display_limit" ordering="1" version_id="2.0.0beta3">6</setting>
		<setting group="" module_id="friend" is_hidden="0" type="boolean" var_name="enable_birthday_notices" phrase_var_name="setting_enable_birthday_notices" ordering="1" version_id="2.0.0beta4">1</setting>
		<setting group="" module_id="friend" is_hidden="0" type="integer" var_name="days_to_check_for_birthday" phrase_var_name="setting_days_to_check_for_birthday" ordering="1" version_id="2.0.0beta4">7</setting>
		<setting group="" module_id="friend" is_hidden="0" type="boolean" var_name="show_empty_birthdays" phrase_var_name="setting_show_empty_birthdays" ordering="1" version_id="2.0.0beta4">0</setting>
		<setting group="" module_id="friend" is_hidden="0" type="integer" var_name="friend_suggestion_search_total" phrase_var_name="setting_friend_suggestion_search_total" ordering="1" version_id="2.0.0rc12">50</setting>
		<setting group="" module_id="friend" is_hidden="0" type="boolean" var_name="enable_friend_suggestion" phrase_var_name="setting_enable_friend_suggestion" ordering="1" version_id="2.0.0rc12">0</setting>
		<setting group="" module_id="friend" is_hidden="0" type="integer" var_name="friend_suggestion_timeout" phrase_var_name="setting_friend_suggestion_timeout" ordering="1" version_id="2.0.0rc12">1440</setting>
		<setting group="" module_id="friend" is_hidden="0" type="boolean" var_name="friend_suggestion_user_based" phrase_var_name="setting_friend_suggestion_user_based" ordering="1" version_id="2.0.0rc12">0</setting>
		<setting group="" module_id="friend" is_hidden="0" type="boolean" var_name="hide_denied_requests_from_pending_list" phrase_var_name="setting_hide_denied_requests_from_pending_list" ordering="1" version_id="2.0.7">1</setting>
		<setting group="" module_id="friend" is_hidden="0" type="integer" var_name="friend_cache_limit" phrase_var_name="setting_friend_cache_limit" ordering="1" version_id="3.0.0Beta1">100</setting>
		<setting group="" module_id="friend" is_hidden="0" type="boolean" var_name="friends_only_profile" phrase_var_name="setting_friends_only_profile" ordering="1" version_id="3.0.1">0</setting>
		<setting group="" module_id="friend" is_hidden="0" type="integer" var_name="cache_rand_list_of_friends" phrase_var_name="setting_cache_rand_list_of_friends" ordering="3" version_id="3.6.0rc1">60</setting>
		<setting group="" module_id="friend" is_hidden="0" type="boolean" var_name="cache_is_friend" phrase_var_name="setting_cache_is_friend" ordering="4" version_id="3.6.0rc1">0</setting>
		<setting group="" module_id="friend" is_hidden="0" type="boolean" var_name="cache_friend_list" phrase_var_name="setting_cache_friend_list" ordering="5" version_id="3.6.0rc1">0</setting>
		<setting group="" module_id="friend" is_hidden="0" type="boolean" var_name="load_friends_online_ajax" phrase_var_name="setting_load_friends_online_ajax" ordering="6" version_id="3.6.0rc1">0</setting>
		<setting group="seo" module_id="friend" is_hidden="0" type="large_string" var_name="friend_meta_keywords" phrase_var_name="setting_friend_meta_keywords" ordering="7" version_id="2.0.0rc1">friends, buddies</setting>
	</settings>
	<blocks>
		<block type_id="0" m_connection="core.index-member" module_id="friend" component="mini" location="1" is_active="1" ordering="2" disallow_access="" can_move="1">
			<title><![CDATA[{_p var=&#039;friends_online&#039;}]]></title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="profile.index" module_id="friend" component="profile.small" location="1" is_active="1" ordering="2" disallow_access="" can_move="1">
			<title><![CDATA[{_p var=&#039;friends&#039;}]]></title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="core.index-member" module_id="friend" component="birthday" location="3" is_active="1" ordering="4" disallow_access="" can_move="1">
			<title><![CDATA[{_p var=&#039;upcoming_events&#039;}]]></title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="profile.index" module_id="friend" component="mutual-friend" location="1" is_active="1" ordering="3" disallow_access="" can_move="1">
			<title><![CDATA[{_p var=&#039;mutual_friends&#039;}]]></title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="core.index-member" module_id="friend" component="suggestion" location="3" is_active="1" ordering="5" disallow_access="" can_move="1">
			<title><![CDATA[{_p var=&#039;suggestions&#039;}]]></title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="event.index" module_id="friend" component="birthday" location="3" is_active="1" ordering="1" disallow_access="" can_move="0">
			<title>Birthdays</title>
			<source_code />
			<source_parsed />
		</block>
	</blocks>
	<hooks>
		<hook module_id="friend" hook_type="component" module="friend" call_name="friend.component_block_accept_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="friend" hook_type="component" module="friend" call_name="friend.component_block_request_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="friend" hook_type="component" module="friend" call_name="friend.component_block_list_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="friend" hook_type="controller" module="friend" call_name="friend.component_controller_profile_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="friend" hook_type="controller" module="friend" call_name="friend.component_controller_index_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="friend" hook_type="service" module="friend" call_name="friend.service_callback___call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="friend" hook_type="service" module="friend" call_name="friend.service_request_request_get" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="friend" hook_type="service" module="friend" call_name="friend.service_request_request__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="friend" hook_type="service" module="friend" call_name="friend.service_request_process__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="friend" hook_type="service" module="friend" call_name="friend.service_friend___call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="friend" hook_type="service" module="friend" call_name="friend.service_process__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="friend" hook_type="service" module="friend" call_name="friend.service_list_list__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="friend" hook_type="service" module="friend" call_name="friend.service_list_process_update" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="friend" hook_type="service" module="friend" call_name="friend.service_list_process_delete" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="friend" hook_type="service" module="friend" call_name="friend.service_list_process_move" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="friend" hook_type="service" module="friend" call_name="friend.service_list_process__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="friend" hook_type="component" module="friend" call_name="friend.component_block_profile_small_clean" added="1231935380" version_id="2.0.0alpha1" />
		<hook module_id="friend" hook_type="controller" module="friend" call_name="friend.component_controller_pending_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="friend" hook_type="controller" module="friend" call_name="friend.component_controller_request_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="friend" hook_type="component" module="friend" call_name="friend.component_block_search_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="friend" hook_type="component" module="friend" call_name="friend.component_block_suggestion_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="friend" hook_type="component" module="friend" call_name="friend.component_block_search_process" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="friend" hook_type="component" module="friend" call_name="friend.component_block_mutual_friend_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="friend" hook_type="controller" module="friend" call_name="friend.component_controller_accept_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="friend" hook_type="service" module="friend" call_name="friend.service_suggestion__call" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="friend" hook_type="service" module="friend" call_name="friend.service_block__call" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="friend" hook_type="service" module="friend" call_name="friend.service_suggestion__build_search" added="1261572640" version_id="2.0.0" />
		<hook module_id="friend" hook_type="controller" module="friend" call_name="friend.component_controller_suggestion_clean" added="1261572640" version_id="2.0.0" />
		<hook module_id="friend" hook_type="controller" module="friend" call_name="friend.component_controller_index_mobile_clean" added="1267629983" version_id="2.0.4" />
		<hook module_id="friend" hook_type="service" module="friend" call_name="friend.service_process__updatefriendcount" added="1276177474" version_id="2.0.5" />
		<hook module_id="friend" hook_type="service" module="friend" call_name="friend.service_callback__updatecounter" added="1276177474" version_id="2.0.5" />
		<hook module_id="friend" hook_type="service" module="friend" call_name="friend.service_friend_get" added="1276177474" version_id="2.0.5" />
		<hook module_id="friend" hook_type="service" module="friend" call_name="friend.service_friend_getmutualfriends" added="1276177474" version_id="2.0.5" />
		<hook module_id="friend" hook_type="service" module="friend" call_name="friend.service_callback_getnewsfeed_start" added="1286546859" version_id="2.0.7" />
		<hook module_id="friend" hook_type="template" module="friend" call_name="friend.template_block_congratulate_form" added="1299062480" version_id="2.0.8" />
		<hook module_id="friend" hook_type="component" module="friend" call_name="friend.component_block_list_add_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="friend" hook_type="component" module="friend" call_name="friend.component_block_mutual_browse_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="friend" hook_type="service" module="friend" call_name="friend.service_api__call" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="friend" hook_type="service" module="friend" call_name="friend.component_service_callback_getactivityfeed__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="friend" hook_type="service" module="friend" call_name="friend.service_process_add__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="friend" hook_type="service" module="friend" call_name="friend.service_process_delete__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="friend" hook_type="service" module="friend" call_name="friend.service_getfromcachequery" added="1358258443" version_id="3.5.0beta1" />
		<hook module_id="friend" hook_type="component" module="friend" call_name="friend.component_block_search_get" added="1358258443" version_id="3.5.0beta1" />
		<hook module_id="friend" hook_type="component" module="friend" call_name="friend.component_block_mini_process" added="1358258443" version_id="3.5.0beta1" />
		<hook module_id="friend" hook_type="service" module="friend" call_name="friend.service_request_get__2" added="1361180401" version_id="3.5.0rc1" />
		<hook module_id="friend" hook_type="service" module="friend" call_name="friend.service_request_get__3" added="1361180401" version_id="3.5.0rc1" />
		<hook module_id="friend" hook_type="template" module="friend" call_name="friend.template_block_accept__1" added="1361180401" version_id="3.5.0rc1" />
	</hooks>
	<components>
		<component module_id="friend" component="mini" m_connection="" module="friend" is_controller="0" is_block="1" is_active="1" />
		<component module_id="friend" component="index" m_connection="friend.index" module="friend" is_controller="1" is_block="0" is_active="1" />
		<component module_id="friend" component="list" m_connection="" module="friend" is_controller="0" is_block="1" is_active="1" />
		<component module_id="friend" component="ajax" m_connection="" module="friend" is_controller="0" is_block="0" is_active="1" />
		<component module_id="friend" component="profile.small" m_connection="" module="friend" is_controller="0" is_block="1" is_active="1" />
		<component module_id="friend" component="accept" m_connection="" module="friend" is_controller="0" is_block="1" is_active="1" />
		<component module_id="friend" component="request" m_connection="" module="friend" is_controller="0" is_block="1" is_active="1" />
		<component module_id="friend" component="profile" m_connection="friend.profile" module="friend" is_controller="1" is_block="0" is_active="1" />
		<component module_id="friend" component="pending" m_connection="friend.pending" module="friend" is_controller="1" is_block="0" is_active="1" />
		<component module_id="friend" component="birthday" m_connection="" module="friend" is_controller="0" is_block="1" is_active="1" />
		<component module_id="friend" component="mutual-friend" m_connection="" module="friend" is_controller="0" is_block="1" is_active="1" />
		<component module_id="friend" component="suggestion" m_connection="" module="friend" is_controller="0" is_block="1" is_active="1" />
		<component module_id="friend" component="remove" m_connection="" module="friend" is_controller="0" is_block="1" is_active="1" />
		<component module_id="friend" component="search-small" m_connection="" module="friend" is_controller="0" is_block="1" is_active="1" />
	</components>
	<user_group_settings>
		<setting is_admin_setting="0" module_id="friend" type="boolean" admin="1" user="1" guest="0" staff="1" module="friend" ordering="0">can_add_friends</setting>
		<setting is_admin_setting="0" module_id="friend" type="boolean" admin="1" user="1" guest="0" staff="1" module="friend" ordering="0">can_add_folders</setting>
		<setting is_admin_setting="0" module_id="friend" type="integer" admin="10" user="10" guest="0" staff="10" module="friend" ordering="0">total_folders</setting>
		<setting is_admin_setting="0" module_id="friend" type="boolean" admin="true" user="true" guest="false" staff="true" module="friend" ordering="0">link_to_remove_friend_on_profile</setting>
	</user_group_settings>
	<tables><![CDATA[a:6:{s:13:"phpfox_friend";a:3:{s:7:"COLUMNS";a:8:{s:9:"friend_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"is_page";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"list_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:14:"friend_user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:13:"is_top_friend";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"ordering";a:4:{i:0;s:6:"TINT:3";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:9:"friend_id";s:4:"KEYS";a:4:{s:10:"user_check";a:2:{i:0;s:6:"UNIQUE";i:1;a:2:{i:0;s:7:"user_id";i:1;s:14:"friend_user_id";}}s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"user_id";}s:7:"list_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"list_id";i:1;s:7:"user_id";}}s:14:"friend_user_id";a:2:{i:0;s:5:"INDEX";i:1;s:14:"friend_user_id";}}}s:18:"phpfox_friend_list";a:3:{s:7:"COLUMNS";a:5:{s:7:"list_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"is_profile";a:4:{i:0;s:6:"TINT:3";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:4:"name";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:7:"list_id";s:4:"KEYS";a:3:{s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"user_id";}s:7:"list_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"list_id";i:1;s:7:"user_id";}}s:9:"user_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"user_id";i:1;s:10:"is_profile";}}}}s:23:"phpfox_friend_list_data";a:2:{s:7:"COLUMNS";a:3:{s:7:"list_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:14:"friend_user_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"ordering";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:4:"KEYS";a:2:{s:7:"list_id";a:2:{i:0;s:6:"UNIQUE";i:1;a:2:{i:0;s:7:"list_id";i:1;s:14:"friend_user_id";}}s:9:"list_id_2";a:2:{i:0;s:5:"INDEX";i:1;s:7:"list_id";}}}s:21:"phpfox_friend_request";a:3:{s:7:"COLUMNS";a:9:{s:10:"request_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"is_seen";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:14:"friend_user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"is_ignore";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"list_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"message";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:16:"relation_data_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:11:"PRIMARY_KEY";s:10:"request_id";s:4:"KEYS";a:5:{s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"user_id";i:1;s:14:"friend_user_id";}}s:7:"ignored";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"user_id";i:1;s:9:"is_ignore";}}s:14:"friend_user_id";a:2:{i:0;s:5:"INDEX";i:1;s:14:"friend_user_id";}s:16:"relation_data_id";a:2:{i:0;s:5:"INDEX";i:1;s:16:"relation_data_id";}s:9:"user_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:7:"user_id";i:1;s:7:"is_seen";i:2;s:9:"is_ignore";}}}}s:22:"phpfox_friend_birthday";a:3:{s:7:"COLUMNS";a:7:{s:11:"birthday_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:20:"birthday_user_sender";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:22:"birthday_user_receiver";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:16:"birthday_message";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"egift_id";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:9:"status_id";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:3:"YES";}}s:11:"PRIMARY_KEY";s:11:"birthday_id";s:4:"KEYS";a:2:{s:20:"birthday_user_sender";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:20:"birthday_user_sender";i:1;s:22:"birthday_user_receiver";}}s:11:"birthday_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:11:"birthday_id";i:1;s:22:"birthday_user_receiver";}}}}s:18:"phpfox_friend_hide";a:3:{s:7:"COLUMNS";a:4:{s:7:"hide_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:14:"friend_user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:7:"hide_id";s:4:"KEYS";a:1:{s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"user_id";}}}}]]></tables>
</module>