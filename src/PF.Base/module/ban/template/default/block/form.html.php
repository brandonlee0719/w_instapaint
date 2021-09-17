<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{if !isset($bShow) || $bShow == false}
<div class="form-group">
    <label for="is_active">{_p var='ban_user'}:</label>
    <div class="item_is_active_holder">
        <span class="js_item_active item_is_active" style="position: relative; margin:0;float:left;display:inline;" onclick="$('#showBanForm').show();">
            <input type="radio" name="aBan[bShow]" value="1" {value type='radio' id='is_active' default='1'}/>
                   {_p var='yes'}
        </span>
        <span class="js_item_active item_is_not_active" onclick="$('#showBanForm').hide();">
            <input type="radio" name="aBan[bShow]" value="0" {value type='radio' id='is_active' default='0' selected='true' }/>
                   {_p var='no'}
        </span>
    </div>
</div>
<div id="showBanForm" style="display: none;">
{else}
	<div id="showBanForm">
{/if}

	<div class="form-group">
		<label for="val_ban_reason">{_p var='reason'}</label>
        <textarea name="aBan[reason]" rows="3" id="val_ban_reason" class="form-control"></textarea>
        <div class="help-block">
            {_p var='phrase_variable_when_banning_explanation'}
        </div>
	</div>

	<div class="table form-group">
		<label for="val_days_banned" class="control-label">{_p var='ban_for_how_many_days'}</label>
        <input id="val_days_banned" type="text" name="aBan[days_banned]" value="0" class="form-control">
        <div class="help-block">
            {_p var='0_means_indefinite'}
        </div>
	</div>

	<div class="table form-group">
        <label>{_p var='user_groups'}</label>

        <select name="aBan[return_user_group]" class="form-control" id="val_return_user_group">
            {foreach from=$aUserGroups item=aGroup}
            <option value="{$aGroup.user_group_id}">{$aGroup.title|convert}</option>
            {/foreach}
        </select>
        <div class="help-block">
            {_p var='user_group_to_move_the_user_when_the_ban_expires'}
        </div>
	</div>


	<div class="form-group" {if isset($bHideAffected) && $bHideAffected == true}style="display:none;"{/if}>
		<label>User groups affected</label>
        {foreach from=$aUserGroups item=aGroup}
        <div class="checkbox">
            <label><input type="checkbox" name="aBan[user_groups_affected][]" value="{$aGroup.user_group_id}">{$aGroup.title}</label>
        </div>
        {/foreach}
	</div>
</div>