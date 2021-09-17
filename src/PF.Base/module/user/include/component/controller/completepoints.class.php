<?php
defined('PHPFOX') or exit('NO DICE!');
define('PHPFOX_SKIP_POST_PROTECTION', true);

/**
 * Class User_Component_Controller_Completepoints
 */
class User_Component_Controller_Completepoints extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$this->url()->send('', array(), _p('thank_you_for_your_purchase'));
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('user.component_controller_completepoints_clean')) ? eval($sPlugin) : false);
	}
}
