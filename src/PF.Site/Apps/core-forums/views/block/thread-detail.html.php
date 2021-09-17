<?php
	defined('PHPFOX') or exit('NO DICE!');
?>

{if !isset($bIsPostUpdateText)}
	<article class="">
        {if (!isset($sPermaView) || $sPermaView === null) && $aPost.count != 0 && ((isset($bShowModerator) && $bShowModerator)|| (isset($bIsAdmin) && $bIsAdmin))}
        <div class="moderation_row">
            <label class="item-checkbox">
                <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aPost.post_id}" id="check{$aPost.post_id}" />
                <i class="ico ico-square-o"></i>
            </label>
        </div>
        {/if}
		<div id="post{$aPost.post_id}" class="item-outer">
			{/if}
				<div class="forum_outer">
					<div class="forum_outer-inner">
						<div class="item-media">
							{img user=$aPost suffix='_50_square'}
						</div>

						<div class="item-inner ml-1">
							<div class="item-author">
								{if isset($aPost.cache_name) && $aPost.cache_name}
									<span class="user_profile_link_span"><a href="#">{$aPost.cache_name|clean}</a></span>
								{else}
									{$aPost|user:'':'':25}
								{/if}
							</div>
							<div class="item-time forum-text-overflow">
                                <time>{$aPost.time_stamp|convert_time}</time>
                            </div>
						</div>
						<div class="item-option">
							<a class="forum_post_count" href="{ permalink module='forum.thread' id=$aPost.thread_id title=$aThread.title}view_{$aPost.post_id}">#{$aPost.count}</a>
							{if $aPost.count == 0 && $aThread.hasPermission}
								<div class="dropdown">
									<span data-toggle="dropdown" class="row_edit_bar_action"><i class="ico ico-angle-down"></i></span>
									<ul class="dropdown-menu dropdown-menu-right">
				                        {template file='forum.block.menu'}
									</ul>
								</div>
							{/if}
						</div>
					</div>
                    {if $aPost.view_id}
                        <div class="mt-2">
                            {assign var=aPendingItem value=$aPost.pending_action}
                            {template file='core.block.pending-item-action'}
                        </div>
                    {/if}
					<div class="item-description mt-2 item_view_content" id="js_post_edit_text_{$aPost.post_id}">
						{$aPost.text|parse|split:55}
					</div>
                    
					{if !empty($aPoll.question) && $aPost.count == 0}
						<div class="table_info">
							{_p var='poll'}: {$aPoll.question|clean}
						</div>
						<div class="forum_poll_content">
							{template file='poll.block.entry'}
						</div>
					{/if}
                    {if isset($aPost.attachments)}
                        {module name='attachment.list' sType=forum attachments=$aPost.attachments}
                    {/if}
					{if isset($aThread.tag_list) && $aPost.count == 0}
                        {module name='tag.item' sType='forum' sTags=$aThread.tag_list iItemId=$aThread.thread_id iUserId=$aThread.user_id sMicroKeywords='keywords'}
                    {/if}
                    {if Phpfox::getUserParam('core.can_view_update_info') && !empty($aPost.update_user)}
                    <div class="help-block p_10">
                        <i>{$aPost.last_update_on}</i>
                    </div>
                    {/if}
				</div>
			{if !isset($bIsPostUpdateText)}
		</div>

		{if isset($aPost.aFeed)}
			<div class="forum_time_stamp item-detail-feedcomment">
				{if Phpfox::isModule('feed')}
					{module name='feed.comment' aFeed=$aPost.aFeed}
				{else}
					<div class="js_feed_comment_border">
						<ul>
							{if !isset($aPost.aFeed.feed_mini)}
								{if !empty($aPost.aFeed.feed_icon)}
									<li><img src="{$aPost.aFeed.feed_icon}" alt="" /></li>
								{/if}
								{if isset($aPost.aFeed.time_stamp)}
									<li class="feed_entry_time_stamp">
										<a href="{$aPost.aFeed.feed_link}" class="feed_permalink">{$aPost.aFeed.time_stamp|convert_time:'core.global_update_time'}</a>{if !empty($aPost.aFeed.app_link)} {_p var='via'} {$aPost.aFeed.app_link}{/if}
									</li>

									<li><span> &middot;</span></li>
								{/if}

								{if $aPost.aFeed.privacy > 0 && $aPost.aFeed.user_id == Phpfox::getUserId()}
									<li><div class="js_hover_title">{img theme='layout/privacy_icon.png' alt=$aPost.aFeed.privacy}<span class="js_hover_info">2 {$aPost.aFeed.privacy|privacy_phrase}</span></div></li>

									{if Phpfox::isModule('like')}
										<li><span>&middot;</span></li>
									{/if}
								{/if}
							{/if}

							{if Phpfox::isModule('like') && isset($aPost.Feed.like_type_id)}
								{if isset($aPost.aFeed.like_item_id)}
									{module name='like.link' like_type_id=$aPost.aFeed.like_type_id like_item_id=$aPost.aFeed.like_item_id like_is_liked=$aPost.aFeed.feed_is_liked}
								{else}
									{module name='like.link' like_type_id=$aPost.aFeed.like_type_id like_item_id=$aPost.aFeed.item_id like_is_liked=$aPost.aFeed.feed_is_liked}
								{/if}

								<li><span>&middot;</span></li>

							{/if}

							{if Phpfox::isModule('comment') && (isset($aPost.aFeed.comment_type_id) && $aPost.aFeed.can_post_comment) || (!isset($aPost.aFeed.comment_type_id) && isset($aPost.aFeed.total_comment))}
								<li>
									<a href="{$aPost.aFeed.feed_link}add-comment/" class="{if (isset($sFeedType) && $sFeedType == 'mini') || (!isset($aPost.aFeed.comment_type_id) && isset($aPost.aFeed.total_comment))}{else}js_feed_entry_add_comment no_ajax_link{/if}">{_p var='comment'}</a>
								</li>
								{if (Phpfox::isModule('share') && !isset($aPost.aFeed.no_share)) || (isset($aPost.aFeed.report_module) && isset($aPost.aFeed.force_report))}
									<li><span>&middot;</span></li>
								{/if}
							{/if}
							{if Phpfox::isModule('share') && !isset($aPost.aFeed.no_share)}
								{module name='share.link' type='feed' display='menu' url=$aPost.aFeed.feed_link title=$aPost.aFeed.feed_title}

								{if Phpfox::isModule('report')}
									<li><span>&middot;</span></li>
								{/if}
							{/if}

							{if Phpfox::isModule('report') && isset($aPost.aFeed.report_module) && isset($aPost.aFeed.force_report)}

								<li><a href="#?call=report.add&amp;height=100&amp;width=400&amp;type={$aPost.aFeed.report_module}&amp;id={$aPost.aFeed.item_id}" class="inlinePopup activity_feed_report" title="{$aPost.aFeed.report_phrase}">{_p var='report'}</a></li>
							{/if}

						</ul>

							{plugin call='core.template_block_comment_border_new'}

					</div>
				{/if}
			</div>
		{/if}

	</article>
{/if}
