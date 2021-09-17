<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Controller_Index
 */
class User_Component_Controller_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{	
		if (Phpfox::getParam('core.force_404_check'))
		{
			return Phpfox_Module::instance()->setController('error.404');
		}
		
		$this->url()->send('user.browse');
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('user.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}
