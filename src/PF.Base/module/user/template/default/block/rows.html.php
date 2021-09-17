<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="user_rows">
	<div class="user_rows_image">
		{img user=$aUser suffix='_120_square'}
	</div>
    <div class="user_rows_inner">
        {$aUser|user}
        {if isset($bShowFriendInfo) && $bShowFriendInfo}
            {module name='user.friendship' friend_user_id=$aUser.user_id type='icon' extra_info=true}
        {/if}
    </div>
</div>