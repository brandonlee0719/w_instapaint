<?php 
	defined('PHPFOX') or exit('NO DICE!'); 
?>
	{if isset($isReplies)}
		{foreach from=$aThread.posts name=posts item=aPost}
			{plugin call='forum.template_controller_post_1'}
			    {template file='forum.block.thread-detail'}
			{plugin call='forum.template_controller_post_2'}
		{/foreach}
	{else}
		{if $sPermaView === null}
		{if $aThread.view_id}
            {template file='core.block.pending-item-action'}
		{/if}
		{if !$aThread.is_announcement}
		<div class="forum_header_menu forum-app">
			<ul class="sub_menu_bar clearfix">
				<li class="sub_menu_bar_li fw-bold js_thread_subscribe"  {if $aThread.is_subscribed}style="display:none"{/if}>
					<a href="javascript:void()" onclick="$(this).parent().hide(); $('#js_subscribe').hide(); $('#js_unsubscribe').show(); $.ajaxCall('forum.subscribe', 'thread_id={$aThread.thread_id}&amp;subscribe=1'); return false;" class="subscribe">{_p var='subscribe'}</a>
				</li>
				{if Phpfox::getParam('forum.enable_rss_on_threads') && Phpfox::isModule('rss')}
					<li class="sub_menu_bar_li">
						<a href="{url link='forum.rss' thread=$aThread.thread_id}" title="{_p var='get_rss_for_this_thread'}" class="no_ajax_link rss_link">
							<i class="ico ico-rss-square"></i>
						</a>
					</li>
				{/if}
			</ul>
			<div class="forum-addthis">{addthis url=$aThread.bookmark title=$aThread.title description=$sShareDescription}</div>
		</div>

		{/if}
		{/if}

		{if $sPermaView !== null}
			<div class="table_info">
				<div class="go_left">
					{_p var='viewing_single_post'}
				</div>
				<div class="t_right" style="padding-right:5px;">
					{_p var='thread'}: <a href="{permalink module='forum.thread' id=$aThread.thread_id title=$aThread.title}" title="{$aThread.title|clean}">{$aThread.title|clean|shorten:50:'...'}</a>
				</div>
				<div class="clear"></div>
			</div>
		{/if}

		<div class="forum_thread_view_holder">
			<div id="js_thread_start"></div>
			<meta itemprop="dateCreated" content="{$aThread.time_stamp|micro_time}" />
			<meta itemprop="dateModified" content="{$aThread.time_update|micro_time}" />
			<meta itemprop="interactionCount" content="Posts:{$iTotalPosts}" />
			{if isset($aThread.post_starter)}
			<div class="item-container forum-app detail clearfix">
				<section class="thread_starter">
					{plugin call='forum.template_controller_post_1'}
						{template file='forum.block.thread-detail'}
					{plugin call='forum.template_controller_post_2'}
				</section>
				<section class="thread_replies">
					{if ($iTotalPosts > Phpfox::getParam('forum.total_posts_per_thread'))}
					<div class="tr_view_all">
						<a href="{permalink module='forum.thread' id=$aThread.thread_id title=$aThread.title view=all}" class="ajax view_all_previous" data-add-class="is-clicked" data-add-spin="true">{_p var='view_all_previous_posts'}</a>
					</div>
					{/if}
			{/if}
                    {if count($aThread.posts)}
					<div class="tr_content">
						{foreach from=$aThread.posts name=posts item=aPost}
							{plugin call='forum.template_controller_post_1'}
								{template file='forum.block.thread-detail'}
							{plugin call='forum.template_controller_post_2'}
						{/foreach}
					</div>
                    {/if}
                    {if $bShowModerator}
                        <div class="js_thread_moderator" {if !count($aThread.posts)}style="display: none;"{/if}>
                            {moderation}
                        </div>
                    {/if}

			{if isset($aThread.post_starter)}
					<div id="js_post_new_thread"></div>
				</section>

                {if !empty($aThread.canReply)}
                    <div id="js_thread_quick_reply">
                        {module name='forum.reply' iThreadId=$aThread.thread_id}
                    </div>
                {/if}
			</div>
			{/if}


		</div>
	{/if}