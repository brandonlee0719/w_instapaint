<module>
	<data>
		<module_id>profile</module_id>
		<product_id>phpfox</product_id>
		<is_core>1</is_core>
		<is_active>1</is_active>
		<is_menu>0</is_menu>
		<menu />
		<phrase_var_name>module_profile</phrase_var_name>
		<writable />
	</data>
	<menus>
		<menu module_id="profile" parent_var_name="" m_connection="main_right" var_name="menu_profile" ordering="4" url_value="profile.my" version_id="2.0.0alpha1" disallow_access="a:1:{i:0;s:1:&quot;3&quot;;}" module="profile" />
		<menu module_id="profile" parent_var_name="" m_connection="profile" var_name="menu_profile" ordering="1" url_value="profile" version_id="2.0.0alpha1" disallow_access="" module="profile" />
		<menu module_id="profile" parent_var_name="" m_connection="profile.my" var_name="menu_customize_profile" ordering="4" url_value="profile.designer" version_id="2.0.0alpha3" disallow_access="" module="profile" />
		<menu module_id="profile" parent_var_name="" m_connection="profile.my" var_name="menu_my_profile" ordering="1" url_value="profile" version_id="2.0.0alpha3" disallow_access="" module="profile" />
		<menu module_id="profile" parent_var_name="" m_connection="mobile" var_name="menu_profile_profile_532c28d5412dd75bf975fb951c740a30" ordering="124" url_value="profile" version_id="3.1.0rc1" disallow_access="" module="profile" mobile_icon="small_members-profiles.gif" />
		<menu module_id="profile" parent_var_name="menu_profile" m_connection="" var_name="menu_profile_my_profile_b392d011b7f15183caf21a8bc56fd1fe" ordering="109" url_value="profile" version_id="3.0.0beta4" disallow_access="" module="profile" />
		<menu module_id="profile" parent_var_name="menu_profile" m_connection="" var_name="menu_profile_edit_profile_b392d011b7f15183caf21a8bc56fd1fe" ordering="110" url_value="user.profile" version_id="3.0.0beta4" disallow_access="" module="profile" />
		<menu module_id="profile" parent_var_name="menu_profile" m_connection="" var_name="menu_profile_edit_profile_picture_b392d011b7f15183caf21a8bc56fd1fe" ordering="111" url_value="user.photo" version_id="3.0.0beta4" disallow_access="" module="profile" />
		<menu module_id="profile" parent_var_name="menu_profile" m_connection="" var_name="menu_profile_customize_profile_b392d011b7f15183caf21a8bc56fd1fe" ordering="112" url_value="profile.designer" version_id="3.0.0beta4" disallow_access="" module="profile" />
	</menus>
	<settings>
		<setting group="" module_id="profile" is_hidden="0" type="boolean" var_name="show_empty_tabs" phrase_var_name="setting_show_empty_tabs" ordering="1" version_id="2.0.8">0</setting>
		<setting group="" module_id="profile" is_hidden="0" type="boolean" var_name="profile_caches" phrase_var_name="setting_profile_caches" ordering="2" version_id="3.6.0rc1">0</setting>
		<setting group="seo" module_id="profile" is_hidden="0" type="string" var_name="profile_seo_for_meta_title" phrase_var_name="setting_profile_seo_for_meta_title" ordering="5" version_id="2.0.0rc4">{full_name} - {gender_name} - {location}</setting>
	</settings>
	<hooks>
		<hook module_id="profile" hook_type="component" module="profile" call_name="profile.component_block_info" added="1231935380" version_id="2.0.0alpha1" />
		<hook module_id="profile" hook_type="component" module="profile" call_name="profile.component_block_info_clean" added="1231935380" version_id="2.0.0alpha1" />
		<hook module_id="profile" hook_type="component" module="profile" call_name="profile.component_block_pic_clean" added="1231935380" version_id="2.0.0alpha1" />
		<hook module_id="profile" hook_type="controller" module="profile" call_name="profile.component_controller_index_process_start" added="1231935380" version_id="2.0.0alpha1" />
		<hook module_id="profile" hook_type="controller" module="profile" call_name="profile.component_controller_index_clean" added="1231935380" version_id="2.0.0alpha1" />
		<hook module_id="profile" hook_type="service" module="profile" call_name="profile.service_callback___call" added="1231935380" version_id="2.0.0alpha1" />
		<hook module_id="profile" hook_type="template" module="profile" call_name="profile.template_block_info" added="1231935457" version_id="2.0.0alpha1" />
		<hook module_id="profile" hook_type="template" module="profile" call_name="profile.template_block_menu" added="1232376179" version_id="2.0.0alpha1" />
		<hook module_id="profile" hook_type="controller" module="profile" call_name="profile.component_controller_my_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="profile" hook_type="controller" module="profile" call_name="profile.component_controller_design_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="profile" hook_type="service" module="profile" call_name="profile.service_process__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="profile" hook_type="service" module="profile" call_name="profile.service_profile__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="profile" hook_type="controller" module="profile" call_name="profile.component_controller_private_clean" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="profile" hook_type="controller" module="profile" call_name="profile.component_controller_designer_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="profile" hook_type="controller" module="profile" call_name="profile.component_controller_index_mobile_clean" added="1267629983" version_id="2.0.4" />
		<hook module_id="profile" hook_type="controller" module="profile" call_name="profile.component_controller_info_mobile_clean" added="1267629983" version_id="2.0.4" />
		<hook module_id="profile" hook_type="controller" module="profile" call_name="profile.component_controller_index_process_section" added="1276177474" version_id="2.0.5" />
		<hook module_id="profile" hook_type="service" module="profile" call_name="profile.service_callback_getnewsfeedinfo_start" added="1286546859" version_id="2.0.7" />
		<hook module_id="profile" hook_type="controller" module="profile" call_name="profile.component_controller_index_process_after_requests" added="1286546859" version_id="2.0.7" />
		<hook module_id="profile" hook_type="component" module="profile" call_name="profile.component_block_header_process" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="profile" hook_type="component" module="profile" call_name="profile.component_block_menu_process" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="profile" hook_type="component" module="profile" call_name="profile.component_block_mobile_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="profile" hook_type="component" module="profile" call_name="profile.component_block_pic_process" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="profile" hook_type="controller" module="profile" call_name="profile.component_controller_index_set_header" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="profile" hook_type="controller" module="profile" call_name="profile.component_controller_info_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="profile" hook_type="template" module="profile" call_name="profile.template_block_menu_more" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="profile" hook_type="controller" module="profile" call_name="profile.component_controller_index_process_is_sub_section" added="1323703660" version_id="3.0.0" />
		<hook module_id="profile" hook_type="component" module="profile" call_name="profile.component_block_cover_clean" added="1335951260" version_id="3.2.0" />
		<hook module_id="profile" hook_type="component" module="profile" call_name="profile.component_block_logo_clean" added="1335951260" version_id="3.2.0" />
		<hook module_id="profile" hook_type="component" module="profile" call_name="profile.component_block_pic_start" added="1358258443" version_id="3.5.0beta1" />
		<hook module_id="profile" hook_type="service" module="profile" call_name="profile.service_profile_get_profile_menu" added="1378374384" version_id="3.7.0rc1" />
	</hooks>
	<components>
		<component module_id="profile" component="index" m_connection="profile.index" module="profile" is_controller="1" is_block="0" is_active="1" />
		<component module_id="profile" component="pic" m_connection="" module="profile" is_controller="0" is_block="1" is_active="1" />
		<component module_id="profile" component="menu" m_connection="" module="profile" is_controller="0" is_block="1" is_active="1" />
		<component module_id="profile" component="info" m_connection="" module="profile" is_controller="0" is_block="1" is_active="1" />
		<component module_id="profile" component="header" m_connection="" module="profile" is_controller="0" is_block="1" is_active="1" />
		<component module_id="profile" component="my" m_connection="profile.my" module="profile" is_controller="1" is_block="0" is_active="1" />
		<component module_id="profile" component="info" m_connection="profile.info" module="profile" is_controller="1" is_block="0" is_active="1" />
		<component module_id="profile" component="logo" m_connection="" module="profile" is_controller="0" is_block="1" is_active="1" />
	</components>
	<user_group_settings>
		<setting is_admin_setting="0" module_id="profile" type="boolean" admin="1" user="1" guest="0" staff="1" module="profile" ordering="0">can_post_comment_on_profile</setting>
		<setting is_admin_setting="0" module_id="profile" type="boolean" admin="1" user="1" guest="0" staff="1" module="profile" ordering="0">display_membership_info</setting>
		<setting is_admin_setting="0" module_id="profile" type="boolean" admin="1" user="1" guest="1" staff="1" module="profile" ordering="0">can_view_users_profile</setting>
		<setting is_admin_setting="0" module_id="profile" type="boolean" admin="1" user="1" guest="0" staff="1" module="profile" ordering="0">can_change_cover_photo</setting>
	</user_group_settings>
</module>