<?php 

 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="main_break"></div>
{if $bInvoice}

<h3>{_p var='payment_methods'}</h3>
{module name='api.gateway.form'}

{else}
<div class="info">
	<div class="info_left">
		{_p var='item_you_re_buying'}:
	</div>
	<div class="info_right">
		{$aListing.title|clean}
	</div>		
</div>
<div class="info">
	<div class="info_left">
		{_p var='price'}:
	</div>
	<div class="info_right">
		{$aListing.price|currency:$aListing.currency_id}
	</div>		
</div>
	
<div class="separate"></div>

<div class="p_4">
	{_p var='by_clicking_on_the_button_below_you_commit_to_buy_this_item_from_the_seller'}
	<div class="p_4">
		<form method="post" action="{url link='marketplace.purchase'}">
			<div><input type="hidden" name="id" value="{$aListing.listing_id}" /></div>
			<div><input type="hidden" name="process" value="1" /></div>			
			<input type="submit" value="{_p var='commit_to_buy'}" class="button btn-primary" />
		</form>
	</div>
</div>
{/if}