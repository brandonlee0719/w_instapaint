<?php
    defined('PHPFOX') or exit('NO DICE!');
?>

<div class="music_row album-item" data-songid="{$aSong.song_id}" xmlns="http://www.w3.org/1999/html">
    <div class="item-outer song">
        <div class="item-media">
            <div class="item-media-inner">
                <span class="music-bg-thumb thumb-border" onclick="$Core.music.playSongRow(this)" style="background-image: url(
                        {if $aSong.image_path}
                            {img return_url="true" server_id=$aSong.image_server_id path='music.url_image' file=$aSong.image_path suffix=''}
                        {else}
                           {$sDefaultThumbnail}
                        {/if}
                    )"><span class="music-overlay"><i class="ico ico-play-circle-o"></i></span></span>
            </div>
        </div>

        <div class="item-inner song">
            <div class="item-title">
                <a href="{permalink title=$aSong.title id=$aSong.song_id module='music'}">{$aSong.title|clean}</a>
            </div>

            <div class="item-statistic">
                <span>{_p var='by'} {$aSong|user}</span>
                
                <span>
                    {if $aSong.total_play != 1}
                        {_p var='music_total_plays' total=$aSong.total_play|short_number}
                    {else}
                        {_p var='music_total_play' total=$aSong.total_play|short_number}
                    {/if}
                </span>
            </div>
        </div>
    </div>
    <div class="item-player music_player hide">
        <div class="audio-player dont-unbind-children js_player_holder">
            <audio class="js_song_player" src="{$aSong.song_path}" type="audio/mp3" controls="controls"></audio>
        </div>
    </div>
</div>