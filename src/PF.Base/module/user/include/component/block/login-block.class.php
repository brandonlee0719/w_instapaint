<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Block_Login_Block
 */
class User_Component_Block_Login_Block extends Phpfox_Component 
{
	/**
	 * Controller
	 */
	public function process()
	{
		//Plugin call
		if ($sPlugin = Phpfox_Plugin::get('user.block_login-block_process__start'))
		{eval($sPlugin);}

		// If we are logged in lets not display the login block
		if (Phpfox::isUser())
		{
			return false;
		}

		$aFooter = array();
		$aFooter[_p('forgot_password')] = $this->url()->makeUrl('user.password.request');
		
		// Assign the needed vars for the template
		$this->template()->assign(array(
				'sHeader' => _p('log'),
				'aFooter' =>  $aFooter,
			)
		);
		//Plugin call
		if ($sPlugin = Phpfox_Plugin::get('user.block_login-block_process__end'))
		{eval($sPlugin);}
		
		return 'block';
	}
}
