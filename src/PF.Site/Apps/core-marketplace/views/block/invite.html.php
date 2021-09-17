<?php 

 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="item-container market-widget-block market-app">
{foreach from=$aEventInvites name=minilistings item=aMiniListing}
	{template file='marketplace.block.mini'}
{/foreach}
</div>