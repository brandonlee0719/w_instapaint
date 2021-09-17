<module>
	<data>
		<module_id>language</module_id>
		<product_id>phpfox</product_id>
		<is_core>1</is_core>
		<is_active>1</is_active>
		<is_menu>0</is_menu>
		<menu />
		<phrase_var_name>module_language</phrase_var_name>
		<writable />
	</data>
	<settings>
		<setting group="" module_id="language" is_hidden="0" type="boolean" var_name="lang_pack_helper" phrase_var_name="setting_lang_pack_helper" ordering="0" version_id="2.0.0alpha1">0</setting>
		<setting group="" module_id="language" is_hidden="0" type="boolean" var_name="display_language_flag" phrase_var_name="setting_display_language_flag" ordering="1" version_id="2.0.0alpha1">0</setting>
		<setting group="" module_id="language" is_hidden="0" type="boolean" var_name="auto_detect_language_on_ip" phrase_var_name="setting_auto_detect_language_on_ip" ordering="2" version_id="3.1.0beta1">0</setting>
		<setting group="" module_id="language" is_hidden="0" type="boolean" var_name="no_string_restriction" phrase_var_name="setting_no_string_restriction" ordering="3" version_id="3.7.0beta2">0</setting>
	</settings>
	<hooks>
		<hook module_id="language" hook_type="controller" module="language" call_name="language.component_controller_admincp_file_process" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="language" hook_type="controller" module="language" call_name="language.component_controller_admincp_file_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="language" hook_type="controller" module="language" call_name="language.component_controller_admincp_phrase_add_process" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="language" hook_type="controller" module="language" call_name="language.component_controller_admincp_phrase_add_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="language" hook_type="service" module="language" call_name="language.service_phrase_phrase__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="language" hook_type="service" module="language" call_name="language.service_phrase_process___construct" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="language" hook_type="service" module="language" call_name="language.service_phrase_process__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="language" hook_type="service" module="language" call_name="language.service_language__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="language" hook_type="service" module="language" call_name="language.service_process__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="language" hook_type="component" module="language" call_name="language.component_block_admincp_form_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="language" hook_type="component" module="language" call_name="language.component_block_select_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="language" hook_type="controller" module="language" call_name="language.component_controller_admincp_import_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="language" hook_type="controller" module="language" call_name="language.component_controller_admincp_missing_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="language" hook_type="controller" module="language" call_name="language.component_controller_admincp_add_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="language" hook_type="component" module="language" call_name="language.component_block_sample_clean" added="1260366442" version_id="2.0.0rc11" />
	</hooks>
	<components>
		<component module_id="language" component="ajax" m_connection="" module="language" is_controller="0" is_block="0" is_active="1" />
		<component module_id="language" component="admincp.phrase.add" m_connection="" module="language" is_controller="0" is_block="0" is_active="1" />
		<component module_id="language" component="admincp.file" m_connection="" module="language" is_controller="0" is_block="0" is_active="1" />
		<component module_id="language" component="admincp.index" m_connection="" module="language" is_controller="0" is_block="0" is_active="1" />
		<component module_id="language" component="admincp.phrase.phrase" m_connection="" module="language" is_controller="0" is_block="0" is_active="1" />
	</components>
	<user_group_settings>
		<setting is_admin_setting="0" module_id="language" type="boolean" admin="1" user="0" guest="0" staff="0" module="language" ordering="0">can_manage_lang_packs</setting>
	</user_group_settings>
	<tables><![CDATA[a:3:{s:15:"phpfox_language";a:3:{s:7:"COLUMNS";a:16:{s:11:"language_id";a:4:{i:0;s:8:"VCHAR:12";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"parent_id";a:4:{i:0;s:8:"VCHAR:12";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:5:"title";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"user_select";a:4:{i:0;s:6:"TINT:1";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:13:"language_code";a:4:{i:0;s:8:"VCHAR:12";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"charset";a:4:{i:0;s:8:"VCHAR:15";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"direction";a:4:{i:0;s:7:"VCHAR:3";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"flag_id";a:4:{i:0;s:8:"VCHAR:12";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:9:"server_id";a:4:{i:0;s:6:"TINT:3";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"created";a:4:{i:0;s:9:"VCHAR:150";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:4:"site";a:4:{i:0;s:9:"VCHAR:250";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"is_default";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"is_master";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"version";a:4:{i:0;s:8:"VCHAR:12";i:1;s:5:"4.0.1";i:2;s:0:"";i:3;s:3:"YES";}s:8:"store_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:3:"YES";}}s:11:"PRIMARY_KEY";s:11:"language_id";s:4:"KEYS";a:3:{s:5:"title";a:2:{i:0;s:5:"INDEX";i:1;s:5:"title";}s:10:"is_default";a:2:{i:0;s:5:"INDEX";i:1;s:10:"is_default";}s:11:"user_select";a:2:{i:0;s:5:"INDEX";i:1;s:11:"user_select";}}}s:22:"phpfox_language_phrase";a:3:{s:7:"COLUMNS";a:9:{s:9:"phrase_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:11:"language_id";a:4:{i:0;s:8:"VCHAR:12";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"module_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"product_id";a:4:{i:0;s:8:"VCHAR:25";i:1;s:6:"phpfox";i:2;s:0:"";i:3;s:2:"NO";}s:10:"version_id";a:4:{i:0;s:8:"VCHAR:50";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:8:"var_name";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:4:"text";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:12:"text_default";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:5:"added";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:9:"phrase_id";s:4:"KEYS";a:3:{s:11:"language_id";a:2:{i:0;s:5:"INDEX";i:1;s:11:"language_id";}s:9:"module_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:9:"module_id";i:1;s:8:"var_name";}}s:12:"setting_list";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:11:"language_id";i:1;s:8:"var_name";}}}}s:20:"phpfox_language_rule";a:3:{s:7:"COLUMNS";a:6:{s:7:"rule_id";a:4:{i:0;s:7:"UINT:11";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:11:"language_id";a:4:{i:0;s:8:"VCHAR:12";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"var_name";a:4:{i:0;s:9:"VCHAR:250";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:4:"rule";a:4:{i:0;s:9:"VCHAR:150";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"rule_value";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"ordering";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:7:"rule_id";s:4:"KEYS";a:1:{s:11:"language_id";a:2:{i:0;s:5:"INDEX";i:1;s:11:"language_id";}}}}]]></tables>
</module>