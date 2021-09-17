<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{$sCreateJs}

{literal}
<script type="text/javascript">
function doHideConnection(sValue) {
	if(sValue == "2")
	{
		$('#url_connection').hide();
	} else {
		$('#url_connection').show();
	}
}
</script>
{/literal}
<form method="post" class="from" action="{url link="admincp.component.add"}" id="js_form" onsubmit="{$sGetJsForm}">
{if $bIsEdit}
	<div><input type="hidden" name="id" value="{$aForms.component_id}" /></div>
{/if}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">{_p var='component_details'}</div>
        </div>
        <div class="panel-body">
            {if Phpfox::getUserParam('admincp.can_view_product_options')}
            <div class="form-group">
                <label for="product">{required}{_p var='product'}</label>
                <select name="val[product_id]" id="product_id" class="form-control">
                    {foreach from=$aProducts item=aProduct}
                    <option value="{$aProduct.product_id}"{value type='select' id='product_id' default=$aProduct.product_id}>{$aProduct.title}</option>
                    {/foreach}
                </select>
                {help var='admincp.component_add_product'}
            </div>
            {/if}
            <div class="form-group">
                <label for="module">{required}{_p var='module'}</label>
                <select name="val[module_id]" id="module_id" class="form-control">
                    {foreach from=$aModules key=sModule item=iModuleId}
                    <option value="{$iModuleId}|{$sModule}"{value type='select' id='module_id' default=$iModuleId}>{translate var=$sModule prefix='module'}</option>
                    {/foreach}
                </select>
                {help var='admincp.component_add_module'}
            </div>
            <div class="form-group">
                <label for="component">{required}{_p var='component'}</label>
                <input type="text" class="form-control" name="val[component]" id="component" value="{value type='input' id='component'}" size="30" />
                {help var='admincp.component_add_componen'}
            </div>
            <div class="form-group">
                <label for="type">{required}{_p var='type'}</label>
                <select name="val[type]" id="type" onchange="doHideConnection(this.value);" class="form-control">
                    <option value="">{_p var='select'}</option>
                    <option value="1"{value type='select' id='type' default='1'}>{_p var='controller'}</option>
                    <option value="2"{value type='select' id='type' default='2'}>{_p var='block_actual'}</option>
                </select>
                {help var='admincp.component_add_type'}
            </div>

            <div class="form-group {if $bIsEdit && $aForms.type == '2'}hide{/if}" id="url_connection">
                <label for="m_connection">
                    {_p var='url_connection'}:
                </label>
                <input type="text" class="form-control" name="val[m_connection]" id="m_connection" value="{value type='input' id='m_connection'}" size="30" />
                {help var='admincp.component_add_connection'}
        </div>
        <div class="form-group">
            <label for="is_active">{required}{_p var='active'}</label>
            <div class="radio">
                <label><input type="radio" name="val[is_active]" value="1"{value type='radio' id='is_active' default='1' selected=true}/> {_p var='yes'}</label>
            </div>
            <div class="radio">
                <label><input type="radio" name="val[is_active]" value="0"{value type='radio' id='is_active' default='0'}/> {_p var='no'}</label>
            </div>
            {help var='admincp.component_add_active'}
        </div>
        <div class="form-group">
            <button type="submit" name="_submit" class="btn btn-primary">{_p var='submit'}</button>
        </div>
        </div>
    </div>
</form>