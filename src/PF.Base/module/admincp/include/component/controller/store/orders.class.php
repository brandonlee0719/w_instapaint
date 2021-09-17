<?php

defined('PHPFOX') or exit('NO DICE!');
define('PHPFOX_APP_INSTALLING', true);

class Admincp_Component_Controller_Store_Orders extends Phpfox_Component {
    
	public function process() {
		$Home = new Core\Home(PHPFOX_LICENSE_ID, PHPFOX_LICENSE_KEY);
		$response = $Home->admincp(['return' => $this->url()->makeUrl('admincp.app.add')]);
		if (!isset($response->token)) {
			exit($response->error);
		}

		$token = $response->token;
		$this->template()->setTitle(_p('Store Purchases'))
			->assign([
				'token' => $token,
				'storeUrl' => Core\Home::store(),
				'_k' => PHPFOX_LICENSE_ID,
				'_p' => PHPFOX_LICENSE_KEY
			]);
	}
}