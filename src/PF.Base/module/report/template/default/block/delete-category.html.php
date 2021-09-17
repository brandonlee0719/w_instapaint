<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
?>
<div class="table-responsive">
    <form class="form" method="post" action="{url link='admincp.report.category'}">
        <input type="hidden" name="report_id" value="{$iReportId}">
        {if $iNumberOfReport > 0}
        {if count($aAnotherCategories)}
        {_p var='choose_action_to_do_with_child_reports'}
        <div class="radio">
            <label>
                <input type="radio" name="child_action" value="del" onclick="$('#category_id').attr('disabled', true);" checked>{_p var="delete_all_reports"}
            </label>
        </div>
        <div class="radio">
            <label>
                <input type="radio" name="child_action" value="move" onclick="$('#category_id').attr('disabled', false);">{_p var="move_all_reports"}
            </label>
        </div>
        <label for="category_id">{_p var="move_all_reports_to"}</label>
        <select name="category_id" class="form-control" id="category_id" disabled>
            {foreach from=$aAnotherCategories item=aCategory}
            <option value="{$aCategory.report_id}">{_p var=$aCategory.message}</option>
            {/foreach}
        </select>
        {else}
        <p class="help-block">{_p var='delete_category_notice'}</p>
        {/if}
        {else}
        <p class="help-block">{_p var='are_you_sure'}</p>
        {/if}
        <input type="submit" value="{_p var='delete'}" class="btn btn-danger">
        <input type="button" onclick="return js_box_remove(this);" class="btn" value="{_p var='cancel'}">
    </form>
</div>