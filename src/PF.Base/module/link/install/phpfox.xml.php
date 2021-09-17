<module>
	<data>
		<module_id>link</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>0</is_menu>
		<menu />
		<phrase_var_name>module_link</phrase_var_name>
		<writable />
	</data>
	<hooks>
		<hook module_id="link" hook_type="component" module="link" call_name="link.component_ajax_addviastatusupdate" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="link" hook_type="component" module="link" call_name="link.component_block_attach_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="link" hook_type="component" module="link" call_name="link.component_block_display_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="link" hook_type="component" module="link" call_name="link.component_block_preview_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="link" hook_type="controller" module="link" call_name="link.component_controller_index_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="link" hook_type="service" module="link" call_name="link.service_callback__call" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="link" hook_type="service" module="link" call_name="link.service_link__call" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="link" hook_type="service" module="link" call_name="link.service_process__call" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="link" hook_type="service" module="link" call_name="link.component_service_callback_getactivityfeed__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="link" hook_type="service" module="link" call_name="link.service_callback_checkfeedsharelink" added="1358258443" version_id="3.5.0beta1" />
	</hooks>
    <settings>
        <setting group="" module_id="link" is_hidden="0" type="string" var_name="youtube_data_api_key" phrase_var_name="setting_youtube_data_api_key" ordering="96" version_id="4.6.0">
        </setting>
    </settings>
	<tables><![CDATA[a:2:{s:11:"phpfox_link";a:3:{s:7:"COLUMNS";a:20:{s:7:"link_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"module_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:7:"item_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:14:"parent_user_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:9:"is_custom";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:4:"link";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:5:"image";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:5:"title";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:11:"description";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:11:"status_info";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:7:"privacy";a:4:{i:0;s:6:"TINT:1";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:15:"privacy_comment";a:4:{i:0;s:6:"TINT:1";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:9:"has_embed";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"total_comment";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"total_like";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:13:"total_dislike";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:15:"location_latlng";a:4:{i:0;s:9:"VCHAR:100";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:13:"location_name";a:4:{i:0;s:9:"VCHAR:255";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:11:"PRIMARY_KEY";s:7:"link_id";s:4:"KEYS";a:1:{s:14:"parent_user_id";a:2:{i:0;s:5:"INDEX";i:1;s:14:"parent_user_id";}}}s:17:"phpfox_link_embed";a:2:{s:7:"COLUMNS";a:2:{s:7:"link_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"embed_code";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:4:"KEYS";a:1:{s:7:"link_id";a:2:{i:0;s:6:"UNIQUE";i:1;s:7:"link_id";}}}}]]></tables>
</module>