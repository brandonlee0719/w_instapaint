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
class Core_Component_Controller_Admincp_Country_Child_Index extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$mCountry = Phpfox::getService('core.country')->getCountry($this->request()->get('id'));
		
		if ($mCountry === false)
		{
			return Phpfox_Error::display(_p('not_a_valid_country'));
		}
		
		if (($iId = $this->request()->getInt('delete')))
		{
			if (Phpfox::getService('core.country.child.process')->delete($iId))
			{
				$this->url()->send('admincp.core.country.child', array('id' => $this->request()->get('id')), _p('state_province_successfully_deleted'));
			}
		}		
		
		if ($this->request()->getInt('deleteall'))
		{
			if (Phpfox::getService('core.country.child.process')->deleteAll($this->request()->get('id')))
			{
				$this->url()->send('admincp.core.country', null, _p('country_child_entries_successfully_deleted'));
			}
		}		
		
		$this->template()->setTitle(_p('country_manager'))
			->setBreadCrumb(_p('country_manager'), $this->url()->makeUrl('admincp.core.country'))
			->setBreadCrumb(_p('states_provinces') . ': ' . $mCountry, null, true)
			->setHeader('cache', array(
					'drag.js' => 'static_script',
					'<script type="text/javascript">$Behavior.coreDragInit = function() { Core_drag.init({table: \'#js_drag_drop\', ajax: \'core.countryChildOrdering\'}); }</script>'
				)
			)			
			->assign(array(
					'aChildren' => Phpfox::getService('core.country')->getChildForEdit($this->request()->get('id')),
					'sParentId' => $this->request()->get('id')
				)
            )->setActionMenu([
                _p('add_state_province') => [
                    'url' => $this->url()->makeUrl('admincp.core.country.child.add', ['iso' => $this->request()->get('id')]),
                    'class' => 'popup'
                ]
            ])->setActiveMenu('admincp.globalize.country');
        return null;
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('core.component_controller_admincp_country_child_clean')) ? eval($sPlugin) : false);
	}
}