<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Api_Component_Ajax_Ajax
 */
class Api_Component_Ajax_Ajax extends Phpfox_Ajax
{
	public function processActivityPayment()
	{
		$aParts = explode('|', $this->get('item_number'));

		if ($aReturn = Phpfox::getService('user.process')->purchaseWithPoints($aParts[0], $aParts[1], $this->get('amount'), $this->get('currency_code')))
		{

		    $sMessage = _p('purchase_successfully_completed_dot');
		    $sUrl = Phpfox_Url::instance()->makeUrl('');
		    if (is_array($aReturn)) {
		        $sStatus = $aReturn['status'];
		        if (!empty($sStatus)) {
                    $sMessage = empty($aReturn['message_' . $sStatus]) ? $sMessage : $aReturn['message_' . $sStatus];
                    $sUrl = empty($aReturn['redirect_' . $sStatus]) ? $sUrl : Phpfox_Url::instance()->makeUrl($aReturn['redirect_' . $sStatus]);
                }
            }

			Phpfox::addMessage($sMessage);
			
			$this->call('window.location.href = \'' . $sUrl . '\'');
		}
		else {
			$this->alert(_p('error_purchase_can_not_complete'));
		}
	}
	
	public function updateGatewayActivity()
	{
		Phpfox::getService('api.gateway.process')->updateActivity($this->get('gateway_id'), $this->get('active'));
	}
	
	public function updateGatewayTest()
	{
		if (Phpfox::getService('api.gateway.process')->updateTest($this->get('gateway_id'), $this->get('active')))
		{
			
		}			
	}
}
