<module>
	<data>
		<module_id>error</module_id>
		<product_id>phpfox</product_id>
		<is_core>1</is_core>
		<is_active>1</is_active>
		<is_menu>0</is_menu>
		<menu />
		<phrase_var_name>module_error</phrase_var_name>
		<writable />
	</data>
	<hooks>
		<hook module_id="error" hook_type="controller" module="error" call_name="error.component_controller_notfound_1" added="1361180401" version_id="3.5.0rc1" />
	</hooks>
	<components>
		<component module_id="error" component="display" m_connection="error.display" module="error" is_controller="1" is_block="0" is_active="1" />
		<component module_id="error" component="404" m_connection="error.404" module="error" is_controller="1" is_block="0" is_active="1" />
	</components>
</module>