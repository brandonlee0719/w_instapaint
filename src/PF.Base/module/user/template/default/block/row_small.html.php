<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="user_rows">
	<div class="user_rows_image">
		{img user=$user suffix='_120_square'}
	</div>
	{$user|user}

	{if isset($bShowFriendInfo) && $bShowFriendInfo}
	{module name='user.friendship' friend_user_id=$user.user_id type='icon' extra_info=true}
	{/if}
</div>