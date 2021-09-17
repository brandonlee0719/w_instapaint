<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{if count($aSettings)}
<form method="post" enctype="multipart/form-data">
<input type="hidden" name="cmd" value="_save"/>
<div class="panel panel-default">
    <div class="panel-body">
    {foreach from=$aSettings item=aSetting}
    <div class="form-group {if isset($aSetting.error)}has-error{/if} lines">
        {if PHPFOX_DEBUG }
        <div class="pull-right">
            <input type="text" readonly value="{$aSetting.var_name}" class="input_xs_readonly" onclick="this.select()" />
        </div>
        {/if}
        <label>{$aSetting.info}</label>
        {if $aSetting.type == 'multi_text'}
        {foreach from=$aSetting.value key=mKey item=sDropValue}
        <div class="p_4">
            <div class="input-group">
                <span class="input-group-addon">{$mKey}</span>
                <input class="form-control" type="text" name="val[value][{$aSetting.var_name}][{$mKey}]" value="{$sDropValue|clean}" size="8" />
            </div>
        </div>
        {/foreach}
        {elseif $aSetting.type == 'currency'}
        {module name='core.currency' currency_field_name='val[value]['{$aSetting.var_name']' value_actual=$aSetting.value }
        {elseif $aSetting.type == 'multi_checkbox'}
        {foreach from=$aSetting.options key=mKey item=sDropValue}
        <div class="checkbox">
            <label><input type="checkbox" name="val[value][{$aSetting.var_name}][]" value="{$mKey}" {if is_array($aSetting.value) && in_array($mKey, $aSetting.value)}checked{/if} />{$sDropValue}</label>
        </div>
        {/foreach}
        {elseif $aSetting.type == 'large_string'}
        <textarea cols="60" rows="8" class="form-control" name="val[value][{$aSetting.var_name}]">{$aSetting.value|htmlspecialchars}</textarea>
        {elseif ($aSetting.type == 'string')}
        <input type="text" class="form-control" name="val[value][{$aSetting.var_name}]" value="{$aSetting.value|clean}" size="40" />
        {elseif ($aSetting.type == 'password')}
        <input class="form-control" type="password" name="val[value][{$aSetting.var_name}]" value="{$aSetting.value}" size="40" autocomplete="off" />
        {elseif ($aSetting.type == 'select')}
        <select name="val[value][{$aSetting.var_name}]" class="form-control">
            {foreach from=$aSetting.options key=mKey item=sDropValue}
            <option value="{$mKey}" {if $aSetting.value == $mKey}selected="selected"{/if}>{$sDropValue}</option>
            {/foreach}
        </select>
        {elseif ($aSetting.type == 'integer')}
        <input class="form-control" type="text" name="val[value][{$aSetting.var_name}]" value="{$aSetting.value}" size="40" onclick="this.select();" />
        {elseif ($aSetting.type == 'boolean')}
        <div class="item_is_active_holder">
			<span class="js_item_active item_is_active">
				<input type="radio" value="1" name="val[value][{$aSetting.var_name}]"{if $aSetting.value == 1} checked="checked"{/if}> Yes
			</span>
            <span class="js_item_active item_is_not_active">
				<input type="radio" value="0" name="val[value][{$aSetting.var_name}]"{if $aSetting.value != 1} checked="checked"{/if}> No
			</span>
        </div>
        {elseif ($aSetting.type == 'array')}
        <div class="js_array_holder">
            {if is_array($aSetting.value)}
            {foreach from=$aSetting.value key=iKey item=sValue}
            <div class="p_4" class="js_array{$iKey}">
                <div class="input-group">
                    <input type="text" name="val[value][{$aSetting.var_name}][]" value="{$sValue}" size="120" class="form-control" />
                    <span class="input-group-btn">
                        <a class="btn btn-info" data-cmd="admincp.site_setting_remove_input"><i class="fa fa-remove"></i> </a>
                    </span>
                </div>
            </div>
            {/foreach}
            {/if}
            <div class="js_array_data"></div>
            <div class="js_array_count" style="display:none;">{if isset($iKey)}{$iKey+1}{/if}</div>
            <br />
            <div class="p_4">
                <div class="input-group">
                    <input type="text" name="" placeholder="{_p var='add_a_new_value'}" size="30" class="js_add_to_array form-control" />
                    <span class="input-group-btn">
                        <input type="button" value="{_p var='add'}" class="btn btn-primary" data-rel="val[value][{$aSetting.var_name}][]" data-cmd="admincp.site_setting_add_input" />
                    </span>
                </div>
            </div>
        </div>
        {/if}
        {if isset($aSetting.description)}
        <div class="help-block">{$aSetting.description}</div>
        {/if}
    </div>
    {/foreach}
    </div>
    <div class="panel-footer">
        <button type="submit" class="btn btn-danger">{_p var='Save Changes'}</button>
        <a class="btn btn-link" href="{url link='admincp.block' m_connection=$sConnection}">{_p var='cancel'}</a>
    </div>
    </div>
</form>
{else}
<div class="alert alert-empty">{_p var='there_are_no_settings'}</div>
{/if}