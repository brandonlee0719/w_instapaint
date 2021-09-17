<?php 

 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<link href="{param var='core.path_actual'}PF.Site/Apps/core-music/assets/jscript/mediaelementplayer/mediaelementplayer.css" rel="stylesheet" type="text/css">

<div class="album-detail item_view">
    <div class="item_info">
            {img user=$aAlbum suffix='_50_square'}
        <div class="item_info_author">
            <div>{_p var='By'} {$aAlbum|user:'':'':50}</div>
            <span>{$aAlbum.time_stamp|convert_time}</span>
        </div>
    </div>
    
    {if $aAlbum.view_id != 0}
    <div class="message js_moderation_off" id="js_approve_message">
        {_p var='album_is_pending_approval'}
    </div>
    {/if}

    {if $aAlbum.hasPermission}
        <div class="item_bar">
            <div class="item_bar_action_holder">
                <span role="button" data-toggle="dropdown" class="item_bar_action"><i class="ico ico-gear-o"></i></span>
                <ul class="dropdown-menu dropdown-menu-right">
                    {template file='music.block.menu-album'}
                </ul>
            </div>
        </div>
    {/if}

    {if $aAlbum.view_id == 0}
        <div class="item-comment mb-2">
            <div>
                {module name='feed.mini-feed-action'}
            </div>
            <span class="item-total-view">
                <span>
                {if $aAlbum.total_view != 1}
                    {_p var='music_total_views' total=$aAlbum.total_view|short_number}
                {else}
                    {_p var='music_total_view' total=$aAlbum.total_view|short_number}
                {/if}
                </span>
                <span>
                {if $aAlbum.total_play != 1}
                    {_p var='music_total_plays' total=$aAlbum.total_play|short_number}
                {else}
                    {_p var='music_total_play' total=$aAlbum.total_play|short_number}
                {/if}
                </span>
            </span>
        </div>
    {/if}
    
    <div class="item-content list-view music">
        <div class="item-media view-all albums-bg" style="background-image: url('{param var='core.path_actual'}PF.Site/Apps/core-music/assets/image/song_detail_bg.png');">
            <a href="javascript:void(0)">
                {if !empty($aAlbum.image_path)}
                    <span class="music-bg-thumb thumb-border" style="background-image: url('{img server_id=$aAlbum.server_id path='music.url_image' file=$aAlbum.image_path suffix='_200_square' max_width='200' max_height='200' return_url=true}')"></span>
                {else}
                    <img src="{param var='music.default_song_photo'}">
                {/if}
                <div class="albums-bg-outer">
                    <div class="albums-bg-inner"></div>
                </div>
            </a>

            <div class="item-total text-uppercase text-center">
                <strong>{$aAlbum.total_track}</strong> {if $aAlbum.total_track == 1}{_p var='song'}{else}{_p var='songs'}{/if}
            </div>
            <div class="item-playing mt-3 text-center" style="display: none;">
                {_p var='playing_uppercase'}: <strong id="js_playing_song_title"></strong>
            </div>
        </div>
    </div>
 
    <div class="item-player music_player">
        <div class="music_player-inner">
            {module name='music.track' inline_album=true}
        </div>

        <div class="item-sub-info mt-3">
            <div class="item-description item_view_content">
                <p><b class="text-uppercase">{_p('year')}: {$aAlbum.year}</b></p>
                <p class="text-uppercase title">{_p('description')}</p>
                {$aAlbum.text|highlight:'search'|parse|shorten:200:'feed.view_more':true|split:55|max_line}
            </div>
        </div>
        {if $aAlbum.total_attachment}
            {module name='attachment.list' sType=music_album iItemId=$aAlbum.album_id}
        {/if}
    </div>
    

    <div class="js_moderation_on pt-2 mt-4" {if $aAlbum.view_id != 0}style="display:none;" class="js_moderation_on"{/if}>
        <div class="item-addthis mb-2">{addthis url=$aAlbum.bookmark title=$aAlbum.name description=$sShareDescription}</div>
        <div class="item-detail-feedcomment">
            {module name='feed.comment'}
        </div>
    </div>
</div>