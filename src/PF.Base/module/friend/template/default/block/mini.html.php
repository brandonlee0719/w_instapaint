<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Friend
 * @version 		$Id: mini.html.php 6097 2013-06-20 14:16:21Z Raymond_Benc $
 */
 
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
<div class="block_listing_inline">
	<ul>
{foreach from=$aFriends name=friend item=aFriend}
		<li>
			{if ($redis_enabled)}
			    {$aFriend.photo_link}
			{else}
			    {img user=$aFriend suffix='_50_square' max_width=50 max_height=50}
			{/if}
		</li>	
{/foreach}
	</ul>
	<div class="clear"></div>
</div>
{else}
<div class="extra_info">
	{_p var='no_friends_online'}
</div>
{/if}
{/if}
{if Phpfox::getParam('friend.load_friends_online_ajax') && PHPFOX_IS_AJAX}
<script type="text/javascript">$('#js_total_block_friends_onlin').html('{$iTotalFriendsOnline}');</script>
{/if}