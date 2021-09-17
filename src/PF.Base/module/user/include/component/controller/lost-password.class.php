<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Controller_Lost_Password
 */
class User_Component_Controller_Lost_Password extends Phpfox_Component 
{
	/**
	 * Process the controller
	 *
	 */
	public function process()
	{
		$this->template()->setTitle(_p('lost_password'))
			->setBreadCrumb(_p('lost_password'));
	}
}
