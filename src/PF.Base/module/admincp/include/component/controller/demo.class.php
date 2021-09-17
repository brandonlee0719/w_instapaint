<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Admincp_Component_Controller_Demo extends Phpfox_Component {
	/**
	 * Controller
	 */
	public function process() {

        $this->template()->setTitle('AdminCP Demo Mode');
	}
}