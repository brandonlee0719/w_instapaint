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
 * @package  		Module_Page
 * @version 		$Id: index.class.php 328 2009-03-29 12:26:31Z Raymond_Benc $
 */
class Page_Component_Controller_Admincp_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{		
		Phpfox::getUserParam('page.can_manage_custom_pages', true);
		
		if ($iDeleteId = $this->request()->getInt('delete'))
		{
			if (Phpfox::getService('page.process')->delete($iDeleteId))
			{
				$this->url()->send('admincp.page', null, _p('page_successfully_deleted'));
			}
		}
		
		if ($aVals = $this->request()->getArray('val'))
		{
			if (Phpfox::getService('page.process')->updateActivity($aVals))
			{
				$this->url()->send('admincp.page', null, _p('page_activity_successfully_updated'));
			}
		}
		$this->template()
			->setSectionTitle(_p('custom_pages'))
			->setActionMenu([
				_p('create_a_page') => [
					'custom' => 'data-custom-class="js_box_full"',
					'url' => $this->url()->makeUrl('admincp.page.add')
				]
			])
            ->setPhrase(['error'])
            ->setEditor()
			->setTitle(_p('manage_pages'))
			->setBreadCrumb(_p('manage_pages'))
            ->setActiveMenu('admincp.appearance.page')
			->assign(array(
				'aPages' => Phpfox::getService('page')->get()
			)
		);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('page.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
	}
}