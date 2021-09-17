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
 * @version 		$Id: index.class.php 1931 2010-10-25 11:58:06Z Raymond_Benc $
 */
class Admincp_Component_Controller_Component_Index extends Phpfox_Component
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
		
		if (($iDeleteId = $this->request()->getInt('delete')) && Admincp_Service_Component_Process::instance()->delete($iDeleteId))
		{
			$this->url()->send('admincp.component', null, _p('component_successfully_deleted'));
		}
		
		$this->template()->setTitle(_p('manage_components'))
			->setBreadCrumb(_p('manage_components'), $this->url()->makeUrl('admincp.component'))
            ->setActiveMenu('admincp.techie.component')
			->assign(array(
                'sModuleId'   => $this->request()->get('module', 'core'),
                'aComponents' => Phpfox::getService('admincp.component')->getForManagement(),
			)
		);	
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('admincp.component_controller_component_index_clean')) ? eval($sPlugin) : false);
	}
}