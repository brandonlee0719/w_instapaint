<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="alert alert-warning">
	{_p var='database_size'}: {$iSize|filesize} - {_p var='overhead'}: {$iOverhead|filesize} - {_p var='total_tables'}: {$iCnt}
</div>

<form method="post" action="{url link='admincp.sql'}" class="form">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">{_p var='sql_tables'}</div>
        </div>
        <div class="table-responsive">
            <table class="table table-admin">
                <thead>
                <tr>
                    <th style="width:10px;"><input type="checkbox" name="val[id]" value="" id="js_check_box_all" class="main_checkbox" /></th>
                    <th>{_p var='table'}</th>
                    <th class="t_center">{_p var='records'}</th>
                    <th class="t_center">{_p var='size'}</th>
                    <th class="t_center">{_p var='overhead'}</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$aItems key=iKey item=aItem}
                <tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                    <td><input type="checkbox" name="tables[]" class="checkbox" value="{$aItem.Name}" id="js_id_row{$iKey}" /></td>
                    <td>{$aItem.Name}</td>
                    <td class="t_center">{$aItem.Rows}</td>
                    <td class="t_center">{$aItem.Data_length|filesize}</td>
                    <td class="t_center">{$aItem.Data_free|filesize}</td>
                </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
        <div class="panel-footer">
            <div class="form-group">
                <input type="submit" name="optimize" value="{_p var='optimize_table_s'}" class="btn btn-primary sJsCheckBoxButton disabled" disabled="true" />
                <input type="submit" name="repair" value="{_p var='repair_table_s'}" class="btn btn-danger sJsCheckBoxButton disabled" disabled="true" />
            </div>
        </div>
    </div>
</form>