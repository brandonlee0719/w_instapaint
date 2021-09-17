<upgrade>
	<settings>
		<setting>
			<group />
			<module_id>feed</module_id>
			<is_hidden>0</is_hidden>
			<type>boolean</type>
			<var_name>force_timeline</var_name>
			<phrase_var_name>setting_force_timeline</phrase_var_name>
			<ordering>1</ordering>
			<version_id>3.3.0beta1</version_id>
			<value>0</value>
		</setting>
		<setting>
			<group />
			<module_id>feed</module_id>
			<is_hidden>0</is_hidden>
			<type>boolean</type>
			<var_name>can_add_past_dates</var_name>
			<phrase_var_name>setting_can_add_past_dates</phrase_var_name>
			<ordering>1</ordering>
			<version_id>3.3.0beta1</version_id>
			<value>1</value>
		</setting>
		<setting>
			<group />
			<module_id>feed</module_id>
			<is_hidden>0</is_hidden>
			<type>boolean</type>
			<var_name>timeline_optional</var_name>
			<phrase_var_name>setting_timeline_optional</phrase_var_name>
			<ordering>1</ordering>
			<version_id>3.3.0beta1</version_id>
			<value>0</value>
		</setting>
	</settings>
	<hooks>
		<hook>
			<module_id>feed</module_id>
			<hook_type>component</hook_type>
			<module>feed</module>
			<call_name>feed.component_block_mini_clean</call_name>
			<added>1339076699</added>
			<version_id>3.3.0beta1</version_id>
			<value />
		</hook>
	</hooks>
	<components>
		<component>
			<module_id>feed</module_id>
			<component>time</component>
			<m_connection />
			<module>feed</module>
			<is_controller>0</is_controller>
			<is_block>1</is_block>
			<is_active>1</is_active>
			<value />
		</component>
	</components>
	<blocks>
		<block>
			<type_id>0</type_id>
			<m_connection>profile.index</m_connection>
			<module_id>feed</module_id>
			<component>time</component>
			<location>3</location>
			<is_active>1</is_active>
			<ordering>4</ordering>
			<disallow_access />
			<can_move>0</can_move>
			<title>Feed Timeline</title>
			<source_code />
			<source_parsed />
		</block>
	</blocks>
	<phpfox_update_blocks>
		<block>
			<type_id>0</type_id>
			<m_connection>profile.index</m_connection>
			<module_id>feed</module_id>
			<component>display</component>
			<location>2</location>
			<is_active>1</is_active>
			<ordering>7</ordering>
			<disallow_access />
			<can_move>0</can_move>
			<title />
			<source_code />
			<source_parsed />
		</block>
	</phpfox_update_blocks>
	<sql><![CDATA[a:1:{s:9:"ADD_FIELD";a:1:{s:11:"phpfox_feed";a:1:{s:14:"parent_feed_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}}}]]></sql>
</upgrade>