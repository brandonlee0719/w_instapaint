<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{if $bIsBlocked}
{_p var='you_have_already_blocked_this_user'}
<br />
{_p var='would_you_like_to_unblock_user_info' user_info=$aUser|user}
<ul class="action">
	<li><a href="#" onclick="$.ajaxCall('user.unBlock', 'user_id={$aUser.user_id}'); $('.js_block_this_user').html('{_p var='block_this_user'}'); return false;">{_p var='yes_unblock_this_user'}</a></li>
	<li><a href="#" onclick="tb_remove(); return false;">{_p var='no_do_not_unblock_this_user'}</a></li>
</ul>
{else}
{_p var='are_you_sure_you_want_to_block_user_info' user_info=$aUser|user}
<ul class="action">
	<li><a href="#" onclick="$.ajaxCall('user.processBlock', 'user_id={$aUser.user_id}'); $('.js_block_this_user').html('{_p var='unblock_this_user'}'); return false;">{_p var='yes_block_this_user'}</a></li>
	<li><a href="#" onclick="tb_remove(); return false;">{_p var='no_do_not_block_this_user'}</a></li>
</ul>
{/if}