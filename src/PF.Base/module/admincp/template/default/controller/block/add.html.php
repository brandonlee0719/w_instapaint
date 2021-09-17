<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

{literal}
<script type="text/javascript">
	function changeBlockType(oObj)
	{
		$('.js_block_type').hide();
		$('.js_block_type_id_' + oObj.value).show();		
	}
</script>
{/literal}
{$sCreateJs}
<form method="post" id="js_form" onsubmit="{$sGetJsForm}" class="form" action="{url link="admincp.block.add"}">
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">{if $bIsEdit}Edit Block <strong>#{$aForms.block_id}</strong>{else} Add Block {/if}</div>
    </div>
    <div class="panel-body">
        {if $bIsEdit}
        <input type="hidden" name="block_id" value="{$aForms.block_id}" />
        {/if}
        {if !Phpfox::getUserParam('admincp.can_view_product_options')}
        <input type="hidden" name="val[product_id]" value="1" />
        {/if}
        {if Phpfox::getUserParam('admincp.can_view_product_options')}
        {module name='admincp.product.form'}
        {/if}
        {module name='admincp.module.form' module_form_required=false}
        <div class="form-group">
            <label for="title">
                {if ($bIsEdit && isset($aForms) && $aForms.type_id == 5)}
                {_p var="App Callback"}
                {else}
                {_p var='title'}
                {/if}
            </label>
            <input class="form-control" id="title" type="text" name="val[title]" value="{value id='title' type='input'}" size="30" />
        </div>
        {if $bIsEdit && $aForms.type_id == 5}
        <div><input type="hidden" name="val[type_id]" value="{$aForms.type_id}"></div>
        {else}
        <div class="form-group {if $bIsEdit}hide{/if}">
            <label for="table_left">{_p var='type'}</label>
            <select name="val[type_id]" onchange="return changeBlockType(this);" class="form-control">
                <option value="0">{_p var='select'}:</option>
                <option value="0"{value type='select' id='type_id' default='0'}>{_p var='php_block_file'}</option>
                <option value="1"{value type='select' id='type_id' default='1'}>{_p var='php_code'}</option>
                <option value="2"{value type='select' id='type_id' default='2'}>{_p var='html_code'}</option>
            </select>
        </div>
        {/if}
        <div class="form-group">
            <label for="m_connection">{_p var='controller'}</label>
            <select name="val[m_connection]" id="m_connection" class="form-control">
                {if !$bIsEdit}
                <option value="">{_p var='select'}:</option>
                {/if}
                <option value="">{_p var='none_site_wide'}</option>
                {foreach from=$aControllers key=sName item=aController}
                <optgroup label="{$sName|translate:'module'}">
                {foreach from=$aController item=aCont}
                <option value="{$aCont.m_connection}"{value type='select' id='m_connection' default=$aCont.m_connection}>-- {$aCont.m_connection}</option>
                {/foreach}
                </optgroup>
                {/foreach}
            </select>
            {help var='admincp.block_add_connection'}
        </div>
        {if $bIsEdit && $aForms.type_id == 5}
        <input type="hidden" name="val[component]" value="{$aForms.component}">
        {else}
        <div class="form-group js_block_type js_block_type_id_0 {if $bIsEdit && $aForms.type_id > 0}hide{/if}">
            <label>{_p var='component'}</label>
            <select name="val[component]" id="component" class="form-control">
                <option value="">{_p var='select'}:</option>
                {foreach from=$aComponents key=sName item=aComponent}
                <optgroup label="{$sName|translate:'module'}">
                {foreach from=$aComponent item=aComp}
                <option value="{$sName}|{$aComp.component}"{value type='select' id='component' default=''$sName'|'$aComp.component''}>-- {$aComp.component}</option>
                {/foreach}
                </optgroup>
                {/foreach}
            </select>
            {help var='admincp.block_add_component'}
        </div>
        {/if}
        <div class="form-group">
            <label for="location">{_p var='placement'} {if Phpfox::isAdmin()} <a href="#?call=theme.sample&amp;width=1300" class="inlinePopup" title="{_p var='sample_layout'}">{_p var='view_sample_layout'}</a>{/if}</label>
            <select name="val[location]" id="location" class="form-control">
                {for $i = 1; $i <= 12; $i++}
                <option value="{$i}"{value type='select' id='location' default=$i}>{_p var='block_location_x' x=$i}</option>
                {/for}
            </select>
            {help var='admincp.block_add_placement'}
        </div>

        <div class="form-group hide">
            <label for="">{_p var='can_drag_drop'}</label>
            <label><input type="radio" name="val[can_move]" value="1"{value type='radio' id='can_move' default='1'}/> {_p var='yes'}</label>
            <label><input type="radio" name="val[can_move]" value="0"{value type='radio' id='can_move' default='0' selected=true}/> {_p var='no'}</label>
        </div>

        <div class="form-group hide">
            <label for="">{_p var='active'}</label>
            <label><input type="radio" name="val[is_active]" value="1"{value type='radio' id='is_active' default='1' selected=true}/> {_p var='yes'}</label>
            <label><input type="radio" name="val[is_active]" value="0"{value type='radio' id='is_active' default='0'}/> {_p var='no'}</label>
            {help var='admincp.block_add_active'}
        </div>

        <div class="js_block_type js_block_type_id_1 js_block_type_id_2 {if $bIsEdit && ($aForms.type_id == 0 || $aForms.type_id == 5)} hide{/if}">
            <div class="form-group">
                <label for="source_code">{_p var='php_html_code_optional'}</label>
                <textarea class="form-control" name="val[source_code]" rows="8" id="source_code">{value type='textarea' id='source_code'}</textarea>
            </div>
        </div>

        <div class="form-group">
            <label for="">{_p var='allow_access'}</label>
            {foreach from=$aUserGroups item=aUserGroup}
            <div class="checkbox">
                <label><input type="checkbox" name="val[allow_access][]" value="{$aUserGroup.user_group_id}"{if isset($aAccess) && is_array($aAccess)}{if !in_array($aUserGroup.user_group_id, $aAccess)} checked="checked" {/if}{else} checked="checked" {/if}/> {$aUserGroup.title|convert|clean}</label>
            </div>
            {/foreach}
            {help var='admincp.block_add_access'}
        </div>
    </div>
    <div class="panel-footer">
        <button type="submit" value="_submit" class="btn btn-primary">{_p var='submit'}</button>
    </div>
</div>
</form>