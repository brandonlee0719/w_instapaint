<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox_Component
 * @version 		$Id: add.class.php 1558 2010-05-04 12:51:22Z Raymond_Benc $
 */
class Core_Component_Controller_Admincp_Currency_Add extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$bIsEdit = false;
		if (($sId = $this->request()->get('id')) && ($aCurrency = Phpfox::getService('core.currency')->getForEdit($sId)))
		{
			$bIsEdit = true;
			$this->template()->assign('aForms', $aCurrency);	
		}
		
		if (($aVals = $this->request()->getArray('val')))
		{
		    //Check is letters or digits
            if (isset($aVals['currency_id']) && !ctype_alnum($aVals['currency_id'])) {
                return Phpfox_Error::set(_p("Only letters or digits are accepted."));
            }

			if ($bIsEdit)
			{
				if (Phpfox::getService('core.currency.process')->update($aCurrency['currency_id'], $aVals))
				{
                    $this->url()->send('admincp.core.currency', null, _p('currency_successfully_updated'));
				}				
			}
			else 
			{
				if (Phpfox::getService('core.currency.process')->add($aVals))
				{
					$this->url()->send('admincp.core.currency', null, _p('currency_successfully_added'));
				}
			}
		}
		
		$this->template()->setTitle(_p('currency_manager'))
			->setBreadCrumb(_p('currency_manager'), $this->url()->makeUrl('admincp.core.currency'))		
			->setBreadCrumb(_p('add_currency'), $this->url()->current(), true)
            ->setActiveMenu('admincp.globalize.currency')
			->assign(array(
					'bIsEdit' => $bIsEdit
				)
			);		
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('core.component_controller_admincp_currency_add_clean')) ? eval($sPlugin) : false);
	}
}