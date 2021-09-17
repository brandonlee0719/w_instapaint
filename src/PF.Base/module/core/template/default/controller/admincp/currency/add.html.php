<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<form method="post" action="{url link='admincp.core.currency.add'}" class="form">
    <div class="panel panel-default">
        <div class="panel-body">
            {if $bIsEdit}
            <div><input type="hidden" name="id" value="{$aForms.currency_id}"/></div>
            {/if}
            <div class="form-group">
                <label for="currency_id">{_p var='currency_id'}</label>
                <input type="text" class="form-control" name="val[currency_id]"
                       value="{value type='input' id='currency_id'}" size="5" maxlength="3" id="currency_id"/>
            </div>
            <div class="form-group">
                <label for="symbol">{_p var='symbol'}</label>
                <input id="symbol" class="form-control" type="text" name="val[symbol]"
                       value="{value type='input' id='symbol'}" size="5" maxlength="10"/>
            </div>
            <div class="form-group">
                <label for="symbol">{_p var='format_uppercase'}</label>
                <input id="symbol" class="form-control" type="text" name="val[format]"
                       value="{value type='input' id='format'}" size="5" maxlength="100"/>
                <p class="help-block">
                    {_p var='currency_format_description'}
                </p>
            </div>
            <div class="form-group">
                <label>{_p var='phrase'}</label>
                {if $bIsEdit}
                {module name='language.admincp.form' type='text' id='phrase_var' var_name=$aForms.phrase_var}
                {else}
                {module name='language.admincp.form' type='text' id='phrase_var'}
                {/if}
            </div>
            <div class="form-group">
                <label>{_p var='is_active'}</label>
                <div class="item_is_active_holder">
                    <span class="js_item_active item_is_active">
                        <input type="radio" name="val[is_active]" value="1" {value type='radio' id='is_active'
                               default='1' selected='true' }/> {_p var='yes'}
                    </span>
                    <span class="js_item_active item_is_not_active">
                        <input type="radio" name="val[is_active]" value="0" {value type='radio' id='is_active'
                               default='0' }/> {_p var='no'}
                    </span>
                </div>
            </div>

        </div>
        <div class="panel-footer">
            <button class="btn btn-primary" type="submit">{_p var='submit'}</button>
        </div>
    </div>
</form>
