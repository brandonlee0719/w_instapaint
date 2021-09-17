<?php 

 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<link href="{param var='core.path_actual'}PF.Site/Apps/core-music/assets/jscript/mediaelementplayer/mediaelementplayer.css" rel="stylesheet" type="text/css">
<div class="music-detail item-detail">
    <div class="item_info">
            {img user=$aSong suffix='_50_square'}
        <div class="item_info_author">
            <div>{_p var='By'} {$aSong|user:'':'':50}</div>
    		<span>{$aSong.time_stamp|convert_time}{if $aSong.album_id} {_p var='in'} <a href="{permalink module='music.album' id=$aSong.album_id title=$aSong.album_url}" title="{$aSong.album_url|clean}">{$aSong.album_url|clean|shorten:50:'...'|split:50}</a>{/if}</span>
        </div>
    </div>
    
	{if $aSong.view_id != 0}
		{template file='core.block.pending-item-action'}
	{/if}

	{if $aSong.hasPermission}
	<div class="item_bar">
		<div class="item_bar_action_holder">
			<span role="button" data-toggle="dropdown" class="item_bar_action"><i class="ico ico-gear-o"></i></span>
			<ul class="dropdown-menu dropdown-menu-right">
				{template file='music.block.menu'}
			</ul>			
		</div>		
	</div>       
    {/if}
    {if $aSong.view_id == 0}
        <div class="item-comment mb-2">
            <div>
                {module name='feed.mini-feed-action'}
            </div>
            <span class="item-total-view">
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
            </span>
        </div>
    {/if}
    <div class="item-content">
        <div class="item-media" style="background-image: url('{param var='core.path_actual'}PF.Site/Apps/core-music/assets/image/song_detail_bg.png');">
            <a href="{permalink title=$aSong.title id=$aSong.song_id module='music'}">
                {if !empty($aSong.image_path)}
                    <span class="music-bg-thumb thumb-border" style="background-image: url('{img server_id=$aSong.image_server_id path='music.url_image' file=$aSong.image_path suffix='_200_square' max_width='200' max_height='200' return_url=true}')"></span>
                {else}
                    <img src="{param var='music.default_song_photo'}">
                {/if}
            </a>
        </div>
        <div class="item-player music_player">
    		<div id="js_music_player" class="audio-player dont-unbind-children {if !$aSong.canDownload}disable-download{/if}">
                <div class="js_music_controls">
                    <a href="javascript:void(0)" id="js_music_repeat" class="ml-1 js_music_repeat" title="{_p('repeat')}">
                        <i class="ico ico-play-repeat-o"></i>
                    </a>
                    {if $aSong.canDownload}
                        <a href="{url link='music.download' id=$aSong.song_id}" class="no_ajax_link download" title="{_p('download')}">
                            <span><i class="ico ico-download-alt" aria-hidden="true"></i></span>
                        </a>
                    {/if}
                </div>
                <audio id="js_song_player" src="{$aSong.song_path}" type="audio/mp3" controls="controls"></audio>
            </div>
        </div>
    </div>
    <div class="item-sub-info">
        {if $iTotal = count($aSong.genres)}
            <p class="text-uppercase title">{_p('genres')}</p>
            {foreach from=$aSong.genres item=aGenre key=iKey}
                <a href="{permalink module='music.genre' id=$aGenre.genre_id}">{$aGenre.name}</a>{if ($iKey+1) < $iTotal}, {/if}
            {/foreach}
        {/if}

        <div class="item-description item_view_content mt-3">
            <p class="text-uppercase title">{_p('description')}</p>
            {$aSong.description|parse|shorten:200:'feed.view_more':true|split:55|max_line}
        </div>
        {if $aSong.total_attachment}
            <span>
                {module name='attachment.list' sType=music_song iItemId=$aSong.song_id}
            </span>
        {/if}
    </div>

	<div class="js_moderation_on pt-2 mt-4" {if $aSong.view_id != 0}style="display:none;" class="js_moderation_on"{/if}>
        <div class="item-addthis mb-2">{addthis url=$aSong.bookmark title=$aSong.title description=$sShareDescription}</div>
        <div class="item-detail-feedcomment">
		  {module name='feed.comment'}
        </div>
	</div>
</div>

{literal}
<script type="text/javascript">
    var bLoadedMusicSong = false,
        bPlayed = false,
        bRepeat = false;
    $Behavior.onLoadMusicSong = function(){
        var initPlayer = function(){
            if(bLoadedMusicSong) return;
            bLoadedMusicSong = true;
            $('#js_song_player').mediaelementplayer({
                alwaysShowControls: true,
                features: ['playpause','current','progress','volume','duration'],
                audioVolume: 'horizontal',
                startVolume: 1,
                setDimensions: false,
                success: function(mediaPlayer, domObject) {
                    $('#js_music_player').show();
                    mediaPlayer.addEventListener('loadstart',function() {
                        $('#js_music_repeat').off('click').on('click',function(){
                            bRepeat = !bRepeat;
                            if(bRepeat)
                            {
                                $(this).addClass('active');
                            }
                            else{
                                $(this).removeClass('active');
                            }
                        });
                    });
                    mediaPlayer.addEventListener('playing',function(){
                        if(!bPlayed)
                        {
                            bPlayed = true;
                            $.ajaxCall('music.play', 'id=' + {/literal}{$aSong.song_id}{literal},'GET');
                        }
                    });
                    mediaPlayer.addEventListener('ended', function () {
                        if(bRepeat)
                        {
                            mediaPlayer.play();
                        }
                    });
                },
                error: function() {

                }
            });
        };
        initPlayer();

    }
</script>
{/literal}