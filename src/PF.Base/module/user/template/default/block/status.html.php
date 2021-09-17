<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

<form method="post" class="form" action="{url link='current'}" onsubmit="$(this).ajaxCall('user.updateStatus'); return false;" id="js_user_status_form">
	<div id="header_top_notify" style="position:absolute;">
		<ul>		
		{if Phpfox::isModule('notification') && Phpfox::getParam('notification.notify_on_new_request')}
			{module name='notification.link'}						
		{else}
			<li><a href="{url link='user.photo'}">{$sUserGlobalImage}</a></li>
		{/if}
			<li class="status">
				<span id="js_current_user_status">
					<a href="#" title="{_p var='click_to_change_your_status'}" class="status js_update_status">{_p var='status'}:</a>
					<a href="#" title="{_p var='click_to_change_your_status'}" class="js_update_status js_actual_user_status_{$iCurrentUserId}">{if empty($sUserCurrentStatus)}{_p var='none'}{else}{$sUserCurrentStatus|clean|shorten:80}{/if}</a>
				</span>
				<span style="display:none;" id="js_update_user_status">	
						<input type="text" name="status" value="{$sUserCurrentStatus|clean}" style="vertical-align:middle; padding:0px;" size="30" id="js_status_input" maxlength="160" onfocus="this.select();" />
						{_p var='a_href_onclick_js_user_status_form_ajaxcall_user_updatestatus_return_false_save_a_or_a_href_class_js_update_status_cancel_a'}
				</span>	
			</li>
		</ul>
		<div class="clear"></div>
	</div>
</form>