<module>
	<data>
		<module_id>invite</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>0</is_menu>
		<menu />
		<phrase_var_name>module_invite</phrase_var_name>
		<writable />
	</data>
	<menus>
		<menu module_id="invite" parent_var_name="" m_connection="footer" var_name="menu_invite" ordering="13" url_value="invite" version_id="2.0.0alpha1" disallow_access="" module="invite" />
		<menu module_id="invite" parent_var_name="" m_connection="invite" var_name="menu_pending_invitations" ordering="45" url_value="invite.invitations" version_id="2.0.0alpha1" disallow_access="" module="invite" />
		<menu module_id="invite" parent_var_name="" m_connection="invite" var_name="menu_invite_friends" ordering="44" url_value="invite" version_id="2.0.0alpha1" disallow_access="" module="invite" />
	</menus>
	<settings>
		<setting group="" module_id="invite" is_hidden="0" type="integer" var_name="invite_expire" phrase_var_name="setting_invite_expire" ordering="1" version_id="2.0.0alpha1">7</setting>
		<setting group="" module_id="invite" is_hidden="0" type="integer" var_name="pendings_to_show_per_page" phrase_var_name="setting_pendings_to_show_per_page" ordering="1" version_id="2.0.0alpha1">9</setting>
		<setting group="" module_id="invite" is_hidden="0" type="boolean" var_name="check_duplicate_invites" phrase_var_name="setting_check_duplicate_invites" ordering="1" version_id="2.0.0alpha1">1</setting>
		<setting group="" module_id="invite" is_hidden="0" type="boolean" var_name="make_friends_on_invitee_registration" phrase_var_name="setting_make_friends_on_invitee_registration" ordering="1" version_id="2.0.0alpha1">1</setting>
	</settings>
	<hooks>
		<hook module_id="invite" hook_type="controller" module="invite" call_name="invite.component_controller_index_process_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="invite" hook_type="controller" module="invite" call_name="invite.component_controller_index_process_end" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="invite" hook_type="controller" module="invite" call_name="invite.component_controller_index_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="invite" hook_type="controller" module="invite" call_name="invite.component_controller_invitations_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="invite" hook_type="service" module="invite" call_name="invite.service_invite__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="invite" hook_type="service" module="invite" call_name="invite.service_process__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="invite" hook_type="service" module="invite" call_name="invite.service_callback__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="invite" hook_type="template" module="invite" call_name="invite.template_controller_index_h3_start" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="invite" hook_type="controller" module="invite" call_name="invite.component_controller_index_process_send" added="1276177474" version_id="2.0.5" />
	</hooks>
	<components>
		<component module_id="invite" component="find" m_connection="" module="invite" is_controller="0" is_block="1" is_active="1" />
		<component module_id="invite" component="index" m_connection="invite.index" module="invite" is_controller="1" is_block="0" is_active="1" />
		<component module_id="invite" component="invitations" m_connection="invite.invitations" module="invite" is_controller="1" is_block="0" is_active="1" />
	</components>
	<user_group_settings>
		<setting is_admin_setting="0" module_id="invite" type="integer" admin="1" user="1" guest="0" staff="1" module="invite" ordering="0">points_invite</setting>
	</user_group_settings>
	<tables><![CDATA[a:1:{s:13:"phpfox_invite";a:3:{s:7:"COLUMNS";a:5:{s:9:"invite_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:5:"email";a:4:{i:0;s:9:"VCHAR:100";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"is_used";a:4:{i:0;s:6:"TINT:3";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:9:"invite_id";s:4:"KEYS";a:2:{s:5:"email";a:2:{i:0;s:5:"INDEX";i:1;s:5:"email";}s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"user_id";i:1;s:5:"email";}}}}}]]></tables>
</module>