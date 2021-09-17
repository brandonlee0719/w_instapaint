<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Block_Password
 */
class User_Component_Block_Password extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		Phpfox::isUser(true);
		//Check if user just signed up via facebook app
        $aUser = storage()->get('fb_new_users_'.Phpfox::getUserId());
        $bPassOld = false;
        if (!empty($aUser)) {
            $bPassOld = true;
        }
        $this->template()->assign(['bPassOld' => $bPassOld]);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('user.component_block_password_clean')) ? eval($sPlugin) : false);
	}
}
