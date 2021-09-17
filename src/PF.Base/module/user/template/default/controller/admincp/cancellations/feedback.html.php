<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if empty($aFeedbacks)}
<div class="alert alert-info">
    {_p var='no_feedback_to_review'}
</div>
{else}
<div class="table-responsive">
    <table class="table table-admin">
        <thead>
            <tr>
                <th {table_sort class="w200" asc="udf.full_name asc" desc="udf.full_name desc" query="sort" current=$sCurrent}> {_p var='full_name'} </th>
                <th {table_sort class="w200" asc="udf.user_email asc" desc="udf.user_email desc" query="sort" current=$sCurrent}> {_p var='e_mail'} </th>
                <th {table_sort class="w100" asc="ug.title asc" desc="ug.title desc" query="sort" current=$sCurrent}> {_p var='user_group'} </th>
                <th class=""> {_p var='reasons_given'} </th>
                <th {table_sort class="w200" asc="udf.feedback_text asc" desc="udf.feedback_text desc" query="sort" current=$sCurrent}> {_p var='feedback_text'} </th>
                <th {table_sort class="w100" asc="udf.time_stamp asc" desc="udf.time_stamp desc" query="sort" current=$sCurrent}> {_p var='deleted_on'} </th>
                <th class="w100">{_p var='options'}</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$aFeedbacks item=aFeedback key=iKey name=feedback}
            <tr>
                <td>
                    {$aFeedback.full_name}
                </td>
                <td>
                    {$aFeedback.user_email}
                </td>
                <td>
                    {$aFeedback.user_group_title}
                </td>
                <td>
                    {if isset($aFeedback.reasons)}
                    {foreach from=$aFeedback.reasons item=phrase_var}
                    {_p var=$phrase_var} <br>
                    {/foreach}
                    {/if}
                </td>
                <td>
                    {$aFeedback.feedback_text|clean|shorten:'15':'View More':true|split:30}
                </td>
                <td>
                    {$aFeedback.time_stamp|date:'core.global_update_time'}
                </td>
                <td>
                    <a href="#" onclick="$.ajaxCall('user.deleteFeedback', 'iFeedback={$aFeedback.feedback_id}')">{_p var='delete'}</a>
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>
{/if}