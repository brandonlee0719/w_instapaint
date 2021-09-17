<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Friend
 * @version 		$Id: pending.html.php 3642 2011-12-02 10:01:15Z Miguel_Espinoza $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if count($aPendingRequests)}
{if !PHPFOX_IS_AJAX}
<div class="wrapper-items item-container user-listing" id="collection-pending-requests">
{/if}
    {foreach from=$aPendingRequests name=friend item=aUser}
    <article class="user-item friend_row_holder" data-url="{url link='friend.pending' id=$aUser.request_id}">
        {template file='user.block.rows_wide'}
        <div class="friend_action" title="{_p var='delete'}">
            <div class="js_friend_sort_handler js_friend_edit_order"></div>
            <a href="{url link='friend.pending' id=$aUser.request_id}" class="sJsConfirm friend_pending_remove btn btn-sm btn-danger js_hover_title"><i class="fa fa-trash"></i></a>
        </div>
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