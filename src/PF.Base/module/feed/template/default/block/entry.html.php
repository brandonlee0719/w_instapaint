<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Feed
 * @version 		$Id: entry.html.php 5840 2013-05-09 06:14:35Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{assign var=feed_entry_be value=true}
<div data-feed-id="{$aFeed.feed_id}" data-feed-update="{$aFeed.time_update}" class="{if (isset($sponsor) && $sponsor) || (isset($aFeed.sponsored_feed) && $aFeed.sponsored_feed)}sponsor{/if} _app_{$aFeed.type_id} row_feed_loop js_parent_feed_entry {if isset($aFeed.feed_mini)} row_mini {else}{if isset($bChildFeed)} row1{else}{if isset($phpfox.iteration.iFeed)}{if is_int($phpfox.iteration.iFeed/2)}row1{else}row2{/if}{if $phpfox.iteration.iFeed == 1 && !PHPFOX_IS_AJAX} row_first{/if}{else}row1{/if}{/if}{/if} js_user_feed" id="js_item_feed_{$aFeed.feed_id}">
	{plugin call='feed.template_block_entry_1'}
	<div class="activity_feed_image">	
		{if !isset($aFeed.feed_mini)}		
			{if isset($aFeed.is_custom_app) && $aFeed.is_custom_app && ((isset($aFeed.view_id) && $aFeed.view_id == 7) || (isset($aFeed.gender) && $aFeed.gender < 1))}
				{img server_id=0 path='app.url_image' file=$aFeed.app_image_path suffix='_square' max_width=50 max_height=50}
			{else}
				{if isset($aFeed.user_name) && !empty($aFeed.user_name)}
					{img user=$aFeed suffix='_50_square' max_width=50 max_height=50}
				{else}
					{if !empty($aFeed.parent_user_name)}
						{img user=$aFeed suffix='_50_square' max_width=50 max_height=50 href=$aFeed.parent_user_name}
					{else}
						{img user=$aFeed suffix='_50_square' max_width=50 max_height=50 href=''}
					{/if}
				{/if}
			{/if}
		{/if}
	</div>{*<!-- // .activity_feed_image -->*}
	
	{template file='feed.block.content'}
	
	{plugin call='feed.template_block_entry_3'}	
</div>{* <!--// #js_item_feed_{$aFeed.feed_id} -->*}