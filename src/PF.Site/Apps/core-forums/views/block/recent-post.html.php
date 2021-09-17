<?php
	defined('PHPFOX') or exit('NO DICE!');
?>
<div class="item-container forum-app recent-discussion">
	{foreach from=$threads item=post}
		<article class="">
			<div class="item-outer">
				<div class="item-media">
					{img user=$post suffix='_50_square'}
				</div>
			    <div class="item-inner">
			        <div class="item-title forum-text-overflow">
			            <a href="{permalink module='forum.thread' title=$post.thread_title id=$post.thread_id}view_{$post.post_id}">
			                {$post.thread_title|clean}
			            </a>
			        </div>
			        <div class="item-content item_view_content">
			            <span>{$post.text_parsed|stripbb|striptag|shorten:50:'...'}</span>
			        </div>

			        <div class="item-author-post forum-text-overflow">
			            <p>{_p var="by"} </p>
			            {if isset($post.cache_name) && $post.cache_name}
			                <span class="user_profile_link_span"><a href="#">{$post.cache_name|clean}</a></span>
			            {else}
			                {$post|user}
			            {/if}
			        </div>

			        <time class="item-time">{$post.time_stamp|convert_time}</time>
			    </div>
			</div>
		</article>
	{/foreach}
</div>