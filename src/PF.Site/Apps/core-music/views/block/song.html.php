<?php 

 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div id="js_my_block_track_player"></div>
<div class="music-featured-block music-widget-block item-container list-view music">
	{foreach from=$aSongs name=songs item=aSong}
		{template file='music.block.mini'}
	{/foreach}
</div>