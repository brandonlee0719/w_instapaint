<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<div id="js_actual_upload_form" class="music-share-song">
	{if $bIsEdit}
	<form method="post" action="{url link='music.upload' album=$iAlbumId}" enctype="multipart/form-data">
	<div><input type="hidden" name="id" value="{$aForms.song_id}" /></div>
	<div><input type="hidden" name="upload_via_song" value="1" /></div>
	{else}
	<form method="post" action="{url link='music.upload'}" enctype="multipart/form-data" id="js_music_upload_form">
    <input type="hidden" name="val[time_stamp]" id="js_upload_time_stamp" value="{$iTimestamp}"/>
	{/if}
	{if isset($sModule) && $sModule}
		<div><input type="hidden" name="val[callback_module]" value="{$sModule}" /></div>
	{/if}
	{if isset($iItem) && $iItem}
		<div><input type="hidden" name="val[callback_item_id]" value="{$iItem}" /></div>
	{/if}
        {if !$bIsEdit}
        <div id="js_custom_privacy_input_holder">
        </div>

        <div class="form-group">
            <label for="js_music_album_select">{_p var='album'}:</label>
            <select name="val[album_id]" id="js_music_album_select" class="form-control">
                <option value="0">{_p var='select_an_album'}:</option>
                {foreach from=$aAlbums item=aAlbum}
                    <option value="{$aAlbum.album_id}">{$aAlbum.name|clean}</option>
                {/foreach}
            </select>
        </div>

         <div class="form-group">
            <label for="js_music_album_genre"> {_p var='genre'}:</label>
            <select class="form-control" multiple="multiple" id="js_music_album_genre" name="val[genre][]">
                {foreach from=$aGenres item=aGenre}
                    <option value="{$aGenre.genre_id}" {if isset($aForms.genres) && in_array($aGenre.genre_id,$aForms.genres)}selected{/if}>
                        {softPhrase var=$aGenre.name}
                    </option>
                {/foreach}
            </select>
        </div>
        {if isset($sModule) && $sModule}
            <input type="hidden" name="val[privacy]" value="0">
        {else}
            <div id="js_song_privacy_holder">
                <div class="form-group">
                    <label>{_p var='privacy'}:</label>
                    {module name='privacy.form' privacy_name='privacy' privacy_info='music.control_who_can_see_these_song_s' default_privacy='music.default_privacy_setting'}
                </div>
            </div>
        {/if}
		<div id="js_music_upload_song">

            {module name='core.upload-form' type='music_song'}

            <div class="alert alert-danger text-center mb-2 mt-2" id="js_error_message" style="display: none"></div>
            <div style="display: none" id="js_total_success_holder">
                <b>{_p var='successfully_uploads'}: <span id="js_total_success">0</span> {_p var='song_s'}</b>
            </div>

            <div class="form-group">
                <input type="hidden" name="max_file" id="js_max_file_upload" value="{$iMaxFileUpload}">
            </div>

            <div class="form-group">

                <ul id="js_music_uploaded_section" class="music-uploaded-control item-container">
                </ul>
            </div>

            <p class="help-block">
                <a href="javascript:void(0);" id="js_done_upload" style="display: none !important;" class="btn btn-primary">{_p var='finish'}</a>
            </p>
		</div>
        {else}
            {template file='music.block.upload'}
           <div class="form-group">
                <button onclick="$Core.music.editSong(this,false); return false;" id="js_music_song_submit_{$aForms.song_id}" data-id="{$aForms.song_id}" class="button btn-primary">{_p var='update'}</button>
            </div>
        {/if}
	</form>
</div>
