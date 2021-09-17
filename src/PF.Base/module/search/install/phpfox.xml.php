<module>
	<data>
		<module_id>search</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>0</is_menu>
		<menu />
		<phrase_var_name>module_search</phrase_var_name>
		<writable />
	</data>
	<hooks>
		<hook module_id="search" hook_type="controller" module="search" call_name="search.component_controller_index_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="search" hook_type="service" module="search" call_name="search.service_search__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="search" hook_type="service" module="search" call_name="search.service_process__call" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="search" hook_type="service" module="search" call_name="search.service_callback__call" added="1240687633" version_id="2.0.0beta1" />
	</hooks>
	<user_group_settings>
		<setting is_admin_setting="0" module_id="search" type="boolean" admin="1" user="1" guest="1" staff="1" module="search" ordering="0">can_use_global_search</setting>
	</user_group_settings>
</module>