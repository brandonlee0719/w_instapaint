<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<form class="form" method="post" enctype="multipart/form-data">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">{_p var='add_new_question'}</div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>{_p var='image'}</label>
                <input type="file" name="file" id="input_file" onchange="$Core.User.Spam.fileChanged();" class="form-control" />
                <div id="div_edit_image">
                    {if isset($aQuestion.image_path) && $aQuestion.image_path}
                    <div id="div_edit_image_imge">
                        {img server_id=$aQuestion.server_id path='user.url_user_spam' file=$aQuestion.image_path}
                    </div>
                    <input type="button" class="btn btn-link" id="btn_edit_remove_image" value="{_p var='delete_image'}" onclick="$Core.User.Spam.deleteImage();" />
                    {/if}
                    <input type="hidden" name="val[preserve_image]" value="1" />
                </div>
            </div>
            <div class="form-group">
                <label for="question_text">{_p var='question'}</label>
                <input class="form-control" type="text" name="val[question]" value="{if isset($aQuestion.question_phrase) && isset($aQuestion.question_phrase)}{$aQuestion.question_phrase|clean}{/if}" id="question_text" />
            </div>
            <div class="form-group">
                <label>{_p var='answers'}</label>
            </div>
            {if isset($aQuestion.answers_phrases)}
                {foreach from=$aQuestion.answers_phrases item=msg}
                <div class="valid_answer form-group clearfix">
                    <div class="valid_answer_text input-group">
                        <input type="text" name="val[answer][]" class="form-control" value="{$msg}">
                        <span class="input-group-btn">
                            <a class="btn btn-default" role="button" onclick="$Core.User.Spam.deleteAnswer(this);">
                                <i class="fa fa-remove"></i>
                            </a>
                        </span>
                    </div>
                </div>
                {foreachelse}
                <div class="valid_answer form-group clearfix">
                    <div class="valid_answer_text input-group">
                        <input type="text" name="val[answer][]" class="form-control" value="">
                        <span class="input-group-btn">
                            <a class="btn btn-default" role="button" onclick="$Core.User.Spam.deleteAnswer(this);">
                                <i class="fa fa-remove"></i>
                            </a>
                        </span>
                    </div>
                </div>
                {/foreach}
            {/if}
            <div class="form-group" id="div_add_answers">
                <span id="div_add_answer" onclick="$Core.User.Spam.addAnswer();">
                    <i class="fa fa-plus-circle"></i>{_p var='add_more_answers'}
                </span>
                <div id="div_add_answer">
                </div>
            </div>
        </div>
        <div class="panel-footer">
            {if $iQuestionId}
            <input type="submit" value="{_p var='update'}" id="btn_submit" class="btn btn-primary" />
            {else}
            <input type="submit" value="{_p var='add_question'}" id="btn_submit" class="btn btn-primary" />
            {/if}
            <a class="btn btn-link" href="{url link='admincp.user.spam'}">{_p var='cancel'}</a>
        </div>
    </div>
</form>
<!-- template for adding more answers -->
<div id="tpl_answer" style="display: none;">
    <div class="valid_answer form-group clearfix">
        <div class="valid_answer_text input-group">
            <input type="text" name="val[answer][]" class="form-control"/>
            <span class="input-group-btn">
                <a class="btn btn-default"role="button" onclick='$Core.User.Spam.deleteAnswer(this);'>
                    <i class="fa fa-remove"></i>
                </a>
            </span>
        </div>
    </div>
</div>