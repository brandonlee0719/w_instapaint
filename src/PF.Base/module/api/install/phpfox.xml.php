<module>
	<data>
		<module_id>api</module_id>
		<product_id>phpfox</product_id>
		<is_core>1</is_core>
		<is_active>1</is_active>
		<is_menu>0</is_menu>
		<menu />
		<phrase_var_name>module_api</phrase_var_name>
		<writable />
	</data>
	<hooks>
		<hook module_id="api" hook_type="controller" module="api" call_name="api.component_controller_index_clean" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="api" hook_type="controller" module="api" call_name="api.component_controller_admincp_gateway_index_clean" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="api" hook_type="controller" module="api" call_name="api.component_controller_admincp_gateway_add_clean" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="api" hook_type="controller" module="api" call_name="api.component_controller_gateway_callback_clean" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="api" hook_type="component" module="api" call_name="api.component_block_list_clean" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="api" hook_type="service" module="api" call_name="api.service_gateway_gateway__call" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="api" hook_type="service" module="api" call_name="api.service_gateway_process__call" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="api" hook_type="service" module="api" call_name="api.service_process__call" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="api" hook_type="service" module="api" call_name="api.service_api__call" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="api" hook_type="service" module="api" call_name="api.service_callback__call" added="1244973584" version_id="2.0.0beta4" />
		<hook module_id="api" hook_type="template" module="api" call_name="api.template_block_gateway_form_start" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="api" hook_type="template" module="api" call_name="api.template_block_gateway_form_end" added="1274286148" version_id="2.0.5dev1" />
		<hook module_id="api" hook_type="controller" module="api" call_name="api.component_controller_method_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="api" hook_type="controller" module="api" call_name="api.component_controller_token_clean" added="1319729453" version_id="3.0.0rc1" />
		<hook module_id="api" hook_type="service" module="api" call_name="api.service_api_sendresponse_1" added="1358258443" version_id="3.5.0beta1" />
		<hook module_id="api" hook_type="service" module="api" call_name="api.service_api_createtoken_1" added="1361180401" version_id="3.5.0rc1" />
		<hook module_id="api" hook_type="service" module="api" call_name="api.service_gateway_gateway_getactive_1" added="1362126685" version_id="3.5.0" />
	</hooks>
	<tables><![CDATA[a:2:{s:18:"phpfox_api_gateway";a:2:{s:7:"COLUMNS";a:6:{s:10:"gateway_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:5:"title";a:4:{i:0;s:9:"VCHAR:150";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:11:"description";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:9:"is_active";a:4:{i:0;s:6:"TINT:1";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"is_test";a:4:{i:0;s:6:"TINT:3";i:1;s:1:"0";i:2;s:0:"";i:3;s:2:"NO";}s:7:"setting";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}}s:4:"KEYS";a:2:{s:9:"is_active";a:2:{i:0;s:5:"INDEX";i:1;s:9:"is_active";}s:10:"gateway_id";a:2:{i:0;s:5:"INDEX";i:1;a:2:{i:0;s:10:"gateway_id";i:1;s:9:"is_active";}}}}s:22:"phpfox_api_gateway_log";a:2:{s:7:"COLUMNS";a:5:{s:6:"log_id";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:14:"auto_increment";i:3;s:2:"NO";}s:10:"gateway_id";a:4:{i:0;s:8:"VCHAR:75";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:8:"log_data";a:4:{i:0;s:5:"MTEXT";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}s:10:"ip_address";a:4:{i:0;s:8:"VCHAR:15";i:1;N;i:2;s:0:"";i:3;s:3:"YES";}s:10:"time_stamp";a:4:{i:0;s:7:"UINT:10";i:1;N;i:2;s:0:"";i:3;s:2:"NO";}}s:11:"PRIMARY_KEY";s:6:"log_id";}}]]></tables>
	<install><![CDATA[		
	$aGateways = array(
		array(
			'gateway_id' => 'paypal',
			'title' => 'PayPal',
			'description' => 'Some information about PayPal...',
			'is_active' => '0',
			'is_test' => '0',
			'setting' => serialize(array(
					'paypal_email' => ''
				)
			)
		)
	);	
	foreach ($aGateways as $aGateways)
	{
		$this->database()->insert(Phpfox::getT('api_gateway'), $aGateways);	
	}	
	]]></install>
</module>