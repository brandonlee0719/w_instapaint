<upgrade>
	<settings>
		<setting>
			<group />
			<module_id>comment</module_id>
			<is_hidden>0</is_hidden>
			<type>integer</type>
			<var_name>total_comments_in_activity_feed</var_name>
			<phrase_var_name>setting_total_comments_in_activity_feed</phrase_var_name>
			<ordering>1</ordering>
			<version_id>3.0.0Beta1</version_id>
			<value>2</value>
		</setting>
		<setting>
			<group />
			<module_id>comment</module_id>
			<is_hidden>0</is_hidden>
			<type>integer</type>
			<var_name>total_amount_of_comments_to_load</var_name>
			<phrase_var_name>setting_total_amount_of_comments_to_load</phrase_var_name>
			<ordering>1</ordering>
			<version_id>3.0.0Beta1</version_id>
			<value>10</value>
		</setting>
	</settings>
	<phpfox_update_user_group_settings>
		<setting>
			<is_admin_setting>0</is_admin_setting>
			<module_id>comment</module_id>
			<type>boolean</type>
			<admin>1</admin>
			<user>0</user>
			<guest>0</guest>
			<staff>1</staff>
			<module>comment</module>
			<ordering>0</ordering>
			<value>edit_own_comment</value>
		</setting>
	</phpfox_update_user_group_settings>
	<phpfox_update_blocks>
		<block>
			<type_id>0</type_id>
			<m_connection>profile.index</m_connection>
			<module_id>comment</module_id>
			<component>display</component>
			<location>2</location>
			<is_active>0</is_active>
			<ordering>16</ordering>
			<disallow_access />
			<can_move>1</can_move>
			<title />
			<source_code />
			<source_parsed />
		</block>
	</phpfox_update_blocks>
	<sql><![CDATA[a:1:{s:9:"ADD_FIELD";a:1:{s:14:"phpfox_comment";a:1:{s:10:"total_like";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}}}]]></sql>
</upgrade>