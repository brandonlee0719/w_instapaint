<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

<div id="_privacy_holder_table" class="block">
	<form method="post" class="form" action="{url link='user.privacy'}">
        <div><input type="hidden" name="val[current_tab]" value="" id="current_tab"></div>
        {if Phpfox::getUserParam('user.hide_from_browse')}
		<div id="js_privacy_block_invisible" class="js_privacy_block page_section_menu_holder" {if empty($sActiveTab) || $sActiveTab != 'invisible'}style="display:none;"{/if}>
			<div class="privacy-block-headline">
			<svg enable-background="new 0 0 72 72" version="1.1" viewBox="0 0 72 72" xml:space="preserve" xmlns="http://www.w3.org/2000/svg">
			<path class="st0" d="m44.5 0.9-5.9 5.4c-1.3 1.2-3.4 1.2-4.7 0l-5.9-5.3c-1.8-1.6-4.7-1-5.6 1.2l-4.8 11.6h37.3l-4.8-11.7c-1-2.2-3.8-2.8-5.6-1.2z"/>
			<path class="st0" d="m6.5 24.5c-1.7 0.8-1 3.3 0.8 3.3h57.9c1.9 0 2.4-2.5 0.8-3.3l-7.1-3.7h-45.3l-7.1 3.7z"/>
			<path class="st0" d="m0.6 52.5 3.1 16.8c0.2 1.6 1.7 2.7 3.4 2.7h58.3c1.7 0 3.1-1.2 3.4-2.8l3.1-16.8c0.3-1.6-0.6-3.3-2.2-3.9-7.4-2.9-15.2-4.5-21.4-5.4-1.6-0.2-3.1 0.6-3.7 2l-5.9 13.3c-1 2.3-4.4 2.3-5.4-0.1l-5.7-13.2c-0.6-1.5-2.2-2.3-3.7-2.1-6.2 0.9-14 2.5-21.3 5.4-1.5 0.7-2.4 2.4-2 4.1z"/>
			</svg>
				<div class="privacy-block-headline-description">
					<h3>{_p var='Invisible Mode'}</h3>
					{_p var='invisible_mode_allows_you_to_browse_the_site_without_appearing_on_any_online_lists'}
				</div>
			</div>
			
			<div class="privacy-block-content">
				<div class="item-outer">
					<div class="form-group">
						<label>{_p var='enable_invisible_mode'}</label>
						<div class="item_is_active_holder">
							<span class="js_item_active item_is_active"><input value="1" name="val[invisible]" class="checkbox" type="radio"{if $aUserInfo.is_invisible} checked="checked"{/if} /> {_p var='yes'}</span>
							<span class="js_item_active item_is_not_active"><input value="0" name="val[invisible]" class="checkbox" type="radio"{if !$aUserInfo.is_invisible} checked="checked"{/if} /> {_p var='no'}</span>
						</div>
					</div>
				</div>
			</div>

			<div class="form-group-button mt-1">
				<input type="submit" value="{_p var='save_changes'}" class="btn btn-primary" />
			</div>	
		</div>
        {/if}
	
		{if Phpfox::getUserParam('user.can_control_profile_privacy')}
		<div id="js_privacy_block_profile" class="js_privacy_block page_section_menu_holder" {if empty($sActiveTab) || $sActiveTab != 'profile'}style="display:none;"{/if}>
			<div class="privacy-block-headline">
			<svg 
			version="1.1" viewBox="0 0 72 72" xml:space="preserve"
			xmlns="http://www.w3.org/2000/svg"
			xmlns:xlink="http://www.w3.org/1999/xlink">
			<path fill-rule="evenodd"  fill="rgb(30, 103, 170)"
			d="M52.350,72.000 C41.550,72.000 32.700,63.150 32.700,52.350 C32.700,51.565 32.748,50.792 32.839,50.030 L24.843,48.413 C23.130,53.492 18.277,57.150 12.750,57.150 C5.700,57.150 -0.000,51.450 -0.000,44.400 C-0.000,37.350 5.700,31.650 12.750,31.650 C15.028,31.650 17.164,32.246 19.013,33.289 L23.979,26.194 C20.773,23.478 18.750,19.417 18.750,14.850 C18.750,6.600 25.350,0.000 33.600,0.000 C41.850,0.000 48.450,6.600 48.450,14.850 C48.450,23.100 41.700,29.700 33.600,29.700 C30.921,29.700 28.417,29.004 26.256,27.781 L21.223,34.868 C23.848,37.202 25.500,40.605 25.500,44.400 C25.500,44.807 25.480,45.211 25.441,45.609 L33.373,47.269 C35.630,38.907 43.305,32.700 52.350,32.700 C63.150,32.700 72.000,41.550 72.000,52.350 C72.000,63.150 63.150,72.000 52.350,72.000 ZM26.850,18.150 C31.200,16.650 35.850,16.650 40.200,18.150 C41.389,18.626 42.200,19.574 42.485,20.694 C43.598,19.012 44.250,17.003 44.250,14.850 C44.250,9.000 39.450,4.200 33.600,4.200 C27.750,4.200 22.950,9.000 22.950,14.850 C22.950,16.931 23.560,18.877 24.606,20.524 C24.923,19.434 25.714,18.491 26.850,18.150 ZM12.750,34.650 C7.350,34.650 3.000,39.000 3.000,44.400 C3.000,46.511 3.667,48.460 4.800,50.053 L4.800,49.800 C4.800,48.600 5.700,47.400 6.900,46.950 C10.650,45.600 14.700,45.600 18.300,46.950 C19.650,47.400 20.400,48.450 20.400,49.800 L20.400,50.453 C21.714,48.792 22.500,46.692 22.500,44.400 C22.500,39.000 18.150,34.650 12.750,34.650 ZM52.350,37.050 C43.800,37.050 36.900,43.950 36.900,52.500 C36.900,55.570 37.789,58.427 39.326,60.828 C39.876,59.313 41.081,58.143 42.600,57.600 C48.900,55.350 55.800,55.350 62.100,57.600 C63.605,58.138 64.802,59.369 65.359,60.852 C66.905,58.445 67.800,55.580 67.800,52.500 C67.800,43.950 60.900,37.050 52.350,37.050 ZM52.350,53.100 C49.202,53.100 46.650,50.548 46.650,47.400 C46.650,44.252 49.202,41.700 52.350,41.700 C55.498,41.700 58.050,44.252 58.050,47.400 C58.050,50.548 55.498,53.100 52.350,53.100 ZM12.750,44.250 C10.927,44.250 9.450,42.773 9.450,40.950 C9.450,39.127 10.927,37.650 12.750,37.650 C14.573,37.650 16.050,39.127 16.050,40.950 C16.050,42.773 14.573,44.250 12.750,44.250 ZM33.600,7.350 C35.754,7.350 37.500,9.096 37.500,11.250 C37.500,13.404 35.754,15.150 33.600,15.150 C31.446,15.150 29.700,13.404 29.700,11.250 C29.700,9.096 31.446,7.350 33.600,7.350 Z"/>
			</svg>
				<div class="privacy-block-headline-description">
					<h3>{_p var='user_profile'}</h3>
					{_p var='customize_how_other_users_interact_with_your_profile'}
				</div>
			</div>

			<div class="privacy-block-content">
				{foreach from=$aProfiles item=aModules}
				{foreach from=$aModules key=sPrivacy item=aProfile}
					<div class="item-outer">
						{template file='user.block.privacy-profile'}
					</div>
				{/foreach}
				{/foreach}

				<div class="item-outer">
				<div class="form-group">
					<label for="title">{_p var='date_of_birth'}</label>
					<div class="select-option">
						<select class="form-control" name="val[special][dob_setting]">
							<option value="0"{if empty($aUserInfo.dob_setting)} selected="selected"{/if}>{_p var='select'}:</option>
							<option value="1"{if $aUserInfo.dob_setting == '1'} selected="selected"{/if}>{_p var='show_only_month_amp_day_in_my_profile'}</option>
							<option value="2"{if $aUserInfo.dob_setting == '2'} selected="selected"{/if}>{_p var='display_only_my_age'}</option>
							<option value="3"{if $aUserInfo.dob_setting == '3'} selected="selected"{/if}>{_p var='don_t_show_my_birthday_in_my_profile'}</option>
							<option value="4"{if $aUserInfo.dob_setting == '4'} selected="selected"{/if}>{_p var='show_my_full_birthday_in_my_profile'}</option>
						</select>
					</div>
				</div>
				</div>
			</div>

			<div class="form-group-button mt-1">
				<input type="submit" value="{_p var='save_changes'}" class="btn btn-primary" />
			</div>		
		</div>
		{/if}
		
		<div id="js_privacy_block_items" class="js_privacy_block page_section_menu_holder" {if empty($sActiveTab) || $sActiveTab != 'items'}style="display:none;"{/if}>
			<div class="privacy-block-headline">
				<svg id="js_privacy_block_profile-icon" data-name="js_privacy_block_profile-icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 476.98 489.16"><defs><style>.cls-1{l}fill:none;{r}.cls-2{l}clip-path:url(#clip-path);{r}</style><clipPath id="clip-path" transform="translate(0 0)"><rect class="cls-1" width="476.98" height="489.16"/></clipPath></defs><title>Vector Smart Object</title><g class="cls-2"><path d="M414.37,338.8c-15.1,6-43,19.4-57.2,27.2a1.43,1.43,0,0,0-.2.6,45.66,45.66,0,0,1,2.1,9.2,47.11,47.11,0,0,1,.1,7.5c1.3-1,2.7-2,4.1-3,13.7-9.6,41.8-24.9,57.8-32.9a153.23,153.23,0,0,1,15.3-6.8c-7-4.6-14.4-4.9-22-1.8" transform="translate(0 0)"/><path d="M384.77,187.9l-4.7-14.8a10,10,0,0,0-12.6-6.5l-14.1,4.5a95.74,95.74,0,0,0-25.1-29.9l6.8-13a9.76,9.76,0,0,0-4.2-13.3l-13.8-7.1a10.06,10.06,0,0,0-13.5,4.3l-6.8,13a93.72,93.72,0,0,0-38.9-3.3l-4.5-14.1a10,10,0,0,0-12.6-6.5L226,105.9a10,10,0,0,0-6.5,12.6l4.5,14.1a94,94,0,0,0-29.7,25.2L181,150.9a10.06,10.06,0,0,0-13.5,4.3l-7.1,13.8a10.06,10.06,0,0,0,4.3,13.5l13.4,6.9a93.91,93.91,0,0,0-3.2,38.6l-14.5,4.6a10,10,0,0,0-6.5,12.6l4.7,14.8a10,10,0,0,0,12.6,6.5l14.6-4.6a96.14,96.14,0,0,0,24.9,29.5l-7.1,13.6a10.06,10.06,0,0,0,4.3,13.5l13.8,7.1a10.06,10.06,0,0,0,13.5-4.3l7.1-13.6a95.24,95.24,0,0,0,38.5,3.3l4.6,14.6a10,10,0,0,0,12.6,6.5l14.8-4.7a10,10,0,0,0,6.5-12.6l-4.6-14.5a94.37,94.37,0,0,0,29.7-24.9l13.4,6.9a10.06,10.06,0,0,0,13.5-4.3l7.1-13.8a10.06,10.06,0,0,0-4.3-13.5l-13.3-6.9a95,95,0,0,0,3.4-38.8l14.1-4.5a10,10,0,0,0,6.5-12.6m-172.3-.6a63.88,63.88,0,0,1,75.1-31.9l-8.4,16.2a44.95,44.95,0,0,0-53.4,34l-19.6.8a63,63,0,0,1,6.3-19.1m76.4,38.2a21.58,21.58,0,1,1-9.2-29.1,21.55,21.55,0,0,1,9.2,29.1m-49,48.1a64.06,64.06,0,0,1-33.2-43.4l20.1-.9a45,45,0,0,0,56.3,29.3L294,275.8a63.48,63.48,0,0,1-54.1-2.2m86.4-27.4a64.9,64.9,0,0,1-12.4,16.7L303.07,246a44.49,44.49,0,0,0,6.7-9.6,45.2,45.2,0,0,0-9.5-53.9l8.4-16.3a64,64,0,0,1,17.6,80" transform="translate(0 0)"/><path d="M378,328.2c-9.3,3.7-30.2,10.5-39.5,15.4a46.06,46.06,0,0,1,10.6,9.1,18.75,18.75,0,0,1,1.8-1.2l6.8-3.7c10-5.5,31.1-12.9,42.1-17.8-6.9-4.6-14.3-4.8-21.8-1.8" transform="translate(0 0)"/><path d="M206.27,63.3l-9.8-.1a61.2,61.2,0,0,0-8.7-21.4l6.9-6.9a6.22,6.22,0,0,0,0-8.9l-7.5-7.6a6.22,6.22,0,0,0-8.9,0l-6.9,6.9a61.38,61.38,0,0,0-21.3-9l0.1-9.8a6.42,6.42,0,0,0-6.3-6.4L133.27,0a6.42,6.42,0,0,0-6.4,6.3l-0.1,9.8a61.2,61.2,0,0,0-21.4,8.7l-6.9-6.9a6.22,6.22,0,0,0-8.9,0L82,25.4a6.22,6.22,0,0,0,0,8.9l6.9,6.9a61.38,61.38,0,0,0-9,21.3l-9.8-.1a6.42,6.42,0,0,0-6.4,6.3l-0.1,10.6a6.42,6.42,0,0,0,6.3,6.4l9.8,0.1a61.2,61.2,0,0,0,8.7,21.4l-6.9,6.9a6.22,6.22,0,0,0,0,8.9l7.5,7.6a6.22,6.22,0,0,0,8.9,0l6.9-6.9a61.38,61.38,0,0,0,21.3,9l-0.1,9.8a6.42,6.42,0,0,0,6.3,6.4l10.6,0.1a6.42,6.42,0,0,0,6.4-6.3l0.1-9.8a61.2,61.2,0,0,0,21.4-8.7l6.9,6.9a6.22,6.22,0,0,0,8.9,0l7.6-7.5a6.22,6.22,0,0,0,0-8.9l-6.9-6.9a61.38,61.38,0,0,0,9-21.3l9.8,0.1a6.42,6.42,0,0,0,6.4-6.3l0.1-10.6a6.42,6.42,0,0,0-6.3-6.4M158,94.5a28.28,28.28,0,1,1,.2-40,28.28,28.28,0,0,1-.2,40" transform="translate(0 0)"/><path d="M459.17,352.2c-9.1-.7-28,9.6-33.1,12.2-18.4,9.3-43.9,23.8-60.7,35.3l-3.9,2.6a70.67,70.67,0,0,1-24.4,10c-48.8,10.4-119.8,11.5-119.8,11.5l-4.1.2a6.11,6.11,0,0,1-6.4-5,5.94,5.94,0,0,1,5.4-6.7l105.7-10.1a22.5,22.5,0,0,0,19.6-24.4c-1.3-12.2-12.6-21.2-24.2-19.9l-94-1.2a104.06,104.06,0,0,1-22.8-3.5c-60.6-17.7-94.7-1.1-112.9,6.6a21.86,21.86,0,0,0-20.3-10.5L22.77,352c-13.5.9-23.7,12.2-22.7,25.2l6.5,90c0.9,13,12.6,22.8,26.1,21.9l40.5-2.7a21.83,21.83,0,0,0,18.6-13l178,7.7c19.8,2.1,38.4-.3,55.9-9.5l5.1-2.7,134.6-78.7c6.6-3.8,11.3-10.5,11.6-18,0.3-9-4.9-19-17.8-20M47.47,467.9a13.31,13.31,0,1,1,13.8-13.3,13.6,13.6,0,0,1-13.8,13.3" transform="translate(0 0)"/><path d="M141.37,206.3l-6.6-1.7a40.33,40.33,0,0,0-2.1-15.9l5.9-3.4a4.4,4.4,0,0,0,1.6-6l-3.7-6.4a4.4,4.4,0,0,0-6-1.6l-5.9,3.4a40.1,40.1,0,0,0-12.8-9.8l1.7-6.6a4.47,4.47,0,0,0-3.1-5.4l-7.1-1.9a4.47,4.47,0,0,0-5.4,3.1l-1.7,6.6a40.33,40.33,0,0,0-15.9,2.1l-3.4-5.9a4.4,4.4,0,0,0-6-1.6l-6.4,3.7a4.4,4.4,0,0,0-1.6,6l3.4,5.9a40.1,40.1,0,0,0-9.8,12.8l-6.6-1.7a4.47,4.47,0,0,0-5.4,3.1l-1.9,7.1a4.47,4.47,0,0,0,3.1,5.4l6.6,1.7a40.33,40.33,0,0,0,2.1,15.9l-5.9,3.4a4.4,4.4,0,0,0-1.6,6l3.7,6.4a4.4,4.4,0,0,0,6,1.6l5.9-3.4a40.1,40.1,0,0,0,12.8,9.8l-1.7,6.6a4.47,4.47,0,0,0,3.1,5.4l7.1,1.9a4.47,4.47,0,0,0,5.4-3.1l1.7-6.6a40.33,40.33,0,0,0,15.9-2.1l3.4,5.9a4.4,4.4,0,0,0,6,1.6l6.4-3.7a4.4,4.4,0,0,0,1.6-6l-3.4-5.9a40.1,40.1,0,0,0,9.8-12.8l6.6,1.7a4.47,4.47,0,0,0,5.4-3.1l1.9-7.1a4.47,4.47,0,0,0-3.1-5.4m-28.7.7a19.71,19.71,0,1,1-14-24.1,19.73,19.73,0,0,1,14,24.1" transform="translate(0 0)"/></g></svg>
				<div class="privacy-block-headline-description">
					<h3>{_p var='user_app_sharing_items'}</h3>
					{_p var='customize_your_default_settings_for_when_sharing_new_items_on_the_site'}
				</div>
			</div>

			<div class="privacy-block-content">
				{foreach from=$aItems item=aModules}
				{foreach from=$aModules key=sPrivacy item=aItem}
				<div class="item-outer">
					{template file='user.block.privacy-item'}
				</div>
				{/foreach}
				{/foreach}
			</div>
				
			<div class="form-group-button mt-1">
				<input type="submit" value="{_p var='save_changes'}" class="btn btn-primary" />
			</div>			
		</div>
		
		{if Phpfox::getUserParam('user.can_control_notification_privacy') && count($aPrivacyNotifications)}
		<div id="js_privacy_block_notifications" class="js_privacy_block page_section_menu_holder" {if empty($sActiveTab) || $sActiveTab != 'notifications'}style="display:none;"{/if}>
			<div class="privacy-block-headline">
				<svg id="js_privacy_block_notifications_icon" data-name="js_privacy_block_notifications_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 505.69"><title>Vector Smart Object1</title><path d="M455.93,154H378a160.55,160.55,0,0,0-235.43,0H56.06A56.06,56.06,0,0,0,0,210.06V452.93A56.07,56.07,0,0,0,56.07,509H455.94A56.06,56.06,0,0,0,512,452.94V210.14A56.17,56.17,0,0,0,455.93,154Zm0,34a22,22,0,0,1,12.72,4l-53.06,29.36A159.93,159.93,0,0,0,402.29,188h53.65ZM132,335l0.42,0H388.53a17.17,17.17,0,0,0,.47,1.72L403.6,380H117l14.59-43.28A16.88,16.88,0,0,0,132,335ZM55.78,188H118a159.82,159.82,0,0,0-14.37,37.52L43.07,192A22,22,0,0,1,55.78,188ZM478,452.87h0A22.13,22.13,0,0,1,455.87,475H56.13A22.13,22.13,0,0,1,34,452.87h0V227.45l65,35.94c0,0.65,0,1.29,0,1.94v64L78,391.75a16.9,16.9,0,0,0,6.78,19.54A17.1,17.1,0,0,0,94.22,414H208.93a51.69,51.69,0,0,0,100.22,0H425.87a17.28,17.28,0,0,0,9.52-2.71,16.83,16.83,0,0,0,6.84-19.54L421,329.37v-64c0-2.19-.06-4.37-0.15-6.53L478,227.46V452.87Z" transform="translate(0 -3.31)"/><path d="M273,52.38V20.66c0-8.1-6.5-16-14.52-17.17A17,17,0,0,0,239,20.31V52.73a17,17,0,0,0,19.48,16.82C266.5,68.42,273,60.48,273,52.38Z" transform="translate(0 -3.31)"/><path d="M163.82,90.24a17,17,0,0,0,24-24L164.94,43.37a17,17,0,0,0-24,24Z" transform="translate(0 -3.31)"/><path d="M336.18,95.21a16.91,16.91,0,0,0,12-5L371,67.36a17,17,0,0,0-24-24L324.18,66.24A17,17,0,0,0,336.18,95.21Z" transform="translate(0 -3.31)"/></svg>
				<div class="privacy-block-headline-description">
					<h3>{_p var='user_notifications'}</h3>
				</div>
			</div>
			<div class="privacy-block-content">	
			{foreach from=$aPrivacyNotifications item=aModules}
			{foreach from=$aModules key=sNotification item=aNotification}
			<div class="item-outer">
				{template file='user.block.privacy-notification'}
			</div>
			{/foreach}
			{/foreach}
			</div>
			<div class="form-group-button mt-1">
				<input type="submit" value="{_p var='save_changes'}" class="btn btn-primary" />
			</div>
		</div>
		{/if}

		<div id="js_privacy_block_blocked" class="js_privacy_block page_section_menu_holder" {if empty($sActiveTab) || $sActiveTab != 'blocked'}style="display:none;"{/if}>
			<div class="privacy-block-headline">
				<svg id="js_privacy_block_blocked_icon" data-name="js_privacy_block_blocked_icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 23.89 24"><defs><style>.cls-1{l}fill:none;{r}.cls-2{l}clip-path:url(#clip-path);{r}.cls-3{l}fill:#010101;{r}</style><clipPath id="clip-path" transform="translate(0 0)"><rect class="cls-1" width="23.89" height="24"/></clipPath></defs><title>Vector Smart Object2</title><g class="cls-2"><path class="cls-3" d="M6.61,8.84c0.69,2,2,3.54,3.82,3.54s3.09-1.51,3.8-3.51c1.43-1.44,1.1-2.52.77-3C15.77,2.1,13.39,0,10.3,0,4.75,0,5.72,6.08,5.72,6.08h0c-0.25.57-.33,1.53,0.88,2.76M8.93,4.33L8.52,5.84a6.17,6.17,0,0,0,2.56-1.16A7.36,7.36,0,0,0,11.9,4a2.34,2.34,0,0,0,.38.5,3.88,3.88,0,0,0,.36-1.4,9.36,9.36,0,0,1,.62,1.25,3.22,3.22,0,0,0,.59,1.17c-0.07,2.89-1.36,5.9-3.42,5.9S7.26,8.79,7,6.1c0.53,0,1.88-1.77,1.88-1.77m11.46,6a3.5,3.5,0,1,0,3.5,3.5,3.5,3.5,0,0,0-3.5-3.5m-2.54,3.5a2.54,2.54,0,0,1,2.54-2.54,2.5,2.5,0,0,1,1.27.35L18.2,15.07a2.51,2.51,0,0,1-.35-1.27m2.54,2.54A2.51,2.51,0,0,1,19.12,16l3.46-3.46a2.52,2.52,0,0,1-2.19,3.81M9.64,13.88s-0.34.86-.33,0.87l0.53,0.76H11l0.53-.76-0.33-.87H9.64M20.39,18.3a4.44,4.44,0,0,1-4.3-5.78l-2.31-.85-0.37.9,0.78,0.29-2.84,9.37L11,15.94H9.88L9.53,22.12,6.72,12.85l0.75-.27-0.37-.9-2.76,1,0,0A8.29,8.29,0,0,0,.48,17,8.14,8.14,0,0,0,0,19.08,13.49,13.49,0,0,0,10.42,24h0l0.68,0h0a13.49,13.49,0,0,0,9.73-4.91,7.46,7.46,0,0,0-.14-0.81c-0.12,0-.23,0-0.35,0m-3.68,1.86a8.37,8.37,0,0,1-3,.55l-0.5,0V20.11a7,7,0,0,0,3.52-.56v0.61Z" transform="translate(0 0)"/></g></svg>
				<div class="privacy-block-headline-description">
					<h3>{_p var='user_blocked_user'}</h3>
					{_p var='check_the_boxes_to_unblock_users'}
				</div>
			</div>
			
			{if count($aBlockedUsers)}

			<div class="privacy-block-content">	
			{foreach from=$aBlockedUsers item=aBlockedUser name=blocked}
			<div class="item-outer">
				<div class="form-group">
					{$aBlockedUser|user}
                    <a role="button" id="unblock_user_{$aBlockedUser.block_user_id}" onclick="$.ajaxCall('user.unBlock', 'user_id={$aBlockedUser.block_user_id}&remove_button=true&notice=true')" class="btn btn-default btn-sm">{_p var='user_unblock'}</a>
				</div>
			</div>
			{/foreach}
			</div>

			{else}
			<div class="alert alert-info">
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