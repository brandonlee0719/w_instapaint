<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Block_New
 */
class User_Component_Block_New extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$this->template()->assign(array(
				'aUsers' => Phpfox::getService('user')->getNew()
			)
		);	
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('user.component_block_new_clean')) ? eval($sPlugin) : false);
	}
}
