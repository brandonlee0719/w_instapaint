<?php 
    defined('PHPFOX') or exit('NO DICE!'); 
?>

{item name='CreativeWork'}
	<meta itemprop="dateCreated" content="{$aThread.time_stamp|micro_time}" />
	<meta itemprop="interactionCount" content="Replies:{$aThread.total_post|short_number}" />
	<meta itemprop="interactionCount" content="Views:{$aThread.total_view|short_number}" />
	<div class="js_selector_class_{$aThread.thread_id} item-outer">
		<div class="item-media">
			{img user=$aThread suffix='_50_square'}
		</div>
        <div class="item-inner">
            <div class="item-title forum-text-overflow">
                <a href="{if isset($aThread.sponsor_id)}{url link='ad.sponsor' view=$aThread.sponsor_id}{else}{permalink module='forum.thread' id=$aThread.thread_id title=$aThread.title}{/if}" itemprop="url">
                    {$aThread.title|clean|split:40|shorten:100:'...'}
                </a>
            </div>
            <div class="item-content item_view_content">
                <span>{$aThread.text_parsed|stripbb|striptag}</span>
            </div>

            <div class="item-author-post forum-text-overflow">
                <p>{_p var="by"} </p>
                {if isset($aThread.cache_name) && $aThread.cache_name}
                    <span class="user_profile_link_span"><a href="#">{$aThread.cache_name|clean}</a></span>
                {else}
                    {$aThread|user}
                {/if}
            </div>

            <time class="item-time">{$aThread.time_stamp|convert_time:'core.global_update_time'}</time>
        </div>
	</div>
{/item}