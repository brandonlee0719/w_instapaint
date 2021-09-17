<?php 
defined('PHPFOX') or exit('NO DICE!');

?>
<form class="form" method="post" action="{url link='admincp.report.category'}">
	{if count($aCategories)}
    <div class="table-responsive">
        <table class="table table-admin">
            <thead>
        <tr>
            <th style="width:10px;"><input type="checkbox" name="val[id]" value="" id="js_check_box_all" class="main_checkbox" /></th>
            <th style="width: 10px"></th>
            <th>{_p var='category'}</th>
            <th>{_p var='module'}</th>
        </tr>
            </thead>
            <tbody>
        {foreach from=$aCategories key=iKey item=aCategory}
        <tr id="js_row{$aCategory.report_id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
            <td><input type="checkbox" name="id[]" class="checkbox" value="{$aCategory.report_id}" id="js_id_row{$aCategory.report_id}" /></td>
            <td class="t_center">
                <a href="#" class="js_drop_down_link" title="{_p var='Manage'}"></a>
                <div class="link_menu">
                    <ul>
                        <li><a href="#" onclick="tb_show(oTranslations['delete_category'], $.ajaxBox('report.deleteCategory', 'height=400&width=600&report_id={$aCategory.report_id}')); return false;">{_p var='delete'}</a></li>
                        <li>
                            <a href={url link='admincp.report.add' id=$aCategory.report_id}"">
                                {_p var='edit'}
                            </a>
                        </li>
                    </ul>
                </div>
            </td>
            <td>{_p var=$aCategory.message}</td>
            <td>{$aCategory.module_id|translate:'module'}</td>
        </tr>
        {/foreach}
            </tbody>
        </table>
    </div>
	<div class="table_bottom">
		<input type="submit" name="delete" value="{_p var='delete_selected'}" class="sJsConfirm delete button sJsCheckBoxButton disabled" disabled="true" />
	</div>
	{else}
	<p class="alert alert-empty">
		{_p var='no_categories'}
	</p>
	{/if}
</form>