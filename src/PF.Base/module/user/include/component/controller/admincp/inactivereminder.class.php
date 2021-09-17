<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Controller_Admincp_Inactivereminder
 */
class User_Component_Controller_Admincp_Inactivereminder extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
        $iPage = $this->request()->getInt('page',1);
        $iDays = $this->request()->getInt('day',7);
        if (($aIds = $this->request()->getArray('id')) && count((array) $aIds))
        {
            Phpfox::getService('user.process')->addInactiveJob($aIds);
        }
        $iPageSize = 10;
        $aSorts = array(
            'u.full_name' => _p('name'),
            'u.last_login' => _p('last_login'),
            'u.last_activity' => _p('last_activity'),
            'u.user_id' => _p('id'),
            'ug.title' => _p('groups')
        );
        $sDefaultOrderName = 'u.full_name';
        $sDefaultSort = 'ASC';
        if (Phpfox::getParam('user.user_browse_default_result') == 'last_login')
        {
            $sDefaultOrderName = 'u.last_login';
            $sDefaultSort = 'DESC';
        }
        $aSearch = request()->get('search');
        $aSearchSort = isset($aSearch['sort'])?explode(' ',$aSearch['sort']):[];

        $bCustomSort = false;
        if (isset($aSearchSort[1])) {
            $sDefaultSort = $aSearchSort[1];
            $bCustomSort = true;
        }
        $aFilters = array(
            'sort' => array(
                'type' => 'select',
                'options' => $aSorts,
                'default' => $sDefaultOrderName
            ),
            'sort_by' => array(
                'type' => 'select',
                'options' => array(
                    'DESC' => _p('descending'),
                    'ASC' => _p('ascending')
                ),
                'default' => $sDefaultSort
            ),
        );
        $aSearchParams = array(
            'type' => 'browse',
            'filters' => $aFilters,
            'search' => 'day',
            'custom_search' => true
        );
        $oFilter = Phpfox_Search::instance()->set($aSearchParams);
        if ($bCustomSort) {
            $oFilter->setSort($aSearchSort[0]);
        }
        define('PHPFOX_IS_ADMIN_SEARCH',true);
        $oFilter->setCondition('AND u.profile_page_id = 0 AND u.last_activity < ' .(PHPFOX_TIME - ($iDays * 86400)));

        list($iCnt, $aUsers) = Phpfox::getService('user.browse')->conditions($oFilter->getConditions())
            ->sort($oFilter->getSort())
            ->page($iPage)
            ->limit($iPageSize)
            ->extend(true)
            ->get();
        if ($aUsers) {
            $aCachedMailing = storage()->get('user_inactive_mailing_job');
            if (!empty($aCachedMailing) && !empty($aCachedMailing->value)) {
                foreach ($aUsers as $key => $aUser) {
                    if (in_array($aUser['user_id'], $aCachedMailing->value)) {
                        $aUsers[$key]['in_process'] = 1;
                    }
                    else {
                        $aUsers[$key]['in_process'] = 0;
                    }
                }
            }
        }

		$this->template()->setHeader(array(
			'inactivereminder.js' => 'module_user',
			'inactivereminder.css' => 'module_user'
		))
			->setPhrase(array(
				'stopped',
				'enter_a_number_of_days',
				'enter_a_number_to_size_each_batch',
				'not_enough_users_to_mail',
                'are_you_sure_you_want_send_mail_to_all_inactive_members_who_have_not_logged_in_for_days_days'
			))
            ->assign(array(
                'aUsers' => $aUsers,
                'iDays' => $iDays
            ))
            ->setActiveMenu('admincp.member.inactivereminder')
            ->setBreadCrumb(_p('Members'),'#')
            ->setBreadCrumb(_p('inactive_members'))
			->setSectionTitle(_p('inactive_member_reminder'));
        Phpfox_Pager::instance()->set(array(
            'page' => $iPage,
            'size' => $iPageSize,
            'count' => $iCnt));
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
