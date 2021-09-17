<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

<form method="post" class="form" action="{url link='admincp.user.add'}" enctype="multipart/form-data">
{if $bIsEdit}
	<div><input type="hidden" name="id" value="{$iFormUserId}" id="js_user_id" /></div>
{/if}

	{foreach from=$aEditForm item=aEditForm}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">{$aEditForm.title}</div>
        </div>
        <div class="panel-body">
            {foreach from=$aEditForm.data item=aData}
            <div class="form-group">
                <label for="title">{if isset($aData.required) && $aData.required}{required}{/if}{$aData.title}</label>
                {if $aData.type == 'input:text'}
                <input class="form-control" type="text" name="val[{$aData.id}]" size="30" value="{$aData.value|clean}" />
                {elseif $aData.type == 'input:text:check' || $aData.type == 'input:password:check'}
                <input class="form-control" type="{if $aData.type == 'input:password:check'}password{else}text{/if}" name="val[{$aData.id}]" size="30" value="{$aData.value|clean}" />
                {if $bIsEdit}
                <p class="help-block">
                    <label><input type="checkbox" name="val[{$aData.id}_check]" value="1" class="v_middle" /> {_p var='check_the_box_to_confirm_that_you_want_to_edit_this_field'}</label>
                </p>
                {/if}
                {elseif $aData.type == 'input:textarea'}
                <textarea class="form-control" name="val[{$aData.id}]">{$aData.value|clean}</textarea>
                {if $bIsEdit}
                <p class="help-block">
                    <label><input type="checkbox" name="val[{$aData.id}_check]" value="1" class="v_middle" /> {_p var='check_the_box_to_confirm_that_you_want_to_edit_this_field'}</label>
                </div>
                {/if}
                {elseif $aData.type == 'date_of_birth'}
                {select_date start_year=$sDobStart end_year=$sDobEnd field_separator=' / ' field_order='MDY'}
                {elseif $aData.type == 'select'}
                {if $aData.id == 'user_group_id' && !Phpfox::getUserParam('user.can_edit_user_group_membership')}
                <div><input type="hidden" name="val[{$aData.id}]" value="{$aData.value}" /></div>
                {foreach from=$aData.options key=sOptionValue item=sOptionTitle}
                {if $sOptionValue == $aData.value}
                {$sOptionTitle}
                {/if}
                {/foreach}
                {else}
                <select class="form-control" name="val[{$aData.id}]" id="{$aData.id}">
                    <option value="">{_p var='select'}:</option>
                    {foreach from=$aData.options key=sOptionValue item=sOptionTitle}
                    <option value="{$sOptionValue}"{if $sOptionValue == $aData.value} selected="selected"{/if}>{$sOptionTitle}</option>
                    {/foreach}
                </select>
                {/if}
                {if $aData.id == 'country_iso'}
                {module name='core.country-child' country_child_value=$aUser.country_iso country_child_id=$aUser.country_child_id country_not_user=true}
                {/if}
                {/if}
            </div>
            {/foreach}
        </div>
    </div>
	{/foreach}


    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">{_p var='profile_picture'}</div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>{_p var='photo'}</label>
                {if isset($aUser.user_image) && $aUser.user_image}
                <div id="js_user_photo_{$aUser.user_id}" class="js_user_photo">
                    {img user=$aUser max_width='100' max_height='100' suffix='_100_square' thickbox=true}
                    <a role="button" class="btn btn-danger" data-cmd="admincp.remove_user_image" data-user-id="{$aUser.user_id}"><i class="fa fa-trash"></i></a>
                </div>
                {/if}
                <div style="margin-top: 5px">
                    <input type="file" name="image" accept="image/*" />
                    <p class="help-block">{_p var='you_can_upload_a_jpg_gif_or_png_file'}</p>
                </div>
            </div>
        </div>
    </div>

	{if Phpfox::getUserParam('user.can_edit_other_user_privacy')}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">{_p var='profile_privacy'}</div>
        </div>
        <div class="panel-body">
            {foreach from=$aProfiles item=aModules}
            {foreach from=$aModules key=sPrivacy item=aProfile}
            {template file='user.block.privacy-profile'}
            {/foreach}
            {/foreach}
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">{_p var='notification'}</div>
        </div>
        <div class="panel-body">
            {foreach from=$aPrivacyNotifications item=aModules}
            {foreach from=$aModules key=sNotification item=aNotification}
            {template file='user.block.privacy-notification'}
            {/foreach}
            {/foreach}
        </div>
    </div>
	{/if}


	{if !empty($aSettings)}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">{_p var='custom_fields'}</div>
        </div>
        <div class="panel-body">
            <div id="js_custom_field_holder">
                {template file='user.block.custom'}
            </div>
        </div>
    </div>
	{/if}

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">{_p var='activity_points'}</div>
        </div>
        <div class="panel-body">
            {foreach from=$aActivityPoints key=sActivityKeyName item=aActivityPoint}
                {foreach from=$aActivityPoint key=sActivityPhrase item=iActivityCount}
            <div class="form-group">
                <label>{$sActivityPhrase}</label>
                <input class="form-control" type="text" name="val[activity][{$sActivityKeyName}]" value="{$iActivityCount}" />
            </div>
                {/foreach}
            {/foreach}
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-footer">
            <input type="submit" value="{_p var='update'}" class="btn btn-primary" />
        </div>
    </div>
</form>