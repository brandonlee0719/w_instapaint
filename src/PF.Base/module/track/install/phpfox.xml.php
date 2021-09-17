<module>
	<data>
		<module_id>track</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>0</is_menu>
		<menu />
		<phrase_var_name>module_track</phrase_var_name>
		<writable />
	</data>
	<settings>
		<setting group="" module_id="track" is_hidden="1" type="integer" var_name="cache_recently_viewed_by_timeout" phrase_var_name="setting_cache_recently_viewed_by_timeout" ordering="1" version_id="3.6.0rc1">0</setting>
		<setting group="" module_id="track" is_hidden="1" type="integer" var_name="cache_allow_recurrent_visit" phrase_var_name="setting_cache_allow_recurrent_visit" ordering="2" version_id="3.6.0rc1">0</setting>
		<setting group="" module_id="track" is_hidden="0" type="boolean" var_name="unique_viewers_counter" phrase_var_name="setting_unique_viewers_counter" ordering="2" version_id="4.5.0">1</setting>
	</settings>
	<hooks>
		<hook module_id="track" hook_type="component" module="track" call_name="track.component_block_recent_views_process" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="track" hook_type="component" module="track" call_name="track.component_block_recent_views_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="track" hook_type="service" module="track" call_name="track.service_track___construct" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="track" hook_type="service" module="track" call_name="track.service_track___call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="track" hook_type="service" module="track" call_name="track.service_process__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="track" hook_type="service" module="track" call_name="track.service_callback__call" added="1244973584" version_id="2.0.0beta4" />
	</hooks>
	<components>
		<component module_id="track" component="recent-views" m_connection="" module="track" is_controller="0" is_block="1" is_active="1" />
	</components>
    <tables><![CDATA[a:1:{s:12:"phpfox_track";a:3:{s:7:"COLUMNS";a:6:{s:8:"track_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:7:"type_id";a:4:{i:0;s:8:"VCHAR:75";i:1;s:0:"";i:2;s:0:"";i:3;s:2:"NO";}s:7:"item_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:7:"user_id";a:4:{i:0;s:7:"UINT:10";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:10:"ip_address";a:4:{i:0;s:8:"VCHAR:15";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:8:"track_id";s:4:"KEYS";a:4:{s:7:"type_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"type_id";i:1;s:7:"item_id";}}s:9:"type_id_2";a:2:{i:0;s:5:"INDEX";i:1;a:3:{i:0;s:7:"type_id";i:1;s:7:"item_id";i:2;s:7:"user_id";}}s:9:"type_id_3";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:7:"type_id";i:1;s:7:"user_id";}}s:7:"item_id";a:2:{i:0;s:5:"INDEX";i:1;a:1:{i:0;s:7:"item_id";}}}}}]]></tables>
</module>