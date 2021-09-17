<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Block_Register_Top
 */
class User_Component_Block_Register_Top extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$bPass = false;
		if (!Phpfox::isUser() && (Phpfox_Module::instance()->getFullControllerName() != 'user.register' && Phpfox_Module::instance()->getFullControllerName() != 'core.index-visitor'))
		{
			$bPass = true;
		}
		
		if ($bPass === false)
		{
			return false;
		}
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('user.component_block_register_top_clean')) ? eval($sPlugin) : false);
	}
}
