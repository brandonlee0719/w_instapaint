<?php 


defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="albums-widget-widget item-container grid-view music">
	<div class="sticky-label-icon sticky-sponsored-icon">
		<span class="flag-style-arrow"></span>
		<i class="ico ico-sponsor"></i>
	</div>
    {foreach from=$aSponsorAlbum item=aAlbum}
        {template file='music.block.mini-album'}
    {/foreach}
</div>
