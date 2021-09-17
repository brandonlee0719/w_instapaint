<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{if !PHPFOX_IS_AJAX}
<div id="js_pages_like_join_holder">
{/if}
	{if count($aMembers)}
	<div class="user_rows_mini core-friend-block">
	{foreach from=$aMembers key=iKey name=users item=aUser}
		{template file='user.block.rows'}
	{/foreach}
	</div>
	{/if}
{if !PHPFOX_IS_AJAX}
</div>
{/if}