<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

{if !isset($aForms)}
<form method="post" action="{url link='admincp.user.group.add'}" enctype="multipart/form-data">
    {if isset($bHideApp)}
    <div><input type="hidden" name="hide_app" value="{$bHideApp}" /></div>
    {/if}
	{template file='user.block.admincp.entry'}
	<div class="form-group">
		<label>{_p var='inherit'}</label>
        <select name="val[inherit_id]" class="form-control">
        {foreach from=$aGroups key=iKey item=aGroup}
            <option value="{$aGroup.user_group_id}" {if $aGroup.user_group_id == 2} selected="selected"{/if}>{$aGroup.title|convert}</option>
        {/foreach}
        </select>
	</div>
	<div class="form-group">
		<input type="submit" value="{_p var='add_user_group'}" class="btn btn-primary" />
	</div>
</form>
{else}
{if $aForms.user_group_id == GUEST_USER_ID}
	{module name='help.info' phrase='admincp.not_allowed_for_guests'}
{/if}
{if !$bEditSettings}
<form method="post" class="form" action="{url link='admincp.user.group.add' group_id=$aForms.user_group_id}" enctype="multipart/form-data">
	<div><input type="hidden" name="id" value="{$aForms.user_group_id}" /></div>
    <div><input type="hidden" name="hide_app" value="{$bHideApp}" /></div>
	{template file='user.block.admincp.entry'}
	<div class="form-group">
		<input type="submit" value="{_p var='submit'}" class="btn btn-primary" />
	</div>
</form>
{else}
<form method="get" data-url="{url link='admincp.user.group.add'}">
<input type="hidden" name="setting" value="1" />
<div><input type="hidden" name="hide_app" value="{$bHideApp}" /></div>
<div class="panel panel-default">
    <div class="panel-body row">
        {if !$bHideApp}
        <div class="form-group col-sm-3">
            <label for="val_group_id">{_p var='apps'}</label>
            <select name="module" class="form-control" id="val_group_id" onchange="this.form.submit()">
                {foreach from=$aModules item=aModule}
                <option {if $aModule.module_id == $sModule}selected{/if} value="{$aModule.module_id}">{_p var=$aModule.title'}</option>
                {/foreach}
            </select>
        </div>
        {else}
        <input type="hidden" name="module-id" value="{$sModule}" />
        <input type="hidden" name="module" value="{$sModule}" />
        {/if}
        {if isset($sAppId)}
        <input type="hidden" name="id" value="{$sAppId}" />
        {/if}
        <div class="form-group col-sm-3">
            <label for="val_group_id">{_p var='groups'}</label>
            <select name="group_id" class="form-control" id="val_group_id" onchange="onChangeUserGroupSettings(this)">
                {foreach from=$aGroups item=aGroup}
                <option {if $aGroup.user_group_id == $iGroupId} selected {/if} value="{$aGroup.user_group_id}">{$aGroup.title}</option>
                {/foreach}
            </select>
        </div>
    </div>
</div>
</form>

<form method="post" class="form user-group-settings">
	<input type="hidden" name="id" value="{$aForms.user_group_id}" />
    <div><input type="hidden" name="hide_app" value="{$bHideApp}" /></div>
    {template file='user.block.admincp.setting'}
</form>	
{/if}
{/if}