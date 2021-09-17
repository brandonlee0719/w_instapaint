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
 * @version 		$Id: add.class.php 979 2009-09-14 14:05:38Z Raymond_Benc $
 */
class Attachment_Component_Controller_Admincp_Add extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$bIsEdit = false;
		if (($sExt = $this->request()->get('id')) && ($aExtension = Phpfox::getService('attachment.type')->getForEdit($sExt)))
		{
			$bIsEdit = true;
			$this->template()->assign('aForms', $aExtension);
		}
		
		if (($aVals = $this->request()->getArray('val')))
		{
			if ($bIsEdit)
			{
				if (Phpfox::getService('attachment.process')->updateType($aExtension['extension'], $aVals))
				{
					$this->url()->send('admincp.attachment', null, _p('attachment_type_successfully_updated'));
				}
			}
			else 
			{
				if (Phpfox::getService('attachment.process')->addType($aVals))
				{
					$this->url()->send('admincp.attachment', null, _p('attachment_type_successfully_added'));
				}				
			}
		}
		
		$this->template()
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('attachments_title'), $this->url()->makeUrl('admincp.attachment'))
			->setBreadCrumb(($bIsEdit ? _p('editing_an_attachment_type') . ': ' : _p('add_an_attachment_type')), $this->url()->current())
			->setTitle(($bIsEdit ? _p('editing_an_attachment_type') . ': ' : _p('add_an_attachment_type')))
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
		(($sPlugin = Phpfox_Plugin::get('attachment.component_controller_admincp_add_clean')) ? eval($sPlugin) : false);
	}
}