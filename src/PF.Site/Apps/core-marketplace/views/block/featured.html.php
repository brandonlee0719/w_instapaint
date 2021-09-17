<?php 
	defined('PHPFOX') or exit('NO DICE!'); 
?>
<div class="item-container market-widget-block market-app">
	<div class="sticky-label-icon sticky-featured-icon r-2">
		<span class="flag-style-arrow"></span>
		<i class="ico ico-diamond"></i>
	</div>
	{foreach from=$aFeatured name=minilistings item=aMiniListing}
		{template file='marketplace.block.mini'}
	{/foreach}
</div>
