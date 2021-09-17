<module>
	<data>
		<module_id>report</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>1</is_menu>
		<menu><![CDATA[a:3:{s:30:"report.admin_menu_view_reports";a:1:{s:3:"url";a:1:{i:0;s:6:"report";}}s:30:"report.admin_menu_add_category";a:1:{s:3:"url";a:2:{i:0;s:6:"report";i:1;s:3:"add";}}s:35:"report.admin_menu_manage_categories";a:1:{s:3:"url";a:2:{i:0;s:6:"report";i:1;s:8:"category";}}}]]></menu>
		<phrase_var_name>module_report</phrase_var_name>
		<writable />
	</data>
	<blocks>
		<block type_id="0" m_connection="profile.index" module_id="report" component="profile" location="1" is_active="1" ordering="5" disallow_access="" can_move="0">
			<title>Report User</title>
			<source_code />
			<source_parsed />
		</block>
	</blocks>
	<hooks>
		<hook module_id="report" hook_type="service" module="report" call_name="report.service_data_process__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="report" hook_type="service" module="report" call_name="report.service_report___construct" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="report" hook_type="service" module="report" call_name="report.service_report__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="report" hook_type="controller" module="report" call_name="report.component_controller_admincp_index_clean" added="1242637439" version_id="2.0.0beta2" />
		<hook module_id="report" hook_type="controller" module="report" call_name="report.component_controller_admincp_category_clean" added="1242637439" version_id="2.0.0beta2" />
		<hook module_id="report" hook_type="controller" module="report" call_name="report.component_controller_admincp_add_clean" added="1242637439" version_id="2.0.0beta2" />
		<hook module_id="report" hook_type="service" module="report" call_name="report.service_process__call" added="1242637439" version_id="2.0.0beta2" />
		<hook module_id="report" hook_type="service" module="report" call_name="report.service_callback__call" added="1242637439" version_id="2.0.0beta2" />
		<hook module_id="report" hook_type="component" module="report" call_name="report.component_block_profile_clean" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="report" hook_type="component" module="report" call_name="report.component_block_browse_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="report" hook_type="service" module="report" call_name="report.service_data_data__call" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="report" hook_type="component" module="report" call_name="report.component_block_profile_process" added="1358258443" version_id="3.5.0beta1" />
	</hooks>
	<components>
		<component module_id="report" component="ajax" m_connection="" module="report" is_controller="0" is_block="0" is_active="1" />
		<component module_id="report" component="add" m_connection="" module="report" is_controller="0" is_block="1" is_active="1" />
		<component module_id="report" component="profile" m_connection="" module="report" is_controller="0" is_block="1" is_active="1" />
	</components>
	<user_group_settings>
		<setting is_admin_setting="0" module_id="report" type="boolean" admin="1" user="1" guest="0" staff="1" module="report" ordering="0">can_report_comments</setting>
	</user_group_settings>
	<tables><![CDATA[a:2:{s:13:"phpfox_report";a:3:{s:7:"COLUMNS";a:4:{s:9:"report_id";a:4:{i:0;s:6:"TINT:3";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:9:"module_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"product_id";a:4:{i:0;s:8:"VCHAR:25";i:1;s:6:"phpfox";i:2;s:0:"";i:3;s:2:"NO";}s:7:"message";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:9:"report_id";s:4:"KEYS";a:1:{s:9:"module_id";a:2:{i:0;s:5:"INDEX";i:1;s:9:"module_id";}}}s:18:"phpfox_report_data";a:3:{s:7:"COLUMNS";a:7:{s:7:"data_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:9:"report_id";a:4:{i:0;s:6:"TINT:3";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"item_id";a:4:{i:0;s:9:"VCHAR:150";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:5:"added";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"ip_address";a:4:{i:0;s:8:"VCHAR:15";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"feedback";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:11:"PRIMARY_KEY";s:7:"data_id";s:4:"KEYS";a:3:{s:9:"report_id";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:9:"report_id";i:1;s:7:"item_id";i:2;s:7:"user_id";}}s:7:"item_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"item_id";i:1;s:7:"user_id";}}s:11:"report_id_2";a:2:{i:0;s:5:"INDEX";i:1;s:9:"report_id";}}}}]]></tables>
</module>