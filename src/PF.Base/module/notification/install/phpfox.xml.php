<module>
	<data>
		<module_id>notification</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>0</is_menu>
		<menu />
		<phrase_var_name>module_notification</phrase_var_name>
		<writable />
	</data>
	<settings>
		<setting group="" module_id="notification" is_hidden="0" type="boolean" var_name="notify_on_new_request" phrase_var_name="setting_notify_on_new_request" ordering="1" version_id="2.0.0alpha4">1</setting>
		<setting group="" module_id="notification" is_hidden="0" type="integer" var_name="notify_ajax_refresh" phrase_var_name="setting_notify_ajax_refresh" ordering="1" version_id="2.0.0alpha4">2</setting>
		<setting group="" module_id="notification" is_hidden="0" type="integer" var_name="total_notification_title_length" phrase_var_name="setting_total_notification_title_length" ordering="1" version_id="3.0.0Beta1">100</setting>
	</settings>
	<hooks>
		<hook module_id="notification" hook_type="controller" module="notification" call_name="notification.component_controller_index_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="notification" hook_type="component" module="notification" call_name="notification.component_block_link_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="notification" hook_type="component" module="notification" call_name="notification.component_block_feed_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="notification" hook_type="service" module="notification" call_name="notification.service_notification__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="notification" hook_type="service" module="notification" call_name="notification.service_process__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="notification" hook_type="service" module="notification" call_name="notification.service_callback__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="notification" hook_type="service" module="notification" call_name="notification.service_process_add" added="1276177474" version_id="2.0.5" />
		<hook module_id="notification" hook_type="service" module="notification" call_name="notification.service_api__call" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="notification" hook_type="component" module="notification" call_name="notification.component_ajax_update_1" added="1361180401" version_id="3.5.0rc1" />
	</hooks>
	<components>
		<component module_id="notification" component="feed" m_connection="" module="notification" is_controller="0" is_block="1" is_active="1" />
	</components>
	<tables><![CDATA[a:1:{s:19:"phpfox_notification";a:3:{s:7:"COLUMNS";a:8:{s:15:"notification_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"type_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"item_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:13:"owner_user_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"is_seen";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"is_read";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:15:"notification_id";s:4:"KEYS";a:4:{s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"user_id";}s:13:"owner_user_id";a:2:{i:0;s:5:"INDEX";i:1;s:13:"owner_user_id";}s:9:"user_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"user_id";i:1;s:7:"is_seen";}}s:7:"type_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"type_id";i:1;s:7:"item_id";}}}}}]]></tables>
</module>