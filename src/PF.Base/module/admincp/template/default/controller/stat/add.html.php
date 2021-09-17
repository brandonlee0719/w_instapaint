<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

<form method="post" action="{url link='admincp.stat.add'}">
{if $bIsEdit}
	<div><input type="hidden" name="id" value="{$aForms.stat_id}" /></div>
{/if}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">{_p var='stat_details'}</div>
        </div>
        <div class="panel-body">
            {module name='admincp.product.form'}
            {module name='admincp.module.form'}
            <div class="form-group">
                <label class="required">{_p var='title'}</label>
                {if $bIsEdit}
                {module name='language.admincp.form' type='text' id='phrase_var' var_name=$aForms.phrase_var}
                {else}
                {module name='language.admincp.form' type='text' id='phrase_var'}
                {/if}
            </div>
            <div class="form-group">
                <label class="required" for="stat_link">{_p var='link'}</label>
                <input class="form-control" type="text" name="val[stat_link]" id="stat_link" value="{value id='stat_link' type='input'}" size="40" />
            </div>
            <div class="form-group">
                <label class="required" for="stat_image">{_p var='image'}</label>
                <input class="form-control" type="text" name="val[stat_image]" id="stat_image" value="{value id='stat_image' type='input'}" size="20" />
            </div>
            <div class="form-group">
                <label class="required">{_p var='php_code'}</label>
                <textarea class="form-control" cols="60" rows="8" name="val[php_code]">{value id='php_code' type='textarea'}</textarea>
            </div>

            <div class="form-group">
                <label class="required">{_p var='active'}:</label>
                <div class="item_is_active_holder">
                    <span class="js_item_active item_is_active"><input type="radio" name="val[is_active]" value="1" {value type='radio' id='is_active' default='1' selected='true'}/> {_p var='yes'}</span>
                    <span class="js_item_active item_is_not_active"><input type="radio" name="val[is_active]" value="0" {value type='radio' id='is_active' default='0'}/> {_p var='no'}</span>
                </div>
            </div>
            <div class="form-group">
                <input type="submit" value="{_p var='submit'}" class="btn btn-primary" />
            </div>
        </div>
    </div>
</form>