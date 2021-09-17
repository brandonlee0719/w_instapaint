<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

{$sCreateJs}
<form method="post" class="form" action="{url link="admincp.plugin.hook.add"}" id="js_form" onsubmit="{$sGetJsForm}">
	{if $bIsEdit}
	<div><input type="hidden" name="id" value="{$aForms.product_id}" /></div>
	{/if}

    <div class="panel panel-default">
        <div class="panel-body">
            {_p var='hook_details'}
        </div>
        <div class="panel-footer">
            {if Phpfox::getUserParam('admincp.can_view_product_options')}
            <div class="form-group">
                <label for="product_id">{_p var='product'}</label>
                <select name="val[product_id]" id="product_id" class="form-control">
                    {foreach from=$aProducts item=aProduct}
                    <option value="{$aProduct.product_id}"{value type='select' id='product_id' default=$aProduct.product_id}>{$aProduct.title}</option>
                    {/foreach}
                </select>
            </div>
            {/if}
            <div class="form-group">
                <label for="module_id">{_p var='module'}</label>

                <select name="val[module_id]" id="module_id" class="form-control">
                    <option value="">{_p var='select'}:</option>
                    {foreach from=$aModules key=sModule item=iModuleId}
                    <option value="{$iModuleId}"{value type='select' id='module_id' default=$iModuleId}>{translate var=$sModule prefix='module'}</option>
                    {/foreach}
                </select>
            </div>
            <div class="form-group">
                <label>{_p var='type'}</label>
                <select name="val[hook_type]" id="hook_type" class="form-control">
                    <option value="">{_p var='select'}:</option>
                    {foreach from=$aHookTypes item=sHookType}
                    <option value="{$sHookType}"{value type='select' id='hook_type' default=$sHookType}>{$sHookType}</option>
                    {/foreach}
                </select>
            </div>
            <div class="form-group">
                <label for="call_name">{_p var='call'}</label>
                <input class="form-control" id="call_name" type="text" name="val[call_name]" value="{value type='input' id='call_name'}" size="30" />
            </div>

            <div class="form-group">
                <label>{_p var='active'}</label>
                <div class="radio">
                    <label><input type="radio" name="val[is_active]" style="vertical-align:bottom;" value="1" {value type='radio' id='is_active' default='1' selected=true} />{_p var='yes'}</label>
                </div>
                <div class="radio">
                    <label><input type="radio" name="val[is_active]" style="vertical-align:bottom;" value="0" {value type='radio' id='is_active' default='0'} />{_p var='no'}</label>
                </div>
            </div>

            <div class="form-group">
                <input type="submit" value="{_p var='save'}" class="btn btn-primary" />
            </div>
        </div>
    </div>
</form>