{$sScript}
{if $aMessages}
<ul class="panel-items">
	{foreach from=$aMessages item=aMail}
	<li class="panel-item {if $aMail.viewer_is_new} is_new{/if}">
		<div class="panel-item-content">
			<div class="notification-delete {if $aMail.viewer_is_new} is_new{/if}">
				<a href="#" class="js_hover_title noToggle" onclick="$.ajaxCall('mail.delete', 'id={$aMail.thread_id}', 'GET'); $(this).parents('li:first').slideUp(); return false;">
					<span class="ico ico-inbox"></span>
					<span class="js_hover_info">
						{_p var='archive'}
					</span>
				</a>
			</div>
			</a>
			<a onclick="$(this).removeClass('is_new');" href="{url link='mail.thread' id=$aMail.thread_id}" class="popup {if $aMail.viewer_is_new} is_new{/if}" data-custom-class="mail_thread"></a>
			{if $aMail.user_id == Phpfox::getUserId()}
				{img user=$aMail suffix='_50_square' max_width=50 max_height=50 no_link=true}
				{else}
				{if (isset($aMail.user_id) && !empty($aMail.user_id))}
				{img user=$aMail suffix='_50_square' max_width=50 max_height=50 no_link=true}
				{/if}
			{/if}
			<div class="content">
				<div class="fullname-time">
					<div class="name">
						{$aMail|user}
					</div>
						
					<div class="time">
						{$aMail.time_stamp|convert_time}

						<span class="message-unread s-1 {if $aMail.viewer_is_new} is_new{/if}"></span>
					</div>
				</div>

				<div class="preview item_view_content">
					{if isset($aMail.last_user_id) && $aMail.last_user_id == Phpfox::getUserId()}<span class="ico ico-reply-o"></span> {/if}
					{$aMail.preview|cleanbb|clean}
				</div>
			</div>
		</div>
	</li>
	{/foreach}
</ul>
{else}
<div class="empty-message">
	<img src="{param var='core.path_actual'}PF.Site/flavors/material/assets/images/empty-message.svg" alt="">
    {_p var='you_have_no_messages'}
</div>
{/if}
<div class="panel-actions">
	{if Phpfox::getUserParam('mail.can_compose_message')}
	<a href="{url link='mail.compose'}" class="s-5 popup js_hover_title btn-compose">
		<span class="ico ico-comment-plus-o"></span>
		<span class="js_hover_info">{_p var='compose'}</span>
	</a>
	{/if}
</div>