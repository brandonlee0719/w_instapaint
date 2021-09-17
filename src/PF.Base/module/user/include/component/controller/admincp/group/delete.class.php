<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Controller_Admincp_Group_Delete
 */
class User_Component_Controller_Admincp_Group_Delete extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		Phpfox::getUserParam('user.can_delete_user_group', true);
		
		if ($aVals = $this->request()->getArray('val'))
		{
			if (Phpfox::getService('user.group.process')->delete($aVals))
			{
				$this->url()->send('admincp.user.group', null, _p('successfully_deleted_user_group'));
			}
		}
		
		$aGroup = Phpfox::getService('user.group')->getGroup($this->request()->getInt('id'));
		
		if (!isset($aGroup['user_group_id']))
		{
			return Phpfox_Error::display(_p('unable_to_find_the_user_group_you_want_to_delete'));
		}
		
		if ($aGroup['is_special'])
		{
			return Phpfox_Error::display(_p('not_allowed_to_delete_this_user_group'));
		}
		
		$this->template()
			->setTitle(_p('delete_user_group'))
			->setBreadCrumb(_p('delete_user_group'))
            ->setActiveMenu('admincp.member.group')
			->setBreadCrumb($aGroup['title'], null, true)
			->assign(array(
					'aGroup' => $aGroup,
					'aGroups' => Phpfox::getService('user.group')->get()
				)
			);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('user.component_controller_admincp_group_delete_clean')) ? eval($sPlugin) : false);
	}
}
