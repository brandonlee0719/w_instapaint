<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<form class="form group-add-modal" method="post" data-app="core_groups" data-action-type="submit" data-action="add_group_process">
    <input type="hidden" name="val[type_id]" value="{$iTypeId}">
    <div class="group-add-modal-outer">
        <div class="group-category-photo"
             {if !empty($aMainCategory.image_path)}
             style="background-image: url('{img server_id=$aMainCategory.image_server_id path='core.path_actual' file=$aMainCategory.image_path return_url=true}')"
             {else}
             style="background-image: url('{img path='core.path_actual' file='PF.Site/Apps/core-groups/assets/img/default-category/default_category.png' return_url=true}')"
             {/if}
        >
            <div class="group-category-inner">
                <div class="group-category-title">
                    {_p var=$aMainCategory.name}
                </div>
                <div class="group-category-title-sub">
                    {_p var='you_can_choose_sub_category_optional'}
                </div>
                <div class="form-group group-category-select">
                    <select name="val[category_id]" class="form-control" id="select_sub_category_id">
                        <option value="0" id="no-select-sub-category">{_p var='sub_category'}</option>
                        {foreach from=$aCategories item=aCategory}
                            {foreach from=$aCategory.categories item=aSubCategory}
                            <option value="{$aSubCategory.category_id}" class="select-category-{$aCategory.type_id}" {if $iTypeId != $aCategory.type_id}style="display: none"{/if}>{_p var=$aSubCategory.name}</option>
                            {/foreach}
                        {/foreach}
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-danger" id="add_group_error_messages" style="display: none;"></div>

    <div class="form-group col-xs-12 group-category-input">
        <label for="title" class="">{_p var="group_name"}</label>
        <div>
            <input id="title" name="val[title]" class="form-control col-xs-9" maxlength="64" autofocus required>
            <span class="help-block">{_p var='maximum_64_characters'}</span>
        </div>
    </div>
        
    <div class="form-group col-xs-12 group-privacy-select">
        <label for="group-type" class="">{_p var="type_privacy"}</label>
        <select name="val[reg_method]" class="form-control" id="group-type">
            <option value="0">{_p var='public_group'}</option>
            <option value="1">{_p var='closed_group'}</option>
            <option value="2">{_p var='secret_group'}</option>
        </select>
    </div>

    <div class="group-category-button">
        <input type="submit" class="btn btn-primary btn-round" value="{_p var='create_group'}">
        <a class="btn btn-default btn-round" onclick="return js_box_remove(this);">{_p var='cancel'}</a>
    </div>
</form>