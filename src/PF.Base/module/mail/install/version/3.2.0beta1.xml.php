<upgrade>
	<settings>
		<setting>
			<group />
			<module_id>mail</module_id>
			<is_hidden>0</is_hidden>
			<type>boolean</type>
			<var_name>threaded_mail_conversation</var_name>
			<phrase_var_name>setting_threaded_mail_conversation</phrase_var_name>
			<ordering>1</ordering>
			<version_id>3.2.0beta1</version_id>
			<value>0</value>
		</setting>
	</settings>
	<menus>
		<menu>
			<module_id>mail</module_id>
			<parent_var_name />
			<m_connection>mobile</m_connection>
			<var_name>menu_mail_mail_532c28d5412dd75bf975fb951c740a30</var_name>
			<ordering>119</ordering>
			<url_value>mail</url_value>
			<version_id>3.1.0rc1</version_id>
			<disallow_access />
			<module>mail</module>
			<mobile_icon>small_mail.png</mobile_icon>
			<value />
		</menu>
	</menus>
	<hooks>
		<hook>
			<module_id>mail</module_id>
			<hook_type>controller</hook_type>
			<module>mail</module>
			<call_name>mail.component_controller_thread_clean</call_name>
			<added>1334069444</added>
			<version_id>3.2.0beta1</version_id>
			<value />
		</hook>
	</hooks>
	<sql><![CDATA[a:1:{s:7:"ADD_KEY";a:1:{s:11:"phpfox_mail";a:2:{s:15:"owner_user_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:13:"owner_user_id";i:1;s:14:"viewer_user_id";}}s:7:"mail_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"mail_id";i:1;s:13:"owner_user_id";}}}}}]]></sql>
</upgrade>