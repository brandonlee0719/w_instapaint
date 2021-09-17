<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{literal}
<script type="text/javascript">
<!--
function changeFormValue(sValue)
{
	switch(sValue)
	{
		{/literal}
		case 'boolean':
			sHtml = '<select class="form-control" name="val[value]" id="value"><option value="1" {value type='select' id='value' default='1'}>{_p var='true' phpfox_squote=true}</option><option value="0" {value type='select' id='value' default='0'}>{_p var='false' phpfox_squote=true}</option></select>';
			break;
		case 'password':
			sHtml = '<input type="password" class="form-control"name="val[value]" value="{value type='input' id='value'}" size="40" id="value" autocomplete="off"/>';
			break;
		case 'array':
			sHtml = '<textarea class="form-control" cols="50" rows="8" name="val[value]" id="value">{value type='textarea' id='value'}</textarea>';
			sHtml += '<div class="p_4">{_p var='setting_array_example' phpfox_squote=true}</div>';
			break;
		case 'drop':
			sHtml = '<textarea class="form-control" cols="50" rows="8" name="val[value]" id="value">{value type='textarea' id='value'}</textarea>';
			sHtml += '<div class="p_4">{_p var='setting_drop_down_example' phpfox_squote=true}</div>';
			break;
		{plugin call='admincp.template_controller_setting_add_js_form_value'}
		case 'large_string':
			sHtml = '<textarea class="form-control" cols="50" rows="8" name="val[value]" id="value">{value type='textarea' id='value'}</textarea>';
			break;
		default:
			sHtml = '<input class="form-control"type="text" name="val[value]" value="{value type='input' id='value'}" size="40" id="value" />';
			break;
		{literal}
	}
	$('#js_form_value').html(sHtml);
}
-->
</script>
{/literal}
{$sCreateJs}
<form method="post" class="form" action="{url link="admincp.setting.add"}" id="js_setting_form" onsubmit="{$sGetJsForm}">
	{if $bEdit}
	<div><input type="hidden" name="id" value="{$aForms.setting_id}" /></div>
	<div><input type="hidden" name="val[var_name]" value="{$aForms.var_name}" /></div>
	{/if}


    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">{_p var='setting_details'}</div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>{_p var='product'}</label>
                <select name="val[product_id]" class="form-control">
                    {foreach from=$aProducts item=aProduct}
                    <option value="{$aProduct.product_id}">{$aProduct.title}</option>
                    {/foreach}
                </select>
                {help var='admincp.setting_add_product'}
            </div>
            <div class="form-group">
                <label>{_p var='module'}</label>
                <select name="val[module_id]" class="form-control">
                    {foreach from=$aModules key=sModule item=iModuleId}
                    <option value="{$iModuleId}" {value type='select' id='module_id' default=$iModuleId}>{$sModule}</option>
                    {/foreach}
                </select>
                {help var='admincp.setting_add_module_id'}
            </div>
            <div class="form-group">
                <label>{_p var='group'}</label>
                <select name="val[group_id]" class="form-control">
                    <option value="">{_p var='select'}:</option>
                    {foreach from=$aGroups item=aGroup}
                    <option value="{$aGroup.group_id}" {value type='select' id='group_id' default=$aGroup.group_id}>{$aGroup.var_name}</option>
                    {/foreach}
                </select>
                {help var='admincp.setting_add_group'}
            </div>
            <div class="form-group">
                <label>{_p var='variable'}</label>
                {if $bEdit}
                {$aForms.var_name}
                {else}
                <input type="text" class="form-control" name="val[var_name]" value="{value type='input' id='var_name'}" size="40" id="var_name" maxlength="100" />
                {/if}
                {help var='admincp.setting_add_var'}
            </div>
            <div class="form-group">
                <label>{_p var='type'}</label>
                <select id="js_form_value_actual" name="val[type]" onchange="changeFormValue(this.value);" class="form-control">
                    <option value="string" {value type='select' id='type' default='string'}>{_p var='string'}</option>
                    <option value="large_string" {value type='select' id='type' default='large_string'}>{_p var='large_string'}</option>
                    <option value="password" {value type='select' id='type' default='password'}>{_p var='password'}</option>
                    <option value="boolean" {value type='select' id='type' default='boolean'}>{_p var='boolean'}</option>
                    <option value="integer" {value type='select' id='type' default='integer'}>{_p var='integer'}</option>
                    <option value="array" {value type='select' id='type' default='array'}>{_p var='array'}</option>
                    <option value="drop" {value type='select' id='type' default='drop'}>{_p var='defined_drop_down'}</option>
                    {plugin call='admincp.template_controller_setting_add_type_drop_down'}
                </select>
                {help var='admincp.setting_add_type'}
            </div>
            <div class=" orm-group">
                <label>{_p var='value'}</label>
                <div id="js_form_value_class" >
                    <div id="js_form_value">
                        <textarea class="form-control" cols="60" rows="8" name="val[value]" id="value">{value type='textarea' id='value'}</textarea>
                    </div>
                    {help var='admincp.setting_add_value'}
                </div>
            </div>
        </div>
    </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">{_p var='language_package_details'}</div>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="title">{_p var='title'}</label>
                    <input type="text" class="form-control" name="val[title]" value="{value type='input' id='title'}" size="40" id="title" maxlength="250" />
                    {help var='admincp.setting_add_title'}
                </div>
                <div class="form-group">
                    <label for="info">{_p var='info'}</label>
                    <textarea class="form-control" cols="50" rows="8" name="val[info]" id="info">{value type='textarea' id='info'}</textarea>
                    {help var='admincp.setting_add_info'}
                </div>
                <div class="form-group">
                    <input type="submit" value="{_p var='submit'}" class="btn btn-primary" />
                </div>
            </div>
        </div>
</form>


<script type="text/javascript">
$Behavior.loadCustomFormValues = function(){l}
	var oSelected = document.getElementById('js_form_value_actual');
	changeFormValue(oSelected.options[oSelected.selectedIndex].value);
{r}
</script>