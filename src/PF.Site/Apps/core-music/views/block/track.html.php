<?php


defined('PHPFOX') or exit('NO DICE!');

?>
{if count($aTracks)}
    <div id="js_music_player" class="audio-player dont-unbind-children">
        <div class="js_music_controls">
            <a href="javascript:void(0)" id="js_music_prev" title="{_p('previous')}"><i class="ico ico-play-prev"></i></a>
            <a href="javascript:void(0)" id="js_music_next" title="{_p('next')}"><i class="ico ico-play-next""></i></a>
        </div>
        <span id="js_playing_song_title">{$aTracks.0.title|clean}</span>
        <audio id="js_song_album_player" src="{$aTracks.0.song_path}" type="audio/mp3" controls="controls"></audio>
        <a href="javascript:void(0)" id="js_music_repeat" title="{_p('no_repeat')}" class="repeat_off"><i class="ico ico-play-repeat-o" aria-hidden="true"></i></a>
    </div>
{/if}
<div id="js_block_track_player"></div>

<ul class="album-detail-tracks" id="js_store_album_track">
	{foreach from=$aTracks name=songs item=aSong key=iKey}
	    {template file='music.block.track-entry'}
	{/foreach}
</ul>

{if !count($aTracks)}
    <div id="js_track_song_no_tracks" class="extra_info">
        {_p var='no_songs_have_been_added'}
    </div>
{else}
{/if}

