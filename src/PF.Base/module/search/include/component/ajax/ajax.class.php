<?php
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Search_Component_Ajax_Ajax
 */
class Search_Component_Ajax_Ajax extends Phpfox_Ajax
{
	public function viewMore()
	{
		Phpfox::getComponent('search.index', array(), 'controller');		
		
		$this->replaceWith('#feed_view_more', $this->getContent(false));
		$this->call('$Core.loadInit();');
	}
}
