<module>
	<data>
		<module_id>attachment</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>1</is_menu>
		<menu><![CDATA[a:3:{s:34:"attachment.admin_menu_manage_types";a:1:{s:3:"url";a:1:{i:0;s:10:"attachment";}}s:34:"attachment.admin_menu_add_new_type";a:1:{s:3:"url";a:2:{i:0;s:10:"attachment";i:1;s:3:"add";}}s:28:"attachment.admin_menu_manage";a:1:{s:3:"url";a:2:{i:0;s:10:"attachment";i:1;s:6:"manage";}}}]]></menu>
		<phrase_var_name>module_attachment</phrase_var_name>
		<writable><![CDATA[a:1:{i:0;s:16:"file/attachment/";}]]></writable>
	</data>
	<settings>
		<setting group="" module_id="attachment" is_hidden="0" type="array" var_name="attachment_valid_images" phrase_var_name="setting_attachment_valid_images" ordering="0" version_id="2.0.0alpha1"><![CDATA[s:35:"array('gif', 'jpg', 'jpeg', 'png');";]]></setting>
		<setting group="" module_id="attachment" is_hidden="0" type="integer" var_name="attachment_max_thumbnail" phrase_var_name="setting_attachment_max_thumbnail" ordering="0" version_id="2.0.0alpha1">120</setting>
		<setting group="" module_id="attachment" is_hidden="0" type="integer" var_name="attachment_max_medium" phrase_var_name="setting_attachment_max_medium" ordering="0" version_id="2.0.0alpha1">400</setting>
		<setting group="" module_id="attachment" is_hidden="1" type="integer" var_name="attachment_upload_bars" phrase_var_name="setting_attachment_upload_bars" ordering="0" version_id="2.0.0alpha1">4</setting>
	</settings>
	<hooks>
		<hook module_id="attachment" hook_type="component" module="attachment" call_name="attachment.component_block_list_process" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="attachment" hook_type="component" module="attachment" call_name="attachment.component_block_list_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="attachment" hook_type="component" module="attachment" call_name="attachment.component_block_upload_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="attachment" hook_type="component" module="attachment" call_name="attachment.component_block_add_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="attachment" hook_type="component" module="attachment" call_name="attachment.component_block_current_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="attachment" hook_type="service" module="attachment" call_name="attachment.service_attachment___construct" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="attachment" hook_type="service" module="attachment" call_name="attachment.service_attachment_get_count" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="attachment" hook_type="service" module="attachment" call_name="attachment.service_attachment_select" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="attachment" hook_type="service" module="attachment" call_name="attachment.service_attachment_getforitemedit_start" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="attachment" hook_type="service" module="attachment" call_name="attachment.service_attachment_getforitemedit_end" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="attachment" hook_type="service" module="attachment" call_name="attachment.service_attachment_getfordownload" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="attachment" hook_type="service" module="attachment" call_name="attachment.service_attachment_hasaccess_start" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="attachment" hook_type="service" module="attachment" call_name="attachment.service_attachment_hasaccess_end" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="attachment" hook_type="service" module="attachment" call_name="attachment.service_attachment__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="attachment" hook_type="service" module="attachment" call_name="attachment.service_type___construct" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="attachment" hook_type="service" module="attachment" call_name="attachment.service_type__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="attachment" hook_type="service" module="attachment" call_name="attachment.service_process_add" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="attachment" hook_type="service" module="attachment" call_name="attachment.service_process_updatecounter" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="attachment" hook_type="service" module="attachment" call_name="attachment.service_process_updateinline" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="attachment" hook_type="service" module="attachment" call_name="attachment.service_process_deleteforitem" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="attachment" hook_type="service" module="attachment" call_name="attachment.service_process_delete" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="attachment" hook_type="service" module="attachment" call_name="attachment.service_process_updateitemcount_category" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="attachment" hook_type="service" module="attachment" call_name="attachment.service_process_updateitemcount" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="attachment" hook_type="service" module="attachment" call_name="attachment.service_process__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="attachment" hook_type="controller" module="attachment" call_name="attachment.component_controller_index_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="attachment" hook_type="service" module="attachment" call_name="attachment.service_callback__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="attachment" hook_type="controller" module="attachment" call_name="attachment.component_controller_admincp_index_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="attachment" hook_type="controller" module="attachment" call_name="attachment.component_controller_admincp_add_clean" added="1258389334" version_id="2.0.0rc8" />
		<hook module_id="attachment" hook_type="component" module="attachment" call_name="attachment.component_block_share_clean" added="1319729453" version_id="3.0.0rc1" />
	</hooks>
	<components>
		<component module_id="attachment" component="ajax" m_connection="" module="attachment" is_controller="0" is_block="0" is_active="1" />
		<component module_id="attachment" component="add" m_connection="" module="attachment" is_controller="0" is_block="1" is_active="1" />
		<component module_id="attachment" component="upload" m_connection="" module="attachment" is_controller="0" is_block="1" is_active="1" />
		<component module_id="attachment" component="frame" m_connection="attachment.frame" module="attachment" is_controller="1" is_block="0" is_active="1" />
		<component module_id="attachment" component="current" m_connection="" module="attachment" is_controller="0" is_block="1" is_active="1" />
		<component module_id="attachment" component="archive" m_connection="" module="attachment" is_controller="0" is_block="1" is_active="1" />
		<component module_id="attachment" component="list" m_connection="" module="attachment" is_controller="0" is_block="1" is_active="1" />
		<component module_id="attachment" component="download" m_connection="attachment.download" module="attachment" is_controller="1" is_block="0" is_active="1" />
	</components>
	<user_group_settings>
		<setting is_admin_setting="0" module_id="attachment" type="integer" admin="1" user="1" guest="1" staff="1" module="attachment" ordering="2">points_attachment</setting>
		<setting is_admin_setting="0" module_id="attachment" type="boolean" admin="1" user="1" guest="0" staff="1" module="attachment" ordering="3">can_attach_on_blog</setting>
		<setting is_admin_setting="0" module_id="attachment" type="string" admin="null" user="null" guest="0" staff="null" module="attachment" ordering="1">attachment_limit</setting>
		<setting is_admin_setting="0" module_id="attachment" type="boolean" admin="1" user="1" guest="0" staff="1" module="attachment" ordering="0">delete_own_attachment</setting>
		<setting is_admin_setting="0" module_id="attachment" type="boolean" admin="1" user="0" guest="0" staff="1" module="attachment" ordering="0">delete_user_attachment</setting>
		<setting is_admin_setting="0" module_id="attachment" type="integer" admin="0" user="5000" guest="1" staff="0" module="attachment" ordering="0">item_max_upload_size</setting>
	</user_group_settings>
	<tables><![CDATA[a:2:{s:17:"phpfox_attachment";a:3:{s:7:"COLUMNS";a:18:{s:13:"attachment_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"view_id";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"item_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:11:"category_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:7:"link_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"file_name";a:4:{i:0;s:9:"VCHAR:100";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:9:"file_size";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:11:"destination";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:9:"extension";a:4:{i:0;s:8:"VCHAR:20";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:11:"description";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:14:"video_duration";a:4:{i:0;s:7:"VCHAR:8";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:9:"is_inline";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"is_image";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:8:"is_video";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"counter";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"server_id";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:13:"attachment_id";s:4:"KEYS";a:4:{s:11:"category_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:11:"category_id";i:1;s:7:"user_id";}}s:9:"extension";a:2:{i:0;s:5:"INDEX";i:1;s:9:"extension";}s:7:"user_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"user_id";}s:7:"view_id";a:2:{i:0;s:5:"INDEX";i:1;a:4:{i:0;s:7:"view_id";i:1;s:7:"item_id";i:2;s:11:"category_id";i:3;s:7:"user_id";}}}}s:22:"phpfox_attachment_type";a:2:{s:7:"COLUMNS";a:5:{s:9:"extension";a:4:{i:0;s:8:"VCHAR:20";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"mime_type";a:4:{i:0;s:9:"VCHAR:250";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:9:"is_active";a:4:{i:0;s:6:"TINT:1";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:8:"is_image";a:4:{i:0;s:6:"TINT:1";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:5:"added";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:4:"KEYS";a:1:{s:9:"extension";a:2:{i:0;s:5:"INDEX";i:1;s:9:"extension";}}}}]]></tables>
	<install><![CDATA[
		$aExtensions = array(
			array(
				'extension' => 'jpg',
				'mime_type' => 'image/jpeg',
				'is_active' => '1',
				'is_image' => '1',
				'added' => '1208637306'
			),
			array(
				'extension' => 'jpeg',
				'mime_type' => 'image/jpeg',
				'is_active' => '1',
				'is_image' => '1',
				'added' => '1208637306'
			),
			array(
				'extension' => 'gif',
				'mime_type' => 'image/gif',
				'is_active' => '1',
				'is_image' => '1',
				'added' => '1208637335'
			),
			array(
				'extension' => 'png',
				'mime_type' => 'image/png',
				'is_active' => '1',
				'is_image' => '1',
				'added' => '1212577320'
			),			
			array(
				'extension' => 'zip',
				'mime_type' => 'image/zip',
				'is_active' => '1',
				'is_image' => '1',
				'added' => '1212577320'
			)			
		);
		foreach ($aExtensions as $aExtension)
		{
			$this->database()->insert(Phpfox::getT('attachment_type'), array(
					'extension' => $aExtension['extension'],
					'mime_type' => $aExtension['mime_type'],
					'is_active' => $aExtension['is_active'],
					'is_image' => $aExtension['is_image'],
					'added' => $aExtension['added']
				)
			);
		}
	]]></install>
</module>