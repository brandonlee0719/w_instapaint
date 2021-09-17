<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{$sScript}
{if $aFriends}
<ul class="panel-items">
	{foreach from=$aFriends name=notifications item=aNotification}
	<li id="drop_down_{$aNotification.request_id}" class="panel-item {if !$aNotification.is_seen} is_new{/if}"> 
		<div href="{url link=$aNotification.user_name}" class="panel-item-content">
			{img user=$aNotification suffix='_120_square'}

			<div class="content">
				<div class="name">{$aNotification|user}</div>
				<div class="info">
					{if $aNotification.relation_data_id > 0}
					<div class="extra_info_link">
						<i class="fa fa-heart"></i> {_p var='relationship_request_for'} "{$aNotification.relation_name}"
					</div>
					{else}
						{$aNotification.mutual_friends.total} <span class="to-lower">{_p var='mutual_friends'}</span>
					{/if}
				</div>
			</div>
			<div class="panel-actions">
				{if $aNotification.relation_data_id > 0}
					<span class="btn-action accept s-3" onclick="$(this).parents('.drop_data_action').find('.js_drop_data_add').show(); {if $aNotification.relation_data_id > 0} $.ajaxCall('custom.processRelationship', 'relation_data_id={$aNotification.relation_data_id}&amp;type=accept&amp;request_id={$aNotification.request_id}'); {else} $.ajaxCall('friend.processRequest', 'type=yes&amp;user_id={$aNotification.user_id}&amp;request_id={$aNotification.request_id}&amp;inline=true'); {/if} event.stopPropagation();">
						<span class="ico ico-check"></span>
					</span>
					<span class="btn-action deny s-3" onclick="$(this).parents('.drop_data_action').find('.js_drop_data_add').show(); {if $aNotification.relation_data_id > 0} $.ajaxCall('custom.processRelationship', 'relation_data_id={$aNotification.relation_data_id}&amp;type=deny&amp;request_id={$aNotification.request_id}'); {else} $.ajaxCall('friend.processRequest', 'type=no&amp;user_id={$aNotification.user_id}&amp;request_id={$aNotification.request_id}&amp;inline=true'); {/if} event.stopPropagation();">
						 <span class="ico ico-close"></span>
					</span>
				{else}
					<span class="btn-action accept s-3" onclick="$.ajaxCall('friend.processRequest', 'type=yes&amp;user_id={$aNotification.user_id}&amp;request_id={$aNotification.request_id}&amp;inline=true'); event.stopPropagation();">
						<span class="ico ico-check"></span>
					</span>
					<span class="btn-action deny s-3" onclick="$.ajaxCall('friend.processRequest', 'type=no&amp;user_id={$aNotification.user_id}&amp;request_id={$aNotification.request_id}&amp;inline=true'); event.stopPropagation();">
						<span class="ico ico-close"></span>
					</span>
				{/if}
			</div>
		</div>
	</li>
	{/foreach}
</ul>
{else}
<div class="empty-message">
	<img src="{param var='core.path_actual'}PF.Site/flavors/material/assets/images/empty-message.svg" alt="">
    {_p var='no_new_friend_requests'}
</div>
{/if}