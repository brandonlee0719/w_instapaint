<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Controller_Admincp_Group_Activitypoints
 */
class User_Component_Controller_Admincp_Group_Activitypoints extends Phpfox_Component
{

	/**
	 * Controller
	 * This controller handles invalid user group by 2 means:
	 *		1. getInt('id',0) => if no user group is given its explicitly redirected
	 *		2. getActivityPoints may return a Phpfox_Error
	 */
	public function process()
	{
		$iGroupId = $this->request()->getInt('id', 0);
		$aPoints = Phpfox::getService('user.group.setting')->getActivityPoints($iGroupId);
		if (($aVals = $this->request()->getArray('val')))
		{
			$oService = Phpfox::getService('user.group.setting.process');
			$aUpdate = array();
			foreach ($aVals['module'] as $iSetting => $iValue)
			{
				foreach ($aPoints as $iKey => $aPoint)
				{
					if ($aPoint['setting_id'] == $iSetting && $iValue != $aPoint['value_actual'])
					{
						$aUpdate['value_actual'][$iSetting] = $iValue;
						/* Update the array to show the change in the template without calling DB again */
						$aPoints[$iKey]['value_actual'] = $iValue;
					}
				}
			}
                        if (!empty($aUpdate))
			{
                            $oService->update($aVals['igroup'], $aUpdate);
                        }
			$iGroupId = $aVals['igroup'];
		}
		else if ($iGroupId == 0)
		{
			$this->url()->send('admincp.user.group', null, _p('invalid_user_group'));
		}


		$sUserGroup = Phpfox::getService('user.group')->getGroup($iGroupId);
		if (!Phpfox_Error::isPassed())
		{
			$aError = array_unique(Phpfox_Error::get());
			$sMessage = implode(', ', $aError);
			$this->url()->send('admincp.user.group', null, $sMessage);
		}
		$this->template()
				->setBreadCrumb(_p('manage_activity_points'), $this->url()->makeUrl('current'), true)
				->setTitle(_p('manage_activity_points'))
                ->setActiveMenu('admincp.member.group')
				->assign(array(
					'aPoints' => $aPoints,
					'aUserGroup' => $sUserGroup,
				))
				->setHeader(array(
					'activitypoints.css' => 'module_user'
				))
		;
	}
}
