<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
<script>
    var set_active = false, group_class = '';
    {if ($group_class)}
    group_class = '{$group_class}';
    {/if}
    {literal}
    $Ready(function() {
        if (set_active) {
            return;
        }
        set_active = true;
        $('._is_app_settings').show();
        $('.apps_menu a[href="#settings"]').addClass('active');
        if (group_class) {
            $('.' + group_class + ':not(.is_option_class)').show();

            var do_this = function() {
                var driver = $(this).data('option-class').split('='),
                    s_key = driver[0],
                    s_value = driver[1],
                    i = $(this),
                    t = $('.__data_option_' + s_key + '');

                if (t.length) {
                    if (t.val() == s_value && i.hasClass(group_class)) {
                        i.show();
                    } else {
                        i.hide();
                    }

                    t.change(function () {
                        $('.is_option_class').each(do_this);
                    });
                }
            };

            $('.is_option_class').each(do_this);
        }
    });
    {/literal}
</script>
{if count($aSettings)}
<form method="post" action="{url link='current'}" enctype="multipart/form-data" class="on_change_submit">
<div class="panel panel-default global-settings _is_app_settings">
    <div class="panel-heading">
        <div class="panel-title">
            {_p var='manage_settings'}
            {if ($admincp_help)} <a href="{$admincp_help}" target="_blank" class="pull-right" style="font-size: 20px;"><i class="fa fa-info-circle"></i></a>{/if}
        </div>
    </div>
    <div class="panel-body">
    {foreach from=$aSettings item=aSetting key=var}
    <div id="{$aSetting.var_name}" class="form-group {if isset($aSetting.is_danger) && $aSetting.is_danger} has-warning {/if} {if !empty($aSetting.error)}has-error{/if} lines{if !empty($aSetting.group_class)} {$aSetting.group_class}{/if}{if !empty($aSetting.option_class)} is_option_class{/if}{if !empty($group_class) && !empty($aSetting.group_class) && $group_class != $aSetting.group_class} hidden{/if}"{if !empty($aSetting.option_class)} data-option-class="{$aSetting.option_class}"{/if}>
        {if PHPFOX_DEBUG}
        <div class="pull-right">
            <input readonly type="text" name="val[order][{$aSetting.var_name}]" value="{$aSetting.ordering}" class="input_xs_readonly" onclick="this.select();" size="2" />
            <input readonly type="text" name="param{$aSetting.var_name}" value="{$aSetting.module_id}.{$aSetting.var_name}" class="input_xs_readonly" onclick="this.select();" />
        </div>
        {/if}
        <label class="setting-title">{_p var=$aSetting.setting_title}</label>
        {if isset($aSetting.is_danger) && $aSetting.is_danger}
        <div class="alert alert-warning alert-labeled">
            <div class="alert-labeled-row">
                    <span class="alert-label alert-label-left alert-labelled-cell">
                        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                    </span>
                <p class="alert-body alert-body-right alert-labelled-cell">
                    <strong>{_p var="Warning"}</strong>
                    {_p var="This is an important setting. Select a wrong option here can break the site or affect some features. If you are at all unsure about which option to configure, use the default value or contact us for support"}.
                </p>
            </div>
        </div>
        {/if}
        <div class="clear"></div>
        {if $aSetting.type_id == 'multi_text'}
        {foreach from=$aSetting.values key=mKey item=sDropValue}
        <div class="p_4">
            <div class="input-group">
                <span class="input-group-addon">{$mKey}</span>
                <input class="form-control" type="text" name="val[value][{$aSetting.var_name}][{$mKey}]" value="{$sDropValue|clean}"  />
            </div>
        </div>
        {/foreach}
        {elseif $aSetting.type_id == 'currency'}
        {module name='core.currency' currency_field_name='val[value]['{$aSetting.var_name']' value_actual=$aSetting.values }
        {elseif $aSetting.type_id == 'large_string' || $aSetting.type_id=='big_string'}
        <textarea cols="60" rows="8" class="form-control" name="val[value][{$aSetting.var_name}]">{$aSetting.value_actual|htmlspecialchars}</textarea>
        {elseif ($aSetting.type_id == 'string') || $aSetting.type_id == 'input:text'}
        <div><input type="text" class="form-control" name="val[value][{$aSetting.var_name}]" value="{$aSetting.value_actual|clean}" size="40" /></div>
        {elseif ($aSetting.type_id == 'password')}
        <div><input class="form-control" type="password" name="val[value][{$aSetting.var_name}]" value="{$aSetting.value_actual}" size="40" autocomplete="off" /></div>
        {elseif ($aSetting.type_id == 'drop')}
        <div><input type="hidden" name="val[value][{$aSetting.var_name}][real]" value="{$aSetting.value_actual}" size="40" /></div>
        <select name="val[value][{$aSetting.var_name}][value]" class="form-control">
            {foreach from=$aSetting.values.values key=mKey item=sDropValue}
            <option value="{$sDropValue}" {if $aSetting.values.default == $sDropValue}selected="selected"{/if}>
            {if !empty($sDropValue) && !stripos( $sDropValue, ' ') && !stripos($sDropValue, '.')}
            {php}{$this->_aVars['sDropValue'] = strtolower($this->_aVars['sDropValue']);}{/php}
            {_p var=$sDropValue}
            {else}
            {$sDropValue}
            {/if}
            </option>
            {/foreach}
        </select>
        {elseif ($aSetting.type_id == 'drop_with_key' || $aSetting.type_id== 'select')}
        <select name="val[value][{$aSetting.var_name}]" class="form-control __data_option_{$aSetting.var_name}" data-rel="__data_option_{$aSetting.var_name}">
            {foreach from=$aSetting.values key=mKey item=sDropValue}
            <option value="{$mKey}"{if $aSetting.value_actual == $mKey} selected="selected"{/if}>{$sDropValue}</option>
            {/foreach}
        </select>
        {elseif $aSetting.type_id == 'radio'}
        {foreach from=$aSetting.values key=mKey item=sDropValue}
        <div class="radio">
            <label>
                <input name="val[value][{$aSetting.var_name}]" type="radio" {if $aSetting.value_actual == $mKey} checked{/if} value="{$mKey}" />{$sDropValue}
            </label>
        </div>
        {/foreach}

        {elseif ($aSetting.type_id == 'integer')}
        <input class="form-control" type="text" name="val[value][{$aSetting.var_name}]" value="{$aSetting.value_actual}" size="40" onclick="this.select();" />
        {elseif ($aSetting.type_id == 'boolean') || $aSetting.type_id == 'input:radio'}
        <div class="item_is_active_holder">
            <span class="js_item_active item_is_active">
                <input type="radio" value="1" name="val[value][{$aSetting.var_name}]"{if $aSetting.value_actual == 1} checked="checked"{/if}> Yes
            </span>
            <span class="js_item_active item_is_not_active">
                <input type="radio" value="0" name="val[value][{$aSetting.var_name}]"{if $aSetting.value_actual != 1} checked="checked"{/if}> No
            </span>
        </div>
        {elseif ($aSetting.type_id == 'array')}
        <div class="js_array_holder">
            {if is_array($aSetting.values)}
            {foreach from=$aSetting.values key=iKey item=sValue}
            <div class="p_4" class="js_array{$iKey}">
                <div class="input-group">
                    <input type="text" name="val[value][{$aSetting.var_name}][]" value="{$sValue}" size="120" class="form-control" />
                    <span class="input-group-btn">
                        <a class="btn btn-info" data-cmd="admincp.site_setting_remove_input" data-rel="setting={$aSetting.var_name}&value={$sValue}" title="{_p var='remove'}"><i class="fa fa-remove"></i> </a>
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
                    <input type="text" name="" placeholder="{_p var='add_a_new_value' phpfox_squote=true}" size="30" class="js_add_to_array form-control" />
                    <span class="input-group-btn">
                        <input type="button" value="{_p var='add'}" class="btn btn-info" data-cmd="admincp.site_setting_add_input" data-rel="val[value][{$aSetting.var_name}][]" />
                    </span>
                </div>
            </div>
        </div>
        {/if}
        <p class="help-block">
            {_p var=$aSetting.setting_info}
        </p>
    </div>
    {if $aSetting.var_name == 'watermark_option'}
    <div class="panel panel-default">
        <div class="panel-body">
            image
        </div>
        <div class="panel-footer">
            <div class="form-group">
                <p class="help-block">{_p var='your_current_watermark_image'}</p>
                <p class="help-block">
                    <img src="{$sWatermarkImage}" alt="Watermark Image" />
                </p>
                <p class="help-block">
                    {_p var='b_notice_b_advised_image_is_a_transparent_png_with_a_max_width_height_of_52_pixels'}
                </p>
            </div>
            <div class="form-group" style="margin-bottom:20px;">
                <input type="file" name="watermark" size="30" />
                <p class="help-block">
                    {_p var='you_can_upload_a_jpg_gif_or_png_file'}
                </p>
            </div>
        </div>
    </div>
    {/if}
{/foreach}
    {if $sGroupId == 'mail'}
        <div class="form-group">
            <label>{_p var="Send a Test Email"}</label>
            <input class="form-control" type="text" value="{if isset($test_email)}{$test_email}{/if}" name="val[email_send_test]" placeholder="{_p var='To'}"/>
            <p class="help-block">
                {_p var="Type an email address here and then click Send Test to generate a test email"}
            </p>
        </div>
    {/if}

</div>
</div>
    {if count($aSettings)}
    <div id="table_hover_action_holder">
        <button type="submit" class="btn btn-primary">{_p var='Save Changes'}</button>
        {if $sGroupId == 'mail'}
        <button type="submit" name="test" value="test" class="btn btn-primary">{_p var='Test'}</button>
        {/if}
    </div>
    {/if}
</form>
{else}
<div class="alert alert-empty">{_p var='setting_group_avaliable_settings'}</div>
{/if}


