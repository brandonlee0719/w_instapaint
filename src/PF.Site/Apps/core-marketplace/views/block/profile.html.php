<?php 

 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<ul class="block_listing">
{foreach from=$aListings name=minilistings item=aMiniListing}
	{template file='marketplace.block.mini'}
{/foreach}
</ul>