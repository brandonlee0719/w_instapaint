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
 * @version 		$Id: list.class.php 5382 2013-02-18 09:48:39Z Miguel_Espinoza $
 */
class Subscribe_Component_Controller_List extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		Phpfox::isUser(true);
		$aPurchases = Phpfox::getService('subscribe.purchase')->get(Phpfox::getUserId());
        if ( ($sPlugin = Phpfox_Plugin::get('subscribe.component_controller_list__1')) ){eval($sPlugin); if (isset($mReturnPlugin)){return $mReturnPlugin;}}
        
		$this->template()->setTitle(_p('membership_packages'))
			->setBreadCrumb(_p('membership_packages'), $this->url()->makeUrl('subscribe'))
			->setBreadCrumb(_p('subscriptions'), $this->url()->makeUrl('subscribe.list'), true)
			->assign(array(
					'aPurchases' => $aPurchases
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
		(($sPlugin = Phpfox_Plugin::get('subscribe.component_controller_list_clean')) ? eval($sPlugin) : false);
	}
}