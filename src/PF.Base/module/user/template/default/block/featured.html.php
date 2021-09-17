<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
<ul class="block_listing user_listing_with_image">
{foreach from=$aFeaturedUsers item=aUser name=featured}
	<li>
		<div class="block_listing_image">
			{img user=$aUser suffix='_50_square' max_width=50 max_height=50}
		</div>
		<div class="block_listing_title">
			{$aUser|user:'':'':'':12:true}
		</div>
		<div class="clear"></div>
	</li>
{/foreach}
</ul>