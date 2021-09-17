<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="panel-default">
    <div class="panel-heading">
        <div class="panel-title">{_p var='group_details'}</div>
    </div>
    <div class="panel-body">
        <div {if $bIsEdit} style="display:none;"{/if} >
            {module name='admincp.product.form' class=true}
            {module name='admincp.module.form' class=true}
            <div class="form-group">
                <label for="val_type_id">{required}{_p var='location'}</label>
                <select name="val[type_id]" class="form-control type_id" id="val_type_id">
                    <option value="">{_p var='select'}:</option>
                    {foreach from=$aGroupTypes key=sVar item=sPhrase}
                    <option value="{$sVar}"{value type='select' id='type_id' default=$sVar}>{$sPhrase}</option>
                    {/foreach}
                </select>
            </div>
            <div class="form-group">
                <label for="val_user_group_id">{_p var='user_group'}</label>
                <select name="val[user_group_id]" id="val_user_group_id" class="form-control">
                    <option value="">{_p var='select'}:</option>
                    {foreach from=$aUserGroups key=iKey item=aGroup}
                    <option value="{$aGroup.user_group_id}" {if $bIsEdit && $aGroup.user_group_id == $aForms.user_group_id} selected="selected"{/if}>{$aGroup.title}</option>
                    {/foreach}
                </select>
                <div class="help-block">
                    {_p var='select_only_if_you_want_a_specific_user_group_to_have_special_custom_fields'}
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="group">{required}{_p var='group'}</label>
            {if $bIsEdit}
            {module name='language.admincp.form' type='text' id='group' value=$aForms.group}
            {else}
            {module name='language.admincp.form' type='text' id='group'}
            {/if}
        </div>
    </div>
</div>