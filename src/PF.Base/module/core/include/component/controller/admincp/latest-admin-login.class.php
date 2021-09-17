<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox_Component
 * @version 		$Id: latest-admin-login.class.php 6189 2013-06-29 08:45:09Z Raymond_Benc $
 */
class Core_Component_Controller_Admincp_Latest_Admin_Login extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$iPage = $this->request()->getInt('page');
		
		$aPages = array(20, 30, 40, 50);
		$aDisplays = array();
		foreach ($aPages as $iPageCnt)
		{
			$aDisplays[$iPageCnt] = _p('per_page', array('total' => $iPageCnt));
		}
				
		$aFilters = array(
			'search' => array(
				'type' => 'input:text',
				'search' => "ANDal.name LIKE '%[VALUE]%'"
			),						
			'display' => array(
				'type' => 'select',
				'options' => $aDisplays,
				'default' => '20'
			),
			'sort' => array(
				'type' => 'select',
				'options' => array(
					'time_stamp' => _p('login_time_stamp'),
					'ip_address ' => _p('ip_address')
				),
				'default' => 'time_stamp',
				'alias' => 'al'
			),
			'sort_by' => array(
				'type' => 'select',
				'options' => array(
					'DESC' => _p('descending'),
					'ASC' => _p('ascending')
				),
				'default' => 'DESC'
			)
		);		
		
		$oSearch = Phpfox_Search::instance()->set(array(
				'type' => 'onlineguests',
				'filters' => $aFilters,
				'search' => 'search'
			)
		);		

		list($iCnt, $aUsers) = Phpfox::getService('core.admincp')->getAdminLogins($oSearch->getConditions(), $oSearch->getSort(), $oSearch->getPage(), $oSearch->getDisplay());
		
		Phpfox_Pager::instance()->set(array('page' => $iPage, 'size' => $oSearch->getDisplay(), 'count' => $oSearch->getSearchTotal($iCnt)));
		
		$this->template()->setTitle(_p('admincp_logins'))
			->setBreadCrumb(_p('admincp_logins'))
			->assign(array(
					'aUsers' => $aUsers
				)
			);			
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('core.component_controller_admincp_latest_admin_login_clean')) ? eval($sPlugin) : false);
	}
}