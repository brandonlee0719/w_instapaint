<upgrade>
	<settings>
		<setting>
			<group>time_stamps</group>
			<module_id>user</module_id>
			<is_hidden>0</is_hidden>
			<type>string</type>
			<var_name>user_dob_month_day_year</var_name>
			<phrase_var_name>setting_user_dob_month_day_year</phrase_var_name>
			<ordering>1</ordering>
			<version_id>3.0.0</version_id>
			<value>F j, Y</value>
		</setting>
		<setting>
			<group>time_stamps</group>
			<module_id>user</module_id>
			<is_hidden>0</is_hidden>
			<type>string</type>
			<var_name>user_dob_month_day</var_name>
			<phrase_var_name>setting_user_dob_month_day</phrase_var_name>
			<ordering>2</ordering>
			<version_id>3.0.0</version_id>
			<value>F j</value>
		</setting>
	</settings>
	<hooks>
		<hook>
			<module_id>user</module_id>
			<hook_type>component</hook_type>
			<module>user</module>
			<call_name>user.component_block_tooltip_1</call_name>
			<added>1323345487</added>
			<version_id>3.0.0</version_id>
			<value />
		</hook>
		<hook>
			<module_id>user</module_id>
			<hook_type>template</hook_type>
			<module>user</module>
			<call_name>user.template_controller_profile_form_onsubmit</call_name>
			<added>1323345487</added>
			<version_id>3.0.0</version_id>
			<value />
		</hook>
		<hook>
			<module_id>user</module_id>
			<hook_type>template</hook_type>
			<module>user</module>
			<call_name>user.template_block_tooltip_1</call_name>
			<added>1323345637</added>
			<version_id>3.0.0</version_id>
			<value />
		</hook>
		<hook>
			<module_id>user</module_id>
			<hook_type>template</hook_type>
			<module>user</module>
			<call_name>user.template_block_tooltip_3</call_name>
			<added>1323345637</added>
			<version_id>3.0.0</version_id>
			<value />
		</hook>
		<hook>
			<module_id>user</module_id>
			<hook_type>template</hook_type>
			<module>user</module>
			<call_name>user.template_block_tooltip_5</call_name>
			<added>1323345637</added>
			<version_id>3.0.0</version_id>
			<value />
		</hook>
		<hook>
			<module_id>user</module_id>
			<hook_type>template</hook_type>
			<module>user</module>
			<call_name>user.template_block_tooltip_2</call_name>
			<added>1323345637</added>
			<version_id>3.0.0</version_id>
			<value />
		</hook>
	</hooks>
	<sql><![CDATA[a:1:{s:7:"ADD_KEY";a:1:{s:11:"phpfox_user";a:1:{s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"user_id";i:1;s:9:"status_id";}}}}}]]></sql>
</upgrade>