<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{if isset($aParentFeed) && $aParentFeed.type_id == 'v'}
    <div class="v-feed-video feed_block_title_content {if $aParentFeed.is_stream}feed-video-url{else}feed-video-upload{/if}">
        
        <div class="{if $aParentFeed.is_stream}fb_video_iframe{else}fb_video_player{/if} v-feed-video">{if isset($aParentFeed.embed_code)}{$aParentFeed.embed_code}{/if}
            
        </div>
        <div class="v-feed-inner">
        <a href="{$aParentFeed.feed_link}" class="v-feed-title activity_feed_content_link_title">{$aParentFeed.title}</a>
        <!-- please show view number -->
            <div class="v-feed-view"><span>{$aParentFeed.video_total_view < 100 ? $aParentFeed.video_total_view : '99+'} {if $aParentFeed.video_total_view > 1}{_p var='views_lowercase'}{else}{_p var='view_lowercase'}{/if}</span></div>
            <div class="v-feed-description item_view_content">{$aParentFeed.feed_content|feed_strip|split:55|max_line|stripbb}</div>
        </div>
    </div>
{else}
    <div class="v-feed-video feed_block_title_content {if $aFeed.is_stream}feed-video-url{else}feed-video-upload{/if}">
        
        <div class="{if $aFeed.is_stream}fb_video_iframe{else}fb_video_player{/if}">{if isset($aFeed.embed_code)}{$aFeed.embed_code}{/if}
           
        </div>
        <div class="v-feed-inner">
            <a href="{$aFeed.feed_link}" class="v-feed-title activity_feed_content_link_title">{$aFeed.feed_title}</a>
         <!-- please show view number -->
            <div class="v-feed-view"><span>{$aFeed.video_total_view < 100 ? $aFeed.video_total_view : '99+'} {if $aFeed.video_total_view > 1}{_p var='views_lowercase'}{else}{_p var='view_lowercase'}{/if}</span></div>
            <div class="v-feed-description activity_feed_content_display">{$aFeed.feed_content|feed_strip|split:55|max_line|stripbb}</div>
        </div>
    </div>
{/if}
{unset var=$aParentFeed}
