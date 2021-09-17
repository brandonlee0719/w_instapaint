<?php 
/**
 * [PHPFOX_HEADER]
 *
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if !count($aEvents)}
<div class="extra_info">
	{_p var='no_upcoming_events'}
	<ul class="action">
		<li><a href="{url link='event.add'}">{_p var='add_an_event'}</a></li>
	</ul>
</div>
{else}
<div class="event-sponsored-container event-mini-block-container">
{foreach from=$aEvents name=events item=aEvent}
    {template file='event.block.mini-entry'}
{/foreach}
</div>
{/if}