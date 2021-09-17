<?php
defined('PHPFOX') or exit('NO DICE!');

class Admincp_Component_Controller_Product_Install extends Phpfox_Component {
    
	public function process() {
		$this->template()
            ->setActiveMenu('admincp.techie.product')
            ->setTitle(_p('installing'));
	}
}