<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<form method="post" action="{url link='admincp.v.delete-category'}">
    <div class="panel panel-body">
    <div><input type="hidden" name="delete" value="{$iDeleteId}" /></div>
    <div class="alert alert-warning">
        {_p('are_you_sure_you_want_to_delete_this_category')}
    </div>
    {if $iTotalItems || $hasSub}
        <div class="form-group">
            <label>{_p('select_an_action_to_apply_for_all_videos_as_well_as_sub_categories_belonging_to_this_category')}</label>
            <div class="radio">
                <label><input type="radio" onchange="core_videos_onchangeDeleteCategoryType(1)" name="val[delete_type]" id="delete_type" value="1" checked>{_p('remove_all_videos_and_sub_categories_belonging_to_this_category')}</label>
            </div>
            {if count($aCategories) > 0}
                <div class="radio">
                    <label><input type="radio" onchange="core_videos_onchangeDeleteCategoryType(2)" name="val[delete_type]" id="delete_type" value="2">{_p('select_another_category_to_move_all_videos_and_sub_categories_belonging_to_this_category')}</label>
                </div>
                <select name="val[new_category_id]" id="category_select" class="form-control" style="display: none">
                    {foreach from=$aCategories item=aCategory}
                        <option value="{$aCategory.category_id}">
                            {$aCategory.name|convert}
                        </option>
                    {/foreach}
                </select>
            {/if}
        </div>
    {else}
        <div><input type="hidden" name="val[delete_type]" value="0" /></div>
    {/if}
    </div>
    <div class="panel-footer">
        <input type="submit" value="{_p('Submit')}" class="btn btn-primary" />
        <input onclick="return js_box_remove(this);" type="submit" value="{_p('Cancel')}" class="btn btn-default" />
    </div>
</form>