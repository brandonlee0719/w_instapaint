<?php 
/**
 * [PHPFOX_HEADER]
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if !PHPFOX_IS_AJAX}
<div id="js_event_item_holder" class="item-event-member-list events_item_holder">
{/if}
	{if count($aInvites)}
        {foreach from=$aInvites name=invites item=aUser}
            {template file='user.block.rows'}
        {/foreach}
	{else}
        {if !PHPFOX_IS_AJAX}
            <div class="extra_info">
            {if $iRsvp == 1}
                {_p var='no_attendees'}
            {else}
                {_p var='no_results'}
            {/if}
            </div>
        {/if}
	{/if}
	{pager}
{if !PHPFOX_IS_AJAX}
</div>
{/if}