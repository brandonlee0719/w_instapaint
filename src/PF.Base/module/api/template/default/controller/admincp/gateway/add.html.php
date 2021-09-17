<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<form class="form" method="post" action="{url link='admincp.api.gateway.add'}">
    <div class="panel panel-default">
        <div class="panel-body">
            <input type="hidden" name="id" value="{$aForms.gateway_id}"/>
            <div class="form-group">
                <label for="title">{_p var='title'}</label>
                <input type="text" name="val[title]" id="title" value="{value type='input' id='title'}" size="40"
                       class="form-control"/>
            </div>
            <div class="form-group">
                <label for="description">{_p var='description'}</label>
                <textarea class="form-control" rows="6" name="val[description]" id="description">{value type='textarea' id='description'}</textarea>
            </div>
            <div class="form-group">
                <label for="is_active">{_p var='active'}</label>
                <div class="item_is_active_holder">
                    <span class="js_item_active item_is_active">
                        <input type="radio" name="val[is_active]" value="1" {value type='radio' id='is_active'
                               default='1' selected='true' }/> {_p var='yes'}</span>
                    <span class="js_item_active item_is_not_active">
                        <input type="radio" name="val[is_active]" value="0" {value type='radio' id='is_active'
                               default='0' }/> {_p var='no'}</span>
                </div>
            </div>
            <div class="form-group">
                <label for="is_test">{_p var='test_mode'}</label>
                <div class="item_is_active_holder">
                    <span class="js_item_active item_is_active">
                        <input type="radio" name="val[is_test]" value="1" {value type='radio' id='is_test' default='1' selected='true' }/>
                        {_p var='yes'}
                    </span>
                    <span class="js_item_active item_is_not_active">
                        <input type="radio" name="val[is_test]" value="0" {value type='radio' id='is_test' default='0' }/>
                        {_p var='no'}
                    </span>
                </div>
            </div>
            {if is_array($aForms.custom)}
            {foreach from=$aForms.custom key=sFormField item=aCustom}
            <div class="form-group">
                <label for="title">{$aCustom.phrase}</label>
                {if (isset($aCustom.type) && $aCustom.type == 'textarea')}
                <textarea name="val[setting][{$sFormField}]" rows="8"
                          class="form-control">{$aCustom.value|clean}</textarea>
                {else}
                <input type="text" name="val[setting][{$sFormField}]" id="title" value="{$aCustom.value|clean}"
                       size="40" class="form-control"/>
                {/if}
                {if !empty($aCustom.phrase_info)}
                <p class="help-block">
                    {$aCustom.phrase_info}
                </p>
                {/if}
            </div>
            {/foreach}
            {/if}
        </div>
        <div class="panel-footer">
            <button type="submit" class="btn btn-primary">{_p var='update'}</button>
        </div>
    </div>
</form>
