<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
{if count($aPendingRequests)}
{if !PHPFOX_IS_AJAX}
    <div class="item-container user-listing" id="collection-pending-requests">
{/if}
    {foreach from=$aPendingRequests name=friend item=aUser}
    <article class="user-item friend_row_holder" data-url="{url link='friend.pending' id=$aUser.request_id}">
        {assign var='request_id' value=$aUser.request_id}
        {template file='user.block.rows_wide'}
    </article>
{/foreach}
{pager}
{if !PHPFOX_IS_AJAX}
</div>
{/if}
{else}
{if !PHPFOX_IS_AJAX}
<div class="extra_info">
	{_p var='there_are_no_pending_friends_requests'}
</div>
{/if}
{/if}