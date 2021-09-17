<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Block_Signup_Error
 */
class User_Component_Block_Signup_Error extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{		
		$this->template()->assign(array(
				'aNames' => $this->getParam('aNames')
			)
		);
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('user.component_block_signup_error_clean')) ? eval($sPlugin) : false);
	}
}
