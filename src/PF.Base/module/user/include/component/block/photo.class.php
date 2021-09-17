<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Block_Photo
 */
class User_Component_Block_Photo extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$this->template()->assign(array(
				'iUserId' => $this->request()->get('user_id')
			)
		);	
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('user.component_block_photo_clean')) ? eval($sPlugin) : false);
	}
}
