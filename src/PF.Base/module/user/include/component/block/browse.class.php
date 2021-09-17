<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Block_Browse
 */
class User_Component_Block_Browse extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{		
		$iPage = $this->getParam('page', 0);
		$bIsAdminCp = $this->getParam('bIsAdminCp', false);
		$bOnlyUser = $this->getParam('bOnlyUser', false);
		$iPageSize = 9;
		$oDb = Phpfox_Database::instance();
		
		$aConditions = array();
		if (($sFind = $this->getParam('find')))
		{
			$aConditions[] = 'AND (u.user_name LIKE \'%' . $oDb->escape($sFind) . '%\' OR u.full_name LIKE \'%' . $oDb->escape($sFind) . '%\' OR u.email LIKE \'%' . $oDb->escape($sFind) . '%\')';	
		}
		if ($bOnlyUser) {
		    $aConditions[] = ' AND u.profile_page_id = 0';
        }
		
		list($iCnt, $aUsers) = Phpfox::getService('user.browse')
			->conditions($aConditions)
			->page($iPage)
			->limit($iPageSize)
			->sort('u.last_login DESC')
			->get();
		
		Phpfox_Pager::instance()->set(array('ajax' => 'user.browseAjax', 'page' => $iPage, 'size' => $iPageSize, 'count' => $iCnt, 'bIsAdminCp' => $bIsAdminCp, 'bOnlyUser' => $bOnlyUser));

		$this->template()->assign(array(
				'aUsers' => $aUsers,
				'sPrivacyInputName' => $this->getParam('input'),
				'bIsAjaxSearch' => $this->getParam('is_search', false),
				'bIsAdminCp' => $bIsAdminCp,
                'bOnlyUser' => $bOnlyUser
			)
		);	
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('user.component_block_browse_clean')) ? eval($sPlugin) : false);
	}
}
