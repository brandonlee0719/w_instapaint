<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<form method="post" action="{url link='admincp.rss.group.add'}" onsubmit="$(this).find('.js_btn_submit').prop('disabled', true);">
    <div class="panel panel-default">
        <div class="panel-body">
            {if $bIsEdit}
            <input type="hidden" name="group_id" value="{$aForms.group_id}"/>
            {/if}
            {if !$bIsEdit}
            {module name='admincp.product.form'}
            {module name='admincp.module.form'}
            {/if}

            <div class="form-group">
                <label for="name">{required}{_p var='name'}</label>
                {field_language phrase='name_var' label='Name' field='name_var' format='val[name_var_' size=30 maxlength=255
                help_phrase='if_the_section_title_is_empty_then_its_value_will_have_the_same_value_as_default_language'}
            </div>

            <div class="form-group">
                <label for="active">{required}{_p var='is_active'}</label>
                <div class="item_is_active_holder">
                    <span class="js_item_active item_is_active"><input type="radio" name="val[is_active]" value="1" {value type='radio' id='is_active' default='1' selected='true'
                                                                       }/> {_p var='yes'}</span>
                    <span class="js_item_active item_is_not_active"><input type="radio" name="val[is_active]" value="0" {value type='radio' id='is_active' default='0' }/> {_p var='no'}</span>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" value="{_p var='submit'}" class="btn btn-primary js_btn_submit"/>
        </div>
    </div>
</form>
