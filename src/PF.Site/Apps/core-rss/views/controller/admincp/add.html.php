<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<form method="post" action="{url link='admincp.rss.add'}" onsubmit="$(this).find('.js_btn_submit').prop('disabled', true);">
    <div class="panel panel-default">
        {if $bIsEdit}
        <input type="hidden" name="id" value="{$aForms.feed_id}"/>
        {/if}
        <div class="panel-body">
            {if !$bIsEdit}
            {module name='admincp.product.form'}
            {module name='admincp.module.form'}
            {/if}
            <div class="form-group">
                <label for="group">{required}{_p var='group'}</label>
                <select name="val[group_id]" class="form-control">
                    <option value="">{_p var='select'}:</option>
                    {foreach from=$aGroups item=aGroup}
                    <option value="{$aGroup.group_id}" {value type='select' id='group_id' default=$aGroup.group_id}>{_p var=$aGroup.name_var}</option>
                    {/foreach}
                </select>
            </div>

            <div class="form-group">
                <label for="title">{required}{_p var='title'}</label>
                {field_language phrase='title_var' label='Title' field='title_var' format='val[title_var_' size=30 maxlength=255
                help_phrase='if_the_section_title_is_empty_then_its_value_will_have_the_same_value_as_default_language'}
            </div>

            <div class="form-group">
                <label for="description">{required}{_p var='description'}</label>
                {field_language phrase='description_var' label='Description' type='textarea' rows=5 field='description_var' format='val[description_var_' size=30 maxlength=255
                help_phrase='if_the_section_title_is_empty_then_its_value_will_have_the_same_value_as_default_language'}
            </div>

            <div class="form-group">
                <label for="link">{required}{_p var='link'}</label>
                <input class="form-control" type="text" name="val[feed_link]" id="feed_link" value="{value id='feed_link' type='input'}" size="40"/>
            </div>

            <div class="form-group">
                <label>{_p var='php_group_code'}</label>
                <div class="help-block">
                    {_p var="be_careful_the_incorrect_code_can_break_your_site"}
                </div>
                <textarea class="form-control" cols="60" rows="15" name="val[php_group_code]" id="php_group_code">{value id='php_group_code' type='textarea'}</textarea>
            </div>

            <div class="form-group">
                <label for="php_view_code">{required}{_p var='php_view_code'}</label>
                <div class="help-block">{_p var="be_careful_the_incorrect_code_can_break_your_site"}</div>
                <textarea class="form-control" cols="60" rows="15" name="val[php_view_code]" id="php_view_code">{value id='php_view_code' type='textarea'}</textarea>
            </div>

            <div class="form-group">
                <label>{_p var='site_wide'}</label>
                <div class="item_is_active_holder">
                    <span class="js_item_active item_is_active"><input type="radio" name="val[is_site_wide]" value="1" {value type='radio' id='is_site_wide' default='1' }/> {_p var='yes'}</span>
                    <span class="js_item_active item_is_not_active"><input type="radio" name="val[is_site_wide]" value="0" {value type='radio' id='is_site_wide' default='0' selected='true' }/> {_p var='no'}</span>
                </div>
            </div>

            <div class="form-group">
                <label>{_p var='is_active'}</label>
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
