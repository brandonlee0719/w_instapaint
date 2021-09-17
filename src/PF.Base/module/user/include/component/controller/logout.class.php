<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Controller_Logout
 */
class User_Component_Controller_Logout extends Phpfox_Component 
{
	/**
	 * Process the controller
	 *
	 */
	public function process()
	{
		if ($this->request()->get('req3') != 'done')
		{
			Phpfox::getService('user.auth')->logout();
			
			$this->url()->send('');	
		}
		
		$this->template()->setTitle(_p('logout'));
	}
}
