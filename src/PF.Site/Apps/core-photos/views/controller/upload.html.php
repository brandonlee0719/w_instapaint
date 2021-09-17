<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Photo
 * @version 		$Id: upload.html.php 5616 2013-04-10 07:54:55Z Miguel_Espinoza $
 */

defined('PHPFOX') or exit('NO DICE!');
?>
{if $bProcess}
<div class="main_break">
    {if isset($aFailed)}
    {foreach from=$aFailed key=iCode item=sFileName}
    <div>
        {$iCode} => {$sFileName}
    </div>
    {/foreach}
    {/if}
    <div class="t_right p_top_10" style="font-size:12pt; font-weight:bold;">
        - <a href="{$sSkipLink}">{_p var='skip_this_step'}</a>
    </div>
    <form method="post" action="{url link='photo.upload.process'}">
        <div id="js_upload_error_message"></div>
        {if $aCallback !== null}
        <div><input type="hidden" name="item" value="{$aCallback.group_id}" /></div>
        <div><input type="hidden" name="module" value="group" /></div>

        {/if}
        {plugin call='photo.template_controller_upload_form_process_hidden'}
        {if isset($aImages.0.album_id) && $aImages.0.album_id > 0}
        <div><input type="hidden" name="val[album_id]" value="{$aImages.0.album_id}" /></div>
        {/if}
        {foreach from=$aNextImages item=iNextImageId}
        <div><input type="hidden" name="val[photo_id][]" value="{$iNextImageId}" /></div>
        {/foreach}

        {if count($aImages) > 1}
        <h3>{_p var='global_photo_settings'}</h3>
        <p>{_p var='note_that_global_photo_settings_will_override_any_settings_saved_individually_below'}</p>

        {template file="photo.block.form" bGlobalSetting=true}

        <div class="table_clear">
            <input type="submit" value="{_p var='save_global_settings'}" class="button btn-primary" name="save_global" />
        </div>

        <br />

        <h3>{_p var='uploaded_photos'}</h3>
        {/if}
        {foreach from=$aImages item=aImage name=images}
        {img thickbox=true server_id=$aImage.server_id path='photo.url_photo' file=$aImage.destination suffix='_50' max_width=75 max_height=75 style="position:absolute; right:0px; padding-right:20px; padding-top:5px;"}
        <div class="{if is_int($phpfox.iteration.images/2)}row1{else}row2{/if}{if $phpfox.iteration.images == 1} row_first{/if}">
            {template file='photo.block.form'}
        </div>
        {/foreach}

        <div class="table_clear">
            <input type="submit" value="{_p var='save'}" class="button btn-primary" name="save" /> {_p var='and_then'}
            <select name="val[action]" style="vertical-align:middle;">
                {if $iTotalNextBatch}
                <option value="process">{_p var='process_next_batch_total_left' total=$iTotalNextBatch}</option>
                {/if}
                {if isset($aImages.0.album_id) && $aImages.0.album_id > 0}
                <option value="view_album">{_p var='view_this_album'}</option>
                {/if}
                <option value="view_photo">{_p var='view_your_photos'}</option>
                <option value="upload">{_p var='upload_new_images'}</option>
                {plugin call='photo.template_controller_upload_form_actions'}
            </select>
        </div>
    </form>
    {if isset($aImages.0.album_id) && $aImages.0.privacy > 0}
    <script type="text/javascript">$('.js_public_rating').remove();</script>
    {/if}
</div>
{else}
<div class="main_break"></div>

<div id="js_album_form" style="display:none;">
    {$sCreateJs}
    <form method="post" action="{url link='current'}" id="js_create_new_album" onsubmit="return addNewAlbum();">
        {template file='photo.block.form-album'}
        <div class="table_clear">
            <input type="submit" value="{_p var='submit'}" class="button" /> - <a href="#" onclick="$('#js_album_form').hide(); $('#js_upload_form').show(); $('#content h1').html('{_p var='upload_photos' phpfox_squote=true}'); return false;">{_p var='cancel_lowercase'}</a>
        </div>
        {if Phpfox::getParam('core.display_required')}
        <div class="table_clear">
            {required} {_p var='required_fields'}
        </div>
        {/if}
    </form>
</div>

