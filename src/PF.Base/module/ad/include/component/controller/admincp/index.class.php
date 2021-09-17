<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Ad_Component_Controller_Admincp_Index
 */
class Ad_Component_Controller_Admincp_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
	    (($sPlugin = Phpfox_Plugin::get('ad.component_controller_admincp_process__start')) ? eval($sPlugin) : false);
	    
		$iPage = $this->request()->getInt('page');

        if (($iId = $this->request()->getInt('approve'))) {
            if (Phpfox::getService('ad.process')->approve($iId)) {
                $this->url()->send('admincp.ad', null, _p('ad_successfully_approved'));
            }
        }

        if (($iId = $this->request()->getInt('deny'))) {
            if (Phpfox::getService('ad.process')->deny($iId)) {
                $this->url()->send('admincp.ad', null, _p('ad_successfully_denied'));
            }
        }

        if (($iId = $this->request()->getInt('delete'))) {
            if (Phpfox::getService('ad.process')->delete($iId)) {
                $this->url()->send('admincp.ad', null, _p('ad_successfully_deleted'));
            }
        }

        if (($aVals = $this->request()->getArray('val'))) {
            if (Phpfox::getService('ad.process')->updateActivity($aVals)) {
                $this->url()->send('admincp.ad', null, _p('ad_s_successfully_updated'));
            }
        }

        $aPages = array(5, 10, 15, 20);
        $aDisplays = array();
        foreach ($aPages as $iPageCnt) {
            $aDisplays[$iPageCnt] = _p('per_page', array('total' => $iPageCnt));
        }

		$aSorts = array(
			'ad_id' => _p('recently_added')
		);

        $aFilters = array(
            'status' => array(
                'type' => 'select',
                'options' => array(
                    '1' => _p('pending_approval'),
                    '2' => _p('pending_payment'),
                    '4' => _p('denied')
                ),
                'add_any' => true
            ),
            'display' => array(
                'type' => 'select',
                'options' => $aDisplays,
                'default' => '10'
            ),
            'sort' => array(
                'type' => 'select',
                'options' => $aSorts,
                'default' => 'ad_id'
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
                'type' => 'campaigns',
                'filters' => $aFilters,
                'search' => 'search'
            )
        );
		
		$sStatus = $oSearch->get('status');
		$sView = $this->request()->get('view');
		$iLocation = $this->request()->getInt('location');

        if ($sStatus == '1') {
            $oSearch->setCondition('is_custom = 2');
        } elseif ($sStatus == '2') {
            $oSearch->setCondition('is_custom = 1');
        } elseif ($sStatus == '4') {
            $oSearch->setCondition('is_custom = 4');
        } else {
            switch ($sView) {
                case 'pending':
                    $oSearch->setCondition('is_custom = 2');
                    break;
                default:
                    break;
            }
        }

        if ($iLocation > 0) {
            $oSearch->setCondition('AND location = ' . (int)$iLocation);
        }
		
		$iLimit = $oSearch->getDisplay();		 	    
		
		list($iCnt, $aAds) = Phpfox::getService('ad')->get($oSearch->getConditions(), $oSearch->getSort(), $oSearch->getPage(), $iLimit);
		
		Phpfox_Pager::instance()->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $oSearch->getSearchTotal($iCnt)));
		
		$this->template()->setTitle(_p('manage_ad_campaigns'))
			->setBreadCrumb(_p('manage_ad_campaigns'), $this->url()->makeUrl('admincp.ad'))
			->assign(array(
					'aAds' => $aAds,
					'iPendingCount' => (int) Phpfox::getService('ad')->getPendingCount(),
					'sPendingLink' => Phpfox_Url::instance()->makeUrl('admincp.ad', array('view' => 'pending')),
					'bIsSearch' => ($this->request()->get('search-id') ? true : false),
					'sView' => $sView
				)
			);
			
		(($sPlugin = Phpfox_Plugin::get('ad.component_controller_admincp_process__end')) ? eval($sPlugin) : false);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('ad.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
	}
}
