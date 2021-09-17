<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

{$sCreateJs}
<form method="post" class="form" action="{url link="admincp.setting.group.add"}" id="js_setting_form" onsubmit="{$sGetJsForm}">
    <div class="panel panel-default">
        <div class="panel-body">
            {_p var='group_information'}
        </div>
        <div class="panel-footer">

            <div class="form-group">
                <label>{_p var='product'}</label>
                <select name="val[product_id]" class="form-control">
                    {foreach from=$aProducts item=aProduct}
                    <option value="{$aProduct.product_id}">{$aProduct.title}</option>
                    {/foreach}
                </select>
                {help var='admincp.setting_group_add_product'}
            </div>
            <div class="form-group">
                <label>{_p var='module'}</label>
                <select name="val[module_id]" class="form-control">
                    <option value="">{_p var='select'}:</option>
                    {foreach from=$aModules key=sModule item=iModuleId}
                    <option value="{$iModuleId}" {value type='select' id='module_id' default=$iModuleId}>{$sModule}</option>
                    {/foreach}
                </select>
                {help var='admincp.setting_add_group_module_id'}
            </div>
            <div class="form-group">
                <label for="var_name">{_p var='name'}:</label>
                <input class="form-control" type="text" name="val[var_name]" value="{value type='input' id='var_name'}" size="40" id="var_name" maxlength="75" />
                {help var='admincp.setting_group_add_product'}
            </div>
            <div class="form-group">
                <label for="info">{_p var='info'}</label>
                <textarea class="form-control" cols="50" rows="8" name="val[info]" id="info">{value type='textarea' id='info'}</textarea>
                {help var='admincp.setting_group_add_info'}
            </div>
            <div class="form-group">
                <input type="submit" value="{_p var='submit'}" class="btn btn-primary" />
            </div>
        </div>
    </div>
</form>