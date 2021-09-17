<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<form method="post" action="{url link='admincp.blog.delete-category'}">
    <div class="panel panel-default">
        <div class="panel-body">
            <div><input type="hidden" name="delete" value="{$iDeleteId}" /></div>
            <div class="alert alert-warning">
                {_p var='are_you_sure_you_want_to_delete_this_category'}
            </div>
            {if $iTotalItems || $hasSub}
                <div class="form-group">
                    <label>{_p var='Select an action to apply for all blogs as well as sub categories belonging to this category'}</label>
                        <div class="radio">
                            <label><input type="radio" onchange="core_blogs_onchangeDeleteCategoryType(1)" name="val[delete_type]" id="delete_type" value="1" checked>{_p var='Remove all blogs and sub-categories belonging to this category'}</label>
                        </div>
                        {if count($aCategories) > 0}
                            <div class="radio"><label><input type="radio" onchange="core_blogs_onchangeDeleteCategoryType(3)" name="val[delete_type]" id="delete_type" value="3">{_p var='Select another category to move all blogs and sub-categories belonging to this category'}</label></div>
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
            <input type="submit" value="{_p('Submit')}" class="btn btn-primary" />
            <input onclick="return js_box_remove(this);" type="submit" value="{_p('Cancel')}" class="btn btn-default" />
        </div>
    </div>
</form>
