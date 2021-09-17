<?php 
	defined('PHPFOX') or exit('NO DICE!'); 
?>

<li id="js_album_song_holder_{$aSong.song_id}" class="item-music js_music_store_album_holder dont-unbind dont-unbind-children {if $iKey == 0}active{/if}"{if isset($phpfox.iteration.songs) && $phpfox.iteration.songs > 10} {/if}>
	<div class="item-outer">
		<div class="item-media block_listing_image">
			<a href="javascript:void(0)" class="no_ajax_link">
				<i class="ico ico-play-o"></i>	
			</a>
		</div>

		<div class="item-inner block_listing_title">
			<div class="item-title">
				{if isset($bIsMusicPlayer) && $bIsMusicPlayer}
						<a href="javascript:void(0)" onclick="$.ajaxCall('music.playInFeed', 'id={$aSong.song_id}&amp;track=js_block_track_player{if isset($bIsMusicPlayer) && $bIsMusicPlayer}&amp;is_player=1{/if}'); return false;" class="no_ajax_link">
				{else}

				<a title="{$aSong.title|clean}">
				{/if}
				
				{$aSong.title|clean}</a>
			</div>
			
			<div class="item-count">
				<i class="ico ico-play mr-1"></i>{$aSong.total_play|short_number}
			</div>
		</div>
		
		<div class="music-download-action {if !$aSong.canDownload}disable-download{/if}">
			<a href="{permalink module='music' id=$aSong.song_id title=$aSong.title}"><i class="ico ico-external-link"></i></a>
            {if $aSong.canDownload}
                <a href="{url link='music.download' id=$aSong.song_id}" class="no_ajax_link" title="{_p('download')}">
                    <i class="ico ico-download-alt"></i>
                </a>
            {/if}
		</div>
		
		<div id="js_album_song_{$iKey}" class="js_album_song" data-index="{$iKey}" style="display: none;" data-src="{$aSong.song_path}" data-id="{$aSong.song_id}" data-title="{$aSong.title|clean}"></div>
	</div>
</li>