<div id="js_upload_form">
    <div id="js_upload_error_message"></div>
    <div id="js_upload_inner_form">
        <form method="post" action="{url link='photo.frame'}" id="js_form" enctype="multipart/form-data" target="js_upload_frame" onsubmit="return startProcess(true, true);">
            <div><input type="hidden" name="APC_UPLOAD_PROGRESS" id="progress_key" value="{$iUploadId}"/></div>
            {if $aCallback !== null}
            <div><input type="hidden" name="val[item]" value="{$aCallback.group_id}" /></div>
            <div><input type="hidden" name="val[module]" value="group" /></div>
            <div><input type="hidden" name="val[group_id]" value="{$aCallback.group_id}" /></div>
            <div><input type="hidden" name="val[group_title_url]" value="{$aCallback.title_url}" /></div>
            <div><input type="hidden" name="val[module_id]" value="group" /></div>
            <div><input type="hidden" name="val[album_id]" value="0" id="js_is_photo_callback" /></div>
            {else}
            <div><input type="hidden" name="val[group_id]" value="0" /></div>
            {/if}
            {if isset($sMethod)}
            <div><input type="hidden" name="val[method]" value="{$sMethod}" /></div>
            {/if}
            {plugin call='photo.template_controller_upload_form'}
            {if $aCallback === null && Phpfox::getUserParam('photo.can_create_photo_album')}
            <div class="table form-group" id="album_table">
                <div class="table_left">
                    {_p var='album'}:
                </div>
                <div class="table_right_text">
                    <span id="js_photo_albums"{if !count($aAlbums)} style="display:none;"{/if}>
                    <select name="val[album_id]" id="js_photo_album_select" style="width:200px;">
                        <option value="">{_p var='select_an_album'}:</option>
                        {foreach from=$aAlbums item=aAlbum}
                        <option value="{$aAlbum.album_id}"{if $iAlbumId == $aAlbum.album_id} selected="selected"{/if}>{$aAlbum.name|clean}</option>
                        {/foreach}
                    </select>
                    {_p var='or'}
                    </span>
                    <a href="#" onclick="$('#js_upload_form').hide(); $('#js_album_form').show(); $('#content h1').html('{_p var='create_a_new_album' phpfox_squote=true}'); return false;">{_p var='create_a_new_one'}</a>.
                </div>
            </div>
            {/if}
            <div class="table form-group">
                <div class="table_left">
                    {if isset($sMethod) && $sMethod == 'simple'}
                    {_p var='select_photo_s'}:
                    {/if}
                </div>
                <div class="table_right">
                    {if isset($sMethod) && $sMethod == 'massuploader'}
                    <div id="swfUploaderContainer">
                        <div id="swfUploaderButton"></div>
                    </div>
                    <span id="swfUploadText">{_p var='select_photo_s'}</span>
                    {else if isset($sMethod) && $sMethod =='simple'}
                    <div id="js_progress_uploader"></div>
                    {/if}

                </div>
            </div>
            <div class="clear">&nbsp;</div>
            <div class="table_clear">
                <div id="js_upload_form_outer">
                    {if isset($sMethod) && $sMethod == 'simple'}
                    <input type="submit" value="{_p var='upload'}" class="button btn-primary" />
                    {/if}
                    {_p var='and_then'}
                    <select name="val[action]" id="js_photo_action" style="vertical-align:middle;">
                        <option value="process">{_p var='process_your_photos'}</option>
                        <option value="upload">{_p var='upload_more_photos'}</option>
                        {if $iAlbumId}<option value="view_album" id="js_photo_view_this_album">{_p var='view_this_album'}</option>{/if}
                        <option value="view_photo">{_p var='view_your_photos'}</option>
                        {plugin call='photo.template_controller_upload_form_actions'}
                    </select>
                </div>
                <div id="js_progress_bar"></div>
            </div>

            <div class="separate"></div>

            <div class="extra_info">
                {_p var='you_can_upload_a_jpg_gif_or_png_file'}
                {if $iMaxFileSize !== null}
                <br />
                {_p var='the_file_size_limit_is_file_size_if_your_upload_does_not_work_try_uploading_a_smaller_picture' file_size=$iMaxFileSize|filesize}
                {/if}
            </div>

            {if isset($sMethod) && $sMethod=='massuploader'}
            <div class="p_10">
                {_p var='upload_problems' link=$sMethodUrl}
            </div>
            {/if}

            {plugin call='photo.template_controller_upload_form_extra'}
        </form>
    </div>
    <div id="js_uploaded_images" style="display:none;">
        <h3>{_p var='recently_uploaded'}</h3>
        <div class="label_flow" style="height:200px;">
            <div id="js_uploaded_images_data"></div>
        </div>
        <form method="post" action="{url link='photo.upload.process'}" id="js_post_form">
            {if $aCallback !== null}
            <div><input type="hidden" name="item" value="{$aCallback.group_id}" /></div>
            <div><input type="hidden" name="module" value="group" /></div>
            {/if}
            <div id="js_post_form_content"></div>
            <div style="padding-top:5px; text-align:right;">
                <input type="submit" value="{_p var='process_your_photo_s'}" class="button btn-primary" />
            </div>
        </form>
    </div>
</div>
{/if}