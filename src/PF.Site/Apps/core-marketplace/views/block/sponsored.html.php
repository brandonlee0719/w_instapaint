<?php 
	defined('PHPFOX') or exit('NO DICE!'); 
?>
<div class="item-container market-widget-block market-app">
	<div class="sticky-label-icon sticky-sponsored-icon r-2">
		<span class="flag-style-arrow"></span>
		<i class="ico ico-sponsor"></i>
	</div>
	{foreach from=$aSponsorListings name=listings item=aMiniListing}
		{template file='marketplace.block.mini'}
	{/foreach}
</div>
