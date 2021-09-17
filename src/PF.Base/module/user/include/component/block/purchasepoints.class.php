<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Block_Purchasepoints
 */
class User_Component_Block_Purchasepoints extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$aPurchasePoints = array(10, 20, 30, 40, 50);
		
		$aConversion = Phpfox::getParam('user.points_conversion_rate');
		$sDefault = Phpfox::getService('core.currency')->getDefault();
		$iDefaultPrice = (isset($aConversion[$sDefault]) ? $aConversion[$sDefault] : 0);	

		foreach ($aPurchasePoints as $iKey => $sPurchasePoint)
		{
			$iPayTotal = ($sPurchasePoint * $iDefaultPrice);

			$aPurchasePoints[$iKey] = array(
				'id' => (int) $sPurchasePoint . '|' . $iPayTotal,
				'cost' => $sPurchasePoint . ' (' . Phpfox::getService('core.currency')->getCurrency($iPayTotal) . ')'
			);
		}

		$this->template()->assign(array(
				'aPurchasePoints' => $aPurchasePoints	
			)
		);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('user.component_block_purchasepoints_clean')) ? eval($sPlugin) : false);
	}
}
