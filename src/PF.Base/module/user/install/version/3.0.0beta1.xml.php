<upgrade>
	<settings>
		<setting>
			<group />
			<module_id>user</module_id>
			<is_hidden>0</is_hidden>
			<type>boolean</type>
			<var_name>enable_user_tooltip</var_name>
			<phrase_var_name>setting_enable_user_tooltip</phrase_var_name>
			<ordering>1</ordering>
			<version_id>2.1.0Beta1</version_id>
			<value>1</value>
		</setting>
		<setting>
			<group>registration</group>
			<module_id>user</module_id>
			<is_hidden>0</is_hidden>
			<type>boolean</type>
			<var_name>hide_main_menu</var_name>
			<phrase_var_name>setting_hide_main_menu</phrase_var_name>
			<ordering>16</ordering>
			<version_id>3.0.0beta1</version_id>
			<value>0</value>
		</setting>
		<setting>
			<group>registration</group>
			<module_id>user</module_id>
			<is_hidden>0</is_hidden>
			<type>boolean</type>
			<var_name>invite_only_community</var_name>
			<phrase_var_name>setting_invite_only_community</phrase_var_name>
			<ordering>17</ordering>
			<version_id>3.0.0beta1</version_id>
			<value>0</value>
		</setting>
	</settings>
	<phpfox_update_settings>
		<setting>
			<group>registration</group>
			<module_id>user</module_id>
			<is_hidden>0</is_hidden>
			<type>boolean</type>
			<var_name>captcha_on_signup</var_name>
			<phrase_var_name>setting_captcha_on_signup</phrase_var_name>
			<ordering>9</ordering>
			<version_id>2.0.0alpha1</version_id>
			<value>0</value>
		</setting>
		<setting>
			<group>registration</group>
			<module_id>user</module_id>
			<is_hidden>0</is_hidden>
			<type>boolean</type>
			<var_name>disable_username_on_sign_up</var_name>
			<phrase_var_name>setting_disable_username_on_sign_up</phrase_var_name>
			<ordering>14</ordering>
			<version_id>2.0.5dev1</version_id>
			<value>1</value>
		</setting>
	</phpfox_update_settings>
	<menus>
		<menu>
			<module_id>user</module_id>
			<parent_var_name>menu_settings</parent_var_name>
			<m_connection />
			<var_name>menu_user_logout_4ee1a589029a67e7f1a00990a1786f46</var_name>
			<ordering>100</ordering>
			<url_value>user.logout</url_value>
			<version_id>3.0.0Beta1</version_id>
			<disallow_access><![CDATA[a:1:{i:0;s:1:"3";}]]></disallow_access>
			<module>user</module>
			<value />
		</menu>
	</menus>
	<phpfox_update_menus>
		<menu>
			<module_id>user</module_id>
			<parent_var_name />
			<m_connection>main</m_connection>
			<var_name>menu_browse</var_name>
			<ordering>3</ordering>
			<url_value>user.browse</url_value>
			<version_id>2.0.0alpha1</version_id>
			<disallow_access />
			<module>user</module>
			<value />
		</menu>
		<menu>
			<module_id>user</module_id>
			<parent_var_name />
			<m_connection>main_right</m_connection>
			<var_name>menu_settings</var_name>
			<ordering>5</ordering>
			<url_value>user.setting</url_value>
			<version_id>2.0.0alpha1</version_id>
			<disallow_access><![CDATA[a:1:{i:0;s:1:"3";}]]></disallow_access>
			<module>user</module>
			<value />
		</menu>
	</phpfox_update_menus>
	<components>
		<component>
			<module_id>user</module_id>
			<component>register-top</component>
			<m_connection />
			<module>user</module>
			<is_controller>0</is_controller>
			<is_block>1</is_block>
			<is_active>1</is_active>
			<value />
		</component>
	</components>
	<blocks>
		<block>
			<type_id>0</type_id>
			<m_connection />
			<module_id>user</module_id>
			<component>register-top</component>
			<location>11</location>
			<is_active>1</is_active>
			<ordering>1</ordering>
			<disallow_access />
			<can_move>0</can_move>
			<title>User Register Top</title>
			<source_code />
			<source_parsed />
		</block>
	</blocks>
	<phpfox_update_blocks>
		<block>
			<type_id>0</type_id>
			<m_connection>core.index-visitor</m_connection>
			<module_id>user</module_id>
			<component>register</component>
			<location>11</location>
			<is_active>1</is_active>
			<ordering>3</ordering>
			<disallow_access />
			<can_move>0</can_move>
			<title>Registration Block</title>
			<source_code />
			<source_parsed />
		</block>
		<block>
			<type_id>0</type_id>
			<m_connection>core.index-visitor</m_connection>
			<module_id>user</module_id>
			<component>featured</component>
			<location>4</location>
			<is_active>0</is_active>
			<ordering>5</ordering>
			<disallow_access />
			<can_move>1</can_move>
			<title />
			<source_code />
			<source_parsed />
		</block>
	</phpfox_update_blocks>
	<sql><![CDATA[a:3:{s:9:"ADD_FIELD";a:6:{s:11:"phpfox_user";a:1:{s:15:"profile_page_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:20:"phpfox_user_activity";a:4:{s:19:"activity_music_song";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:20:"activity_marketplace";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:14:"activity_event";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:14:"activity_pages";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:18:"phpfox_user_custom";a:3:{s:20:"cf_record_label_name";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:20:"cf_record_label_type";a:4:{i:0;s:9:"VCHAR:150";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:22:"cf_relationship_status";a:4:{i:0;s:9:"VCHAR:150";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:24:"phpfox_user_custom_value";a:3:{s:20:"cf_record_label_name";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:20:"cf_record_label_type";a:4:{i:0;s:9:"VCHAR:150";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:22:"cf_relationship_status";a:4:{i:0;s:5:"USINT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:17:"phpfox_user_field";a:9:{s:10:"total_blog";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:11:"total_video";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"total_poll";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"total_quiz";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:11:"total_event";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"total_song";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"total_listing";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:11:"total_photo";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:11:"total_pages";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:17:"phpfox_user_space";a:1:{s:11:"space_pages";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}}s:11:"ALTER_FIELD";a:1:{s:11:"phpfox_user";a:2:{s:8:"password";a:4:{i:0;s:7:"CHAR:32";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:13:"password_salt";a:4:{i:0;s:6:"CHAR:3";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}}s:7:"ADD_KEY";a:1:{s:11:"phpfox_user";a:3:{s:11:"public_feed";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:9:"status_id";i:1;s:7:"view_id";i:2;s:13:"last_activity";}}s:11:"status_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:9:"status_id";i:1;s:7:"view_id";i:2;s:9:"full_name";}}s:7:"page_id";a:2:{i:0;s:5:"INDEX";i:1;s:15:"profile_page_id";}}}}]]></sql>
</upgrade>