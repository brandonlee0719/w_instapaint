<?php 


defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="music-sponsor-block music-widget-block item-container grid-view music">
	<div class="sticky-label-icon sticky-sponsored-icon">
		<span class="flag-style-arrow"></span>
		<i class="ico ico-sponsor"></i>
		</div>
	{foreach from=$aSponsorSong item=aSong}
		{template file='music.block.mini'}
	{/foreach}
</div>