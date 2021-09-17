<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="pf_process_form pf_video_process_form">
    <span></span>
    <div class="pf_process_bar"></div>
    <div class="extra_info">{_p('pf_video_uploading_message_share')}</div>
</div>
<div class="pf_video_message" style="display:none;">
    <div class="alert alert-success">{if !$bIsAjaxBrowsing} {_p('your_video_has_successfully_been_uploaded_we_are_processing_it_and_you_will_be_notified_when_its_ready_you_can_edit_detail')} {else} {_p('your_video_has_successfully_been_uploaded_we_are_processing_it_and_you_will_be_notified_when_its_ready_you_can_share_you_think_for_your_video')}{/if}</div>
    <div>
        <a href="#" class="pf_v_upload_success_cancel button btn-sm">{_p('cancel_and_remove_video_uploaded')}</a>
    </div>
</div>
<div class="alert alert-danger" id="pf_video_add_error_link" style="display: none"></div>

{if $bUploadSuccess}
    <div id="pf_v_success_message">
        <div class="alert alert-info">{_p('your_video_has_successfully_been_saved_and_will_be_published_when_we_are_done_processing_it')}</div>
        <div class="form-group">
            <a href="#" class="pf_v_success_continue button btn-sm btn btn-primary">{_p('Continue')}</a>
        </div>
    </div>
{/if}
{if isset($bAddFalse) && $bAddFalse}
    <div id="pf_video_add_error" class="alert alert-danger">{_p('we_could_not_find_a_video_there_please_try_again')}</div>
{/if}

{if !$bIsAjaxBrowsing}
    <div class="pf_upload_form" {if $bUploadSuccess} style="display: none" {/if}>
        {if $bAllowVideoUploading}
            <div class="pf_select_video" id="pf_select_video_no_ajax">
                {module name='core.upload-form' type='v'}
                <span class="extra_info hide_it">
                    <a href="#" class="pf_v_upload_cancel button btn-sm">{_p('Cancel')}</a>
                </span>
            </div>
        {/if}
        <form method="post" data-add-spin="true" id="core_js_video_form" {if isset($sGetJsForm)}onsubmit="{$sGetJsForm}"{/if}
              action="{if isset($sModule) && !empty($sModule)}{url link='video.share' module=$sModule item=$iItemId}{else}{url link='video.share'}{/if}"
        >
            {$sCreateJs}
            <div><input type="hidden" name="val[default_image]" value="" id="video_default_image" /></div>
            <div><input type="hidden" name="val[embed_code]" value="" id="video_embed_code"/></div>
            {if $iItemId}
                <div><input type="hidden" name="val[callback_module]" value="{$sModule}"></div>
                <div><input type="hidden" name="val[callback_item_id]" value="{$iItemId}"></div>
            {/if}

            <div class="pf_v_video_url">
                <div class="table form-group">
                    <div class="table_right">
                        <input class="form-control" type="text" oninput="$('.pf_v_url_cancel').hide();" name="val[url]" id="video_url" size="40" placeholder="{if $bAllowVideoUploading}{_p('or paste a URL')}{else}{_p('Paste a URL')}{/if}"/>
                    </div>
                    <span class="extra_info hide_it">
                        <a href="#" class="pf_v_url_cancel">{_p('Cancel')}</a>
                        <span style="display: none;" class="form-spin-it pf_v_url_processing"><i class="fa fa-spin fa-circle-o-notch"></i></span>
                    </span>
                </div>
            </div>
            <div class="table_clear"></div>

            <div class="form-group">
                    <label for="title">{required}{_p('title')}:</label>
                    <input class="form-control close_warning" type="text" name="val[title]" value="{value type='input' id='title'}" id="title" size="40" required/>
            </div>
            {plugin call='video.template_controller_add_textarea_start'}
            <div class="form-group">
                <label for="text">{_p('description')}:</label>
                {editor id='text'}
            </div>
            <div class="form-group">
                    <label for="text">{_p('categories')}:</label>
                        {module name='v.add_category_list'}
            </div>
            {if empty($sModule)}
            <div class="form-group">
                    <label for="text">{_p('privacy')}:</label>
                    {if Phpfox::isModule('privacy')}
                        {module name='privacy.form' privacy_name='privacy' privacy_info='video_control_who_can_see_this_video' default_privacy='v.default_privacy_setting'}
                    {/if}
            </div>
            {/if}
            <div class="pf_v_video_submit">
                <div class="table_clear">
                    <ul class="table_clear_button">
                        {plugin call='video.template_controller_add_submit_buttons'}
                        <li><input type="submit" name="val[update]" value="{_p var='save'}" class="button btn-primary" /></li>
                    </ul>
                    <div class="clear"></div>
                </div>
            </div>
        </form>
    </div>
{else}
    {if $bAllowVideoUploading}
        <div class="pf_select_video">
            {module name='core.upload-form' type='v'}
            <span class="extra_info hide_it">
                <a href="#" class="pf_v_upload_cancel button btn-sm">{_p('Cancel')}</a>
            </span>
        </div>
    {/if}
    <div class="pf_v_video_url">
        <div class="table form-group">
            <div class="table_right">
                <input class="form-control" oninput="$('.pf_v_url_cancel').hide();" type="text" name="val[url]" id="video_url" size="40" placeholder="{if $bAllowVideoUploading}{_p('or paste a URL')}{else}{_p('Paste a URL')}{/if}"/>
            </div>
            <span class="extra_info hide_it">
                <a href="#" class="pf_v_url_cancel">{_p('Cancel')}</a>
                <span style="display: none;" class="form-spin-it pf_v_url_processing"><i class="fa fa-spin fa-circle-o-notch"></i></span>
            </span>
        </div>
    </div>

    <div class="pf_video_caption" style="display:none;">
        <div class="table">
            <div class="table_right">
                <input class="form-control" type="text" placeholder="{_p('video_title')}" name="val[title]" value="" id="title" size="40" />
            </div>
        </div>
    </div>
    <div id="pf_v_share_success_message" style="display: none">
        <div class="alert alert-info">
            {_p('your_video_has_successfully_been_saved_and_will_be_published_when_we_are_done_processing_it')}
            <div class="form-group">
                <a href="#" class="pf_v_message_cancel button btn-sm pull-right">{_p('Continue')}</a>
            </div>
        </div>
    </div>
{/if}