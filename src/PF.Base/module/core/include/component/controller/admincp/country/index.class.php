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
 * @version 		$Id: index.class.php 6113 2013-06-21 13:58:40Z Raymond_Benc $
 */
class Core_Component_Controller_Admincp_Country_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		if (($sExport = $this->request()->get('export')))
		{
			$oArchiveExport = Phpfox::getLib('archive.export')->set(array('zip'));
			if (($aData = Phpfox::getService('core.country')->export($sExport)))
			{
				$oArchiveExport->download('phpfox-country-' . $aData['name'] . '', 'xml', $aData['file']);
			}			
		}
		
		if (($sIso = $this->request()->get('delete')))
		{
			if (Phpfox::getService('core.country.process')->delete($sIso))
			{
				$this->url()->send('admincp.core.country', null, _p('country_successfully_deleted'));
			}
		}
		
		$this->template()->setTitle(_p('country_manager'))
			->setSectionTitle(_p('admincp_menu_country'))
			->setActionMenu([
				_p('new_country') => [
					'url' => $this->url()->makeUrl('admincp.core.country.add'),
					'class' => 'popup'
				],
				_p('admincp_menu_country_import') => [
					'url' => $this->url()->makeUrl('admincp.core.country.import')
				]

			])
			->setBreadCrumb(_p('country_manager'), $this->url()->makeUrl('admincp.core.country'))
			->setHeader('cache', array(
					'drag.js' => 'static_script',
					'<script type="text/javascript">$Behavior.coreDragInit = function() { Core_drag.init({table: \'#js_drag_drop\', ajax: \'core.countryOrdering\'}); }</script>'
				)
			)
			->assign(array(
					'aCountries' => Phpfox::getService('core.country')->getForEdit()
				)
			)
            ->setActiveMenu('admincp.globalize.country');
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('core.component_controller_admincp_country_index_clean')) ? eval($sPlugin) : false);
	}
}