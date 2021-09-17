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
 * @package  		Module_Share
 * @version 		$Id: ajax.class.php 6970 2013-12-04 17:11:50Z Fern $
 */
class Share_Component_Ajax_Ajax extends Phpfox_Ajax
{
	public function popup()
	{		
		Phpfox::getBlock('share.frame', array(
				'type' => htmlspecialchars($this->get('type')),
				'url' => $this->get('url'),
				'title' => htmlspecialchars($this->get('title'))
			)
		);
	}
	
	public function sendFriends()
	{
		Phpfox::isUser(true);
		
		if (Phpfox::getService('mail.process')->add($this->get('val')))
		{
			$this->setMessage(_p('message_successfully_sent'));
		}
	}
}