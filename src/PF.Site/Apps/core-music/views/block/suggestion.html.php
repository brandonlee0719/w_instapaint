<?php 
	defined('PHPFOX') or exit('NO DICE!'); 
?>

<div class="music-suggest-block music-widget-block item-container list-view music">
	{foreach from=$aSuggestSongs item=aSong}
		{template file='music.block.mini'}
	{/foreach}
</div>