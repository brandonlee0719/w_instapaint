<module>
	<data>
		<module_id>ad</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>1</is_menu>
		<menu><![CDATA[a:4:{s:30:"ad.admin_menu_manage_campaigns";a:1:{s:3:"url";a:1:{i:0;s:2:"ad";}}s:31:"ad.admin_menu_manage_placements";a:1:{s:3:"url";a:2:{i:0;s:2:"ad";i:1;s:9:"placement";}}s:22:"ad.admin_menu_invoices";a:1:{s:3:"url";a:2:{i:0;s:2:"ad";i:1;s:7:"invoice";}}s:33:"ad.admin_menu_manage_sponsorships";a:1:{s:3:"url";a:2:{i:0;s:2:"ad";i:1;s:7:"sponsor";}}}]]></menu>
		<phrase_var_name>module_ad</phrase_var_name>
		<writable><![CDATA[a:1:{i:0;s:12:"file/pic/ad/";}]]></writable>
	</data>
	<menus>
		<menu module_id="ad" parent_var_name="" m_connection="footer" var_name="menu_ad_advertise_251d164643533a527361dbe1a7b9235d" ordering="16" url_value="ad" version_id="2.0.5" disallow_access="" module="ad" />
	</menus>
	<settings>
		<setting group="" module_id="ad" is_hidden="0" type="boolean" var_name="enable_ads" phrase_var_name="setting_enable_ads" ordering="1" version_id="2.0.0beta3">1</setting>
		<setting group="" module_id="ad" is_hidden="0" type="boolean" var_name="advanced_ad_filters" phrase_var_name="setting_advanced_ad_filters" ordering="1" version_id="3.4.0beta1">0</setting>
		<setting group="" module_id="ad" is_hidden="0" type="integer" var_name="ad_multi_ad_count" phrase_var_name="setting_ad_multi_ad_count" ordering="1" version_id="3.7.0beta1">5</setting>
	</settings>
	<hooks>
		<hook module_id="ad" hook_type="controller" module="ad" call_name="ad.component_controller_index_clean" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="ad" hook_type="controller" module="ad" call_name="ad.component_controller_admincp_index_clean" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="ad" hook_type="controller" module="ad" call_name="ad.component_controller_admincp_add_clean" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="ad" hook_type="controller" module="ad" call_name="ad.component_controller_sample_clean" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="ad" hook_type="component" module="ad" call_name="ad.component_block_display_clean" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="ad" hook_type="component" module="ad" call_name="ad.component_block_sample_clean" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="ad" hook_type="service" module="ad" call_name="ad.service_process__call" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="ad" hook_type="service" module="ad" call_name="ad.service_ad__call" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="ad" hook_type="service" module="ad" call_name="ad.service_callback__call" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="ad" hook_type="service" module="ad" call_name="ad.service_ad_construct__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="ad" hook_type="service" module="ad" call_name="ad.service_ad_updateactivity__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="ad" hook_type="service" module="ad" call_name="ad.service_ad_updateactivity__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="ad" hook_type="service" module="ad" call_name="ad.service_ad_update__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="ad" hook_type="service" module="ad" call_name="ad.service_ad_update__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="ad" hook_type="service" module="ad" call_name="ad.service_ad_delete__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="ad" hook_type="service" module="ad" call_name="ad.service_ad_delete__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="ad" hook_type="service" module="ad" call_name="ad.service_ad_add__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="ad" hook_type="service" module="ad" call_name="ad.service_ad_add__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="ad" hook_type="service" module="ad" call_name="ad.service_ad_get__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="ad" hook_type="service" module="ad" call_name="ad.service_ad_get__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="ad" hook_type="service" module="ad" call_name="ad.service_ad_getforblock__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="ad" hook_type="service" module="ad" call_name="ad.service_ad_getforblock__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="ad" hook_type="service" module="ad" call_name="ad.service_ad_getadredirect__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="ad" hook_type="service" module="ad" call_name="ad.service_ad_getadredirect__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="ad" hook_type="service" module="ad" call_name="ad.service_ad_getforedit__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="ad" hook_type="service" module="ad" call_name="ad.service_ad_getforedit__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="ad" hook_type="service" module="ad" call_name="ad.service_ad_getsizes__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="ad" hook_type="service" module="ad" call_name="ad.service_ad_getsizes__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="ad" hook_type="service" module="ad" call_name="ad.service_callback_construct__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="ad" hook_type="component" module="ad" call_name="ad.component_ajax_update__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="ad" hook_type="component" module="ad" call_name="ad.component_ajax_update__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="ad" hook_type="controller" module="ad" call_name="ad.component_controller_iframe_clean" added="1263387694" version_id="2.0.2" />
		<hook module_id="ad" hook_type="controller" module="ad" call_name="ad.component_controller_process__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="ad" hook_type="controller" module="ad" call_name="ad.component_controller_admincp_add_process__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="ad" hook_type="controller" module="ad" call_name="ad.component_controller_admincp_add_process__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="ad" hook_type="controller" module="ad" call_name="ad.component_controller_admincp_process__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="ad" hook_type="controller" module="ad" call_name="ad.component_controller_admincp_process__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="ad" hook_type="component" module="ad" call_name="ad.component_block_display_process__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="ad" hook_type="component" module="ad" call_name="ad.component_block_display_process__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="ad" hook_type="template" module="ad" call_name="ad.template_block_display__start" added="1263387694" version_id="2.0.2" />
		<hook module_id="ad" hook_type="template" module="ad" call_name="ad.template_block_display__end" added="1263387694" version_id="2.0.2" />
		<hook module_id="ad" hook_type="controller" module="ad" call_name="ad.component_controller_preview_clean" added="1271160844" version_id="2.0.5" />
		<hook module_id="ad" hook_type="controller" module="ad" call_name="ad.component_controller_invoice_index_clean" added="1271160844" version_id="2.0.5" />
		<hook module_id="ad" hook_type="controller" module="ad" call_name="ad.component_controller_image_clean" added="1271160844" version_id="2.0.5" />
		<hook module_id="ad" hook_type="controller" module="ad" call_name="ad.component_controller_add_clean" added="1271160844" version_id="2.0.5" />
		<hook module_id="ad" hook_type="controller" module="ad" call_name="ad.component_controller_manage_clean" added="1271160844" version_id="2.0.5" />
		<hook module_id="ad" hook_type="controller" module="ad" call_name="ad.component_controller_admincp_invoice_clean" added="1271160844" version_id="2.0.5" />
		<hook module_id="ad" hook_type="controller" module="ad" call_name="ad.component_controller_admincp_placement_add_clean" added="1271160844" version_id="2.0.5" />
		<hook module_id="ad" hook_type="controller" module="ad" call_name="ad.component_controller_admincp_placement_index_clean" added="1271160844" version_id="2.0.5" />
		<hook module_id="ad" hook_type="component" module="ad" call_name="ad.component_block_sponsored_clean" added="1271160844" version_id="2.0.5" />
		<hook module_id="ad" hook_type="template" module="ad" call_name="ad.template_controller_index" added="1271160844" version_id="2.0.5" />
		<hook module_id="ad" hook_type="service" module="ad" call_name="ad.service_ad_getadsponsor__start" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="ad" hook_type="controller" module="ad" call_name="ad.component_controller_sponsor_clean" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="ad" hook_type="controller" module="ad" call_name="ad.component_controller_admincp_sponsor_process__start" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="ad" hook_type="controller" module="ad" call_name="ad.component_controller_admincp_sponsor_process__end" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="ad" hook_type="controller" module="ad" call_name="ad.component_controller_admincp_sponsor__clean" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="ad" hook_type="service" module="ad" call_name="ad.service_ad_deleteinvoice__start" added="1286546859" version_id="2.0.7" />
		<hook module_id="ad" hook_type="service" module="ad" call_name="ad.service_process_addcustom_before_insert_ad" added="1286546859" version_id="2.0.7" />
		<hook module_id="ad" hook_type="service" module="ad" call_name="ad.service_process_addcustom_before_insert_invoice" added="1286546859" version_id="2.0.7" />
		<hook module_id="ad" hook_type="component" module="ad" call_name="ad.component_block_inner_process__start" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="ad" hook_type="component" module="ad" call_name="ad.component_block_inner_process__end" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="ad" hook_type="service" module="ad" call_name="ad.service_ad_getforblock__1" added="1361180401" version_id="3.5.0rc1" />
	</hooks>
	<components>
		<component module_id="ad" component="sponsored" m_connection="" module="ad" is_controller="0" is_block="1" is_active="1" />
		<component module_id="ad" component="index" m_connection="index" module="ad" is_controller="1" is_block="0" is_active="1" />
		<component module_id="ad" component="manage-sponsor" m_connection="ad.manage-sponsor" module="ad" is_controller="1" is_block="0" is_active="1" />
	</components>
	<user_group_settings>
		<setting is_admin_setting="0" module_id="ad" type="boolean" admin="true" user="true" guest="true" staff="true" module="ad" ordering="0">show_ads</setting>
		<setting is_admin_setting="0" module_id="ad" type="boolean" admin="0" user="0" guest="0" staff="0" module="ad" ordering="0">can_create_ad_campaigns</setting>
		<setting is_admin_setting="0" module_id="ad" type="boolean" admin="0" user="1" guest="0" staff="0" module="ad" ordering="0">ad_campaigns_must_be_approved_first</setting>
	</user_group_settings>
	<tables><![CDATA[a:5:{s:9:"phpfox_ad";a:3:{s:7:"COLUMNS";a:28:{s:5:"ad_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:9:"is_custom";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"type_id";a:4:{i:0;s:6:"TINT:1";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:4:"name";a:4:{i:0;s:9:"VCHAR:150";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"url_link";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"start_date";a:4:{i:0;s:7:"UINT:11";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"end_date";a:4:{i:0;s:7:"UINT:11";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"total_view";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:11:"total_click";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:6:"is_cpm";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"1";i:2;s:0:"";i:3;s:2:"NO";}s:9:"is_active";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"1";i:2;s:0:"";i:3;s:2:"NO";}s:13:"module_access";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:8:"location";a:4:{i:0;s:8:"VCHAR:50";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"country_iso";a:4:{i:0;s:6:"CHAR:2";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:6:"gender";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"age_from";a:4:{i:0;s:6:"TINT:2";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:6:"age_to";a:4:{i:0;s:6:"TINT:2";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"user_group";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:9:"html_code";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"count_view";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:11:"count_click";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"image_path";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:9:"server_id";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"gmt_offset";a:4:{i:0;s:8:"VCHAR:15";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:19:"disallow_controller";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:11:"postal_code";a:4:{i:0;s:4:"TEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:13:"city_location";a:4:{i:0;s:4:"TEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:11:"PRIMARY_KEY";s:5:"ad_id";s:4:"KEYS";a:2:{s:9:"is_active";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:9:"is_active";i:1;s:8:"location";}}s:9:"is_custom";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:9:"is_custom";i:1;s:7:"type_id";}}}}s:17:"phpfox_ad_invoice";a:3:{s:7:"COLUMNS";a:9:{s:10:"invoice_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:5:"ad_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"is_sponsor";a:4:{i:0;s:6:"TINT:1";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"currency_id";a:4:{i:0;s:6:"CHAR:3";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:5:"price";a:4:{i:0;s:10:"DECIMAL:14";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:6:"status";a:4:{i:0;s:8:"VCHAR:20";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:15:"time_stamp_paid";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:10:"invoice_id";s:4:"KEYS";a:3:{s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"user_id";}s:10:"is_sponsor";a:2:{i:0;s:5:"INDEX";i:1;s:10:"is_sponsor";}s:5:"ad_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:5:"ad_id";i:1;s:10:"is_sponsor";}}}}s:14:"phpfox_ad_plan";a:3:{s:7:"COLUMNS";a:8:{s:7:"plan_id";a:4:{i:0;s:4:"UINT";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"d_width";a:4:{i:0;s:7:"VCHAR:4";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:8:"d_height";a:4:{i:0;s:7:"VCHAR:4";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:8:"block_id";a:4:{i:0;s:6:"TINT:3";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:5:"title";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:4:"cost";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"is_active";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:6:"is_cpm";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"1";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:7:"plan_id";s:4:"KEYS";a:1:{s:8:"block_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:8:"block_id";i:1;s:9:"is_active";}}}}s:17:"phpfox_ad_sponsor";a:2:{s:7:"COLUMNS";a:17:{s:10:"sponsor_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:9:"module_id";a:4:{i:0;s:8:"VCHAR:50";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"item_id";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"country_iso";a:4:{i:0;s:6:"CHAR:2";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:6:"gender";a:4:{i:0;s:6:"TINT:1";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"age_from";a:4:{i:0;s:6:"TINT:2";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:6:"age_to";a:4:{i:0;s:6:"TINT:2";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:13:"campaign_name";a:4:{i:0;s:9:"VCHAR:511";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"impressions";a:4:{i:0;s:4:"UINT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:3:"cpm";a:4:{i:0;s:10:"DECIMAL:14";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"start_date";a:4:{i:0;s:7:"UINT:11";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:12:"auto_publish";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"is_custom";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"1";i:2;s:0:"";i:3;s:2:"NO";}s:9:"is_active";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"total_view";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:11:"total_click";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:10:"sponsor_id";}s:17:"phpfox_ad_country";a:3:{s:7:"COLUMNS";a:4:{s:13:"ad_country_id";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:5:"ad_id";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"country_id";a:4:{i:0;s:6:"CHAR:2";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"child_id";a:4:{i:0;s:4:"UINT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:11:"PRIMARY_KEY";s:13:"ad_country_id";s:4:"KEYS";a:1:{s:5:"ad_id";a:2:{i:0;s:5:"INDEX";i:1;s:5:"ad_id";}}}}]]></tables>
</module>