<module>
	<data>
		<module_id>contact</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>1</is_menu>
		<menu><![CDATA[a:1:{s:29:"contact.admin_menu_categories";a:1:{s:3:"url";a:1:{i:0;s:7:"contact";}}}]]></menu>
		<phrase_var_name>module_contact</phrase_var_name>
		<writable />
	</data>
	<menus>
		<menu module_id="contact" parent_var_name="" m_connection="footer" var_name="menu_contact" ordering="15" url_value="contact" version_id="2.0.0alpha1" disallow_access="" module="contact" />
	</menus>
	<settings>
		<setting group="" module_id="contact" is_hidden="0" type="boolean" var_name="contact_enable_captcha" phrase_var_name="setting_contact_enable_captcha" ordering="1" version_id="2.0.0alpha1">1</setting>
		<setting group="" module_id="contact" is_hidden="0" type="boolean" var_name="allow_html_in_contact" phrase_var_name="setting_allow_html_in_contact" ordering="1" version_id="2.0.0alpha1">1</setting>
		<setting group="" module_id="contact" is_hidden="0" type="string" var_name="contact_staff_emails" phrase_var_name="setting_contact_staff_emails" ordering="1" version_id="2.0.0alpha2" />
		<setting group="" module_id="contact" is_hidden="0" type="boolean" var_name="enable_auto_responder" phrase_var_name="setting_enable_auto_responder" ordering="1" version_id="2.0.8">1</setting>
		<setting group="" module_id="contact" is_hidden="0" type="large_string" var_name="auto_responder_subject" phrase_var_name="setting_auto_responder_subject" ordering="1" version_id="2.0.8">Thank you for contacting us</setting>
		<setting group="" module_id="contact" is_hidden="0" type="large_string" var_name="auto_responder_message" phrase_var_name="setting_auto_responder_message" ordering="1" version_id="2.0.8">We have received your message and will reply as soon as possible. Have a nice day</setting>
	</settings>
	<hooks>
		<hook module_id="contact" hook_type="controller" module="contact" call_name="contact.component_controller_index_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="contact" hook_type="service" module="contact" call_name="contact.service_process_add_start" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="contact" hook_type="service" module="contact" call_name="contact.service_process_add_end" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="contact" hook_type="service" module="contact" call_name="contact.service_process__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="contact" hook_type="service" module="contact" call_name="contact.service_callback__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="contact" hook_type="service" module="contact" call_name="contact.service_contact__call" added="1240687633" version_id="2.0.0beta1" />
	</hooks>
	<tables><![CDATA[a:1:{s:23:"phpfox_contact_category";a:3:{s:7:"COLUMNS";a:3:{s:11:"category_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:8:"ordering";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:5:"title";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:11:"category_id";s:4:"KEYS";a:1:{s:8:"ordering";a:2:{i:0;s:5:"INDEX";i:1;s:8:"ordering";}}}}]]></tables>
	<install><![CDATA[
		$aContactCategories = array(
			'Sales' => '0',	
			'Support' => '1',			
			'Suggestions' => '2'
		);
		foreach ($aContactCategories as $sTitle => $iOrdering)
		{
			$this->database()->insert(Phpfox::getT('contact_category'), array(
					'title' => $sTitle,
					'ordering' => $iOrdering
				)
			);
		}
	]]></install>
</module>