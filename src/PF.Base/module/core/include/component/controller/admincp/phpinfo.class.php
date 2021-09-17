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
 * @version 		$Id: phpinfo.class.php 982 2009-09-16 08:11:36Z Raymond_Benc $
 */
class Core_Component_Controller_Admincp_Phpinfo extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		ob_clean();
		
		phpinfo();
		
		$sPhpInfo = ob_get_contents();

		ob_clean();	
		
		preg_match( "#<body>(.*)</body>#is" , $sPhpInfo, $aMatches);
		
		$sPhpInfo = $aMatches[1];

		$sPhpInfo = str_replace('<table>','<table class="table table-admin">',$sPhpInfo);
		
		$this->template()->setTitle(_p('php_info'))
			->setBreadCrumb(_p('system'), $this->url()->makeUrl('admincp.core.system'))
			->setSectionTitle(_p('php_info'))
			->assign(array(
				'sPhpInfo' => $sPhpInfo
			)
		);			
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('core.component_controller_phpinfo_clean')) ? eval($sPlugin) : false);
	}
}