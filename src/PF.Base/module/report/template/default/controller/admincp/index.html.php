<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: index.html.php 2525 2011-04-13 18:03:20Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" action="{url link='admincp.report'}" class="form">
	{if count($aReports)}
    <div class="table-responsive">
        <table class="table table-admin">
            <thead>
                <tr>
                    <th class="w20" >
                        <input type="checkbox" name="val[id]" value="" id="js_check_box_all" class="main_checkbox" />
                    </th>
                    <th>{_p var='module'}</th>
                    <th>{_p var='category'}</th>
                    <th>{_p var='total'}</th>
                    <th>{_p var='date'}</th>
                    <th>{_p var='last_report'}</th>
                    <th>{_p var='feedback'}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$aReports key=iKey item=aReport}
                <tr id="js_row{$aReport.data_id}">
                    <td><input type="checkbox" name="id[]" class="checkbox" value="{$aReport.data_id}" id="js_id_row{$aReport.data_id}" /></td>
                    <td><a href="{url link='admincp.report' view=$aReport.data_id}">{$aReport.module_id|translate:'module'}</a></td>
                    <td>{softPhrase var=$aReport.message}</td>
                    <td class="t_center"><a href="#" onclick="tb_show('{_p var='Reported by'}', $.ajaxBox('report.browse', 'height=400&amp;width=600&amp;data_id={$aReport.data_id}')); return false;">{$aReport.total_report}</a></td>
                    <td>{$aReport.added|date:'core.global_update_time'}</td>
                    <td>{$aReport|user}</td>
                    <td>{$aReport.feedback}</td>
                </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
	<div class="table_hover_action">
		<input type="submit" name="process" value="{_p var='process_selected'}" class="sJsConfirm delete btn btn-primary sJsCheckBoxButton disabled" disabled title="{_p var='User will receive an email notify for their report'}"/>
		<input type="submit" name="ignore" value="{_p var='ignore_selected'}" class="sJsConfirm delete btn sJsCheckBoxButton disabled" disabled="true" />
	</div>
	{else}
	<div class="alert alert-empty">
		{_p var='no_reports'}
	</div>
	{/if}
</form>

{pager}