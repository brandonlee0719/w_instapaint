<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class User_Component_Block_Admincp_Setting
 */
class User_Component_Block_Admincp_Setting extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$aGroup = Phpfox::getService('user.group')->getGroup($this->request()->get('group_id'));

		$aSettings = Phpfox::getService('user.group.setting')->get($this->request()->get('group_id'), $this->request()->get('module_id'));
		
		$aCurr = array();
		$aAvoidDup = array();

		foreach ($aSettings as $sModule => $aSets)
		{
			foreach ($aSets as $iKey => $mSets)
			{
				foreach ($mSets as $jKey => $aSetting)
				{
                    if (preg_match('/_sponsor_price/i',$aSetting['name']))
                    {
                        $aVals = Phpfox::getLib('parse.format')->isSerialized($aSetting['value_actual']) ? unserialize($aSetting['value_actual']) : 'No price set';
                        if (is_array($aVals) && is_numeric(reset($aVals))) // so a module can have 2 settings with currencies (music.song, music.album)
                        {
                            $this->setParam('currency_value_val[value_actual]['.$aSetting['setting_id'].']', $aVals);
                        }
                        $aSettings[$sModule][$this->request()->get('module_id')][$jKey]['isCurrency'] = 'Y';
                    }

					if (isset($aAvoidDup[$aSetting['setting_id']]))
					{
						unset($aSettings[$sModule][$iKey][$jKey]);
					}
					$aAvoidDup[$aSetting['setting_id']] = true;
				}
			}
		}
		
		$this->template()->assign(array(
				'aSettings' => $aSettings,
				'aForms' => $aGroup,
				'aCurrency' => $aCurr
			)
		);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('user.component_block_admincp_setting_clean')) ? eval($sPlugin) : false);
	}
}
