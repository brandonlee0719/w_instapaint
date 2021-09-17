<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{if is_array($aGroupFeeds)}
{foreach from=$aGroupFeeds key=sGroup item=aFeeds name=feeds}
<div class="item-rss-outer">
	<div class="item-title-block"><div class="item-title">{_p var=$sGroup}</div><a href="#item-rss-collapse-outer-list-{$phpfox.iteration.feeds}" data-toggle="collapse" class="item-collapse-icon"><span class="ico ico-angle-up"></span></a></div>
	<ul class="item-action collapse in" id="item-rss-collapse-outer-list-{$phpfox.iteration.feeds}">
	{foreach from=$aFeeds item=aFeed}
		{if isset($aFeed.child)}
		<li><div class="item-action-parent"><a href="{url link='rss' id=$aFeed.feed_id}" class="no_ajax_link"><span class="ico ico-rss-square"></span>{_p var=$aFeed.title_var}</a><a href="#item-rss-collapse-child-list-{$aFeed.feed_id}" data-toggle="collapse" class="item-collapse-icon"><span class="ico ico-angle-up"></span></a></div>
			<ul class="item-action-child-list collapse in" id="item-rss-collapse-child-list-{$aFeed.feed_id}">
			{foreach from=$aFeed.child key=sLink item=sPhrase}
				<li><a href="{$sLink}" class="no_ajax_link"><span class="ico ico-rss-square"></span>{$sPhrase|convert}</a></li>	
			{/foreach}
			</ul>
		</li>
		{else}
		<li><div class="item-action-parent"><a href="{url link='rss' id=$aFeed.feed_id}" class="no_ajax_link"><span class="ico ico-rss-square"></span>{_p var=$aFeed.title_var}</a></div></li>
		{/if}
	{/foreach}
	</ul>
</div>
{/foreach}
{else}
<div class="alert alert-info">
	{_p var='no_rss_feeds_are_available'}
</div>
{/if}
