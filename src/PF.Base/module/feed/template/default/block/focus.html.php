<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{if empty($aFeed.empty_content) }
<div class="activity_feed_content_text{if isset($aFeed.comment_type_id) && $aFeed.comment_type_id == 'poll'} js_parent_module_feed_{$aFeed.comment_type_id}{/if}">


	{if !empty($aFeed.feed_mini_content)}
	<div class="activity_feed_content_status">
		<div class="activity_feed_content_status_left">
			<img src="{$aFeed.feed_icon}" alt="" class="v_middle" /> {$aFeed.feed_mini_content}
		</div>
		<div class="activity_feed_content_status_right">
			{template file='feed.block.link'}
		</div>
		<div class="clear"></div>
	</div>
	{/if}

	{if isset($aFeed.feed_status) && (!empty($aFeed.feed_status) || $aFeed.feed_status == '0')}
	<div class="activity_feed_content_status">{if strpos($aFeed.feed_status, '<br />') >= 200}{$aFeed.feed_status|feed_strip|shorten:200:'feed.view_more':true|split:55|max_line}{else}{$aFeed.feed_status|feed_strip|split:55|max_line|shorten:200:'feed.view_more':true}
		{/if}
	</div>
	{/if}

	<div class="activity_feed_content_link">
		{if $aFeed.type_id == 'friend' && isset($aFeed.more_feed_rows) && is_array($aFeed.more_feed_rows) && count($aFeed.more_feed_rows)}
		{foreach from=$aFeed.more_feed_rows item=aFriends}
		{$aFriends.feed_image}
		{/foreach}
		{$aFeed.feed_image}
		{else}

		{if !empty($aFeed.feed_image)}
		<div class="activity_feed_content_image"{if isset($aFeed.feed_custom_width)} style="width:{$aFeed.feed_custom_width};"{/if}>
		{if is_array($aFeed.feed_image)}
		<div class="activity_feed_multiple_image feed-img-stage-{$aFeed.total_image}">
			{foreach from=$aFeed.feed_image item=sFeedImage name=image}
			<div class="img-{$phpfox.iteration.image}">
				{$sFeedImage}
			</div>
			{/foreach}
		</div>
		<div class="clear"></div>
		{else}
		<a href="{if isset($aFeed.feed_link_actual)}{$aFeed.feed_link_actual}{else}{$aFeed.feed_link}{/if}"{if !isset($aFeed.no_target_blank)} target="_blank"{/if} class="{if isset($aFeed.custom_css)} {$aFeed.custom_css} {/if}{if !empty($aFeed.feed_image_onclick)}{if !isset($aFeed.feed_image_onclick_no_image)}play_link {/if} no_ajax_link{/if}"{if !empty($aFeed.feed_image_onclick)} onclick="{$aFeed.feed_image_onclick}"{/if}{if !empty($aFeed.custom_rel)} rel="{$aFeed.custom_rel}"{/if}{if isset($aFeed.custom_js)} {$aFeed.custom_js} {/if}{if Phpfox::getParam('core.no_follow_on_external_links')} rel="nofollow"{/if}>{if !empty($aFeed.feed_image_onclick)}{if !isset($aFeed.feed_image_onclick_no_image)}<span class="play_link_img">{_p var='play'}</span>{/if}{/if}{$aFeed.feed_image}</a>
		{/if}

	</div>
	{/if}

	{if isset($aFeed.feed_image_banner)}
	<div class="feed_banner">
		{$aFeed.feed_image_banner}
		{/if}

		{if isset($aFeed.load_block)}
		{module name=$aFeed.load_block this_feed_id=$aFeed.feed_id}
		{else}

		<div class="feed_block_title_content {if (!empty($aFeed.feed_content) || !empty($aFeed.feed_custom_html)) && empty($aFeed.feed_image) && empty($aFeed.feed_image_banner)} activity_feed_content_no_image{/if}{if !empty($aFeed.feed_image)} activity_feed_content_float{/if}"{if isset($aFeed.feed_custom_width)} style="margin-left:{$aFeed.feed_custom_width};"{/if}>
		{if !empty($aFeed.feed_title) || $aFeed.type_id == 'link'}
		{if isset($aFeed.feed_title_sub)}
						<span class="user_profile_link_span" id="js_user_name_link_{$aFeed.feed_title_sub|clean}">
					{/if}
					<a href="{if isset($aFeed.feed_link_actual)}{$aFeed.feed_link_actual}{else}{$aFeed.feed_link}{/if}" class="activity_feed_content_link_title"{if isset($aFeed.feed_title_extra_link)} target="_blank"{/if}{if Phpfox::getParam('core.no_follow_on_external_links')} rel="nofollow"{/if}>{$aFeed.feed_title|clean|split:30}</a>
							{if isset($aFeed.feed_title_sub)}
						</span>
		{/if}
		{if !empty($aFeed.feed_title_extra)}
		<div class="activity_feed_content_link_title_link">
			<a href="{$aFeed.feed_title_extra_link}" target="_blank"{if Phpfox::getParam('core.no_follow_on_external_links')} rel="nofollow"{/if}>{$aFeed.feed_title_extra|clean}</a>
		</div>
		{/if}
		{/if}
		{if !empty($aFeed.feed_content)}
		<div class="activity_feed_content_display">
            {$aFeed.feed_content|feed_strip|split:55|max_line}
		</div>
		{/if}
		{if !empty($aFeed.feed_custom_html)}
		<div class="activity_feed_content_display_custom">
			{$aFeed.feed_custom_html}
		</div>
		{/if}

		{if !empty($aFeed.app_content)}
		{$aFeed.app_content}
		{/if}

		{if !empty($aFeed.parent_module_id)}
		{module name='feed.mini' parent_feed_id=$aFeed.parent_feed_id parent_module_id=$aFeed.parent_module_id}
		{/if}

		{if (isset($aFeed.parent_is_app)) && empty($aFeed.parent_module_id)}
		<div class="feed_is_child" style="display: block">
			<div class="feed_stream" data-feed-url="{url link='feed.stream' id=$aFeed.parent_is_app}"></div>
		</div>
		{/if}

	</div>

	{/if}

	{if isset($aFeed.feed_image_banner)}
</div>
{/if}

{if !empty($aFeed.feed_image)}
<div class="clear"></div>
{/if}
{/if}
</div>
</div>

{if Phpfox::getParam('feed.enable_check_in') && Phpfox::getParam('core.google_api_key') != '' && !empty($aFeed.location_name) && isset($aFeed.location_latlng) && isset($aFeed.location_latlng.latitude)}
<div class="activity_feed_location">
    <div id="{$aFeed.feed_id}" class="pf-feed-map" data-component="pf_map" data-lat="{$aFeed.location_latlng.latitude}" data-lng="{$aFeed.location_latlng.longitude}" data-id="{$aFeed.feed_id}"></div>
</div>
{/if}

{else}
<div class="activity_feed_content_text empty_content"></div>
{/if}