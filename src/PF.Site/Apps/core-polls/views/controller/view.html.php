<?php
	defined('PHPFOX') or exit('NO DICE!'); 
?>

<div class="item_view poll-app">
	{template file='poll.block.entry'}
    
	<div {if $aPoll.view_id == 1}style="display:none;" class="js_moderation_on poll-app-addthis-parent pt-2"{/if} class="poll-app-addthis-parent pt-2">
		<div class="poll-app-addthis mb-3">{addthis url=$aPoll.bookmark title=$aPoll.question description=$sShareDescription}</div>
		<div class="item-detail-feedcomment">
		{module name='feed.comment'}
		</div>
	</div>
</div>