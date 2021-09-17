<article class="music_row item-music" id="js_controller_music_track_{$aSong.song_id}" data-songid="{$aSong.song_id}" xmlns="http://www.w3.org/1999/html">
    <div class="item-outer {if !isset($aSong.is_in_feed) && $aSong.hasPermission}item-manage{/if}">
        <div class="item-title">
            <a class="fw-bold" href="{permalink title=$aSong.title id=$aSong.song_id module='music'}">{$aSong.title|clean}</a>
        </div>
        <div class="item-media">
            <div class="item-statistic dot-separate">
                {if $aSong.total_play != 1}
                    {_p var='music_total_plays' total=$aSong.total_play|short_number}
                {else}
                    {_p var='music_total_play' total=$aSong.total_play|short_number}
                {/if}
            </div>
            <span class="button-play" onclick="$Core.music.playSongRow(this)"><i class="ico ico-play"></i></span>
        </div>
    </div>
    
    <div class="item-player music_player">
        <div class="audio-player dont-unbind-children js_player_holder  {if !Phpfox::getUserParam('music.can_download_songs')}disable-download{/if}">
            <div class="js_music_controls">
                <a href="javascript:void(0)" class="js_music_repeat ml-1" title="{_p('repeat')}">
                    <i class="ico ico-play-repeat-o"></i>
               </a>
                {if Phpfox::getUserParam('music.can_download_songs')}
                    <a href="{url link='music.download' id=$aSong.song_id}" class="no_ajax_link download" title="{_p('download')}">
                        <span>
                            <i class="ico ico-download-alt" aria-hidden="true"></i>
                        </span>
                    </a>
                {/if}
            </div>
            <audio class="js_song_player" src="{$aSong.song_path}" type="audio/mp3" controls="controls"></audio>
        </div>
    </div>
 
</article>