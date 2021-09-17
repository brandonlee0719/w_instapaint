<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: share.html.php 7024 2014-01-07 14:54:37Z Fern $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<script type="text/javascript">
{literal}
	function sendFeed(oObj)
	{
		$('#btnShareFeed').attr('disabled', 'disabled');
		$('#imgShareFeedLoading').show();
		$(oObj).ajaxCall('feed.share');
		
		return false;
	}
{/literal}
</script>

<div>
	<form class="form" method="post" action="#" onsubmit="return sendFeed(this);">
		<div><input type="hidden" name="val[parent_feed_id]" value="{$iFeedId}" /></div>
		<div><input type="hidden" name="val[parent_module_id]" value="{$sShareModule|clean}" /></div>
		<select class="form-control" name="val[post_type]" onchange="if (this.value == '1') {l} $('#js_feed_share_friend_holder').hide(); {r} else {l} $('#js_feed_share_friend_holder').show(); {r}">
			<option value="1">{_p var='on_your_wall'}</option>
			<option value="2">{_p var='on_a_friend_s_wall'}</option>
		</select>

		<div class="p_top_8" id="js_feed_share_friend_holder" style="display:none;">
            {module name='friend.search-small' input_name='val[friends]'}
		</div>

		<div class="p_top_8">
			<textarea class="form-control" rows="4" name="val[post_content]"></textarea>
		</div>
		<div class="p_top_8">
			<input type="submit" id="btnShareFeed" value="{_p var='post'}" class="btn btn-primary" />
			{img theme='ajax/small.gif' style="display:none" id="imgShareFeedLoading"}
		</div>
	</form>
</div>
<script type="text/javascript">
	$Core.loadInit();
</script>