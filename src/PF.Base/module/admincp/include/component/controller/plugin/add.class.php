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
 * @version 		$Id: add.class.php 2000 2010-10-29 11:24:24Z Raymond_Benc $
 */
class Admincp_Component_Controller_Plugin_Add extends Phpfox_Component
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
		
		$bIsEdit = false;
		$aValidation = array(
			'product_id' => _p('select_product'),
			'title' => _p('provide_a_title_for_your_plugin'),
			'call_name' => _p('select_a_hook'),
			'php_code' => _p('provide_php_code_for_your_plugin')
		);		
		
		$oValid = Phpfox_Validator::instance()->set(array('sFormName' => 'js_form', 'aParams' => $aValidation));
		
		if (($iEditId = $this->request()->get('id')))
		{			
			$aPlugin = Phpfox::getService('admincp.plugin')->getForEdit($iEditId);
			if (isset($aPlugin['plugin_id']))
			{
				$bIsEdit = true;				
				$this->template()->assign(array(
						'aForms' => $aPlugin						
					)
				);
			} else {
                $bIsEdit = false;
            }
		}			

		if ($aVals = $this->request()->getArray('val'))
		{
			if ($bIsEdit)
			{
				if (Phpfox::getService('admincp.plugin.process')->update($aPlugin['plugin_id'], $aVals))
				{
					$this->url()->send('admincp.plugin.add', array('id' => $aPlugin['plugin_id']), _p('plugin_successfully_updated'));
				}
			}
			else 
			{
				if (Phpfox::getService('admincp.plugin.process')->add($aVals))
				{
					$this->url()->send('admincp.plugin', null, _p('plugin_successfully_added'));
				}
			}
		}
		
		$this->template()->setTitle(_p('create_plugin'))
			->setBreadCrumb(_p('create_plugin'), $this->url()->current(), true)
            ->setActiveMenu('admincp.techie.plugin')
			->assign(array(
				'sCreateJs' => $oValid->createJS(),
				'sGetJsForm' => $oValid->getJsForm(),			
				'aHooks' => Phpfox::getService('admincp.plugin')->getHooks(),
				'bIsEdit' => $bIsEdit
			)
		);
	}
}