<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{$sCreateJs}
<form action="{url link='admincp.egift.add-gift'}" method="post" id="core_js_egift_form" onsubmit="{$sGetJsForm}; $Core.onSubmitForm(this, true);" enctype="multipart/form-data">
    <div class="panel panel-default">
        {if $bIsEdit}
        <input type="hidden" name="edit" value="{$iEditId}" />
        {/if}
        <div class="panel-body">
            <div class="form-group">
                <label for="title">{required}{_p var='title'}:</label>
                <input class="form-control" type="text" id="title" name="val[title]" maxlength="255" required value="{value type='input' id='title'}">
            </div>
            <div class="form-group">
                <label for="file">{required}{_p var='choose_file'}:</label>
                <input type="file" id="file" name="file">
                <p class="extra_info">
                    {_p var='allowed_file_extensions_jpg_png_gif'}.
                    <br />
                    {if $iMaxFileSize !== null}
                    {_p var='the_file_size_limit_is_file_size_if_your_upload_does_not_work_try_uploading_a_smaller_picture' file_size=$iMaxFileSize|filesize}
                    {/if}
                    {if isset($aForms.category) && !empty($aForms.category)}
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="val[egift_id]" value="{$aForms.egift_id}">
                    <br />{_p var='uploading_a_picture_will_overwrite_the_current_one_for_this_item'}.
                    {else}
                    <input type="hidden" name="action" value="upload">
                    {/if}
                </p>
            </div>
            {if !empty($aForms.current_image)}
            <div class="form-group">
                <label for="current_image">{_p var='current_image'}</label>
                <div class="">
                    <img src="{$aForms.current_image}" alt="{_p var='current_image'}" style="max-height: 120px; max-width: 120px">
                </div>
            </div>
            {/if}
            <div class="form-group">
                <label for="select_category">{required}{_p var='choose_category'}:</label>
                <select name="val[category]" id="select_category" class="form-control">
                    {foreach from=$aCategories item=aCategory key=iKey}
                    <option value="{$aCategory.category_id}" {if isset($aForms.category_id) && $aForms.category_id == $aCategory.category_id}selected{/if}>{_p var=$aCategory.phrase}</option>
                    {/foreach}
                </select>
            </div>
            <div class="form-group">
                <label>{_p var='price'}:</label>
                {if isset($aForms.price) && !empty($aForms.price)}
                {module name='core.currency' currency_field_name=val[currency] currency_value_val[currency]=$aForms.price}
                {else}
                {module name='core.currency' currency_field_name=val[currency]}
                {/if}
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" class="btn btn-primary" value="{if $bIsEdit}{_p var='edit_egift'}{else}{_p var='add_egift'}{/if}" />
            <input onclick="return js_box_remove(this);" type="submit" value="{_p var='cancel'}" class="btn btn-default" />
        </div>

    </div>
</form>
