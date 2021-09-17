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
 * @version 		$Id: import.class.php 1572 2010-05-06 12:37:24Z Raymond_Benc $
 */
class Core_Component_Controller_Admincp_Country_Import extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$bOverwrite = ($this->request()->getInt('overwrite') ? true : false);		
		
		if (isset($_FILES['file_import']) && ($aFile = $_FILES['file_import']) && ($aVals = $this->request()->getArray('val')))
		{
            $aFile = Phpfox_File::instance()->load('file_import', array('txt'));
            if ($aFile === false) {
                return false;
            }
		    if (empty($aVals['country_iso'])) {
		        return Phpfox_Error::set(_p('You must select country to import states/provinces.'));
            }
			if (($aLog = Phpfox::getService('core.country.process')->importFromText($aVals, $aFile)) && $aLog['completed'] > 0)
			{
				$this->url()->send('admincp.core.country.child', array('id' => $aVals['country_iso']), _p('text_import_successfully_completed', array('completed' => $aLog['completed'], 'failed' => $aLog['failed'])));
			}
		}
		
		if (isset($_FILES['import']) && ($aFile = $_FILES['import']))
		{
			if (Phpfox::getService('core.country.process')->import($aFile, $bOverwrite))
			{
				$this->url()->send('admincp.core.country', null, _p('import_successfully_completed'));
			}
		}		
		
		$this->template()->setTitle(_p('import_countries_states_provinces'))
			->setBreadCrumb(_p('country_manager'), $this->url()->makeUrl('admincp.core.country'))
			->setBreadCrumb(_p('import'), null, true)
			->assign(array(
					
				)
			)->setActiveMenu('admincp.globalize.country');
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('core.component_controller_admincp_country_import_clean')) ? eval($sPlugin) : false);
	}
}