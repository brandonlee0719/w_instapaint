<upgrade>
	<user_group_settings>
		<setting>
			<is_admin_setting>0</is_admin_setting>
			<module_id>custom</module_id>
			<type>boolean</type>
			<admin>true</admin>
			<user>true</user>
			<guest>false</guest>
			<staff>true</staff>
			<module>custom</module>
			<ordering>0</ordering>
			<value>can_have_relationship</value>
		</setting>
	</user_group_settings>
	<components>
		<component>
			<module_id>custom</module_id>
			<component>cf_about_me</component>
			<m_connection />
			<module>custom</module>
			<is_controller>0</is_controller>
			<is_block>1</is_block>
			<is_active>1</is_active>
			<value />
		</component>
		<component>
			<module_id>custom</module_id>
			<component>cf_who_i_d_like_to_meet</component>
			<m_connection />
			<module>custom</module>
			<is_controller>0</is_controller>
			<is_block>1</is_block>
			<is_active>1</is_active>
			<value />
		</component>
	</components>
	<blocks>
		<block>
			<type_id>0</type_id>
			<m_connection>profile.info</m_connection>
			<module_id>custom</module_id>
			<component>display</component>
			<location>7</location>
			<is_active>0</is_active>
			<ordering>5</ordering>
			<disallow_access />
			<can_move>1</can_move>
			<title>Custom Profile Info</title>
			<source_code />
			<source_parsed />
		</block>
		<block>
			<type_id>0</type_id>
			<m_connection>profile.info</m_connection>
			<module_id>custom</module_id>
			<component>panel</component>
			<location>7</location>
			<is_active>0</is_active>
			<ordering>4</ordering>
			<disallow_access />
			<can_move>1</can_move>
			<title>Custom Profile Blocks</title>
			<source_code />
			<source_parsed />
		</block>
		<block>
			<type_id>0</type_id>
			<m_connection>profile.info</m_connection>
			<module_id>custom</module_id>
			<component>cf_about_me</component>
			<location>2</location>
			<is_active>1</is_active>
			<ordering>2</ordering>
			<disallow_access />
			<can_move>1</can_move>
			<title>About Me (Custom)</title>
			<source_code />
			<source_parsed />
		</block>
		<block>
			<type_id>0</type_id>
			<m_connection>profile.info</m_connection>
			<module_id>custom</module_id>
			<component>cf_who_i_d_like_to_meet</component>
			<location>2</location>
			<is_active>1</is_active>
			<ordering>3</ordering>
			<disallow_access />
			<can_move>1</can_move>
			<title><![CDATA[Who I&#039;d Like to Meet (Custom)]]></title>
			<source_code />
			<source_parsed />
		</block>
	</blocks>
	<phpfox_update_blocks>
		<block>
			<type_id>0</type_id>
			<m_connection>profile.index</m_connection>
			<module_id>custom</module_id>
			<component>panel</component>
			<location>7</location>
			<is_active>0</is_active>
			<ordering>19</ordering>
			<disallow_access />
			<can_move>1</can_move>
			<title />
			<source_code />
			<source_parsed />
		</block>
		<block>
			<type_id>0</type_id>
			<m_connection>profile.index</m_connection>
			<module_id>custom</module_id>
			<component>display</component>
			<location>7</location>
			<is_active>0</is_active>
			<ordering>18</ordering>
			<disallow_access />
			<can_move>1</can_move>
			<title />
			<source_code />
			<source_parsed />
		</block>
	</phpfox_update_blocks>
	<sql><![CDATA[a:1:{s:9:"ADD_FIELD";a:1:{s:19:"phpfox_custom_field";a:1:{s:8:"has_feed";a:4:{i:0;s:6:"TINT:1";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}}}]]></sql>
</upgrade>