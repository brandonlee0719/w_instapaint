<?php 
	defined('PHPFOX') or exit('NO DICE!'); 
?>

<div class="albums-widget-widget item-container list-view music">
	{foreach from=$aRelatedAlbums item=aAlbum}
	    {template file='music.block.mini-album'}
	{/foreach}
</div>