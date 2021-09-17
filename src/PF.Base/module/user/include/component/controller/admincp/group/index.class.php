<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Controller_Admincp_Group_Index
 */
class User_Component_Controller_Admincp_Group_Index extends Phpfox_Component 
{
	/**
	 * Controller
	 */
	public function process()
	{	
		$this->template()
            ->setBreadCrumb(_p('Members'),'#')
			->setBreadCrumb(_p('manage_user_groups'), null, true)
            ->setActiveMenu('admincp.member.group')
			->setTitle(_p('manage_user_groups'))
			->setSectionTitle(_p('manage_user_groups'))
			->setActionMenu([
                _p('create_user_group') => [
					'class' => 'popup',
					'url' => $this->url()->makeUrl('admincp.user.group.add')
				]
			])
			->assign(array(
			    'bShowClearCache'=>true,
				'aGroups' => Phpfox::getService('user.group')->getForEdit(),
			)
		);
	}
}
