<?php
	defined('PHPFOX') or exit('NO DICE!');
?>

{if !PHPFOX_IS_AJAX}
{template file='forum.block.search'}
{/if}

{if !PHPFOX_IS_AJAX && count($aThreads) && !$bIsSearch && Phpfox::getParam('forum.enable_rss_on_threads') && Phpfox::isModule('rss')}
	<a href="{if $aCallback === null}{url link='forum.rss' forum=$aForumData.forum_id}{elseif $aCallback.module_id == 'pages'}{url link='forum.rss' pages=$aCallback.item_id}{elseif $aCallback.module_id == 'groups'}{url link='forum.rss' groups=$aCallback.item_id}{/if}" title="{_p var='rss_for_this_forum'}" class="no_ajax_link rss_link rss-link-forum text-uppercase" style="display: none;">
		<i class="ico ico-rss-square"></i>{_p var='subscribe_our_rss_feed'}
	</a>
{/if}

{if !PHPFOX_IS_AJAX && $aCallback === null && !$bIsSearch}
	{template file='forum.block.entry'}
{/if}

{if !PHPFOX_IS_AJAX && !$bIsSearch && count($aAnnouncements)}
	<div class="forum_section_header announcements title">
		<div class="text-capitalize">{_p var='announcements'}</div>
	</div>
	<div class="item-container forum-app my-thread">
		{foreach from=$aAnnouncements item=aThread}
			{template file='forum.block.thread-rows'}
		{/foreach}
	</div>
{/if}

{if count($aThreads)}
	{if !PHPFOX_IS_AJAX}
	{if isset($bResult) && $bResult}
	<div class="forum_section_header posts title">
		<div class="text-capitalize">{_p var='posts'}</div>
	</div>
	{else}
	<div class="forum_section_header threads title">
		<div class="text-capitalize">{_p var='threads'}</div>
	</div>
	{/if}
	{/if}
	{if isset($bResult) && $bResult}
		<div class="item-container forum-app new-post">
			{foreach from=$aThreads item=aPost}
				{template file='forum.block.post'}
			{/foreach}
		</div>
	{else}
		<div class="item-container forum-app my-thread">
			{foreach from=$aThreads item=aThread}
				{template file='forum.block.thread-rows'}
			{/foreach}
		</div>
	{/if}

	{pager}
    {if !isset($bIsPostSearch) && $bShowModerator }
        {moderation}
    {/if}
{else}
    {if !PHPFOX_IS_AJAX && (!isset($aForums) || empty($aForums))}
    <div style="margin-top: 20px;">
        {if !$bIsSearch}
        <i>{_p var='forum_have_no_thread'}</i>
        {elseif $bIsSearch && isset($bResult) && $bResult}
        <i>{_p var='no_posts_found'}</i>
        {elseif $bIsSearch}
        <i>{_p var='no_threads_found'}</i>
        {/if}
    </div>
    {/if}
{/if}
