<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Controller_Promotion
 */
class User_Component_Controller_Promotion extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		Phpfox::isUser(true);
		$aUserGroup = Phpfox::getService('user.group')->getGroup(Phpfox::getUserBy('user_group_id'));

		$this->template()
            ->setTitle(_p('promotions'))
			->setBreadCrumb(_p('promotions'))
			->assign(array(
				'aUserGroup' => $aUserGroup
			)
		);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('user.component_controller_promotion_clean')) ? eval($sPlugin) : false);
	}
}
