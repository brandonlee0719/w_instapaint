<?php 
    defined('PHPFOX') or exit('NO DICE!'); 
?>

{if isset($bIsEditAlbum) && $bIsEditAlbum && !isset($bIsUploaded)}
    <div id="js_music_upload_song" class="music-share-song">
        <div class="form-group">
            <label for="">{_p var='genre'}:</label>
                
            <select class="form-control" multiple="multiple" name="val[genre][]">
                {foreach from=$aGenres item=aGenre}
                    <option value="{$aGenre.genre_id}" {if isset($aForms.genres) && in_array($aGenre.genre_id,$aForms.genres)}selected{/if}>
                        {softPhrase var=$aGenre.name}
                    </option>
                {/foreach}
            </select>
        </div>
        <input type="hidden" name="val[time_stamp]" id="js_upload_time_stamp" value="{$iTimestamp}"/>

        {module name='core.upload-form' type='music_song' id=''}

        <div class="alert alert-danger text-center mb-2" id="js_error_message" style="display: none"></div>
            <div style="display: none" id="js_total_success_holder">
                <b>{_p var='successfully_uploads'}: <span id="js_total_success">0</span> {_p var='song_s'}</b>
            </div>
        <div class="form-group">
            <input type="hidden" name="max_file" id="js_max_file_upload" value="{$iMaxFileUpload}">
        </div>

        <ul id="js_music_uploaded_section" class="music-uploaded-control item-container">
        </ul>

        <p class="help-block">
            <a href="javascript:void(0);" id="js_done_upload" style="display: none !important;" class="btn btn-primary">{_p var='finish'}</a>
        </p>
    </div>
    {if $bIsEdit}
        <input type="hidden" name="val[inline]" value="1" />
        <input type="hidden" name="val[album_id]" id="js_album_id" value="{$aForms.album_id}" />
    {/if}
{else}

    <div class="js_uploaded_file_holder music-item item-outer" id="js_file_holder_{$aForms.song_id}">
        {if !empty($aForms.error) && !$bIsEdit}
            <div class="item-inner">
                <p class="text-danger">{$aForms.title} - {$aForms.error}</p>
                <div class="item-actions">
                    <a href="javascript:void(0)" onclick="$(this).parents('.js_uploaded_file_holder').remove();"><i class="ico ico-close"></i></a>
                </div>
            </div>
        {else}
            {if !$bIsEdit}
            <div class="item-inner">
                <span id="js_song_image_{$aForms.song_id}" class="music-icon-upload"><i class="ico ico-music-note-o"></i></span>
                <a class="item-title " id="js_song_title_{$aForms.song_id}" href="{permalink module='music' id=$aForms.song_id title=$aForms.title}">{$aForms.title}</a>
                <div class="item-actions">
                    {if isset($aForms.canEdit) && $aForms.canEdit}
                        <a href="javascript:void(0)" title="{_p('edit')}" onclick="$Core.music.showForm(this);" class="js_show_form"><i class="ico ico-compose"></i></a>
                        <a href="javascript:void(0)" title="{_p('close')}" style="display: none;" onclick="$Core.music.hideForm(this);" class="js_hide_form" data-id={$aForms.song_id}><i class="ico ico-compose"></i></a>
                    {/if}
                    {if isset($aForms.canDelete) && $aForms.canDelete}
                        <a href="javascript:void(0)" class="delete" title="{_p('delete_this_song')}" onclick="$Core.music.deleteSongInAddForm(this);" data-id="{$aForms.song_id}" data-album-id="{$aForms.album_id}"><i class="ico ico-close"></i></a>
                    {/if}
                </div>
            </div>
            {/if}

            <div class="js_music_form_holder" {if !$bIsEdit}style="display: none;"{/if}>
                {if !$bIsEdit}
                    <div class="valid_message" id="js_music_upload_valid_message" style="display:none;">
                        {_p var='successfully_uploaded_the_mp3'}
                    </div>
                {/if}
                <div>
                    <input type="hidden" name="val[attachment{if !$bIsEdit}_{$aForms.song_id}{/if}]" class="js_attachment" value="{value type='input' id='attachment'}" />
                </div>
                {if isset($sModule) && $sModule}

                {else}
                    <div id="js_custom_privacy_input_holder{if !$bIsEdit}_{$aForms.song_id}{/if}">
                    {if $bIsEdit && Phpfox::isModule('privacy')}
                        {if isset($bIsEditAlbum)}
                            {module name='privacy.build' privacy_item_id=$aForms.album_id privacy_module_id='music_album'}
                        {else}
                            {module name='privacy.build' privacy_item_id=$aForms.song_id privacy_module_id='music_song'}
                        {/if}
                    {/if}
                    </div>
                {/if}
                <div class="music-app info-each-item">
                    <div class="form-group" {if $bIsEdit && !$aForms.album_id && !$bCanSelectAlbum}style="display:none;"{/if}>
                        <label>{_p var='album'}:</label>
                        <select name="{if !$bIsEdit}val[{$aForms.song_id}][album_id]{else}val[album_id]{/if}" id="js_music_album_select" class="form-control" {if isset($bCanSelectAlbum) && !$bCanSelectAlbum}disabled{/if} onchange="if (empty(this.value)) {l} $(this).closest('.js_music_form_holder').find('.js_song_privacy_holder').slideDown(); {r} else {l} $(this).closest('.js_music_form_holder').find('.js_song_privacy_holder').slideUp(); {r}">
                            {if !isset($bCanSelectAlbum) || $bCanSelectAlbum}<option value="0">{_p var='select_an_album'}:</option>{/if}
                            {foreach from=$aAlbums item=aAlbum}
                                <option value="{$aAlbum.album_id}" {if isset($aForms.album_id) && ($aForms.album_id == $aAlbum.album_id)}selected{/if}>{$aAlbum.name|clean}</option>
                            {/foreach}
                        </select>
                    </div>

                    <div class="form-group song_name">
                        <label>{required}{_p var='song_name'}:</label>
                        {if !$bIsEdit}
                            <input class="form-control close_warning js_song_name" type="text" name="val[{$aForms.song_id}][title]" value="{$aForms.title}" size="30" id="title_{$aForms.song_id}" />
                        {else}
                            <input class="form-control close_warning" type="text" name="val[title]" value="{value type='input' id='title'}" size="30" id="title" />
                        {/if}
                    </div>

                    <div class="form-group song_name">
                         <label>{_p var='description'}:</label>
                        {if !$bIsEdit}
                            {editor id='description_'$aForms.song_id}
                        {else}
                            {editor id='description'}
                        {/if}
                    </div>

                    <div class="_form_extra">
                        {if !isset($bIsEditAlbum)}
                            <div class="form-group" style="display:none;">
                                <label>
                                    {if isset($aUploadAlbums) && count($aUploadAlbums)}
                                        {_p var='album'}:
                                    {else}
                                        {_p var='album_name'}:
                                    {/if}
                                </label>
                                {if isset($aUploadAlbums) && count($aUploadAlbums)}
                                    <select class="form-control" name="val[{$aForms.song_id}][album_id]" id="music_album_id_select" onchange="if (empty(this.value)) {l} $('#js_song_privacy_holder').slideDown(); {r} else {l} $('#js_song_privacy_holder').slideUp(); {r}">
                                        <option value="">{_p var='select'}:</option>
                                        {foreach from=$aUploadAlbums item=aAlbum}
                                            <option value="{$aAlbum.album_id}"{value type='select' id='album_id' default=$aAlbum.album_id}>{$aAlbum.name|clean}</option>
                                        {/foreach}
                                    </select>
                                    <div class="extra_info_link"><a href="#" onclick="$('#js_create_new_music_album').show(); $('#js_create_new_music_album input').focus(); return false;">{_p var='or_create_a_new_album'}</a></div>
                                    <div id="js_create_new_music_album" class="p_top_8" style="display:none;">
                                        <input class="form-control" type="text" name="val[{$aForms.song_id}][new_album_title]" value="{value type='input' id='new_album_title'}" size="30" />
                                    </div>
                                {else}
                                    <input class="form-control" type="text" name="val[{$aForms.song_id}][new_album_title]" value="{value type='input' id='new_album_title'}" size="30" /> <span class="extra_info">{_p var='optional'}</span>
                                {/if}
                            </div>
                        {/if}

                        <div class="form-group song_name">
                            <label>{_p var='genre'}:</label>
                            <select class="form-control" multiple="multiple" name="{if !$bIsEdit}val[{$aForms.song_id}][genre][]{else}val[genre][]{/if}">
                            {foreach from=$aGenres item=aGenre}
                                <option value="{$aGenre.genre_id}" {if in_array($aGenre.genre_id,$aForms.genres)}selected{/if}>
                                    {softPhrase var=$aGenre.name}
                                </option>
                            {/foreach}
                            </select>
                        </div>

                        <div class="form-group" id="js_upload_photo_section_{$aForms.song_id}">
                            {template file='music.block.upload-photo'}
                        </div>

                        {if isset($sModule) && $sModule}
                        {else}
                            {if !isset($bIsEditAlbum) && Phpfox::isModule('privacy')}
                            <div id="js_song_privacy_holder" class="js_song_privacy_holder" {if $aForms.album_id > 0}style="display:none"{/if}>
                                <div class="form-group">
                                    <label>{_p var='privacy'}:</label>
                                    {if !$bIsEdit}
                                        {module name='privacy.form' privacy_name='privacy' privacy_array=$aForms.song_id privacy_info='music.control_who_can_see_this_song' default_privacy='music.default_privacy_setting' privacy_custom_id='js_custom_privacy_input_holder_'$aForms.song_id''}
                                    {else}
                                        {module name='privacy.form' privacy_name='privacy' privacy_info='music.control_who_can_see_this_song' default_privacy='music.default_privacy_setting'}
                                    {/if}
                                </div>
                            </div>
                            {/if}
                        {/if}
                        {if !$bIsEdit && isset($aForms.canEdit) && $aForms.canEdit}
                            <div class="form-group">
                                <buton class="btn btn-primary" type="submit" id="js_music_song_submit_{$aForms.song_id}" onclick="$Core.music.editSong(this,true); return false;" data-id="{$aForms.song_id}">{_p('update')}</buton>
                            </div>
                        {/if}
                    </div>
                </div>
            </div>
        {/if}
    </div>
{/if}