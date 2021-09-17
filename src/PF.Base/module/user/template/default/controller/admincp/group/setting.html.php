<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

{if $sCacheSetting}
<div class="p_4">
	<div class="p_4">	
		<div class="go_left t_right" style="width:34px;"><b>{_p var='php'}</b>:</div>
		<div><input type="text" name="php" value="Phpfox::getUserParam('{$sCacheSetting}')" size="40" onclick="this.select();" /></div>
		<div class="clear"></div>
	</div>
</div>
{/if}
{$sCreateJs}
<form method="post" action="{url link="admincp.user.group.setting"}" id="js_form" onsubmit="{$sGetJsForm}">
{token}
{if isset($aForms.setting_id)}
<div><input type="hidden" name="id" value="{$aForms.setting_id}" /></div>
{/if}
{if $iGroupId}
<div><input type="hidden" name="gid" value="{$iGroupId}" /></div>
{/if}

<div class="panel panel-default">
    <div class="panel-body">
        {_p var='setting_details'}
    </div>
    <div class="panel-footer">
        <div class="form-group">
            <label>{_p var='product'}</label>
            <select name="val[product_id]" class="form-control">
                {foreach from=$aProducts item=aProduct}
                <option value="{$aProduct.product_id}"{value type='select' id='product_id' default=$aProduct.product_id}>{$aProduct.title}</option>
                {/foreach}
            </select>
            {help var='admincp.user_add_setting_product'}
        </div>
        <div class="form-group">
            <label>{_p var='module'}</label>
            <select name="val[module]" class="form-control">
                {foreach from=$aModules key=sModule item=iModuleId}
                <option value="{$iModuleId}|{$sModule}"{value type='select' id='module' default=''$iModuleId'|'$sModule''}>{$sModule}</option>
                {/foreach}
            </select>
            {help var='admincp.user_add_setting_module'}
        </div>
        <div class="form-group">
            <label>{_p var='varname'}</label>
            <input class="form-control" type="text" name="val[name]" value="{value type='input' id='name'}" size="40" id="name" maxlength="250" />
            {help var='admincp.user_add_setting_name'}
        </div>
        <div class="form-group">
            <label>{_p var='type'}</label>
            <select name="val[type]" class="form-control">
                {foreach from=$aTypes item=sType}
                <option value="{$sType}"{value type='select' id='type' default=$sType}>{$sType}</option>
                {/foreach}
            </select>
            {help var='admincp.user_add_setting_type'}
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-body">
        {_p var='user_group_values'}
    </div>
    <div class="panel-footer">
        {foreach from=$aUserGroups item=aUserGroup}
        <div class="form-group">
            <label>{$aUserGroup.title|convert|clean}</label>
            <input type="text" class="form-control" name="val[user_group][{$aUserGroup.user_group_id}]" value="{if isset($aUserGroup.value)}{$aUserGroup.value}{/if}" size="40" />
            {help var='admincp.user_add_setting_value'}
        </div>
        {/foreach}
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        {_p var='language_package_details'}
    </div>
    <div class="panel-body">
        <div class="form-group">
            <label>{_p var='info'}</label>
            {foreach from=$aLanguages item=aLanguage}
            <b>{$aLanguage.title}</b>
            <div class="p_4">
                <textarea class="form-control" cols="50" rows="5" name="val[text][{$aLanguage.language_id}]">{if isset($aLanguage.text)}{$aLanguage.text}{/if}</textarea>
            </div>
            {/foreach}
            {help var='admincp.user_add_setting_info'}
        </div>
        <div class="form-group">
            <input type="submit" value="{_p var='submit'}" class="btn btn-primary" />
        </div>
    </div>
</div>
</form>