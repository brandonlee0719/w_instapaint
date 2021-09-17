<?php
	defined('PHPFOX') or exit('NO DICE!');
?>

{if (count($aListings))}
	<div class="item-container market-app listing" id="collection-item-listings">
	    {foreach from=$aListings name=listings item=aListing}
	    	{module name='marketplace.rows'}
	    {/foreach}
	</div>
{pager}
{elseif !PHPFOX_IS_AJAX}
	{_p var='no_marketplace_listings_found'}
{/if}

{if $bShowModerator}
    {moderation}
{/if}