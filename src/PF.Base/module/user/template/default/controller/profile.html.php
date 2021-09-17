<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="page_section_menu page_section_menu_header">
	<div>
	<ul class="nav nav-tabs nav-justified">
		<li class="active">
			<a href="#" class="js_custom_change_group" id="group_basic">{_p var='basic_information'}</a>
		</li>
		{if count($aGroups)}
			{foreach from=$aGroups name=groups item=aGroup}
				<li class="{if $phpfox.iteration.groups == count($aGroups) && Phpfox::isModule('music') && !Phpfox::getUserParam('music.can_upload_music_public')} last{/if}">
          <a href="#{$aGroup.group_id}" class="js_custom_change_group" id="group_{$aGroup.group_id}">{_p var=$aGroup.phrase_var_name}</a>
        </li>
			{/foreach}
		{else}
			<div class="main_break"></div>
		{/if}
	</ul>
	</div>
	<div class="clear"></div>
</div>

	<form method="post" class="form" action="{url link='user.profile'}"{if !$bIsEdit} onsubmit="{plugin call='user.template_controller_profile_form_onsubmit'} $Core.processing(); $(this).ajaxCall('custom.updateFields'); return false;"{/if}>
	{if isset($iUserId)}
		<div><input type="hidden" name="id" value="{$iUserId}" /></div>
	{/if}
<div class="js_custom_groups js_custom_group_basic">
	    <div class="form-group">
            <label for="country_iso">{_p var='location'}:</label>
            {select_location}
            {module name='core.country-child'}
		</div>

		<div class="form-group">
            <label for="city_location">{_p var='city'}:</label>
            <input class="form-control" type="text" name="val[city_location]" id="city_location" value="{value type='input' id='city_location'}" size="30" />
		</div>

		<div class="form-group">
            <label for="postal_code">{_p var='zip_postal_code'}:</label>
            <input class="form-control" type="text" name="val[postal_code]" id="postal_code" value="{value type='input' id='postal_code'}" size="10" />
		</div>

		<div class="separate"></div>
		{if Phpfox::getUserParam('user.can_edit_dob')}
		<div class="form-group">
			<label>{_p var='date_of_birth'}:</label>
            {select_date start_year=$sDobStart end_year=$sDobEnd field_separator='' field_order='MDY' bUseDatepicker=false sort_years='DESC'}
		</div>
		{/if}

		{if Phpfox::getUserParam('user.can_edit_gender_setting')}
		<div class="form-group">
            <label for="gender">{_p var='gender'}:</label>
            {select_gender}
		</div>
		{/if}

		{if Phpfox::getParam('user.enable_relationship_status') && Phpfox::getUserParam('custom.can_have_relationship') && isset($aRelations) && !empty($aRelations)}
		<div class="form-group">
			<label>{_p var='relationship_status'}</label>
				<select class="form-control" name="val[relation]" id="relation" onchange="$Behavior.displayRelationshipChange()">
					{foreach from=$aRelations item=aRelation}
						<option value="{$aRelation.relation_id}" {if isset($aForms.relation_id) && $aForms.relation_id == $aRelation.relation_id} selected="selected"{/if}>{_p var=$aRelation.phrase_var_name} </option>
					{/foreach}
				</select>

				<script type="text/javascript">
					var aRelationshipChange = {$sJsArray};

					{if isset($aForms.relation_id)}
						$Behavior.setRelationship = function(){l} $('#relation').val({$aForms.relation_id}); $Behavior.setRelationship = function(){l}{r} {r}
					{/if}
				</script>

				<span id="relation_with">
						<div class="edit_friend_relation">
                            <span id="js_custom_search_friend"></span>
                            <span id="sFriendImage">
                                {if isset($aForms.with_user) &&  !empty($aForms.with_user) && $aForms.with_user.with_user_id > 0}
                                    {img user=$aForms.with_user suffix='_50_square' max_width=30 max_height=30}
                                {/if}
                            </span>
                            {if isset($aForms.with_user.user_id) && !empty($aForms.with_user.user_id)}
                                <input type="hidden" id="relation_with_input_hidden" name="val[relation_with]" value="{$aForms.with_user.user_id}">
                            {/if}
                            <div id="js_custom_search_friend_placement"></div>

                            <input type="hidden" name="val[previous_relation_with]" value="{if isset($aForms.with_user.user_id)}{$aForms.with_user.user_id}{else}0{/if}">
                            <input type="hidden" name="val[previous_relation_type]" value="{if isset($aForms.relation_id)}{$aForms.relation_id}{else}0{/if}">
                            {if isset($aForms.with_user.status_id) && $aForms.with_user.status_id == 1}
                            <div class="message">{_p var='pending_confirmation'}</div>
                            {/if}
						</div>
						{literal}
						<script type="text/javascript">
						$Behavior.profileSearchFriends = function()
						{
							$Core.searchFriends({
								'id': '#js_custom_search_friend',
								'placement': '#js_custom_search_friend_placement',
								'width': '300px',
								'max_search': 10,
								'input_name': 'friends',
								'default_value': {/literal}{if isset($aForms.with_user) && $aForms.with_user.with_user_id > 0}'{$aForms.with_user.full_name}' {else} '{_p var='search_friends_by_their_name'}'{/if}{literal},
								'search_input_id' : 'sFriendInput',
								'onclick': function()
								{
									return false;
								},
								'onBeforePrepend' : function()
								{
									$('#sFriendInput').val($Core.searchFriendsInput.aFoundUser['full_name']);
									$Core.searchFriendsInput.sHtml = '';
									if ($('#sFriendImage').length < 1)
									{
										$('#sFriendInput').before('<span id="sFriendImage"></span>');
									}
									$('#sFriendImage').html('' + $Core.searchFriendsInput.aFoundUser['user_image'] + '');
									$('#js_custom_search_friend_placement').hide();

									if ($('#relation_with_input_hidden').length > 0)
									{
										$('#relation_with_input_hidden').remove();
									}

									if ($('#relation_with_input_hidden').length < 1)
									{
										$('#sFriendImage').after('<input type="hidden" id="relation_with_input_hidden" name="val[relation_with]" value="' + $Core.searchFriendsInput.aFoundUser['user_id'] + '">');
									}
									else
									{
										$('#relation_with_input_hidden').val($Core.searchFriendsInput.aFoundUser['user_id']);
									}
								}
							});
						};
						</script>
						{/literal}
				</span>
			</div>
		{/if}
</div>

	{if count($aSettings)}
		{foreach from=$aSettings item=aSetting}
			<div class="form-group js_custom_groups js_custom_group_{$aSetting.group_id}" style="display:none;">
				<label class="table_left">
                {if $aSetting.is_required}{required}{/if}
				{_p var=$aSetting.phrase_var_name}:
				</label>
                {template file='custom.block.form'}
			</div>
		{/foreach}
	{else}
		<p class="help-block">
			{if Phpfox::getUserParam('custom.can_add_custom_fields')}
				{_p var='no_custom_fields_have_been_added'}
				<ul class="action">
					<li><a href="{url link='admincp.custom.add'}">{_p var='add_a_new_custom_field'}</a></li>
				</ul>
			{/if}
		</p>
	{/if}
	{plugin call='user.template_controller_profile_form'}
	<div class="from-group">
		<input type="submit" value="{_p var='update'}" class="btn btn-primary" id="js_custom_submit_button"> <span id="js_custom_update_info"></span>
	</div>
</form>