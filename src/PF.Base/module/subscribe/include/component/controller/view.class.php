<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox_Component
 * @version 		$Id: view.class.php 1339 2009-12-19 00:37:55Z Raymond_Benc $
 */
class Subscribe_Component_Controller_View extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		if (!($aPurchase = Phpfox::getService('subscribe.purchase')->getInvoice($this->request()->getInt('id'))))
		{
			return Phpfox_Error::display(_p('unable_to_find_this_invoice'));
		}
		$this->template()->setTitle(_p('membership_packages'))
			->setBreadCrumb(_p('membership_packages'), $this->url()->makeUrl('subscribe'))
			->setBreadCrumb(_p('subscriptions'), $this->url()->makeUrl('subscribe.list'))
			->setBreadCrumb(_p('order_purchase_id_title', array(
					'purchase_id' => $aPurchase['purchase_id'],
					'title' => Phpfox_Locale::instance()->convert($aPurchase['title'])
				)
			), null, true)
			->assign(array(
				'aPurchase' => $aPurchase
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
		(($sPlugin = Phpfox_Plugin::get('subscribe.component_controller_view_clean')) ? eval($sPlugin) : false);
	}
}