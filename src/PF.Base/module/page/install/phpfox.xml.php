<module>
	<data>
		<module_id>page</module_id>
		<product_id>phpfox</product_id>
		<is_core>1</is_core>
		<is_active>1</is_active>
		<is_menu>0</is_menu>
		<menu />
		<phrase_var_name>module_page</phrase_var_name>
		<writable />
	</data>
	<menus>
		<menu module_id="page" parent_var_name="" m_connection="footer" var_name="menu_terms" ordering="14" url_value="terms" version_id="2.0.0alpha1" disallow_access="" module="page" />
	</menus>
	<hooks>
		<hook module_id="page" hook_type="controller" module="page" call_name="page.component_controller_admincp_index_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="page" hook_type="controller" module="page" call_name="page.component_controller_admincp_add_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="page" hook_type="controller" module="page" call_name="page.component_controller_view_process" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="page" hook_type="controller" module="page" call_name="page.component_controller_view_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="page" hook_type="service" module="page" call_name="page.service_log_process__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="page" hook_type="service" module="page" call_name="page.service_page_getforedit" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="page" hook_type="service" module="page" call_name="page.service_page_get" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="page" hook_type="service" module="page" call_name="page.service_page_getcache" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="page" hook_type="service" module="page" call_name="page.service_page_getpage" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="page" hook_type="service" module="page" call_name="page.service_page__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="page" hook_type="service" module="page" call_name="page.service_callback___call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="page" hook_type="service" module="page" call_name="page.service_process__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="page" hook_type="template" module="page" call_name="page.template_controller_view_start" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="page" hook_type="template" module="page" call_name="page.template_controller_view_end" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="page" hook_type="component" module="page" call_name="page.component_block_view_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="page" hook_type="template" module="page" call_name="page.template_controller_admincp_add_editor" added="1274286148" version_id="2.0.5dev1" />
	</hooks>
	<components>
		<component module_id="page" component="admincp.index" m_connection="" module="page" is_controller="0" is_block="0" is_active="1" />
		<component module_id="page" component="admincp.add" m_connection="" module="page" is_controller="0" is_block="0" is_active="1" />
		<component module_id="page" component="admincp.ajax" m_connection="" module="page" is_controller="0" is_block="0" is_active="1" />
		<component module_id="page" component="view" m_connection="page.view" module="page" is_controller="1" is_block="0" is_active="1" />
	</components>
	<user_group_settings>
		<setting is_admin_setting="0" module_id="page" type="boolean" admin="1" user="0" guest="0" staff="0" module="page" ordering="0">can_manage_custom_pages</setting>
	</user_group_settings>
	<tables><![CDATA[a:3:{s:11:"phpfox_page";a:3:{s:7:"COLUMNS";a:17:{s:7:"page_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"module_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"product_id";a:4:{i:0;s:8:"VCHAR:25";i:1;s:6:"phpfox";i:2;s:0:"";i:3;s:2:"NO";}s:9:"is_active";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"is_phrase";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"parse_php";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:12:"has_bookmark";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"add_view";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"full_size";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"1";i:2;s:0:"";i:3;s:2:"NO";}s:5:"title";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"title_url";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:15:"disallow_access";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:5:"added";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"total_view";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:16:"total_attachment";a:4:{i:0;s:5:"USINT";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"total_tag";a:4:{i:0;s:5:"USINT";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:7:"page_id";s:4:"KEYS";a:4:{s:9:"url_value";a:2:{i:0;s:5:"INDEX";i:1;s:9:"title_url";}s:7:"page_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"page_id";i:1;s:9:"is_active";}}s:9:"is_active";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:9:"is_active";i:1;s:9:"title_url";}}s:10:"product_id";a:2:{i:0;s:5:"INDEX";i:1;s:10:"product_id";}}}s:15:"phpfox_page_log";a:2:{s:7:"COLUMNS";a:3:{s:7:"page_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"updated";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:4:"KEYS";a:1:{s:7:"page_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"page_id";}}}s:16:"phpfox_page_text";a:2:{s:7:"COLUMNS";a:5:{s:7:"page_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:4:"text";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"text_parsed";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"keyword";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:11:"description";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:4:"KEYS";a:1:{s:7:"page_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"page_id";}}}}]]></tables>
</module>