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
 * @version 		$Id: online-guest.class.php 4694 2012-09-20 08:50:15Z Miguel_Espinoza $
 */
class Core_Component_Controller_Admincp_Online_Guest extends Phpfox_Component
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
				'search' => "AND ls.name LIKE '%[VALUE]%'"
			),						
			'display' => array(
				'type' => 'select',
				'options' => $aDisplays,
				'default' => '20'
			),
			'sort' => array(
				'type' => 'select',
				'options' => array(
					'last_activity' => _p('last_activity'),
					'ip_address ' => _p('ip_address')
				),
				'default' => 'last_activity',
				'alias' => 'ls'
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
		
		$oSearch->setCondition('AND ls.user_id = 0');
		// The following condition was added to match the function log.session->getOnlineStats
		$oSearch->setCondition('AND ls.last_activity > '  . (PHPFOX_TIME - (Phpfox::getParam('log.active_session')*60)));

		list($iCnt, $aGuests) = Phpfox::getService('log')->getOnlineGuests($oSearch->getConditions(), $oSearch->getSort(), $oSearch->getPage(), $oSearch->getDisplay());
		
		Phpfox_Pager::instance()->set(array('page' => $iPage, 'size' => $oSearch->getDisplay(), 'count' => $oSearch->getSearchTotal($iCnt)));
		
		$this->template()->setTitle(_p('online_guests'))
			->setBreadCrumb(_p('online'), $this->url()->makeUrl('admincp.core.online-guest'))
			->setBreadCrumb(_p('guests_bots'), null, true)
			->assign(array(
					'aGuests' => $aGuests
				)
			);	
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('core.component_controller_admincp_online_guest_clean')) ? eval($sPlugin) : false);
	}
}