{literal}
<script type="text/javascript">
    var bLoadedAlbumSong = false,
        bFirstPlayed = true,
        bShuffle = false,
        bRepeat = 0,
        aPlayedSong = [],
        bState = 0,
        iSongIndex = 0,
        isPlaying = false,
        iTotalSong = {/literal}{$iTotalSong}{literal};
    $Behavior.onLoadAlbumSong = function(){
        var initAlbumPlayer = function(){
            if(bLoadedAlbumSong) return;
            bLoadedAlbumSong = true;
            $('#js_song_album_player').mediaelementplayer({
                alwaysShowControls: true,
                features: ['playpause','current','progress','volume','duration'],
                audioVolume: 'horizontal',
                startVolume: 0.5,
                setDimensions: false,
                success: function(mediaPlayer, domObject) {
                    $('#js_music_player').show();
                    mediaPlayer.addEventListener('loadstart',function() {
                        $('.js_music_store_album_holder').find('.block_listing_title, .block_listing_image a').off('click').on('click', function () {
                            isPlaying = false;
                            var obj = $(this).closest('.js_music_store_album_holder').find('.js_album_song');
                            iSongIndex = obj.data('index');
                            bFirstPlayed = false;
                            toggleActiveMusic(obj);
                            $.ajaxCall('music.play', 'id=' + obj.data('id'),'GET');
                            mediaPlayer.setSrc(obj.data('src'));
                            playAlbumSongs(mediaPlayer);
                            return false;
                        });
                        $('#js_music_prev').off('click').on('click',function(){
                            iSongIndex = (bShuffle) ? generateRandomIndex(iSongIndex,0,(iTotalSong-1)) : (iSongIndex - 1);
                            if(iSongIndex < 0)
                            {
                                iSongIndex = 0;
                                return;
                            }
                            bFirstPlayed = false;
                            isPlaying = false;
                            var ele_id = $('#js_album_song_'+iSongIndex);
                            $('#js_playing_song_title').html($(ele_id).data('title'));
                            toggleActiveMusic(ele_id);
                            mediaPlayer.setSrc(ele_id.data('src'));
                            if(bState) {
                                $.ajaxCall('music.play', 'id=' + ele_id.data('id'), 'GET');
                                playAlbumSongs(mediaPlayer);
                            }
                        });
                        $('#js_music_next').off('click').on('click',function(){
                            iSongIndex = (bShuffle) ? generateRandomIndex(iSongIndex,0,(iTotalSong-1)) : (iSongIndex + 1);
                            bFirstPlayed = false;
                            isPlaying = false;
                            var ele_id = $('#js_album_song_'+iSongIndex);
                            $('#js_playing_song_title').html($(ele_id).data('title'));
                            if(ele_id.length)
                            {
                                toggleActiveMusic(ele_id);
                                mediaPlayer.setSrc(ele_id.data('src'));
                                if(bState) {
                                    $.ajaxCall('music.play', 'id=' + ele_id.data('id'), 'GET');
                                    playAlbumSongs(mediaPlayer);
                                }
                            }
                            else if(!bShuffle){
                                iSongIndex--;
                            }
                        });
                        $('#js_music_repeat').off('click').on('click',function(){
                            switch (bRepeat){
                                case 0:
                                    bRepeat = 1;
                                    $(this).removeClass('repeat_off').addClass('repeat_one').attr('title','{/literal}{_p('repeat_one_song')}{literal}');
                                    $(this).find('i').removeClass('ico-play-repeat-o').addClass('ico-play-repeat-one-o');
                                    break;
                                case 1:
                                    bRepeat = 2;
                                    $(this).removeClass('repeat_one').addClass('repeat_all').attr('title','{/literal}{_p('repeat_all_songs')}{literal}');
                                    $(this).find('i').removeClass('ico-play-repeat-one-o').addClass('ico-play-repeat-o');
                                    break;
                                case 2:
                                    bRepeat = 3;
                                    bShuffle = 1;
                                    $(this).removeClass('repeat_all').addClass('shuffle').attr('title','{/literal}{_p('shuffle_upper')}{literal}');
                                    $(this).find('i').removeClass('ico-play-repeat-o').addClass('ico-shuffle');
                                    break;
                                case 3:
                                    bRepeat = 0;
                                    bShuffle = 0;
                                    $(this).removeClass('shuffle').addClass('repeat_off').attr('title','{/literal}{_p('no_repeat')}{literal}');
                                    $(this).find('i').removeClass('ico-shuffle').addClass('ico-play-repeat-o');
                                    break;
                            }
                        });
                    });
                    mediaPlayer.addEventListener('playing',function(){
                        bState = 1;
                        isPlaying = true;
                        var ele = $('#js_album_song_'+iSongIndex);
                        $('#js_playing_song_title').html(ele.data('title')).parent().show();
                        ele.closest('.js_music_store_album_holder').find('.block_listing_image a').off('click').on('click',function(){
                            $(this).removeClass('active');
                            mediaPlayer.pause();
                        }).addClass('active');
                        if(aPlayedSong.indexOf(iSongIndex) < 0)
                        {
                            aPlayedSong.push(iSongIndex);
                        }
                        if(bFirstPlayed && iSongIndex == 0)
                        {
                            $.ajaxCall('music.play', 'id=' + $('#js_album_song_0').data('id'),'GET');
                            bFirstPlayed = false;
                        }
                    });
                    mediaPlayer.addEventListener('pause',function(){
                        bState = 0;
                        isPlaying = false;
                        $('#js_album_song_'+iSongIndex).closest('.js_music_store_album_holder').find('.block_listing_image a').off('click').on('click',function(){
                            $(this).addClass('active');
                            playAlbumSongs(mediaPlayer);
                        }).removeClass('active');
                    });
                    mediaPlayer.addEventListener('ended', function () {
                        isPlaying = false;
                        if(bRepeat == 1)
                        {
                            playAlbumSongs(mediaPlayer);
                            return;
                        }
                        iSongIndex = (bShuffle) ? generateRandomIndex(iSongIndex,0,(iTotalSong-1)) : (iSongIndex + 1);
                        bFirstPlayed = false;
                        var next_id = $('#js_album_song_' + iSongIndex);
                        if(next_id.length)
                        {
                            toggleActiveMusic(next_id);
                            $.ajaxCall('music.play', 'id=' + next_id.data('id'),'GET');
                            if(aPlayedSong.indexOf(iSongIndex) < 0) {
                                aPlayedSong.push(iSongIndex);
                            }
                            mediaPlayer.setSrc(next_id.data('src'));
                            playAlbumSongs(mediaPlayer);
                        }
                        else{
                            toggleActiveMusic($('#js_album_song_0'));
                            $('#js_album_song_holder_' + $('#js_album_song_0').data('id')).addClass('active');
                            mediaPlayer.setSrc($('#js_album_song_0').data('src'));
                            iSongIndex = 0;
                            if(bRepeat == 2)
                            {
                                playAlbumSongs(mediaPlayer);
                            }
                        }
                    });
                },
                error: function() {

                }
            });
        };
        initAlbumPlayer();
    }
    function generateRandomIndex(current, min, max) {
        if (max == 1) {
            return 0;
        }
        var gen = Math.floor(Math.random() * (max - min + 1)) + min;
        while (gen == current) {
            gen = Math.floor(Math.random() * (max - min + 1)) + min;
        }

        if((aPlayedSong.indexOf(gen) > -1) && (aPlayedSong.length < iTotalSong) && aPlayedSong.length)
        {
            return generateRandomIndex(gen,min,max);
        }
        else if(aPlayedSong.length == iTotalSong){
            aPlayedSong = [];
        }
        return gen;
    }
    function toggleActiveMusic(divId) {
        $('.js_music_store_album_holder').removeClass('active');
        $('#js_album_song_holder_' + divId.data('id')).addClass('active');
        $('.js_music_store_album_holder').find('.block_listing_image a').removeClass('active');
    }
    function playAlbumSongs(mediaPlayer) {
        mediaPlayer.play();
        setTimeout(function(){
            if (!isPlaying) {
                $('#js_music_player').find('.mejs__playpause-button').trigger('click');
            }
            isPlaying = true;
        },500);
    }

</script>
{/literal}