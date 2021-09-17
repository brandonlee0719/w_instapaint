<?php 
/**
 * [PHPFOX_HEADER]
 *
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if isset($aEvent.rsvp_id)}
<div class="feed_comment_extra">
	<a href="#" onclick="tb_show('{_p var='rsvp' phpfox_squote=true}', $.ajaxBox('event.rsvp', 'height=130&amp;width=300&amp;id={$aEvent.event_id}{if $aCallback !== false}&amp;module={$aCallback.module}&amp;item={$aCallback.item}{/if}')); return false;" id="js_event_rsvp_{$aEvent.event_id}">
	{if $aEvent.rsvp_id == 3}
		{_p var='not_attending'}
	{elseif $aEvent.rsvp_id == 2}
		{_p var='maybe_attending'}
	{elseif $aEvent.rsvp_id == 1}
		{_p var='attending'}
	{else}
		{_p var='respond'}
	{/if}
	</a>
</div>
{/if}