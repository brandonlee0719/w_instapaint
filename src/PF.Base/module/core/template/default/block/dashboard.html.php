<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: dashboard.html.php 1951 2010-10-27 13:25:25Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if !PHPFOX_IS_AJAX}
<div id="js_core_dashboard">
{/if}
	<div class="user_status_update">
		<div class="user_status_update_ajax">
			{img theme='ajax/small.gif'}
		</div>	
		<input type="text" class="input_status" id="js_global_status_input" name="status" {if empty($sGlobalUserStatus)}class="default_value"{/if} value="{if empty($sGlobalUserStatus)}{_p var='what_is_on_your_mind'}{else}{$sGlobalUserStatus|clean}{/if}" size="30" maxlength="160" onfocus="if (this.value == '{_p var='what_is_on_your_mind' phpfox_squote=true}') {left_curly} this.value = ''; {right_curly} this.select(); $('#js_share_user_status').show();" onblur="if (this.value == '') {left_curly} this.value = '{_p var='what_is_on_your_mind' phpfox_squote=true}'; {right_curly}" />
		<div class="p_4" id="js_share_user_status" style="display:none;">
			<div class="dashboard_share_clear">
				(<a href="#" onclick="$('#js_share_user_status').hide(); $.ajaxCall('user.clearStatus'); return false;" title="{_p var='clear_your_current_status'}">{_p var='clear'}</a>)
			</div>
			<div class="t_right">
				<input type="button" value="{_p var='share'}" class="button btn-primary" onclick="$('.user_status_update_ajax').show(); $('#js_share_user_status').hide(); $('#js_global_status_input').ajaxCall('user.updateStatus', 'inline=true');" />
				<input type="button" name="null" value="{_p var='cancel'}" onclick="var sInputCache = $('#js_global_status_input').val(); $('#js_global_status_input').val(''); $('#js_global_status_input').val(sInputCache); $('#js_share_user_status').hide(); return false;" class="button" />
			</div>
		</div>
	</div>
	<div class="js_core_dashboard dashboard_core {if $sBlockLocation != 'sidebar'} go_left{/if}">
		<a href="{url link='user.photo'}" title="{_p var='click_to_change_profile_photo'}">{$sImage}</a>
		<div class="p_2 dashboard_user_group">
			{if !empty($aUserGroup.icon_ext)}
			<div class="p_2"><img src="{param var='core.url_icon'}{$aUserGroup.icon_ext}" alt="{$aUserGroup.title|clean}" title="{$aUserGroup.title|clean}" /></div>
			{/if}
			{$aUserGroup.prefix}{$aUserGroup.title|convert|clean}{$aUserGroup.suffix}
		</div>
		<div class="extra_info">
			<div class="dashboard_info">{_p var='profile_views'}: {$sTotalUserViews}</div>
			<div class="dashboard_info">{_p var='last_login'}: {$sLastLogin}</div>
		</div>
	</div>
	<div class="js_core_dashboard dashboard_menu{if $sBlockLocation != 'sidebar'} dashboard_menu_width{/if}{if $sBlockLocation != 'sidebar'} go_left{/if}">
		{if isset($aDashboards.submit) && count($aDashboards.submit)}
		{img theme='misc/application_add.png' alt='' class='v_middle'} <b>{_p var='submit_links'}</b>
		<ul class="action">
		    {if isset($aDashboards.submit)}
			{foreach from=$aDashboards.submit item=aLink}
			<li><a href="{url link=$aLink.link}">{img theme=$aLink.image alt='' class='v_middle'} {$aLink.phrase}</a></li>
			{/foreach}
			{/if}
		</ul>
		{/if}
	</div>
	<div class="js_core_dashboard dashboard_menu{if $sBlockLocation != 'sidebar'} dashboard_menu_width{/if}{if $sBlockLocation != 'sidebar'} go_left{/if}">
		{if isset($aDashboards.edit) && count($aDashboards.edit)}
		{img theme='misc/application_edit.png' alt='' class='v_middle'} <b>{_p var='manage_links'}</b>
		<ul class="action">
		    {if isset($aDashboards.edit)}
			{foreach from=$aDashboards.edit item=aLink}
			<li><a href="{url link=$aLink.link}">{img theme=$aLink.image alt='' class='v_middle'} {$aLink.phrase}</a></li>
			{/foreach}
		    {/if}
		</ul>	
		{/if}	
	</div>	
	<div class="clear"></div>
{if !PHPFOX_IS_AJAX}
</div>
{/if}