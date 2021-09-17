<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{if (isset($app_content))}
{$app_content}
{else}

{if $bCanViewPage}
    {if isset($aWidget) && $aWidget.text}
		<div class="block item_view_content">
			{$aWidget.text|parse}
		</div>
    {elseif $sCurrentModule == 'info' && !$iViewCommentId}
		<div class="block item_view_content">
            {if $aPage.text || $aPage.location_name}
                {if $aPage.text}<div class="info">{_p('description')}</div>{$aPage.text|parse}{/if}
                {if setting('core.google_api_key')}
                    {if $aPage.location_name}<div class="info">{_p('location')}: {$aPage.location_name}</div>{/if}
                    {if $aPage.location_latitude && $aPage.location_longitude}
                        <div id="js_location_view" data-app="core_pages" data-action="init_google_map" data-action-type="init" {if isset($sLat)}data-lat="{$sLat}"{/if} {if isset($sLng)}data-lng="{$sLng}"{/if} {if isset($sLocationName)}data-lname="{$sLocationName}"{/if}></div>
                    {/if}
                {/if}
            {else}
                {_p var='block_info_no_content'}
            {/if}
        </div>
	{elseif $sCurrentModule == 'pending'}
		{if isset($aPendingUsers) && count($aPendingUsers)}
			{foreach from=$aPendingUsers name=pendingusers item=aPendingUser}
				<div id="js_pages_user_entry_{$aPendingUser.signup_id}" class="user_rows">
					{if \Phpfox::getService('pages')->isAdmin($this->_aVars['aPage']['page_id'])}
					<div class="_moderator">
						<a href="#{$aPendingUser.signup_id}" class="moderate_link built" rel="pages"><i class="fa"></i></a>
					</div>
					{/if}
					<div class="user_rows_image">
						{img user=$aPendingUser suffix='_120_square' max_width='120' max_height='120'}
					</div>
					{$aPendingUser|user|shorten:50:'...'}
				</div>
			{/foreach}
			{moderation}
		{else}
		{/if}
	{else}
		{if $bHasPermToViewPageFeed}
			
		{else}
			{_p var='unable_to_view_this_section_due_to_privacy_settings'}
		{/if}
	{/if}
{else}
	<div class="message">
		{if isset($aPage.is_invited) && $aPage.is_invited}	
			{_p var='you_have_been_invited_to_join_this_community'}
		{else}
			{_p var='due_to_privacy_settings_this_page_is_not_visible'}
			{if $aPage.page_type == '1' && $aPage.reg_method == '2'}
				{_p var='this_page_is_also_invite_only'}
			{/if}
		{/if}
	</div>
{/if}

{/if}