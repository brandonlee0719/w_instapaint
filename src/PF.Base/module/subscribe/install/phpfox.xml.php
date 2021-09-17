<module>
	<data>
		<module_id>subscribe</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>1</is_menu>
		<menu><![CDATA[a:4:{s:36:"subscribe.admin_menu_manage_packages";a:1:{s:3:"url";a:1:{i:0;s:9:"subscribe";}}s:40:"subscribe.admin_menu_create_new_packages";a:1:{s:3:"url";a:2:{i:0;s:9:"subscribe";i:1;s:3:"add";}}s:36:"subscribe.admin_menu_purchase_orders";a:1:{s:3:"url";a:2:{i:0;s:9:"subscribe";i:1;s:4:"list";}}s:31:"subscribe.admin_menu_comparison";a:1:{s:3:"url";a:2:{i:0;s:9:"subscribe";i:1;s:7:"compare";}}}]]></menu>
		<phrase_var_name>module_subscribe</phrase_var_name>
		<writable><![CDATA[a:1:{i:0;s:19:"file/pic/subscribe/";}]]></writable>
	</data>
	<menus>
		<menu module_id="subscribe" parent_var_name="" m_connection="subscribe" var_name="menu_my_subscriptions" ordering="80" url_value="subscribe.list" version_id="2.0.0beta4" disallow_access="" module="subscribe" />
		<menu module_id="subscribe" parent_var_name="" m_connection="subscribe" var_name="menu_membership_packages" ordering="81" url_value="subscribe" version_id="2.0.0beta4" disallow_access="" module="subscribe" />
	</menus>
	<settings>
		<setting group="" module_id="subscribe" is_hidden="0" type="boolean" var_name="enable_subscription_packages" phrase_var_name="setting_enable_subscription_packages" ordering="1" version_id="2.0.0beta4">0</setting>
		<setting group="" module_id="subscribe" is_hidden="0" type="boolean" var_name="subscribe_is_required_on_sign_up" phrase_var_name="setting_subscribe_is_required_on_sign_up" ordering="1" version_id="2.0.0beta4">0</setting>
	</settings>
	<hooks>
		<hook module_id="subscribe" hook_type="component" module="subscribe" call_name="subscribe.component_block_message_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="subscribe" hook_type="controller" module="subscribe" call_name="subscribe.component_controller_admincp_index_clean" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="subscribe" hook_type="controller" module="subscribe" call_name="subscribe.component_controller_admincp_add_clean" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="subscribe" hook_type="controller" module="subscribe" call_name="subscribe.component_controller_view_clean" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="subscribe" hook_type="controller" module="subscribe" call_name="subscribe.component_controller_register_clean" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="subscribe" hook_type="controller" module="subscribe" call_name="subscribe.component_controller_complete_clean" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="subscribe" hook_type="controller" module="subscribe" call_name="subscribe.component_controller_list_clean" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="subscribe" hook_type="component" module="subscribe" call_name="subscribe.component_block_upgrade_clean" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="subscribe" hook_type="component" module="subscribe" call_name="subscribe.component_block_list_clean" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="subscribe" hook_type="service" module="subscribe" call_name="subscribe.service_process__call" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="subscribe" hook_type="service" module="subscribe" call_name="subscribe.service_callback__call" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="subscribe" hook_type="service" module="subscribe" call_name="subscribe.service_subscribe__call" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="subscribe" hook_type="service" module="subscribe" call_name="subscribe.service_purchase_process__call" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="subscribe" hook_type="service" module="subscribe" call_name="subscribe.service_purchase_purchase__call" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="subscribe" hook_type="controller" module="subscribe" call_name="subscribe.component_controller_admincp_list_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="subscribe" hook_type="service" module="subscribe" call_name="subscribe.service_purchase_process_update_pre_log" added="1286546859" version_id="2.0.7" />
		<hook module_id="subscribe" hook_type="controller" module="subscribe" call_name="subscribe.component_controller_list__1" added="1361175548" version_id="3.5.0rc1" />
		<hook module_id="subscribe" hook_type="controller" module="subscribe" call_name="subscribe.component_controller_register__1" added="1361175548" version_id="3.5.0rc1" />
		<hook module_id="subscribe" hook_type="template" module="subscribe" call_name="subscribe.template_controller_register__1" added="1361175548" version_id="3.5.0rc1" />
		<hook module_id="subscribe" hook_type="template" module="subscribe" call_name="subscribe.template_controller_register__2" added="1361175548" version_id="3.5.0rc1" />
		<hook module_id="subscribe" hook_type="template" module="subscribe" call_name="subscribe.template_controller_register__3" added="1361175548" version_id="3.5.0rc1" />
		<hook module_id="subscribe" hook_type="template" module="subscribe" call_name="subscribe.template_controller_register__4" added="1361175548" version_id="3.5.0rc1" />
		<hook module_id="subscribe" hook_type="template" module="subscribe" call_name="subscribe.template_controller_register__5" added="1361175548" version_id="3.5.0rc1" />
		<hook module_id="subscribe" hook_type="template" module="subscribe" call_name="subscribe.template_controller_register__6" added="1361175548" version_id="3.5.0rc1" />
		<hook module_id="subscribe" hook_type="template" module="subscribe" call_name="subscribe.template_controller_register__7" added="1361175548" version_id="3.5.0rc1" />
		<hook module_id="subscribe" hook_type="template" module="subscribe" call_name="subscribe.template_controller_list__1" added="1361175548" version_id="3.5.0rc1" />
		<hook module_id="subscribe" hook_type="template" module="subscribe" call_name="subscribe.template_controller_list__2" added="1361175548" version_id="3.5.0rc1" />
		<hook module_id="subscribe" hook_type="template" module="subscribe" call_name="subscribe.template_controller_list__3" added="1361175548" version_id="3.5.0rc1" />
		<hook module_id="subscribe" hook_type="template" module="subscribe" call_name="subscribe.template_controller_list__4" added="1361175548" version_id="3.5.0rc1" />
		<hook module_id="subscribe" hook_type="template" module="subscribe" call_name="subscribe.template_controller_list__5" added="1361175548" version_id="3.5.0rc1" />
		<hook module_id="subscribe" hook_type="template" module="subscribe" call_name="subscribe.template_controller_list__6" added="1361175548" version_id="3.5.0rc1" />
		<hook module_id="subscribe" hook_type="template" module="subscribe" call_name="subscribe.template_controller_list__7" added="1361175548" version_id="3.5.0rc1" />
		<hook module_id="subscribe" hook_type="template" module="subscribe" call_name="subscribe.template_controller_view__1" added="1361175548" version_id="3.5.0rc1" />
		<hook module_id="subscribe" hook_type="template" module="subscribe" call_name="subscribe.template_controller_view__2" added="1361175548" version_id="3.5.0rc1" />
	</hooks>
	<components>
		<component module_id="subscribe" component="ajax" m_connection="" module="subscribe" is_controller="0" is_block="0" is_active="1" />
		<component module_id="subscribe" component="message" m_connection="" module="subscribe" is_controller="0" is_block="1" is_active="1" />
		<component module_id="subscribe" component="index" m_connection="subscribe.index" module="subscribe" is_controller="1" is_block="0" is_active="1" />
	</components>
	<crons>
		<cron module_id="subscribe" type_id="2" every="1"><![CDATA[Phpfox::getService('subscribe.purchase.process')->downgradeExpiredSubscribers();]]></cron>
	</crons>
	<tables><![CDATA[a:3:{s:24:"phpfox_subscribe_package";a:3:{s:7:"COLUMNS";a:17:{s:10:"package_id";a:4:{i:0;s:5:"USINT";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:5:"title";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:11:"description";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:4:"cost";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:14:"recurring_cost";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:16:"recurring_period";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"user_group_id";a:4:{i:0;s:6:"TINT:3";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:15:"fail_user_group";a:4:{i:0;s:6:"TINT:3";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"image_path";a:4:{i:0;s:8:"VCHAR:50";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:9:"server_id";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"is_active";a:4:{i:0;s:6:"TINT:3";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:15:"is_registration";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:11:"is_required";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"show_price";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"1";i:2;s:0:"";i:3;s:2:"NO";}s:8:"ordering";a:4:{i:0;s:5:"USINT";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:12:"total_active";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:16:"background_color";a:4:{i:0;s:8:"VCHAR:50";i:1;s:4:"null";i:2;s:0:"";i:3;s:3:"YES";}}s:11:"PRIMARY_KEY";s:10:"package_id";s:4:"KEYS";a:3:{s:9:"is_active";a:2:{i:0;s:5:"INDEX";i:1;s:9:"is_active";}s:10:"package_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:10:"package_id";i:1;s:9:"is_active";}}s:11:"is_active_2";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:9:"is_active";i:1;s:15:"is_registration";}}}}s:25:"phpfox_subscribe_purchase";a:3:{s:7:"COLUMNS";a:7:{s:11:"purchase_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:10:"package_id";a:4:{i:0;s:5:"USINT";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"currency_id";a:4:{i:0;s:6:"CHAR:3";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:5:"price";a:4:{i:0;s:10:"DECIMAL:14";i:1;s:4:"0.00";i:2;s:0:"";i:3;s:2:"NO";}s:6:"status";a:4:{i:0;s:8:"VCHAR:20";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:11:"purchase_id";s:4:"KEYS";a:3:{s:11:"purchase_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:11:"purchase_id";i:1;s:7:"user_id";}}s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"user_id";}s:9:"user_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"user_id";i:1;s:6:"status";}}}}s:24:"phpfox_subscribe_compare";a:2:{s:7:"COLUMNS";a:3:{s:10:"compare_id";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:13:"feature_title";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:13:"feature_value";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:10:"compare_id";}}]]></tables>
</module>