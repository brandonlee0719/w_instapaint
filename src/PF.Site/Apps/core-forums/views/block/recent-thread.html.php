<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="item-container forum-app recent-discussion">
	{foreach from=$threads item=aThread}
		{template file='forum.block.thread-entry'}
	{/foreach}
</div>
