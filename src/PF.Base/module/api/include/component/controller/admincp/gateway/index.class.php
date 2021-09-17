<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Api_Component_Controller_Admincp_Gateway_Index
 */
class Api_Component_Controller_Admincp_Gateway_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$this->template()->setTitle(_p('payment_gateways'))
            ->setBreadCrumb(_p('Globalization'),'#')
            ->setBreadCrumb(_p('payment_gateways'), $this->url()->makeUrl('admincp.api.gateway'))
			->setSectionTitle(_p('payment_gateways'))
            ->setActiveMenu('admincp.settings.payments')
			->assign(array(
					'aGateways' => Phpfox::getService('api.gateway')->getForAdmin()
				)
			);			
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('api.component_controller_admincp_gateway_index_clean')) ? eval($sPlugin) : false);
	}
}
