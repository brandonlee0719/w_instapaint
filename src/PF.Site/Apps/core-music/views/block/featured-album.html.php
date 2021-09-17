<?php 
	defined('PHPFOX') or exit('NO DICE!'); 
?>

<div class="albums-widget-widget item-container list-view music">
	<div class="sticky-label-icon sticky-featured-icon">
		<span class="flag-style-arrow"></span>
		<i class="ico ico-diamond"></i>
	</div>
	{foreach from=$aFeaturedAlbums item=aAlbum}
		{template file='music.block.mini-album'}
	{/foreach}
</div>