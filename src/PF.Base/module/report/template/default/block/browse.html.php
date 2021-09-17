<?php 
defined('PHPFOX') or exit('NO DICE!');

?>
<div class="table-responsive">
    <table class="table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th>{_p var='user'}</th>
                <th>{_p var='category'}</th>
                <th>{_p var='date'}</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$aReports item=aReport}
            <tr>
                <td>{$aReport|user}</td>
                <td>{_p var=$aReport.message}</td>
                <td>{$aReport.added|date:'core.global_update_time'}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>