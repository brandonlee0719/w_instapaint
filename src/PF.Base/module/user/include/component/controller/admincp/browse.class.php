<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Controller_Admincp_Browse
 */
class User_Component_Controller_Admincp_Browse extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		define('PHPFOX_IS_ADMIN_SEARCH', true);

		$this->template()
            ->setBreadCrumb(_p('Members'),'#')
            ->setSectionTitle(_p('browse_members'))
            ->setActiveMenu('admincp.member.browse');
		
		return Phpfox_Module::instance()->setController('user.browse');
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('user.component_controller_admincp_browse_clean')) ? eval($sPlugin) : false);
	}
}
