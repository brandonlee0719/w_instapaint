<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Api_Component_Controller_Admincp_Gateway_Add
 */
class Api_Component_Controller_Admincp_Gateway_Add extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$this->_setMenuName('admincp.api.gateway');
		
		if (!($aGateway = Phpfox::getService('api.gateway')->getForEdit($this->request()->get('id'))))
		{
			return Phpfox_Error::display(_p('unable_to_find_the_payment_gateway'));
		}
		
		if (($aVals = $this->request()->getArray('val')))
		{
			if (Phpfox::getService('api.gateway.process')->update($aGateway['gateway_id'], $aVals))
			{
				$this->url()->send('admincp.api.gateway.add', array('id' => $aGateway['gateway_id']), _p('gateway_successfully_updated'));
			}
		}
		
		$this->template()->setTitle(_p('payment_gateways'))
            ->setBreadCrumb(_p('Globalization'),'#')
			->setBreadCrumb(_p('payment_gateways'), $this->url()->makeUrl('admincp.api.gateway'))
            ->setActiveMenu('admincp.settings.payments')
			->setBreadCrumb(_p('editing') . ': ' . $aGateway['title'], $this->url()->current(), true)
			->assign(array(
					'aForms' => $aGateway
				)
			);
        return null;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('api.component_controller_admincp_gateway_add_clean')) ? eval($sPlugin) : false);
	}
}
