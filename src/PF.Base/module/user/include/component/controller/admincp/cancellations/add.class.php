<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Controller_Admincp_Cancellations_Add
 */
class User_Component_Controller_Admincp_Cancellations_Add extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$this->_setMenuName('admincp.user.cancellations.add');
		// is user trying to edit or add an item?
		if (($aVals = $this->request()->getArray('val')))
		{
			if (Phpfox::getService('user.cancellations.process')->add($aVals))
			{	
				if (isset($aVals['iDeleteId']))
				{
					$sMessage = _p('option_updated_successfully');
				}
				else 
				{
					$sMessage = _p('option_added_successfully');
				}
				
				$this->url()->send('admincp.user.cancellations.manage', null, $sMessage);
			}
		}

		// is user requesting an item for edit?
		if (($iId = $this->request()->getInt('id')))
		{
			$aEdit = Phpfox::getService('user.cancellations')->get($iId, true);
			if (empty($aEdit))
			{
				Phpfox_Error::set(_p('item_not_found'));
			}
            $aEdit = reset($aEdit);
			$this->template()->assign(array('aForms' => $aEdit));
		}
		
		
		$this->template()
            ->setTitle(_p('add_cancellation_options'))
            ->setActiveMenu('admincp.member.cancellations')
            ->setActionMenu([_p('cancellation_options')=> $this->url()->makeUrl('admincp.user.cancellations.manage')])
			->setBreadCrumb(_p('add_cancellation_options'), $this->url()->makeUrl('admincp.user.cancellations.add'), true)
			;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('user.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
	}
}
