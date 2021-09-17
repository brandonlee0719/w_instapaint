<?php 

 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div id="js_feature_block_track_player"></div>

<div class="music-featured-block music-widget-block item-container list-view music">
	<div class="sticky-label-icon sticky-featured-icon">
		<span class="flag-style-arrow"></span>
		<i class="ico ico-diamond"></i>
	</div>
	{foreach from=$aFeaturedSongs item=aSong}
		{template file='music.block.mini'}
	{/foreach}
</div>