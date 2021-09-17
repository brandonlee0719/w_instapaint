<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<form class="form page-add-modal" method="post" data-app="core_pages" data-action-type="submit" data-action="add_page_process">
    <input type="hidden" name="val[type_id]" value="{$iTypeId}">
    <div class="page-add-modal-outer">
        <div class="page-category-photo"
             {if $aMainCategory.image_path}
             style="background-image: url('{img server_id=$aMainCategory.image_server_id path='core.path_actual' file=$aMainCategory.image_path return_url=true}')"
             {else}
             style="background-image: url('{img path='core.path_actual' file='PF.Site/Apps/core-pages/assets/img/default-category/default_category.png' return_url=true}')"
             {/if}
        >
            <div class="page-category-inner">
                <div class="page-category-title">
                    {_p var=$aMainCategory.name}
                </div>
                <div class="page-category-title-sub">
                {_p var='you_can_choose_sub_category_optional'}
                </div>
                <div class="form-group page-category-select">
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

    <div class="error_message" id="add_page_error_messages" style="display: none;"></div>
    
    <div class="form-group col-xs-12 page-category-input">
        <label for="title" class="">{_p var="page_name"}</label>
        <div>
            <input id="title" name="val[title]" class="form-control col-xs-9" maxlength="64" autofocus required>
            <span class="help-block">{_p var='maximum_64_characters'}</span>
        </div>
    </div>
    
    <div class="page-category-button">
        <a class="btn btn-default btn-round" onclick="return js_box_remove(this);">{_p var='cancel'}</a>
        <input type="submit" class="btn btn-primary btn-round" value="{_p var='create_page'}">
    </div>
</form>