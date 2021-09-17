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
 * @package  		Module_Admincp
 * @version 		$Id: cache.class.php 6584 2013-09-05 09:59:17Z Miguel_Espinoza $
 */
class Admincp_Component_Controller_Maintain_Cache extends Phpfox_Component 
{
	/**
	 * Controller
	 */
	public function process()
	{		
		Phpfox::getUserParam('admincp.can_clear_site_cache', true);
		
		if ($this->request()->get('unlock'))
		{
			Phpfox::getLib('cache')->unlock();
			
			$this->url()->send('admincp.maintain.cache', null, _p('cache_system_unlocked'));
		}
		
		if ($this->request()->get('all'))
		{
			Phpfox_Database::instance()->update(Phpfox::getT('setting'), array('value_actual' => ((int) Phpfox::getParam('core.css_edit_id') + 1)), 'var_name = \'css_edit_id\'');

			Phpfox::getLib('cache')->remove();
			Phpfox::getLib('template.cache')->remove();
			Phpfox::getLib('cache')->removeStatic();

            $sLicenseError = '';

			if ($sPlugin = Phpfox_Plugin::get('admincp.component_controller_maintain_1'))
			{
				eval($sPlugin);
			}
			if (PHPFOX_IS_AJAX_PAGE){
                return [
                    'content' => _p('cached_cleared') . $sLicenseError
                ];
            } else {
                Phpfox::addMessage(_p('cached_cleared') . $sLicenseError);
//                $this->url()->send('admincp.maintain.cache',['clear'=>'1']);
            }

		}
		
		if ($aIds = $this->request()->getArray('id'))
		{			
			foreach ($aIds as $sKey => $aItems)
			{				
				foreach ($aItems as $sId)
				{					
					Phpfox::getLib('cache')->remove($sId, 'path');	
				}						
			}			

			$this->url()->send('admincp', array('maintain', 'cache'), _p('cached_cleared'));
		}
		
		$iPage = $this->request()->getInt('page');
		
		$aPages = array(20, 30, 40, 50);
		$aDisplays = array();
		foreach ($aPages as $iPageCnt)
		{
			$aDisplays[$iPageCnt] = _p('per_page', array('total' => $iPageCnt));
		}		
		
		$aSorts = array(
			'time_stamp' => _p('timestamp'),
			'file_name' => _p('cache_name'),
			'data_size' => _p('data_size')
		);
		
		$aFilters = array(
			'search' => array(
				'type' => 'input:text',
				'search' => "AND file_name LIKE '%[VALUE]%'"
			),				
			'display' => array(
				'type' => 'select',
				'options' => $aDisplays,
				'default' => '20'
			),
			'sort' => array(
				'type' => 'select',
				'options' => $aSorts,
				'default' => 'time_stamp'				
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
				'type' => 'cache',
				'filters' => $aFilters,
				'search' => 'search'
			)
		);
		
		$iLimit = $oSearch->getDisplay();
		list($iCnt, $aCaches) = Phpfox_Cache::instance()->getCachedFiles($oSearch->getConditions(), $oSearch->getSort(), $oSearch->getPage(), $iLimit);

		Phpfox_Pager::instance()->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $oSearch->getSearchTotal($iCnt)));

		if ($this->request()->get('clear'))
		{
			$aCaches = array();
			$iCnt = 0;	
		}

		$this->template()
            ->setActiveMenu('admincp.maintain.cache')
			->setTitle(_p('cache_manager'))
			->setBreadCrumb(_p('cache_manager'))
			->setSectionTitle(_p('cache_manager'))
            ->setActionMenu([
                _p('clear_cache') => [
                    'url' => $this->url()->makeUrl('admincp.maintain.cache', ['all' => true]),
                    'class' => 'btn-danger',
                    'custom' => 'data-caption="' . _p('clear_cache') . '"'
                ]
            ])
			->assign(array(
			    'bShowClearCache'=>true,
				'iCacheCnt' => $iCnt,
				'aCaches' => $aCaches,
				'aStats' => Phpfox::getLib('cache')->getStats(),
				'bCacheLocked' => (file_exists(PHPFOX_DIR_CACHE . 'cache.lock') ? true : false),
				'sUnlockCache' => $this->url()->makeUrl('admincp.maintain.cache', array('unlock' => 'true'))
			)
		);
        return null;
	}
}