<?php
    defined('PHPFOX') or exit('NO DICE!');
?>
<div class="album-item">
    <div class="item-outer">
        <!-- photo -->
        <div class="item-media albums-bg">
            <div class="item-media-inner">
                <a  class="music-bg-thumb thumb-border gradient" href="{if isset($aAlbum.sponsor_id)}{url link='ad.sponsor' view=$aAlbum.sponsor_id}{else}{permalink module='music.album' id=$aAlbum.album_id title=$aAlbum.name}{/if}"
                    style="background-image: url(
                        {if $aAlbum.image_path}
                            {img return_url="true" server_id=$aAlbum.server_id path='music.url_image' file=$aAlbum.image_path suffix='_120_square'}
                        {else}
                           {$sDefaultThumbnail}
                        {/if}
                    )">
                    <span class="albums-songs"><i class="ico ico-music-note"></i>{$aAlbum.total_track}</span>
                </a>
            </div>
            <div class="albums-bg-outer"><div class="albums-bg-inner"></div></div>
        </div>
        <!-- info -->
        <div class="item-inner">
            <div class="item-title">
                <a href="{if isset($aAlbum.sponsor_id)}{url link='ad.sponsor' view=$aAlbum.sponsor_id}{else}{permalink module='music.album' id=$aAlbum.album_id title=$aAlbum.name}{/if}">{$aAlbum.name|clean}</a>
            </div>

            <div class="item-statistic">
                <span>{_p var='by'} {$aAlbum|user}</span>
                <span>{if $aAlbum.total_play == 1}{_p var='1_play'}{else}{$aAlbum.total_play|short_number} {_p var='plays_lowercase'}{/if}</span>
            </div>
        </div>
    </div>
</div>
