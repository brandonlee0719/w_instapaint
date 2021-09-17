<?php
/**
 * [PHPFOX_HEADER]
 *
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<div class="event-invited-container event-mini-block-container">
	{foreach from=$aEventInvites item=aEvent}
		{template file='event.block.mini-entry'}
	{/foreach}
</div>