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
 * @version 		$Id: add.class.php 1931 2010-10-25 11:58:06Z Raymond_Benc $
 */
class Admincp_Component_Controller_Plugin_Hook_Add extends Phpfox_Component
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
			'hook_type' => _p('select_what_type_of_a_hook_this_is')
		);		
		
		$oValid = Phpfox_Validator::instance()->set(array('sFormName' => 'js_form', 'aParams' => $aValidation));
		
		if ($aVals = $this->request()->getArray('val'))
		{
			if (Phpfox::getService('admincp.plugin.process')->addHook($aVals))
			{
				$this->url()->send('admincp.plugin.hook.add', null, _p('hook_successfully_added'));
			}
		}
		
		$this->template()->setTitle(_p('add_hook'))
			->setBreadCrumb(_p('add_hook'))
            ->setActiveMenu('admincp.techie.plugin')
			->assign(array(
				'aProducts' => Phpfox::getService('admincp.product')->get(),
				'aModules' => Phpfox::getService('admincp.module')->getModules(),
				'sCreateJs' => $oValid->createJS(),
				'sGetJsForm' => $oValid->getJsForm(),
				'bIsEdit' => $bIsEdit,
				'aHookTypes' => array(
					'library',
					'service',
					'component',
					'template'
				)
			)
		);
	}
}