<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="user_welcome_images">
	{foreach from=$aUserImages name=userimages item=aUserImage}{img user=$aUserImage suffix='_50_square' max_width=50 max_height=50}{/foreach}	
</div>