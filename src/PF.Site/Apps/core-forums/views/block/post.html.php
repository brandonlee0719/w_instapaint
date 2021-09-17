<?php
	defined('PHPFOX') or exit('NO DICE!');
?>

{if !isset($bIsPostUpdateText)}
	<article class="">
        {if isset($sView) && $sView == 'pending-post' && ( (isset($bShowModerator) && $bShowModerator) || (isset($bIsAdmin) && $bIsAdmin) )}
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
							<div class="item-time forum-text-overflow"><time>{$aPost.time_stamp|convert_time}</time><span class="dot">.</span><span class="color">{_p var='posted_in'}</span> <a href="{permalink module='forum.thread' id=$aPost.thread_id title=$aPost.thread_title}">{$aPost.thread_title|clean}</a></div>
						</div>
					</div>
					<div class="item-description mt-2 item_view_content" id="js_post_edit_text_{$aPost.post_id}">
						{$aPost.text|stripbb|striptag|split:55}
					</div>
				</div>
			{if !isset($bIsPostUpdateText)}
		</div>

	</article>
{/if}
