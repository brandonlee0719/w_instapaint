<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Api_Component_Block_Gateway_Form
 */
class Api_Component_Block_Gateway_Form extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$aGatewayData = $this->getParam('gateway_data');
		
		$bIsThickBox = $this->getParam('bIsThickBox');

		$this->template()->assign(array(
				'aGateways' => Phpfox::getService('api.gateway')->get($aGatewayData),
				'aGatewayData' => $aGatewayData,
				'bIsThickBox' => $bIsThickBox
			)
		);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('api.component_block_list_clean')) ? eval($sPlugin) : false);
	}
}
