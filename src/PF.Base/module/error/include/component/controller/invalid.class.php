<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Error_Component_Controller_invalid extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$this->template()->errorClearAll();
		$this->template()->setTitle(_p('Invalid content'));
		$this->template()->setBreadCrumb(_p('Sorry, this content isn\'t available right now'));
		$this->template()->assign('aFilterMenus', array());
        return null;
	}
}