<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<article class="albums-item list-view music" id="js_album_{$aAlbum.album_id}">
	<div class="item-outer">
        <div class="item-media albums-bg view-all">
            <div class="item-media-inner">
                <a  href="{permalink title=$aAlbum.name id=$aAlbum.album_id module='music.album'}"
                    class="music-bg-thumb thumb-border"
                    style="background-image:url(
                        {if $aAlbum.image_path}
                            {img return_url="true" server_id=$aAlbum.server_id title=$aAlbum.name path='music.url_image' file=$aAlbum.image_path suffix=''}
                        {else}
                            {param var='music.default_album_photo'}
                        {/if}
                    )">
                    <span class="albums-songs"><i class="ico ico-music-note"></i>{$aAlbum.total_track}</span>
                    <span class="music-overlay"><i class="ico ico-play-circle-o"></i></span>
                    <div class="flag_style_parent">
                        {if $aAlbum.is_sponsor}
                        <div class="sticky-label-icon sticky-sponsored-icon">
                            <span class="flag-style-arrow"></span>
                            <i class="ico ico-sponsor"></i>
                        </div>
                        {/if}
                        {if $aAlbum.is_featured}
                        <div class="sticky-label-icon sticky-featured-icon">
                            <span class="flag-style-arrow"></span>
                            <i class="ico ico-diamond"></i>
                        </div>
                        {/if}
                    </div>
                </a>
               {if isset($bShowModerator) && $bShowModerator && !isset($aAlbum.is_in_feed)}
                    <div class="moderation_row">
                           <label class="item-checkbox">
                               <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aAlbum.album_id}" id="check{$aAlbum.album_id}" />
                               <i class="ico ico-square-o"></i>
                           </label>
                    </div>
               {/if}
            </div>
            <div class="albums-bg-outer"><div class="albums-bg-inner"></div></div>
        </div>

        <div class="item-inner">
            <div class="item-title">
                <a href="{permalink title=$aAlbum.name id=$aAlbum.album_id module='music.album'}">{$aAlbum.name|clean}</a>
            </div>

            <div class="item-statistic dot-separate">
                <span>
                    {if $aAlbum.total_play != 1}
                        {_p var='music_total_plays' total=$aAlbum.total_play|short_number}
                    {else}
                        {_p var='music_total_play' total=$aAlbum.total_play|short_number}
                    {/if}
                </span>
                <span class="music-dots">.</span>
                <span>
                    {if $aAlbum.total_view != 1}
                        {_p var='music_total_views' total=$aAlbum.total_view|short_number}
                    {else}
                        {_p var='music_total_view' total=$aAlbum.total_view|short_number}
                    {/if}
                </span>  
            </div>
            {if !isset($aAlbum.is_in_feed)}
                <div class="item-author">
                    {if $aAlbum.user_image}
                        <a  class="item-author-avatar" href="#"
                            style="background-image: url(
                                {img user=$aAlbum suffix='_50_square' return_url=true}
                            )">
                        </a>
                    {else}
                        {img user=$aAlbum suffix='_50_square'}
                    {/if}
                    <div class="item-author-infor">
                        <span>{_p var='by'} {$aAlbum|user}</span>
                        <span>{$aAlbum.time_stamp|convert_time}</span>
                    </div>
                </div>
                {if $aAlbum.hasPermission}
                    <div class="item-option">
                        <div class="dropdown">
                            <span role="button" class="row_edit_bar_action" data-toggle="dropdown">
                                <i class="ico ico-gear-o"></i>
                            </span>
                            <ul class="dropdown-menu dropdown-menu-right">
                                {template file='music.block.menu-album'}
                            </ul>
                        </div>
                    </div>
                {/if}
            {/if}
        </div>
    </div>
</article>