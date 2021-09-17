<?php
	defined('PHPFOX') or exit('NO DICE!'); 
?>
<div class="item-container poll-widget-block poll-app">
	<div class="sticky-label-icon sticky-featured-icon">
		<span class="flag-style-arrow"></span>
		<i class="ico ico-diamond"></i>
	</div>
	{foreach from=$aFeaturedPolls item=aPoll}
		{template file='poll.block.mini'}
	{/foreach}
</div>