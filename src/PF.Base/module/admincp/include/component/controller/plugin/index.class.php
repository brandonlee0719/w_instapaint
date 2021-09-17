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
 * @version 		$Id: index.class.php 1931 2010-10-25 11:58:06Z Raymond_Benc $
 */
class Admincp_Component_Controller_Plugin_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		if (Phpfox::getParam('core.phpfox_is_hosted'))
		{
			$this->url()->send('admincp');
		}		
		
		if ($aVals = $this->request()->getArray('val'))
		{
			if (Phpfox::getService('admincp.plugin.process')->updateActive($aVals))
			{
				$this->url()->send('admincp.plugin', null, _p('plugin_s_updated'));
			}			
		}
		
		if ($sDeletePlugin = $this->request()->get('delete'))
		{
			if (Phpfox::getService('admincp.plugin.process')->delete($sDeletePlugin))
			{
				$this->url()->send('admincp.plugin', null, _p('plugin_successfully_deleted'));
			}
		}		
		
		$this->template()->setTitle(_p('manage_plugins'))
			->setBreadCrumb(_p('manage_plugins'))
            ->setActiveMenu('admincp.techie.plugin')
			->assign(array(
				'aPlugins' => Phpfox::getService('admincp.plugin')->get()
			)
		);
	}
}