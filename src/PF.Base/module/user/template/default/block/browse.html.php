<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{if !$bIsAjaxSearch}
<script type="text/javascript">
	var sPrivacyInputName = '{$sPrivacyInputName}';
	var sSearchByValue = '{_p var='search_by_email_full_name_or_user_name' phpfox_squote=true}';
{literal}

	updateCheckBoxes();
	
	function removeFromSelectList(sId)
	{
		$('.js_cached_user_id_' + sId + '').remove();
		$('#js_users_checkbox_' + sId).attr('checked', false);
		$('#js_user_input_' + sId).remove();
		$('.js_cached_user_id_' + sId).remove(); return false;		
		
		return false;
	}
	
	function addUserToSelectList(oObject, sId)
	{		
		if (oObject.checked)
		{
			iCnt = 0;
			$('.js_cached_user_name').each(function()
			{			
				iCnt++;
			});			

			if (function_exists('plugin_addFriendToSelectList'))
			{
				plugin_addFriendToSelectList(sId);
			}
			
			$('#js_selected_users').append('<div class="js_cached_user_name row1 js_cached_user_id_' + sId + '' + (iCnt ? '' : ' row_first') + '" id="js_user_id_' + sId + '"><span style="display:none;">' + sId + '</span><input type="hidden" name="val[' + sPrivacyInputName + '][]" value="' + sId + '" /><a href="#" onclick="return removeFromSelectList(' + sId + ');"><img src="' + getParam('sImagePath') + 'misc/delete.gif" class="delete_hover" style="vertical-align:middle;" /></a> ' + $('#js_user_' + sId + '').html() + '</div>');
			
			sNewHeight = ($('#js_selected_users').height() + 30);
			if (sNewHeight < 100)
			{
				$('#js_selected_users').height(sNewHeight);
			}		
			p('#js_default_' + sPrivacyInputName + '');
			$('#js_default_' + sPrivacyInputName + '').remove();
			$('#js_selected_users').show();
		}
		else
		{
			$('.js_cached_user_id_' + sId).remove();
			$('#js_user_input_' + sId).remove();
		}
	}
	
	function updateCheckBoxes()
	{
		iCnt = 0;
		$('.js_cached_user_name').each(function()
		{			
			iCnt++;
			$('#js_users_checkbox_' + $(this).find('span').html()).attr('checked', true);
		});
		
		$('#js_selected_count').html((iCnt / 2));
	}
	
	function showLoader()
	{		
		{/literal}
		$('#js_user_search_content').html($.ajaxProcess('{_p var='loading' phpfox_squote=true}', 'large'));
		{literal}
	}

	function cancelUserSelection()
	{		
		$('#js_selected_users').html('');	
		$Core.loadInit(); 
		tb_remove();
	}	
	
	$Behavior.user_browse_check_browser = function()
	{
		if ($.browser.mozilla) 
		{
			$('.js_is_enter').keypress(checkForEnter);
		} 
		else 
		{
			$('.js_is_enter').keydown(checkForEnter);
		}
	};	
	
	function checkForEnter(event)
	{
		if (event.keyCode == 13) 
		{
			showLoader(); 
			
			$.ajaxCall('friend.browseAjax', 'find=' + $('#js_find_user').val() + '&amp;input=' + sPrivacyInputName + '');
		
			return false;	
		}
	}	
	
{/literal}
</script>

<div id="js_user_loader_info"></div>
<div id="js_user_loader">
	<form method="post" action="{url link='current'}" onsubmit="$.ajaxCall('user.browseAjax', 'find=' + $('#js_find_user').val() + '&amp;input={$sPrivacyInputName}&amp;bIsAdminCp={$bIsAdminCp}&amp;bOnlyUser={$bOnlyUser}'); return false;">
		<input type="text" name="find" value="{_p var='search_by_email_full_name_or_user_name'}" onfocus="if (this.value == sSearchByValue){literal}{{/literal}this.value = ''; $(this).removeClass('default_value');{literal}}{/literal}" class="default_value form-control" onblur="if (this.value == ''){literal}{{/literal}this.value = sSearchByValue; $(this).addClass('default_value');{literal}}{/literal}" id="js_find_user" size="30" onkeydown="$(this).parent().submit()"/>
	</form>
{/if}
	<div id="js_user_search_content">
		{pager}
		<div class="main_break"></div>
		<div class="label_flow" style="height:200px;">
		{foreach from=$aUsers name=users item=aUser}
			<div class="{if is_int($phpfox.iteration.users/2)}row1{else}row2{/if}{if $phpfox.iteration.users == 1} row_first{/if}">
				<span><input type="checkbox" class="checkbox" name="user[]" class="js_users_checkbox" id="js_users_checkbox_{$aUser.user_id}" value="{$aUser.user_id}" onclick="addUserToSelectList(this, '{$aUser.user_id}');" style="vertical-align:middle;" /></span>
				<span id="js_user_{$aUser.user_id}">{$aUser|user:'':' onclick="return plugin_userLinkClick(this);"'}</span>
			</div>
		{foreachelse}
			<div class="extra_info">
				{_p var='sorry_no_users_were_found'}
		</div>	
		{/foreach}
		</div>
	</div>
{if !$bIsAjaxSearch}	
	<div class="main_break t_right">
		<input type="button" name="submit" value="{_p var='use_selected'}" onclick="$Core.loadInit(); tb_remove();" class="button" />
		<input type="button" name="cancel" value="{_p var='cancel_uppercase'}" onclick="cancelUserSelection();" class="button" />
	</div>	
</div>
{/if}