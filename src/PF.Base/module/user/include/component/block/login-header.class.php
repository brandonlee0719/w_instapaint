<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Block_Login_Header
 */
class User_Component_Block_Login_Header extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		if ($sPlugin = Phpfox_Plugin::get('user.component_block_login_header')){eval($sPlugin);if (isset($mReturnFromPlugin)){return $mReturnFromPlugin;}}
		return null;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('user.component_block_login_header_clean')) ? eval($sPlugin) : false);
	}
}
