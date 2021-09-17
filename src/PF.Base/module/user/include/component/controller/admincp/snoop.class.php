<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Controller_Admincp_Snoop
 */
class User_Component_Controller_Admincp_Snoop extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		Phpfox::isUser(true);
		Phpfox::getUserParam('user.can_member_snoop', true);

		$aUser = Phpfox::getService('user')->getUser($this->request()->getInt('user', 0));
		if (empty($aUser))
		{
			$this->url()->send('admincp',array(), _p('that_user_does_not_exist'));
		}
		if (($sVal = $this->request()->get('action')) == 'proceed' && Phpfox::getService('user.auth')->snoop($aUser))
		{
			$this->url()->send('');
		}
		$this->template()
				->setHeader(array(
					'snoop.css' => 'module_user'
				))
            ->setActiveMenu('admincp.member.browse')
				->setBreadCrumb(_p('member_snoop'), null, true)
				->assign(array(
					'aUser' => $aUser,
					'user_name' => $aUser['user_name'],
					'full_name' => $aUser['full_name'],
					'user_link' => $this->url()->makeUrl($aUser['user_name'])

				));
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('user.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}
