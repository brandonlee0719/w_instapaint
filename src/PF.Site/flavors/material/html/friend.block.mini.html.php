<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
{if Phpfox::getParam('friend.load_friends_online_ajax') && !PHPFOX_IS_AJAX}
<script type="text/javascript">
$Behavior.setTimeoutFriends = function(){l}
	setTimeout('$.ajaxCall(\'friend.getOnlineFriends\', \'\', \'GET\')', 1000);
	$Behavior.setTimeoutFriends = function(){l}{r}
{r}
</script>
{else}
{if count($aFriends)}
<ul class="user_rows_mini core-friend-block friend-online-block">
{foreach from=$aFriends name=friend item=aFriend}
	<li class="user_rows">
		<div class="user_rows_image">
			{if ($redis_enabled)}
			{$aFriend.photo_link}
			{else}
			{img user=$aFriend suffix='_50_square' max_width=50 max_height=50 class='js_hover_title'}
			{/if}
		</div>
	</li>	
{/foreach}
    {if $iRemainCount > 0}
	<li class="user_rows view-friend-more">
		<a href="{url link='profile.friend' view='online'}">+{$iRemainCount}</a>
	</li>
    {/if}
</ul>
{else}
<div class="extra_info">
	{_p var='no_friends_online'}
</div>
{/if}
{/if}