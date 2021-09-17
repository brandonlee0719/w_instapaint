<module>
	<data>
		<module_id>feed</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>0</is_menu>
		<menu />
		<phrase_var_name>module_feed</phrase_var_name>
		<writable />
	</data>
	<menus>
		<menu module_id="feed" parent_var_name="" m_connection="mobile" var_name="menu_feed_news_feed_532c28d5412dd75bf975fb951c740a30" ordering="116" url_value="feed" version_id="3.1.0rc1" disallow_access="" module="feed" mobile_icon="small_activity-feed.png" />
	</menus>
	<settings>
		<setting group="" module_id="feed" is_hidden="0" type="boolean" var_name="feed_only_friends" phrase_var_name="setting_feed_only_friends" ordering="0" version_id="2.0.0alpha1">0</setting>
		<setting group="" module_id="feed" is_hidden="0" type="integer" var_name="feed_display_limit" phrase_var_name="setting_feed_display_limit" ordering="0" version_id="2.0.0alpha1">10</setting>
		<setting group="time_stamps" module_id="feed" is_hidden="0" type="string" var_name="feed_display_time_stamp" phrase_var_name="setting_feed_display_time_stamp" ordering="1" version_id="2.0.0alpha3">F j, Y g:i a</setting>
		<setting group="" module_id="feed" is_hidden="0" type="integer" var_name="total_likes_to_display" phrase_var_name="setting_total_likes_to_display" ordering="1" version_id="3.0.0Beta1">2</setting>
		<setting group="" module_id="feed" is_hidden="0" type="integer" var_name="refresh_activity_feed" phrase_var_name="setting_refresh_activity_feed" ordering="1" version_id="3.0.0beta1">60</setting>
		<setting group="" module_id="feed" is_hidden="0" type="integer" var_name="feed_limit_days" phrase_var_name="setting_feed_limit_days" ordering="1" version_id="3.0.0beta3">0</setting>
		<setting group="" module_id="feed" is_hidden="0" type="string" var_name="twitter_share_via" phrase_var_name="setting_twitter_share_via" ordering="1" version_id="3.0.0rc1">YourSite</setting>
		<setting group="" module_id="feed" is_hidden="0" type="boolean" var_name="add_feed_for_comments" phrase_var_name="setting_add_feed_for_comments" ordering="1" version_id="3.4.0beta1">0</setting>
		<setting group="" module_id="feed" is_hidden="0" type="boolean" var_name="enable_check_in" phrase_var_name="setting_enable_check_in" ordering="1" version_id="3.5.0beta1">0</setting>
		<setting group="" module_id="feed" is_hidden="0" type="boolean" var_name="enable_tag_friends" phrase_var_name="setting_enable_tag_friends" ordering="1" version_id="4.6.0">1</setting>
		<setting group="" module_id="feed" is_hidden="0" type="boolean" var_name="cache_each_feed_entry" phrase_var_name="setting_cache_each_feed_entry" ordering="2" version_id="3.6.0rc1">0</setting>
		<setting group="" module_id="feed" is_hidden="1" type="boolean" var_name="allow_comments_on_feeds" phrase_var_name="setting_allow_comments_on_feeds" ordering="1" version_id="2.0.0alpha3">1</setting>
		<setting group="" module_id="feed" is_hidden="1" type="array" var_name="user_feed_display_limit" phrase_var_name="setting_user_feed_display_limit" ordering="1" version_id="2.0.0beta3"><![CDATA[s:29:"array(5, 10, 15, 20, 25, 30);";]]></setting>
		<setting group="" module_id="feed" is_hidden="1" type="integer" var_name="height_for_resized_videos" phrase_var_name="setting_height_for_resized_videos" ordering="1" version_id="2.1.0beta2">260</setting>
		<setting group="" module_id="feed" is_hidden="1" type="integer" var_name="width_for_resized_videos" phrase_var_name="setting_width_for_resized_videos" ordering="1" version_id="2.1.0beta2">300</setting>
		<setting group="" module_id="feed" is_hidden="1" type="boolean" var_name="allow_choose_sort_on_feeds" phrase_var_name="setting_allow_choose_sort_on_feeds" ordering="1" version_id="4.4.0">1</setting>
        <setting group="" module_id="feed" is_hidden="0" type="drop" var_name="top_stories_update" phrase_var_name="setting_top_stories_update" ordering="1" version_id="4.5.1"><![CDATA[a:2:{s:7:"default";s:7:"comment";s:6:"values";a:3:{i:0;s:7:"comment";i:1;s:4:"like";i:2;s:4:"both";}}]]></setting>
        <setting group="" module_id="feed" is_hidden="0" type="integer" var_name="feed_sponsor_cache_time" phrase_var_name="setting_feed_sponsor_cache_time" ordering="1" version_id="4.6.0">60</setting>
        <setting group="" module_id="feed" is_hidden="0" type="drop" var_name="default_sort_criterion_feed" phrase_var_name="setting_default_sort_criterion_feed" ordering="1" version_id="4.6.0"><![CDATA[a:2:{s:7:"default";s:11:"top_stories";s:6:"values";a:2:{i:0;s:11:"top_stories";i:1;s:11:"most_recent";}}]]></setting>
	</settings>
	<blocks>
		<block type_id="0" m_connection="group.view" module_id="feed" component="display" location="2" is_active="1" ordering="12" disallow_access="" can_move="0">
			<title>Group Feeds</title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="core.index-member" module_id="feed" component="display" location="2" is_active="1" ordering="9" disallow_access="" can_move="0">
			<title>Activity Feed</title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="profile.index" module_id="feed" component="display" location="2" is_active="1" ordering="7" disallow_access="" can_move="0">
			<title>Activity Feed</title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="event.view" module_id="feed" component="display" location="4" is_active="1" ordering="7" disallow_access="" can_move="0">
			<title>Activity Feed</title>
			<source_code />
			<source_parsed />
		</block>
		<block type_id="0" m_connection="pages.view" module_id="feed" component="display" location="2" is_active="1" ordering="10" disallow_access="" can_move="0">
			<title>Feed display</title>
			<source_code />
			<source_parsed />
		</block>
	</blocks>
	<hooks>
		<hook module_id="feed" hook_type="controller" module="feed" call_name="feed.component_controller_user_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="feed" hook_type="controller" module="feed" call_name="feed.component_controller_view_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="feed" hook_type="service" module="feed" call_name="feed.service_feed___construct" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="feed" hook_type="service" module="feed" call_name="feed.service_feed__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="feed" hook_type="service" module="feed" call_name="feed.service_process__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="feed" hook_type="controller" module="feed" call_name="feed.component_controller_index_clean" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="feed" hook_type="component" module="feed" call_name="feed.component_block_setting_clean" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="feed" hook_type="service" module="feed" call_name="feed.service_block__call" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="feed" hook_type="service" module="feed" call_name="feed.service_process_add__start" added="1260366442" version_id="2.0.0rc11" />
		<hook module_id="feed" hook_type="service" module="feed" call_name="feed.service_process_add__end" added="1260366442" version_id="2.0.0rc11" />
		<hook module_id="feed" hook_type="service" module="feed" call_name="feed.service_process_like_notify" added="1261572640" version_id="2.0.0" />
		<hook module_id="feed" hook_type="component" module="feed" call_name="feed.component_block_display_process_flike" added="1261572640" version_id="2.0.0" />
		<hook module_id="feed" hook_type="component" module="feed" call_name="feed.component_block_like_list_clean" added="1261572640" version_id="2.0.0" />
		<hook module_id="feed" hook_type="service" module="feed" call_name="feed.service_feed_get_mobile_types" added="1267629983" version_id="2.0.4" />
		<hook module_id="feed" hook_type="controller" module="feed" call_name="feed.component_controller_view_mobile_clean" added="1267629983" version_id="2.0.4" />
		<hook module_id="feed" hook_type="component" module="feed" call_name="feed.component_ajax_getcommenttext" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="feed" hook_type="controller" module="feed" call_name="feed.component_controller_admincp_index_clean" added="1276177474" version_id="2.0.5" />
		<hook module_id="feed" hook_type="component" module="feed" call_name="feed.component_block_display_process" added="1276177474" version_id="2.0.5" />
		<hook module_id="feed" hook_type="template" module="feed" call_name="feed.template_block_entry_1" added="1286546859" version_id="2.0.7" />
		<hook module_id="feed" hook_type="template" module="feed" call_name="feed.template_block_entry_2" added="1286546859" version_id="2.0.7" />
		<hook module_id="feed" hook_type="template" module="feed" call_name="feed.template_block_entry_3" added="1286546859" version_id="2.0.7" />
		<hook module_id="feed" hook_type="controller" module="feed" call_name="feed.component_controller_user_mobile_clean" added="1288281378" version_id="2.0.7" />
		<hook module_id="feed" hook_type="service" module="feed" call_name="feed.service_feed_get_start" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="feed" hook_type="service" module="feed" call_name="feed.service_process_deletefeed" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="feed" hook_type="template" module="feed" call_name="feed.template_block_comment_border" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="feed" hook_type="service" module="feed" call_name="feed.service_feed_getsharelinks__start" added="1334069444" version_id="3.2.0beta1" />
		<hook module_id="feed" hook_type="controller" module="feed" call_name="feed.component_controller_index_feeddisplay" added="1335951260" version_id="3.2.0" />
		<hook module_id="feed" hook_type="service" module="feed" call_name="feed.service_process_add__end2" added="1335951260" version_id="3.2.0" />
		<hook module_id="feed" hook_type="service" module="feed" call_name="feed.service_process_delete__end" added="1335951260" version_id="3.2.0" />
		<hook module_id="feed" hook_type="service" module="feed" call_name="feed.service_process_addcomment__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="feed" hook_type="component" module="feed" call_name="feed.component_block_mini_clean" added="1339076699" version_id="3.3.0beta1" />
		<hook module_id="feed" hook_type="template" module="feed" call_name="feed.component_block_display_process_header" added="1358258443" version_id="3.5.0beta1" />
		<hook module_id="feed" hook_type="service" module="feed" call_name="feed.service_feed_get_userprofile" added="1358258443" version_id="3.5.0beta1" />
		<hook module_id="feed" hook_type="service" module="feed" call_name="feed.service_feed_get_buildquery" added="1358258443" version_id="3.5.0beta1" />
		<hook module_id="feed" hook_type="service" module="feed" call_name="feed.service_feed_getsharelinks__end" added="1363075699" version_id="3.5.0" />
		<hook module_id="feed" hook_type="component" module="feed" call_name="feed.component_block_loaddates_clean" added="1372931660" version_id="3.6.0" />
	</hooks>
	<components>
		<component module_id="feed" component="display" m_connection="" module="feed" is_controller="0" is_block="1" is_active="1" />
		<component module_id="feed" component="view" m_connection="feed.view" module="feed" is_controller="1" is_block="0" is_active="1" />
		<component module_id="feed" component="user" m_connection="feed.user" module="feed" is_controller="1" is_block="0" is_active="1" />
		<component module_id="feed" component="form2" m_connection="" module="feed" is_controller="0" is_block="1" is_active="1" />
	</components>
	<user_group_settings>
		<setting is_admin_setting="0" module_id="feed" type="boolean" admin="1" user="1" guest="0" staff="1" module="feed" ordering="0">can_post_comment_on_feed</setting>
		<setting is_admin_setting="0" module_id="feed" type="boolean" admin="1" user="1" guest="0" staff="1" module="feed" ordering="0">can_delete_own_feed</setting>
		<setting is_admin_setting="0" module_id="feed" type="boolean" admin="1" user="0" guest="0" staff="1" module="feed" ordering="0">can_delete_other_feeds</setting>
		<setting is_admin_setting="0" module_id="feed" type="string" admin="0" user="0" guest="999999" staff="0" module="feed" ordering="0">feed_sponsor_price</setting>
		<setting is_admin_setting="0" module_id="feed" type="boolean" admin="true" user="false" guest="false" staff="false" module="feed" ordering="0">can_sponsor_feed</setting>
		<setting is_admin_setting="0" module_id="feed" type="boolean" admin="true" user="true" guest="true" staff="true" module="feed" ordering="0">auto_publish_sponsored_item</setting>
		<setting is_admin_setting="0" module_id="feed" type="boolean" admin="false" user="false" guest="false" staff="false" module="feed" ordering="0">can_purchase_sponsor</setting>
        <setting is_admin_setting="0" module_id="feed" type="boolean" admin="1" user="1" guest="0" staff="1" module="feed" ordering="0">can_edit_own_user_status</setting>
        <setting is_admin_setting="0" module_id="feed" type="boolean" admin="1" user="0" guest="0" staff="1" module="feed" ordering="0">can_edit_other_user_status</setting>
	</user_group_settings>
	<tables><![CDATA[a:4:{s:11:"phpfox_feed";a:3:{s:7:"COLUMNS";a:15:{s:7:"feed_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:6:"app_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"privacy";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:15:"privacy_comment";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"type_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:14:"parent_user_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"item_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:14:"feed_reference";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:14:"parent_feed_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:16:"parent_module_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:11:"time_update";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"content";a:4:{i:0;s:4:"TEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"total_view";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:7:"feed_id";s:4:"KEYS";a:6:{s:9:"privacy_2";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:7:"privacy";i:1;s:10:"time_stamp";i:2;s:14:"feed_reference";}}s:9:"privacy_3";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:7:"privacy";i:1;s:7:"user_id";i:2;s:14:"feed_reference";}}s:9:"privacy_4";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:7:"privacy";i:1;s:14:"parent_user_id";i:2;s:14:"feed_reference";}}s:7:"type_id";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:7:"type_id";i:1;s:7:"item_id";i:2;s:14:"feed_reference";}}s:11:"time_update";a:2:{i:0;s:5:"INDEX";i:1;s:11:"time_update";}s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:7:"user_id";i:1;s:14:"feed_reference";i:2;s:10:"time_stamp";}}}}s:19:"phpfox_feed_comment";a:3:{s:7:"COLUMNS";a:12:{s:15:"feed_comment_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:14:"parent_user_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"privacy";a:4:{i:0;s:6:"TINT:3";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:15:"privacy_comment";a:4:{i:0;s:6:"TINT:3";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"content";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:13:"total_comment";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"total_like";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"total_dislike";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:15:"location_latlng";a:4:{i:0;s:9:"VCHAR:100";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:13:"location_name";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:11:"PRIMARY_KEY";s:15:"feed_comment_id";s:4:"KEYS";a:2:{s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"user_id";}s:14:"parent_user_id";a:2:{i:0;s:5:"INDEX";i:1;s:14:"parent_user_id";}}}s:17:"phpfox_feed_share";a:2:{s:7:"COLUMNS";a:12:{s:8:"share_id";a:4:{i:0;s:6:"TINT:3";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:10:"product_id";a:4:{i:0;s:8:"VCHAR:75";i:1;s:6:"phpfox";i:2;s:0:"";i:3;s:3:"YES";}s:9:"module_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:5:"title";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"description";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"block_name";a:4:{i:0;s:8:"VCHAR:50";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"no_input";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"is_frame";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:12:"ajax_request";a:4:{i:0;s:8:"VCHAR:25";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"no_profile";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:4:"icon";a:4:{i:0;s:8:"VCHAR:30";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:8:"ordering";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:8:"share_id";}s:19:"phpfox_feed_sponsor";a:3:{s:7:"COLUMNS";a:4:{s:10:"sponsor_id";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"feed_id";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"total_views";a:4:{i:0;s:7:"UINT:11";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:16:"time_stamp_added";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:10:"sponsor_id";s:4:"KEYS";a:1:{s:7:"feed_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"feed_id";}}}}]]></tables>
</module>