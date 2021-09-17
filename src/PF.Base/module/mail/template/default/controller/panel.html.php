{if $aMessages}
<ul class="panel_rows">
	{foreach from=$aMessages item=aMail}
	<li>
		<div class="notification_delete">
			<a href="#" class="js_hover_title noToggle remove-btn" onclick="$.ajaxCall('mail.delete', 'id={$aMail.thread_id}', 'GET'); $(this).parents('li:first').slideUp(); return false;">
				<span class="fa fa-times"></span>
				<span class="js_hover_info">
					{_p var='archive'}
				</span>
			</a>
		</div>
		<a onclick="$(this).removeClass('is_new');" href="{url link='mail.thread' id=$aMail.thread_id}" class="popup {if $aMail.viewer_is_new} is_new{/if}" data-custom-class="mail_thread">
			<div class="panel_rows_image">
				{if $aMail.user_id == Phpfox::getUserId()}
				{img user=$aMail suffix='_50_square' max_width=50 max_height=50 no_link=true}
				{else}
				{if (isset($aMail.user_id) && !empty($aMail.user_id))}
				{img user=$aMail suffix='_50_square' max_width=50 max_height=50 no_link=true}
				{/if}
				{/if}
			</div>
			<div class="panel_rows_content">
				<div class="panel_focus">
					{foreach from=$aMail.users name=mailusers item=aMailUser key=index}
						<span>{$aMailUser.full_name|shorten:35:'...'|clean}</span>{if $index != count($aMail.users)},{/if}
					{/foreach}
				</div>
                <div class="panel_rows_preview item_view_content">
                    {if Phpfox::getParam('mail.show_preview_message')}
					    {$aMail.preview|cleanbb|clean}
                    {/if}
				</div>
				<div class="panel_rows_time">
					{$aMail.time_stamp|convert_time}
				</div>
			</div>
		</a>
	</li>
	{/foreach}
</ul>
{else}
<div class="message">
    {_p var='no_new_messages'}
</div>
{/if}
<div class="panel_actions clearfix">
	{if Phpfox::getUserParam('mail.can_compose_message')}
	<a href="{url link='mail.compose'}" class="popup js_hover_title pull-left"><i class="fa fa-pencil-square"></i><span class="js_hover_info">{_p var='compose'}</span></a>
	{/if}
</div>