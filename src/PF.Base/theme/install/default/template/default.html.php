<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="nb_message_holder">
	{$sMessage}
	{if isset($sNext)}
	 Please hold...
	<meta http-equiv="refresh" content="2;url={$sNext}" />
	{/if}
</div>

<div class="nb_message_image">
	{img theme='layout/ajax_loader_blue_128.gif'}
</div>