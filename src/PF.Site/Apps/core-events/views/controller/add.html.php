<?php 
/**
 * [PHPFOX_HEADER]
 *
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{$sCreateJs}
<form class="form item-event-form-add" method="post" action="{url link='current'}" enctype="multipart/form-data" onsubmit="return startProcess({$sGetJsForm}, false);" id="js_event_form" >
{if !empty($sModule)}
	<div><input type="hidden" name="module" value="{$sModule|htmlspecialchars}" /></div>
{/if}
{if !empty($iItem)}
	<div><input type="hidden" name="item" value="{$iItem|htmlspecialchars}" /></div>
{/if}
{if $bIsEdit}
	<div><input type="hidden" name="id" value="{$aForms.event_id}" /></div>
{/if}
    <div><input type="hidden" name="val[current_tab]" value="" id="current_tab"></div>

	<div id="js_event_block_detail" class="js_event_block page_section_menu_holder" {if !empty($sActiveTab) && $sActiveTab != 'detail'}style="display:none;"{/if}>
        <div><input type="hidden" name="val[attachment]" class="js_attachment" value="{value type='input' id='attachment'}" /></div>
		<div class="form-group">
			{required}<label for="title">{_p var='event_name'}</label>
				<input type="text" name="val[title]" value="{value type='input' id='title'}" id="title" size="40" maxlength="100" class="form-control close_warning" />
		</div>

		<div class="form-group" style="width: 200px;">
				<label for="category">{_p var='category'}</label>
				{$sCategories}
		</div>

		<div class="form-group">
				<label for="description">{_p var='description'}</label>
				{editor id='description' rows='6'}
		</div>			
			
		<div class="form-group">
				<label>{_p var='start_time'}</label>
				<div style="position: relative;" class="js_event_select">
					{select_date prefix='start_' id='_start' start_year='current_year' end_year='+1' field_separator=' / ' field_order='MDY' default_all=true add_time=true start_hour='+1' time_separator='event.time_separator'}
				</div>
		</div>	
		
		<div class="form-group" id="js_event_add_end_time">
				<label>{_p var='end_time'}</label>
				<div style="position: relative;" class="js_event_select">
				{select_date prefix='end_' id='_end' start_year='current_year' end_year='+1' field_separator=' / ' field_order='MDY' default_all=true add_time=true start_hour='+4' time_separator='event.time_separator'}
				</div>
		</div>		

		<div class="form-group">
			{required}<label for="location">{_p var='location_venue'}</label>
				<input type="text" name="val[location]" value="{value type='input' id='location'}" id="location" size="40" maxlength="200" class="form-control" />
		</div>
		
		<div id="js_event_add_country" class="item-event-add-country">
			<div class="form-group item-event-address">
					<label for="street_address">{_p var='address'}</label>
					<input type="text" name="val[address]" value="{value type='input' id='address'}" id="address" size="30" maxlength="200" class="form-control" />
			</div>			 			 
				
			<div class="form-group item-event-city">
					<label for="city">{_p var='city'}</label>
					<input type="text" name="val[city]" value="{value type='input' id='city'}" id="city" size="20" maxlength="200" class="form-control" />
			</div>		
			
			<div class="form-group item-event-postal">
					<label for="postal_code">{_p var='zip_postal_code'}</label>
					<input type="text" name="val[postal_code]" value="{value type='input' id='postal_code'}" id="postal_code" size="10" maxlength="20" class="form-control" />
			</div>		
			 
			<div class="form-group item-event-country">
					<label for="country_iso">{_p var='country'}</label>
					{select_location}
					{module name='core.country-child'}
				</div>
			</div>
            {if !empty($aForms.current_image) && !empty($aForms.event_id)}
                {module name='core.upload-form' type='event' current_photo=$aForms.current_image id=$aForms.event_id}
            {else}
                {module name='core.upload-form' type='event' }
            {/if}
            {if empty($sModule) && Phpfox::isModule('privacy')}
            <div class="form-group-flow">
                    <label>{_p var='event_privacy'}</label>
                    {module name='privacy.form' privacy_name='privacy' privacy_info='event.control_who_can_see_this_event' privacy_no_custom=true default_privacy='event.display_on_profile'}       
            </div>
            {/if}
            <div class="">
                <input type="submit" value="{if $bIsEdit}{_p var='update'}{else}{_p var='submit'}{/if}" class="button btn-primary js_event_submit_form"/>
            </div>
    </div>

    <div id="js_event_block_invite" class="js_event_block page_section_menu_holder" {if empty($sActiveTab) || $sActiveTab != 'invite'}style="display:none;"{/if}>
        <div class="block">
            <div class="form-group">
                <label for="js_find_friend">{_p var='invite_friends'}</label>
                {if isset($aForms.event_id)}
                <div id="js_selected_friends" class="hide_it"></div>
                {module name='friend.search' input='invite' hide=true friend_item_id=$aForms.event_id friend_module_id='event' }
                {/if}
            </div>
            <div class="form-group invite-friend-by-email">
                <label for="emails">{_p var='invite_people_via_email'}</label>
                <input name="val[emails]" id="emails" class="form-control" data-component="tokenfield" data-type="email" >
                <p class="help-block">{_p var='separate_multiple_emails_with_a_comma'}</p>
            </div>
            <div class="form-group">
                <label for="personal_message">{_p var='add_a_personal_message'}</label>
                <textarea rows="1" name="val[personal_message]" id="personal_message" class="form-control textarea-auto-scale" placeholder="{_p var='write_message'}"></textarea>
            </div>
            <div class="form-group">
                <input type="submit" value="{_p var='send_invitations'}" class="btn btn-primary" name="invite_submit"/>
            </div>

        </div>
    </div>

    {if $bIsEdit}
	<div id="js_event_block_manage" class="js_event_block page_section_menu_holder" {if empty($sActiveTab) || $sActiveTab != 'manage'}style="display:none;"{/if}>
		{module name='event.list'}
	</div>
	{/if}
	
	{if $bIsEdit && Phpfox::getUserParam('event.can_mass_mail_own_members')}
        <div id="js_event_block_email" class="js_event_block page_section_menu_holder" {if empty($sActiveTab) || $sActiveTab != 'email'}style="display:none;"{/if}>
            <p class="help-block">
                {_p var='send_out_an_email_to_all_the_guests_that_are_joining_this_event'}
                {if isset($aForms.mass_email) && $aForms.mass_email}
                    <br />
                    {_p var='last_mass_email'}: {$aForms.mass_email|date:'mail.mail_time_stamp'}
                {/if}
            </p>

            <div class="mass-email-guests-block">
                <div id="js_send_email"{if !$bCanSendEmails} style="display:none;"{/if}>
                    <div class="form-group">
                        <label for="js_mass_email_subject">{_p var='subject'}</label>
                        <input type="text" name="val[mass_email_subject]" value="" size="30" id="js_mass_email_subject" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label for="js_mass_email_text">{_p var='text'}</label>
                        <textarea class="form-control" rows="8" name="val[mass_email_text]" id="js_mass_email_text"></textarea>
                    </div>
                </div>
            </div>
            <ul>
                <li><input type="button" value="{_p var='send'}" class="btn btn-primary" onclick="$('#js_event_mass_mail_li').show(); $.ajaxCall('event.massEmail', 'type=message&amp;id={$aForms.event_id}&amp;subject=' + $('#js_mass_email_subject').val() + '&amp;text=' + $('#js_mass_email_text').val()); return false;" /></li>
                <li id="js_event_mass_mail_li" style="display:none;">{img theme='ajax/add.gif' class='v_middle'} <span id="js_event_mass_mail_send">Sending mass email...</span></li>
            </ul>
            <div id="js_send_email_fail"{if $bCanSendEmails} style="display:none;"{/if}>
                <p class="help-block">
                    {_p var='you_are_unable_to_send_out_any_mass_emails_at_the_moment'}
                    <br />
                    {_p var='please_wait_till'}: <span id="js_time_left">{$iCanSendEmailsTime|date:'mail.mail_time_stamp'}</span>
                </p>
            </div>
        </div>
	{/if}
	
</form>
{section_menu_js}

<script type="text/javascript">
{literal}
	$Behavior.resetDatepicker = function(){
		$('.js_event_select .js_date_picker').datepicker('option', 'maxDate', '+1y');
	};
{/literal}
</script>
