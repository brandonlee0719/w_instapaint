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
 * @version 		$Id: missing.class.php 1390 2010-01-13 13:30:08Z Raymond_Benc $
 */
class Admincp_Component_Controller_Setting_Missing extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$iPage = $this->request()->getInt('page', 0);
		
		$aXml = Phpfox::getService('core')->getModulePager('settings', $iPage, 5);
		
		if ($aXml === false)
		{
			$sPhrase = _p('missing_settings_successfully_imported');
			
			Phpfox::getLib('cache')->removeGroup('setting');
			
			$this->url()->send('admincp.setting', null, $sPhrase);
		}
		
		$aModules = array();
		if (is_array($aXml))
		{
			$iMissing = Phpfox::getService('admincp.setting.process')->findMissingSettings($aXml);
			
			foreach ($aXml as $sModule => $sPhrases)
			{
				$aModules[] = $sModule;
			}
			
			$this->template()->setHeader('<meta http-equiv="refresh" content="2;url=' . $this->url()->makeUrl('admincp.setting.missing', array('page' => ($iPage + 1))) . '">');
		} else {
            $iMissing = 0;
        }

		$this->template()
            ->setTitle(_p('missing_settings'))
            ->setActiveMenu('admincp.maintain.missing')
			->setBreadCrumb(_p('missing_settings'), $this->url()->makeUrl('admincp.setting'))
			->assign(array(
					'aModules' => $aModules,
					'iMissing' => $iMissing
				)
			);			
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('admincp.component_controller_setting_missing_clean')) ? eval($sPlugin) : false);
	}
}