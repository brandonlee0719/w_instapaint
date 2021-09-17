<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<form method="post" action="{url link='admincp.egift.delete-category'}">
    <div class="panel panel-default">
        <div class="panel-body">
            <div><input type="hidden" name="delete" value="{$iDeleteId}" /></div>
            <div class="alert alert-warning">
                {_p var='are_you_sure_you_want_to_delete_this_category'}
            </div>
            {if $iTotalItems}
                <div class="form-group">
                    <label>{_p var='select_an_action_to_all_egifts_of_this_category'}</label>
                        <div class="radio">
                            <label><input type="radio" onchange="core_egifts_onchangeDeleteCategoryType(1)" name="val[delete_type]" id="delete_type" value="1" checked>{_p var='remove_all_egifts_belonging_to_this_category'}</label>
                        </div>
                        {if count($aCategories) > 0}
                            <div class="radio"><label><input type="radio" onchange="core_egifts_onchangeDeleteCategoryType(3)" name="val[delete_type]" id="delete_type" value="3">{_p var='select_another_category_for_all_egifts_belonging_to_this_category'}</label></div>
                            <select name="val[new_category_id]" id="category_select" class="form-control" style="display: none">
                                {foreach from=$aCategories item=aCategory}
                                    <option value="{$aCategory.category_id}">
                                        {$aCategory.name|convert}
                                    </option>
                                    {foreach from=$aCategory.sub item=aSubCategory}
                                        <option value="{$aSubCategory.category_id}">
                                            --{$aSubCategory.name|convert}
                                        </option>
                                    {/foreach}
                                {/foreach}
                            </select>
                        {/if}
                    </ul>
                </div>
            {else}
                <div><input type="hidden" name="val[delete_type]" value="0" /></div>
            {/if}
        </div>
        <div class="panel-footer">
            <input type="submit" value="{_p var='submit'}" class="btn btn-primary" />
            <input onclick="return js_box_remove(this);" type="submit" value="{_p var='cancel'}" class="btn btn-default" />
        </div>
    </div>
</form>
