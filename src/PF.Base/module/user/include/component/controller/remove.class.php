<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Controller_Remove
 */
class User_Component_Controller_Remove extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		Phpfox::isUser(true);
		if (!Phpfox::getUserParam('user.can_delete_own_account'))
		{
			Phpfox_Url::instance()->send('');
		}
		if (Phpfox::isModule('friend'))
		{
			list($iCnt, $aShowFriends) = Phpfox::getService('friend')->get('friend.is_page = 0 AND friend.user_id = ' . Phpfox::getUserId() . ' AND ' . Phpfox_Database::instance()->isNotNull('user_image'), 'RAND()', 0, 3);
		
			$this->template()->assign(array(
				'aFriends' => $aShowFriends,
				'aReasons' => Phpfox::getService('user')->getReasons()
				)
			);
		} // is not confirming
		if ($this->request()->get('req3') == 'confirm')
		{			
			if (($aVals = $this->request()->getArray('val')))
			{				
				// user inputted password, no turning back now...
				if (Phpfox::getService('user.cancellations.process')->cancelAccount($aVals))
				{
					// redirect is in the cancel account because of the logout
				}
			}
		}
		$this->template()->setTitle(_p('cancel_account'))
			->setBreadCrumb(_p('cancel_account'));
		
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('user.component_controller_register_clean')) ? eval($sPlugin) : false);
	}
}
