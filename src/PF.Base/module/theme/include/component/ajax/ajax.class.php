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
 * @package  		Module_Theme
 * @version 		$Id: ajax.class.php 5345 2013-02-13 09:44:03Z Raymond_Benc $
 */
class Theme_Component_Ajax_Ajax extends Phpfox_Ajax
{
	public function sample()
	{
		if (Phpfox::isAdmin())
		{
			echo '<iframe src="' . Phpfox_Url::instance()->makeUrl('theme', array('sample', 'get-block-layout' => 'true')) . '" width="100%" height="400" frameborder="0"></iframe>';
		}
	}

	public function updateOrder()
	{		
		if ($this->get('sMode', '') == 'designdnd')
		{

			$this->alert(_p('you_are_not_allowed_to_make_use_of_this_feature'));
			if (Phpfox::getService('theme.process')->updateOrderDnD($this->get('val'), $this->get('sController')))
			{
				$this->softNotice(_p('order_updated'));
			}
			else
			{
				$this->alert(_p('something_bad_happened'));
			}
		}
		else
		{
			Phpfox::getService('theme.process')->updateOrder($this->get('val'));
		}
	}
	
	/**
	 * Loads a new block into the page. This function is used in the designDnD feature
	 * when the user drops a new block into the page.
	 * It calls $Core.loadInit() which calls the enableDnD() function after the new
	 * block has been added.
	 */
	public function loadNewBlock()
	{
		/* Need security checks here */
		$sName = str_replace('new_js_block_border_', '',$this->get('sId'));
		$aParts = explode('_',$sName);
		
		define('PHPFOX_DESIGN_DND_OVERWRITE', true);
		Phpfox::getBlock($aParts[0].'.'.$aParts[1], array('sDeleteBlock' => str_replace('new_js_block_border_', '',$this->get('sId')), 'bPassOverAjaxCall' => true), true);
		
		$sBlock = $this->getContent(false);
		$sBlock = str_replace(array("\n", "\t"), '', $sBlock);
		
		
		
		$this->html('#clone_'.$this->get('sId'), $sBlock);
		$this->call('$("#clone_'.$this->get('sId') . '").removeClass("do_not_count").addClass("js_sortable");');
		/* We rebuild the order to make sure it includes the new block */
		$this->call('$oDesignDnD.buildOrder();');
		$this->call('$Core.loadInit();');
		$this->call('$.ajaxCall("theme.updateOrder", $oDesignDnD.sOrder);');
	}
}