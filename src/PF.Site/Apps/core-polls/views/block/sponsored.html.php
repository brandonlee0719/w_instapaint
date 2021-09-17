<?php
	defined('PHPFOX') or exit('NO DICE!'); 
?>
<div class="item-container poll-widget-block poll-app">
	<div class="sticky-label-icon sticky-sponsored-icon">
		<span class="flag-style-arrow"></span>
		<i class="ico ico-sponsor"></i>
	</div>
	{foreach from=$aSponsorPolls item=aPoll}
		{template file='poll.block.mini'}
	{/foreach}
</div>