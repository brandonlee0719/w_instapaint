<module>
	<data>
		<module_id>like</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>0</is_menu>
		<menu />
		<phrase_var_name>module_like</phrase_var_name>
		<writable />
	</data>
	<settings>
		<setting group="" module_id="like" is_hidden="0" type="boolean" var_name="show_user_photos" phrase_var_name="setting_show_user_photos" ordering="1" version_id="3.3.0beta2">0</setting>
	</settings>
	<hooks>
		<hook module_id="like" hook_type="component" module="like" call_name="like.component_block_browse_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="like" hook_type="component" module="like" call_name="like.component_block_link_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="like" hook_type="service" module="like" call_name="like.service_callback__call" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="like" hook_type="service" module="like" call_name="like.service_like__call" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="like" hook_type="service" module="like" call_name="like.service_process__call" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="like" hook_type="service" module="like" call_name="like.service_process_add__1" added="1335951260" version_id="3.2.0" />
		<hook module_id="like" hook_type="service" module="like" call_name="like.service_process_delete__1" added="1335951260" version_id="3.2.0" />
	</hooks>
	<tables><![CDATA[a:3:{s:11:"phpfox_like";a:3:{s:7:"COLUMNS";a:6:{s:7:"like_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"type_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"item_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"feed_table";a:4:{i:0;s:9:"VCHAR:255";i:1;s:4:"feed";i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:7:"like_id";s:4:"KEYS";a:4:{s:7:"type_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"type_id";i:1;s:7:"item_id";}}s:9:"type_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:7:"type_id";i:1;s:7:"item_id";i:2;s:7:"user_id";}}s:9:"type_id_3";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"type_id";i:1;s:7:"user_id";}}s:7:"item_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"item_id";}}}s:17:"phpfox_like_cache";a:2:{s:7:"COLUMNS";a:3:{s:7:"type_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"item_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:4:"KEYS";a:1:{s:9:"type_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:7:"type_id";i:1;s:7:"item_id";i:2;s:7:"user_id";}}}}s:13:"phpfox_action";a:3:{s:7:"COLUMNS";a:6:{s:9:"action_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:14:"action_type_id";a:4:{i:0;s:5:"USINT";i:1;s:1:"2";i:2;s:0:"";i:3;s:2:"NO";}s:12:"item_type_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"item_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:9:"action_id";s:4:"KEYS";a:4:{s:7:"type_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:12:"item_type_id";i:1;s:7:"item_id";}}s:9:"type_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:12:"item_type_id";i:1;s:7:"item_id";i:2;s:7:"user_id";}}s:9:"type_id_3";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:12:"item_type_id";i:1;s:7:"user_id";}}s:7:"item_id";a:2:{i:0;s:5:"INDEX";i:1;s:7:"item_id";}}}}]]></tables>
</module>