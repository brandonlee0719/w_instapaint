<module>
	<data>
		<module_id>request</module_id>
		<product_id>phpfox</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>0</is_menu>
		<menu />
		<phrase_var_name>module_request</phrase_var_name>
		<writable />
	</data>
	<hooks>
		<hook module_id="request" hook_type="component" module="request" call_name="request.component_block_feed_process" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="request" hook_type="controller" module="request" call_name="request.component_controller_index_process" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="request" hook_type="controller" module="request" call_name="request.component_controller_index_clean" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="request" hook_type="service" module="request" call_name="request.service_request_getfeed" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="request" hook_type="service" module="request" call_name="request.service_request__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="request" hook_type="service" module="request" call_name="request.service_process__call" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="request" hook_type="template" module="request" call_name="request.template_controller_index" added="1231838390" version_id="2.0.0alpha1" />
		<hook module_id="request" hook_type="controller" module="request" call_name="request.component_controller_view_clean" added="1240687633" version_id="2.0.0beta1" />
		<hook module_id="request" hook_type="service" module="request" call_name="request.service_callback__call" added="1244973584" version_id="2.0.0beta4" />
	</hooks>
	<components>
		<component module_id="request" component="index" m_connection="request.index" module="request" is_controller="1" is_block="0" is_active="1" />
	</components>
</module>