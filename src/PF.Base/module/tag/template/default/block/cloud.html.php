<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{if count($aRows)}
	{if Phpfox::getParam('tag.enable_hashtag_support') && $bHashTag}
		<div id="hashtag_cloud">
	{/if}
	<div class="tag_cloud">
		<ul>
			{foreach from=$aRows item=aRow}
				<li>
					<a href="{$aRow.link}" data-text="{$aRow.key|clean}" class="site_hash_tag">
						{if Phpfox::getParam('tag.enable_hashtag_support') && $bHashTag}#{/if}{$aRow.key|clean}
					</a>
				</li>
			{/foreach}
		</ul>
		<div class="clear"></div>
	</div>
	<div class="extra_info">
		{_p var='trending_since_since' since=$sTrendingSince}
	</div>
	{if Phpfox::getParam('tag.enable_hashtag_support') && $bHashTag}
		</div>
	{/if}
{else}
	<div class="message">
		{_p var='no_tags_have_been_found'}
	</div>
{/if}