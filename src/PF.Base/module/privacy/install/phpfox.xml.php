<module>
	<data>
		<module_id>privacy</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>0</is_menu>
		<menu />
		<phrase_var_name>module_privacy</phrase_var_name>
		<writable />
	</data>
	<hooks>
		<hook module_id="privacy" hook_type="controller" module="privacy" call_name="privacy.component_controller_invalid_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="privacy" hook_type="service" module="privacy" call_name="privacy.service_privacy_get_start" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="privacy" hook_type="service" module="privacy" call_name="privacy.service_privacy_get_end" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="privacy" hook_type="service" module="privacy" call_name="privacy.service_privacy__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="privacy" hook_type="service" module="privacy" call_name="privacy.service_process__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="privacy" hook_type="component" module="privacy" call_name="privacy.component_block_list_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="privacy" hook_type="service" module="privacy" call_name="privacy.service_privacy_get" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="privacy" hook_type="service" module="privacy" call_name="privacy.service_callback__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="privacy" hook_type="component" module="privacy" call_name="privacy.component_block_build_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="privacy" hook_type="component" module="privacy" call_name="privacy.component_block_form_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="privacy" hook_type="component" module="privacy" call_name="privacy.component_block_friend_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="privacy" hook_type="service" module="privacy" call_name="privacy.service_privacy_getphrase" added="1358258443" version_id="3.5.0beta1" />
		<hook module_id="privacy" hook_type="component" module="privacy" call_name="privacy.component_block_form_process" added="1358258443" version_id="3.5.0beta1" />
	</hooks>
	<components>
		<component module_id="privacy" component="invalid" m_connection="privacy.invalid" module="privacy" is_controller="1" is_block="0" is_active="1" />
	</components>
	<user_group_settings>
		<setting is_admin_setting="0" module_id="privacy" type="boolean" admin="1" user="0" guest="0" staff="1" module="privacy" ordering="0">can_view_all_items</setting>
		<setting is_admin_setting="0" module_id="privacy" type="boolean" admin="1" user="0" guest="0" staff="1" module="privacy" ordering="0">can_comment_on_all_items</setting>
	</user_group_settings>
	<tables><![CDATA[a:1:{s:14:"phpfox_privacy";a:3:{s:7:"COLUMNS";a:6:{s:10:"privacy_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:9:"module_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:7:"item_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:14:"friend_list_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:5:"added";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:10:"privacy_id";s:4:"KEYS";a:2:{s:9:"module_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:9:"module_id";i:1;s:7:"item_id";}}s:14:"friend_list_id";a:2:{i:0;s:5:"INDEX";i:1;s:14:"friend_list_id";}}}}]]></tables>
</module>