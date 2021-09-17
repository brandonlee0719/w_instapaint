<module>
	<data>
		<module_id>ban</module_id>
		<product_id>phpfox</product_id>
		<is_core>1</is_core>
		<is_active>1</is_active>
		<is_menu>0</is_menu>
		<menu />
		<phrase_var_name>module_ban</phrase_var_name>
		<writable />
	</data>
	<hooks>
		<hook module_id="ban" hook_type="service" module="ban" call_name="ban.service_word__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="ban" hook_type="service" module="ban" call_name="ban.service_process__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="ban" hook_type="service" module="ban" call_name="ban.service_ban__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="ban" hook_type="service" module="ban" call_name="ban.service_callback__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="ban" hook_type="controller" module="ban" call_name="ban.component_controller_admincp_word_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="ban" hook_type="controller" module="ban" call_name="ban.component_controller_admincp_ip_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="ban" hook_type="controller" module="ban" call_name="ban.component_controller_admincp_display_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="ban" hook_type="controller" module="ban" call_name="ban.component_controller_admincp_default_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="ban" hook_type="controller" module="ban" call_name="ban.component_controller_admincp_email_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="ban" hook_type="controller" module="ban" call_name="ban.component_controller_admincp_username_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="ban" hook_type="controller" module="ban" call_name="ban.component_controller_spam_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="ban" hook_type="controller" module="ban" call_name="ban.component_controller_message_clean" added="1258389334" version_id="2.0.0rc8" />
	</hooks>
	<tables><![CDATA[a:2:{s:10:"phpfox_ban";a:3:{s:7:"COLUMNS";a:10:{s:6:"ban_id";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"type_id";a:4:{i:0;s:9:"VCHAR:100";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"find_value";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"replacement";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:7:"user_id";a:4:{i:0;s:7:"UINT:11";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"days_banned";a:4:{i:0;s:5:"USINT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:17:"return_user_group";a:4:{i:0;s:6:"TINT:3";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:6:"reason";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:20:"user_groups_affected";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:6:"ban_id";s:4:"KEYS";a:1:{s:7:"type_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"type_id";}}}s:15:"phpfox_ban_data";a:3:{s:7:"COLUMNS";a:8:{s:11:"ban_data_id";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:6:"ban_id";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:16:"start_time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:14:"end_time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:17:"return_user_group";a:4:{i:0;s:6:"TINT:3";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:6:"reason";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"is_expired";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:11:"ban_data_id";s:4:"KEYS";a:1:{s:6:"ban_id";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:6:"ban_id";i:1;s:7:"user_id";i:2;s:14:"end_time_stamp";}}}}}]]></tables>
</module>