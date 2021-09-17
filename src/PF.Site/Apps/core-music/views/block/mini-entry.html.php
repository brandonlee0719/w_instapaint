<article class="music_row item-music" id="js_controller_music_track_{$aSong.song_id}" data-songid="{$aSong.song_id}" xmlns="http://www.w3.org/1999/html">
    <div class="item-outer">
        <div class="item-media">
            <a class="music-bg-thumb thumb-border" href="{permalink title=$aSong.title id=$aSong.song_id module='music'}"
                style="background-image: url(
                    {if !empty($aSong.image_path)}
                        {img return_url="true" server_id=$aSong.image_server_id path='music.url_image' file=$aSong.image_path suffix='_200_square' max_width='200' max_height='200'}
                    {else}
                        {param var='music.default_song_photo'}
                    {/if}     
                )">
            </a>
            {if !isset($aSong.is_in_feed)}
                <div class="{if $bShowModerator} moderation_row{/if}">
                   {if !empty($bShowModerator)}
                       <label class="item-checkbox">
                           <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aSong.song_id}" id="check{$aSong.song_id}" />
                           <i class="ico ico-square-o"></i>
                       </label>
                   {/if}
                </div>
            {/if}
            <span class="button-play hide" onclick="$Core.music.playSongRow(this)"><i class="ico ico-play-o"></i></span>
            <div class="flag_style_parent">
                {if isset($sMusicView) && $sMusicView == 'my' && $aSong.view_id == 1}
                <div class="sticky-label-icon sticky-pending-icon">
                    <span class="flag-style-arrow"></span>
                    <i class="ico ico-clock-o"></i>
                </div>
                {/if}
                {if $aSong.is_sponsor}
                <div class="sticky-label-icon sticky-sponsored-icon">
                    <span class="flag-style-arrow"></span>
                    <i class="ico ico-sponsor"></i>
                </div>
                {/if}
                {if $aSong.is_featured}
                <div class="sticky-label-icon sticky-featured-icon">
                    <span class="flag-style-arrow"></span>
                    <i class="ico ico-diamond"></i>
                </div>
                {/if}
            </div>
        </div>

        <div class="item-inner {if !isset($aSong.is_in_feed) && $aSong.hasPermission}item-manage{/if}">
            <div class="item-title mt-2">
                <a class="fw-bold" href="{permalink title=$aSong.title id=$aSong.song_id module='music'}">
                    {$aSong.title|clean}
                </a>
            </div>

            <div class="item-info">
                <div class="left">
                    {if isset($aSong.genres) && $iTotal = count($aSong.genres)}
                        <div class="item-categories">
                            {template file='music.block.song-genres'}
                        </div>
                    {/if}
                    <div class="item-info-inner">
                        {if !isset($aSong.is_in_feed)}
                        <div class="item-author">
                            {img user=$aSong suffix='_50'}
                            <div class="pl-1">
                                <span class="user_info">{_p var='by'} {$aSong|user}</span>
                                <span class="time_info">{$aSong.time_stamp|convert_time}</span>
                            </div>
                        </div>
                        {/if}
                    </div>
                </div>
                <div class="item-statistic dot-separate">
                    <span>
                        {if $aSong.total_view != 1}
                            {_p var='music_total_views' total=$aSong.total_view|short_number}
                        {else}
                            {_p var='music_total_view' total=$aSong.total_view|short_number}
                        {/if}
                    </span>
                    <span>
                        {if $aSong.total_play != 1}
                            {_p var='music_total_plays' total=$aSong.total_play|short_number}
                        {else}
                            {_p var='music_total_play' total=$aSong.total_play|short_number}
                        {/if}
                    </span>
                </div>
            </div>

            <span class="button-play" onclick="$Core.music.playSongRow(this)"><i class="ico ico-play-o"></i></span>
        </div>
        
        {if !isset($aSong.is_in_feed)}
            {if $aSong.hasPermission}
                <div class="item-option">
                    <div class="dropdown">
                        <span role="button" class="row_edit_bar_action" data-toggle="dropdown">
                            <i class="ico ico-gear-o"></i>
                        </span>
                        <ul class="dropdown-menu dropdown-menu-right">
                            {template file='music.block.menu'}
                        </ul>
                    </div>
                </div>
            {/if}
        {/if}
    </div>

    <div class="item-player music_player">
        <div class="audio-player dont-unbind-children js_player_holder {if !$aSong.canDownload}disable-download{/if}">
            <div class="js_music_controls">
                <a href="javascript:void(0)" class="js_music_repeat" title="{_p('repeat')}">
                    <i class="ico ico-play-repeat-o"></i>
               </a>
                {if $aSong.canDownload}
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