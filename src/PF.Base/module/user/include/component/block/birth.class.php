<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Block_Birth
 */
class User_Component_Block_Birth extends Phpfox_Component 
{
	/**
	 * Controller
	 */
	public function process()
	{
		$aUser = (PHPFOX_IS_AJAX ? Phpfox::getService('user')->get($this->request()->getInt('profile_user_id'), true) : $this->getParam('aUser'));
		$aBirthDay = Phpfox::getService('user')->getAgeArray((PHPFOX_IS_AJAX ? $aUser['birthday'] : $aUser['birthday_time_stamp']));
		if (empty($aUser['birthday']))
		{
			return false;
		}
		$this->template()->assign(array(
				'aUser' => $aUser,
				'sBirthDisplay' => Phpfox::getTime(Phpfox::getParam('user.user_dob_month_day'), mktime(0, 0, 0, $aBirthDay['month'], $aBirthDay['day'], $aBirthDay['year']), false)
			)
		);
        return null;
	}
}
