<module>
	<data>
		<module_id>custom</module_id>
		<product_id>phpfox</product_id>
		<is_core>1</is_core>
		<is_active>1</is_active>
		<is_menu>0</is_menu>
		<menu />
		<phrase_var_name>module_custom</phrase_var_name>
		<writable />
	</data>
	<settings>
		<setting group="" module_id="custom" is_hidden="0" type="boolean" var_name="hide_custom_fields_when_empty" phrase_var_name="setting_hide_custom_fields_when_empty" ordering="1" version_id="2.0.0alpha3">1</setting>
	</settings>
	<blocks>
		<block type_id="0" m_connection="profile.index" module_id="custom" component="cf_about_me" location="1" is_active="1" ordering="1" disallow_access="" can_move="1">
			<title>About Me (Custom)</title>
			<source_code />
			<source_parsed />
		</block>
	</blocks>
	<hooks>
		<hook module_id="custom" hook_type="controller" module="custom" call_name="custom.component_controller_admincp_index_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="custom" hook_type="controller" module="custom" call_name="custom.component_controller_admincp_group_add_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="custom" hook_type="controller" module="custom" call_name="custom.component_controller_admincp_add_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="custom" hook_type="component" module="custom" call_name="custom.component_block_panel_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="custom" hook_type="component" module="custom" call_name="custom.component_block_form_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="custom" hook_type="component" module="custom" call_name="custom.component_block_display_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="custom" hook_type="component" module="custom" call_name="custom.component_block_group_form_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="custom" hook_type="component" module="custom" call_name="custom.component_block_entry_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="custom" hook_type="service" module="custom" call_name="custom.service_process__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="custom" hook_type="service" module="custom" call_name="custom.service_custom__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="custom" hook_type="service" module="custom" call_name="custom.service_group_process__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="custom" hook_type="service" module="custom" call_name="custom.service_group_group__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="custom" hook_type="service" module="custom" call_name="custom.service_callback__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="custom" hook_type="service" module="custom" call_name="custom.service_process_updatefields" added="1240688954" version_id="2.0.0beta1" />
		<hook module_id="custom" hook_type="component" module="custom" call_name="custom.component_ajax_edit" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="custom" hook_type="component" module="custom" call_name="custom.component_block_block_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="custom" hook_type="service" module="custom" call_name="custom.service_relation_process_updaterelationship__1" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="custom" hook_type="service" module="custom" call_name="custom.service_relation_process__call" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="custom" hook_type="service" module="custom" call_name="custom.component_service_callback_getactivityfeed__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="custom" hook_type="service" module="custom" call_name="custom.service_custom_getforedit_1" added="1358258443" version_id="3.5.0beta1" />
		<hook module_id="custom" hook_type="component" module="custom" call_name="custom.component_ajax_updatefields__1" added="1363075699" version_id="3.5.0" />
	</hooks>
	<components>
		<component module_id="custom" component="panel" m_connection="" module="custom" is_controller="0" is_block="1" is_active="1" />
		<component module_id="custom" component="display" m_connection="" module="custom" is_controller="0" is_block="1" is_active="1" />
		<component module_id="custom" component="cf_about_me" m_connection="" module="custom" is_controller="0" is_block="1" is_active="1" />
		<component module_id="custom" component="cf_who_i_d_like_to_meet" m_connection="" module="custom" is_controller="0" is_block="1" is_active="1" />
		<component module_id="custom" component="cf_movies" m_connection="" module="custom" is_controller="0" is_block="1" is_active="1" />
		<component module_id="custom" component="cf_drinker" m_connection="" module="custom" is_controller="0" is_block="1" is_active="1" />
		<component module_id="custom" component="cf_smoker" m_connection="" module="custom" is_controller="0" is_block="1" is_active="1" />
		<component module_id="custom" component="cf_interests" m_connection="" module="custom" is_controller="0" is_block="1" is_active="1" />
		<component module_id="custom" component="cf_music" m_connection="" module="custom" is_controller="0" is_block="1" is_active="1" />
		<component module_id="custom" component="cf_college" m_connection="" module="custom" is_controller="0" is_block="1" is_active="1" />
	</components>
	<user_group_settings>
		<setting is_admin_setting="0" module_id="custom" type="boolean" admin="1" user="1" guest="0" staff="1" module="custom" ordering="0">can_edit_own_custom_field</setting>
		<setting is_admin_setting="0" module_id="custom" type="boolean" admin="1" user="0" guest="0" staff="1" module="custom" ordering="0">can_edit_other_custom_fields</setting>
		<setting is_admin_setting="0" module_id="custom" type="boolean" admin="1" user="0" guest="0" staff="1" module="custom" ordering="0">can_manage_custom_fields</setting>
		<setting is_admin_setting="0" module_id="custom" type="boolean" admin="1" user="0" guest="0" staff="1" module="custom" ordering="0">can_add_custom_fields</setting>
		<setting is_admin_setting="0" module_id="custom" type="boolean" admin="1" user="0" guest="0" staff="1" module="custom" ordering="0">can_add_custom_fields_group</setting>
		<setting is_admin_setting="0" module_id="custom" type="boolean" admin="0" user="0" guest="0" staff="0" module="custom" ordering="0">has_special_custom_fields</setting>
		<setting is_admin_setting="0" module_id="custom" type="string" admin="" user="" guest="" staff="" module="custom" ordering="0">custom_table_name</setting>
		<setting is_admin_setting="0" module_id="custom" type="boolean" admin="true" user="true" guest="false" staff="true" module="custom" ordering="0">can_have_relationship</setting>
	</user_group_settings>
	<tables><![CDATA[a:5:{s:19:"phpfox_custom_field";a:3:{s:7:"COLUMNS";a:16:{s:8:"field_id";a:4:{i:0;s:5:"USINT";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:10:"field_name";a:4:{i:0;s:9:"VCHAR:150";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"module_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"product_id";a:4:{i:0;s:8:"VCHAR:25";i:1;s:6:"phpfox";i:2;s:0:"";i:3;s:2:"NO";}s:13:"user_group_id";a:4:{i:0;s:6:"TINT:3";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"type_id";a:4:{i:0;s:9:"VCHAR:150";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"group_id";a:4:{i:0;s:5:"USINT";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:15:"phrase_var_name";a:4:{i:0;s:9:"VCHAR:250";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"type_name";a:4:{i:0;s:8:"VCHAR:50";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"var_type";a:4:{i:0;s:8:"VCHAR:20";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"is_active";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"1";i:2;s:0:"";i:3;s:2:"NO";}s:11:"is_required";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"has_feed";a:4:{i:0;s:6:"TINT:1";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:9:"on_signup";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"ordering";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"is_search";a:4:{i:0;s:6:"TINT:1";i:1;i:0;i:2;s:0:"";i:3;s:3:"YES";}}s:11:"PRIMARY_KEY";s:8:"field_id";s:4:"KEYS";a:1:{s:8:"field_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:8:"field_id";i:1;s:13:"user_group_id";}}}}s:19:"phpfox_custom_group";a:3:{s:7:"COLUMNS";a:8:{s:8:"group_id";a:4:{i:0;s:5:"USINT";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:9:"module_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"product_id";a:4:{i:0;s:8:"VCHAR:25";i:1;s:6:"phpfox";i:2;s:0:"";i:3;s:2:"NO";}s:13:"user_group_id";a:4:{i:0;s:6:"TINT:3";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"type_id";a:4:{i:0;s:9:"VCHAR:150";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:15:"phrase_var_name";a:4:{i:0;s:9:"VCHAR:250";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"is_active";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"1";i:2;s:0:"";i:3;s:2:"NO";}s:8:"ordering";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:8:"group_id";s:4:"KEYS";a:2:{s:13:"user_group_id";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:13:"user_group_id";i:1;s:7:"type_id";i:2;s:9:"is_active";}}s:15:"phrase_var_name";a:2:{i:0;s:5:"INDEX";i:1;s:15:"phrase_var_name";}}}s:20:"phpfox_custom_option";a:3:{s:7:"COLUMNS";a:3:{s:9:"option_id";a:4:{i:0;s:5:"USINT";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:8:"field_id";a:4:{i:0;s:5:"USINT";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:15:"phrase_var_name";a:4:{i:0;s:9:"VCHAR:250";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:9:"option_id";s:4:"KEYS";a:1:{s:8:"field_id";a:2:{i:0;s:5:"INDEX";i:1;s:8:"field_id";}}}s:22:"phpfox_custom_relation";a:2:{s:7:"COLUMNS";a:3:{s:11:"relation_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:15:"phrase_var_name";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:12:"confirmation";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:11:"relation_id";}s:27:"phpfox_custom_relation_data";a:3:{s:7:"COLUMNS";a:8:{s:16:"relation_data_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:11:"relation_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:12:"with_user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:9:"status_id";a:4:{i:0;s:6:"TINT:1";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"total_like";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"total_dislike";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"total_comment";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:16:"relation_data_id";s:4:"KEYS";a:2:{s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"user_id";}s:12:"with_user_id";a:2:{i:0;s:5:"INDEX";i:1;s:12:"with_user_id";}}}}]]></tables>
	<install><![CDATA[
		$aRelations = array(
			1 => array('phrase_var_name' => 'custom.custom_relation_blank', 'confirmation' => 0),
			2 => array('phrase_var_name' => 'custom.custom_relation_single', 'confirmation' => 0),
			3 => array('phrase_var_name' => 'custom.custom_relation_engaged', 'confirmation' => 1),
			4 => array('phrase_var_name' => 'custom.custom_relation_married', 'confirmation' => 1),
			5 => array('phrase_var_name' => 'custom.custom_relation_it_s_complicated', 'confirmation' => 0),
			6 => array('phrase_var_name' => 'custom.custom_relation_in_an_open_relationship', 'confirmation' => 1),
			7 => array('phrase_var_name' => 'custom.custom_relation_widowed', 'confirmation' => 0),
			8 => array('phrase_var_name' => 'custom.custom_relation_separated', 'confirmation' => 0),
			9 => array('phrase_var_name' => 'custom.custom_relation_divorced', 'confirmation' => 0),
			10 => array('phrase_var_name' => 'custom.custom_relation_in_a_relationship', 'confirmation' => 1),
		);
		foreach ($aRelations as $iId => $aRelation)
		{
			$this->database()->insert(Phpfox::getT('custom_relation'), array(
					'relation_id' => $iId,
					'phrase_var_name' => $aRelation['phrase_var_name'],
					'confirmation' => $aRelation['confirmation']
				)
			);
		}
	]]></install>
</module>