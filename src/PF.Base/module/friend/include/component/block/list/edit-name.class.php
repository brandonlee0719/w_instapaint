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
 * @version 		$Id: edit-name.class.php 2682 2011-06-20 19:56:20Z Raymond_Benc $
 */
class Friend_Component_Block_List_Edit_Name extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		$iListId = $this->request()->getInt('id');
		$aList = Phpfox::getService('friend.list')->getList($iListId, Phpfox::getUserId());
		$this->template()->assign(array(
			'aList' => $aList
		));
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('friend.component_block_list_edit_name_clean')) ? eval($sPlugin) : false);
	}
}