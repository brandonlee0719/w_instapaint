<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="user_rows_mini core-friend-block">
	{foreach from=$aMutualFriends key=iKey name=friend item=aUser}
	{template file='user.block.rows'}
	{/foreach}
</div>