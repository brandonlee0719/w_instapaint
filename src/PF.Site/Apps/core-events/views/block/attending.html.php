<?php 
/**
 * [PHPFOX_HEADER]
 *
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if count($aInvites)}
    <div class="item-event-member-block">
        <div class="item-event-member-list">
        {foreach from=$aInvites name=invites item=aUser}
            {template file='user.block.rows'}
        {/foreach}
        {pager}
        </div>
    </div>
{else}
    <div class="help-block p-2">
        {_p var='no_guests_found'}
    </div>
{/if}