<?php
    defined('PHPFOX') or exit('NO DICE!');
?>
{if isset($ajaxLoadLike) && $ajaxLoadLike}
<div id="js_like_body_{$aFeed.feed_id}">
{/if}

{if Phpfox::getParam('like.show_user_photos')}
	<div class="activity_like_holder comment_mini" style="position:relative;">
		<a href="#" class="like_count_link js_hover_title" onclick="return $Core.box('like.browse', 450, 'type_id={if isset($aFeed.like_type_id)}{$aFeed.like_type_id}{else}{$aFeed.type_id}{/if}&amp;item_id={$aFeed.item_id}');">
			{$aFeed.feed_total_like|number_format}
			<span class="js_hover_info">
				{if defined('PHPFOX_IS_THEATER_MODE')}{_p var='likes'}{else}{_p var='people_who_like_this'}{/if}
			</span>
		</a>
		{module name='like.displayactions' aFeed=$aFeed}

		<div class="like_count_link_holder">
			{foreach from=$aFeed.likes name=likes item=aLikeRow}
				{img user=$aLikeRow suffix='_50_square' max_width=32 max_height=32 class='js_hover_title v_middle'}&nbsp;
			{/foreach}
		</div>
	</div>
{else}

	{if !empty($aFeed.feed_like_phrase)}
	<div class="activity_like_holder" id="activity_like_holder_{$aFeed.feed_id}">
		{$aFeed.feed_like_phrase}
	</div>
	{else}
	<div class="activity_like_holder activity_not_like">
		{_p var='when_not_like'}
	</div>
	{/if}

{/if}
{if isset($ajaxLoadLike) && $ajaxLoadLike}
</div>
{/if}
