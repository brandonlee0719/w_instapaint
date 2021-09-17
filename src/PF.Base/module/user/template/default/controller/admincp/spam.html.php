<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="panel panel-default">
    {if count($aQuestions)}
    <table class="table table-admin">
        <tr class="tbl_questions_header">
            <th style="width: 66px;"></th>
            <th>{_p var='image'}</th>
            <th>{_p var='question'}</th>
            <th>{_p var='answers'}</th>
        </tr>
        {foreach from=$aQuestions item=aQuestion key=index}
        <tr id="tr_new_question_{$aQuestion.question_id}" class="{if $index % 2}checkRow{else}tr{/if}" style="display: table-row;">
            <td class="question_actions">
                <a href="#" class="js_drop_down_link" title="{_p var='manage'}"></a>
                <div class="link_menu">
                    <ul>
                        <li><a href="{url link='admincp.user.spams.add' id=$aQuestion.question_id}">{_p var='edit'}</a></li>
                        <li><a href="{url link='admincp.user.spam' delete={$aQuestion.question_id}" class="sJsConfirm" data-message="{_p var='are_you_sure'}">{_p var='delete'}</a></li>
                    </ul>
                </div>
            </td>
            <td class="question_image">
                {img server_id=$aQuestion.server_id path='user.url_user_spam' file=$aQuestion.image_path}
            </td>
            <td class="question_question">
                {$aQuestion.question_phrase}
            </td>
            <td class="question_answers">
                <ul>
                    {foreach from=$aQuestion.answers_phrases item=sAnswer}
                    <li>{$sAnswer}</li>
                    {/foreach}
                </ul>
            </td>
        </tr>
        {/foreach}
    </table>
    {else}
    <div class="panel-body">
        <div class="alert alert-info">{_p var='there_is_no_question'}</div>
    </div>
    {/if}
</div>