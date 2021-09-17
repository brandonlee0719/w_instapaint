<?php
defined('PHPFOX') or exit('NO DICE!');
?>

	<div id="js_register_step2">
		{plugin call='user.template_default_block_register_step2_6'}
	{if !isset($bIsPosted) && Phpfox::getParam('user.multi_step_registration_form')}
		<div class="p_bottom_10">{_p var='complete_this_step_to_setup_your_personal_profile'}</div>
	{/if}

	{if Phpfox::getParam('core.registration_enable_dob')}
		<div class="table form-group">
			<label class="required">{_p var='birthday'}</label>
            {select_date start_year=$sDobStart end_year=$sDobEnd field_separator=' / ' field_order='MDY' bUseDatepicker=false sort_years='DESC'}
		</div>
	{/if}

	{if Phpfox::getParam('core.registration_enable_gender')}
		<div class="form-group">
            <label for="gender" class="required">{_p var='i_am'}</label>
            {select_gender}
		</div>
	{/if}

	{if Phpfox::getParam('core.registration_enable_location')}
		<div class="form-group">
            <label for="country_iso" class="required">{_p var='location'}:</label>
            {select_location}
            {module name='core.country-child' country_force_div=true}
		</div>
	{/if}

	{if Phpfox::getParam('core.city_in_registration')}
		<div class="form-group">
            <label for="city_location">{_p var='city'}</label>
            <input class="form-control" type="text" name="val[city_location]" id="city_location" value="{value type='input' id='city_location'}" size="30" />
		</div>
	{/if}
	
	{if Phpfox::getParam('core.registration_enable_timezone')}
		<div class="form-group">
			<label class="table_left">{_p var='time_zone'}</label>
            <select class="form-control" name="val[time_zone]">
            {foreach from=$aTimeZones key=sTimeZoneKey item=sTimeZone}
                <option value="{$sTimeZoneKey}"{if (Phpfox::getTimeZone() == $sTimeZoneKey && !isset($iTimeZonePosted)) || (isset($iTimeZonePosted) && $iTimeZonePosted == $sTimeZoneKey) || (Phpfox::getParam('core.default_time_zone_offset') == $sTimeZoneKey)} selected="selected"{/if}>{$sTimeZone}</option>
            {/foreach}
            </select>
		</div>
	{/if}
		{plugin call='user.template_default_block_register_step2_7'}
		{template file='user.block.custom'}
		{plugin call='user.template_default_block_register_step2_8'}
	</div>
	
	{module name='user.showspamquestion'}
	
	{if Phpfox::getParam('user.force_user_to_upload_on_sign_up')}
		<div class="separate"></div>
		<div class="form-group">
			<label class="required">{_p var='profile_image'}</label>
            <input type="file" name="image" accept="image/*"/>
            <p class="help-block">
                {_p var='you_can_upload_a_jpg_gif_or_png_file'}
            </p>
		</div>
	{/if}

{plugin call='user.template_controller_register_pre_captcha'}
{if Phpfox::isModule('captcha') && Phpfox::getParam('user.captcha_on_signup')}
<div id="js_register_capthca_image"{if Phpfox::getParam('user.multi_step_registration_form') && !isset($bIsPosted)} style="display:none;"{/if}>
{module name='captcha.form'}
</div>
{/if}