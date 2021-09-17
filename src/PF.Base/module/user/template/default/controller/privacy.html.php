<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

<div id="_privacy_holder_table" class="block">
	<form method="post" class="form" action="{url link='user.privacy'}">
        <div><input type="hidden" name="val[current_tab]" value="" id="current_tab"></div>
		{if Phpfox::getUserParam('user.hide_from_browse')}
		<div id="js_privacy_block_invisible" class="js_privacy_block page_section_menu_holder" {if empty($sActiveTab) || $sActiveTab != 'invisible'}style="display:none;"{/if}>
			<p class="help-block">
				{_p var='invisible_mode_allows_you_to_browse_the_site_without_appearing_on_any_online_lists'}
			</p>
			<br />
            <div class="form-group">
                <label>{_p var='enable_invisible_mode'}</label>
                <div class="item_is_active_holder">
                    <span class="js_item_active item_is_active"><input value="1" name="val[invisible]" class="checkbox" type="radio"{if $aUserInfo.is_invisible} checked="checked"{/if} /> {_p var='yes'}</span>
                    <span class="js_item_active item_is_not_active"><input value="0" name="val[invisible]" class="checkbox" type="radio"{if !$aUserInfo.is_invisible} checked="checked"{/if} /> {_p var='no'}</span>
                </div>
            </div>

			<div class="form-group">
				<input type="submit" value="{_p var='save_changes'}" class="btn btn-primary" />
			</div>	
		</div>		
		{/if}
	
		{if Phpfox::getUserParam('user.can_control_profile_privacy')}
		<div id="js_privacy_block_profile" class="js_privacy_block page_section_menu_holder" {if empty($sActiveTab) || $sActiveTab != 'profile'}style="display:none;"{/if}>
			<p class="help-block">
				{_p var='customize_how_other_users_interact_with_your_profile'}
			</p>
			{foreach from=$aProfiles item=aModules}
			{foreach from=$aModules key=sPrivacy item=aProfile}
				{template file='user.block.privacy-profile'}
			{/foreach}
			{/foreach}
            <div class="form-group">
                <label for="title">{_p var='date_of_birth'}</label>
                <div>
                    <select class="form-control" name="val[special][dob_setting]">
                        <option value="0"{if empty($aUserInfo.dob_setting)} selected="selected"{/if}>{_p var='select'}:</option>
                        <option value="1"{if $aUserInfo.dob_setting == '1'} selected="selected"{/if}>{_p var='show_only_month_amp_day_in_my_profile'}</option>
                        <option value="2"{if $aUserInfo.dob_setting == '2'} selected="selected"{/if}>{_p var='display_only_my_age'}</option>
                        <option value="3"{if $aUserInfo.dob_setting == '3'} selected="selected"{/if}>{_p var='don_t_show_my_birthday_in_my_profile'}</option>
                        <option value="4"{if $aUserInfo.dob_setting == '4'} selected="selected"{/if}>{_p var='show_my_full_birthday_in_my_profile'}</option>
                    </select>
                </div>
            </div>

			<div class="form-group">
				<input type="submit" value="{_p var='save_changes'}" class="btn btn-primary" />
			</div>		
		</div>
		{/if}
		
		<div id="js_privacy_block_items" class="js_privacy_block page_section_menu_holder" {if empty($sActiveTab) || $sActiveTab != 'items'}style="display:none;"{/if}>
			<p class="help-block">
				{_p var='customize_your_default_settings_for_when_sharing_new_items_on_the_site'}
			</p>
			{foreach from=$aItems item=aModules}
			{foreach from=$aModules key=sPrivacy item=aItem}
				{template file='user.block.privacy-item'}
			{/foreach}
			{/foreach}	
				
			<div class="form-group">
				<input type="submit" value="{_p var='save_changes'}" class="btn btn-primary" />
			</div>			
		</div>
		
		{if Phpfox::getUserParam('user.can_control_notification_privacy') && count($aPrivacyNotifications)}
		<div id="js_privacy_block_notifications" class="js_privacy_block page_section_menu_holder" {if empty($sActiveTab) || $sActiveTab != 'notifications'}style="display:none;"{/if}>
			{foreach from=$aPrivacyNotifications item=aModules}
			{foreach from=$aModules key=sNotification item=aNotification}
				{template file='user.block.privacy-notification'}
			{/foreach}
			{/foreach}
			
			<div class="form-group">
				<input type="submit" value="{_p var='save_changes'}" class="btn btn-primary" />
			</div>
		</div>
		{/if}

		<div id="js_privacy_block_blocked" class="js_privacy_block page_section_menu_holder" {if empty($sActiveTab) || $sActiveTab != 'blocked'}style="display:none;"{/if}>
			{if count($aBlockedUsers)}
			<p class="block-help">
				{_p var='check_the_boxes_to_unblock_users'}
			</p>
			{foreach from=$aBlockedUsers item=aBlockedUser name=blocked}
			<div class="go_left" style="width:30%;">
				<div class="{if is_int($phpfox.iteration.blocked/2)}row1{else}row2{/if}{if $phpfox.iteration.blocked == 1 || $phpfox.iteration.blocked == 2 || $phpfox.iteration.blocked == 3} row_first{/if}">
					<label><input type="checkbox" name="val[blocked][]" value="{$aBlockedUser.block_user_id}" class="v_middle" /> {$aBlockedUser|user}</label>
				</div>
			</div>
			{if is_int($phpfox.iteration.blocked / 3)}
			<div class="clear"></div>
			{/if}		
			{/foreach}
			<div class="clear"></div>
			<div class="form-group">
				<input type="submit" value="{_p var='save_changes'}" class="btn btn-primary" />
			</div>		
			{else}
			<div class="extra_info">
				{_p var='you_have_not_blocked_any_users'}
			</div>
			{/if}
		</div>
		
	</form>
</div>

{if isset($bGoToBlocked)}
<script type="text/javascript">
	$Behavior.showBlocked = function()
	{l}
		$("a[rel^='js_privacy_block_blocke']").click();
	{r}
</script>
{/if}