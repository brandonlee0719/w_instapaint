<?php
defined('PHPFOX') or exit('NO DICE!');

class Friend_Component_Block_List_Add extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
	    $this->template()->assign([
	       'bIsLimit' => Phpfox::getService('friend.list')->reachedLimit()
        ]);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('friend.component_block_list_add_clean')) ? eval($sPlugin) : false);
	}
}
