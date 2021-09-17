<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

<form method="post" action="#" class="form" onsubmit="$('#js_updating_basic_info').hide(); $('#js_updating_basic_info_load').html($.ajaxProcess('{_p var='updating' phpfox_squote=true}')).show(); $(this).ajaxCall('user.updateAccountSettings'); return false;">
	{if Phpfox::getUserParam('user.can_edit_dob')}
    <div class="form-group">
        <label for="date_of_birth">{required}{_p var='date_of_birth'}</label>
        {select_date start_year='1900' end_year='2008' field_separator=' <div style="margin-top:5px;"></div> ' field_order='MDY'}
    </div>

	<div class="separate" style="margin-top:5px;"></div>
	{/if}

	{if Phpfox::getUserParam('user.can_edit_gender_setting')}
    <div class="form-group">
        <label for="gender">{required}{_p var='gender'}</label>
        {select_gender}
    </div>
	{/if}
    <div class="form-group">
        <label for="country_iso">{required}{_p var='location'}</label>
        {select_location style='width:100px;'}
    </div>

	{foreach from=$aSettings item=aSetting}
        <div class="form-group">
            <label>{_p var=$aSetting.phrase_var_name}</label>
            {template file='custom.block.form'}
        </div>
	{/foreach}
	
	{plugin call='user.template_block_setting_form'}
	
	<div class="form-group" id="js_updating_basic_info" style="margin-top:8px;">
		<input type="submit" value="{_p var='update'}" class="btn btn-primary" />
			- <a href="#" onclick="$('#js_basic_info_data').show(); $('#js_basic_info_form').hide(); return false;">{_p var='cancel'}</a>
			- <a href="{url link='user.setting'}">{_p var='go_advanced'}</a>
	</div>

	<div id="js_updating_basic_info_load" style="margin-top:8px; display:none;">
	
	</div>
</form>