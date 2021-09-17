<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
{if !isset($bIsFriendController)}
{if count($aFriends)}
<ul id="js_new_friend_holder_drop">
{/if}
{/if}
{if !count($aFriends)}
{if !PHPFOX_IS_AJAX }
{if !isset($bIsFriendController)}
<div class="drop_data_empty">
{else}
<div class="extra_info">
{/if}
	{_p var='no_new_requests'}
</div>
{/if}
{else}
<div class="item-container" id="collection-friends-incoming">
{foreach from=$aFriends name=friends item=aFriend}
    {if !isset($bIsFriendController)}
	<li id="js_new_friend_request_{$aFriend.request_id}" class="holder_notify_drop_data with_padding{if $phpfox.iteration.friends == 1} first{/if} js_friend_request_{$aFriend.request_id}{if !$aFriend.is_seen} is_new{/if}">
	{/if}
        <article class="user-item" data-url="{url link=$aFriend.user_name}" data-uid="{$aFriend.request_id}" id="request-{$aFriend.request_id}">
            
            <div class="item-outer">
                <span class="item-media-src">
                 {if isset($bIsFriendController)}
                    <!-- Moderate checkbox -->
                    <div class="moderation_row">
                        <label class="item-checkbox">
                            <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aFriend.request_id}"/>
                            <i class="ico ico-square-o"></i>
                        </label>
                    </div>
                    {/if}
                    {img user=$aFriend max_width='50' max_height='50' suffix='_50_square'}
                </span>
                <div class="item-inner">
                    <div class="username">
                        {$aFriend|user}
                    </div>
                    <div class="item-info">
                        {if $aFriend.relation_data_id > 0}
                        <div class="extra_info_link">
                            {img theme='misc/heart.png' class='v_middle'} {_p var='relationship_request'}
                        </div>
                        {else}
                            {if isset($aFriend.mutual_friends) && $aFriend.mutual_friends.total > 0}
                            <div class="extra_info_link">
                                <a href="#" onclick="$Core.box('friend.getMutualFriends', 300, 'user_id={$aFriend.friend_user_id}'); return false;">
                                    {if $aFriend.mutual_friends.total == 1}
                                    {_p var='1_mutual_friend'}
                                    {else}
                                    {_p var='total_mutual_friends' total=$aFriend.mutual_friends.total}
                                    {/if}
                                </a>
                            </div>
                            {/if}
                        {/if}
                        {plugin call='friend.template_block_accept__1'}

                        {if !empty($aFriend.message)}
                        <div class="extra_info">
                            {$aFriend.message|clean:false|shorten:20:'friend.view_more':true}
                        </div>
                        {/if}
                    </div>
                </div>
                <ul class="extra_info_middot" style="display:none;">
                    <li><a href="#" onclick="$Core.composeMessage({l}user_id: {$aFriend.user_id}{r}); return false;">{_p var='send_a_message'}</a></li>
                    <li>&middot;</li>
                    <li><a href="{url link=$aFriend.user_name}">{_p var='view_profile'}</a></li>
                </ul>
                <div class="drop_data_content">
                    <div class="drop_data_user">
                        <div class="drop_data_action">
                            <div class="js_drop_data_add" style="display:none; padding-right:5px;">
                                {img theme='ajax/add.gif'}
                            </div>
                            <div class="js_drop_data_button" id="drop_down_{$aFriend.request_id}">
                                <ul class="table_clear_button inline">
                                    <li><input type="button" name="" value="{_p var='confirm'}" class="btn btn-primary" onclick="$(this).parents('.drop_data_action').find('.js_drop_data_add').show(); {if $aFriend.relation_data_id > 0} $.ajaxCall('custom.processRelationship', 'relation_data_id={$aFriend.relation_data_id}&amp;type=accept&amp;request_id={$aFriend.request_id}'); {else} $.ajaxCall('friend.processRequest', 'type=yes&amp;user_id={$aFriend.user_id}&amp;request_id={$aFriend.request_id}&amp;manage_all_request=true'); {/if}" /></li>
                                    <li><input type="button" name="" value="{_p var='deny'}" class="btn btn-danger" onclick="$(this).parents('.drop_data_action').find('.js_drop_data_add').show(); {if $aFriend.relation_data_id > 0} $.ajaxCall('custom.processRelationship', 'relation_data_id={$aFriend.relation_data_id}&amp;type=deny&amp;request_id={$aFriend.request_id}'); {else} $.ajaxCall('friend.processRequest', 'type=no&amp;user_id={$aFriend.user_id}&amp;request_id={$aFriend.request_id}&amp;manage_all_request=true'); {/if}" /></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </article>
	{if !isset($bIsFriendController)}
	</li>
	{/if}
{/foreach}
</div>
{/if}
{if !isset($bIsFriendController)}
{if count($aFriends)}
</ul>

{literal}
<script type="text/javascript">	
	var $iTotalFriends = parseInt($('#js_total_new_friend_requests').html());
	var $iNewTotalFriends = 0;
	$('#js_new_friend_holder_drop li.holder_notify_drop_data').each(function()
	{
		$iNewTotalFriends++;
	});
	
	$iTotalFriends = parseInt(($iTotalFriends - $iNewTotalFriends));
	if ($iTotalFriends < 0)
	{
		$iTotalFriends = 0;
	}
	
	if ($iTotalFriends === 0)
	{
		$('span#js_total_new_friend_requests').html('').hide();
	}
	else
	{
		$('span#js_total_new_friend_requests').html($iTotalFriends);
	}	
</script>
{/literal}

{/if}
<a href="{url link='friend.accept'}" class="holder_notify_drop_link">{_p var='see_all_friend_requests'}</a>
{/if}