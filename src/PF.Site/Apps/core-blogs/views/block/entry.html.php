<?php
defined('PHPFOX') or exit('NO DICE!');

?>
<div class="blog-item " data-url="" data-uid="{$aItem.blog_id}" id="js_blog_entry_{$aItem.blog_id}">
    <!-- moderate_checkbox -->
    <div class="{if !empty($bShowModerator)} moderation_row{/if}">
        {if !empty($bShowModerator) && !isset($bBlogView)}
        <label class="item-checkbox">
            <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aItem.blog_id}" id="check{$aItem.blog_id}" />
            <i class="ico ico-square-o"></i>
        </label>
        {/if}
    </div>
    <div class="item-outer {if empty($aItem.image)}none-image{/if} {if (isset($sView) && $sView == 'my') && $aItem.is_approved == INACTIVATE && !isset($bBlogView)}has-pending{/if} {if $aItem.is_sponsor}has-sponsor{/if} {if $aItem.is_featured}has-feature{/if}">
        <!-- show in media from 991px  -->
        <div class="item-title-author-responsive" style="display: none">
            {if !isset($bBlogView)}
                {if isset($sView) && $sView == 'my'}
                    {if $aItem.post_status == BLOG_STATUS_DRAFT}
                        <!-- Draft mark-->
                        <span class="blog_status_draft">{_p var='draft_info'}</span>
                    {/if}
                {/if}
                <!-- title -->
                <div class="item-title">
                    <a href="{permalink module='blog' id=$aItem.blog_id title=$aItem.title}" id="js_blog_edit_inner_title{$aItem.blog_id}" class="link ajax_link" itemprop="url">{$aItem.title|clean}</a>
                </div>
                <!-- author -->
                <div class="item-author dot-separate">
                    <span class="item-author-info">{_p var='by_full_name' full_name=$aItem|user:'':'':50:'':'author'}</span>
                    <span>{_p var='on'} {$aItem.time_stamp|convert_time:'core.global_update_time'}<span>{plugin call='blog.template_block_entry_date_end'}</span></span>
                </div>
            {/if}
        </div>
        {if !empty($aItem.image)}
            <!-- image -->
            <a class="item-media-src" href="{permalink module='blog' id=$aItem.blog_id title=$aItem.title}">
                <span style="background-image: url({$aItem.image})"></span>
                <div class="item-icon">
                    {if (isset($sView) && $sView == 'my') && $aItem.is_approved == INACTIVATE}
                        <div class="sticky-label-icon sticky-pending-icon">
                            <span class="flag-style-arrow"></span>
                            <i class="ico ico-clock-o"></i>
                        </div>
                    {/if}
                    {if $aItem.is_sponsor}
                    <!-- Sponsor -->
                    <div class="sticky-label-icon sticky-sponsored-icon">
                        <span class="flag-style-arrow"></span>
                        <i class="ico ico-sponsor"></i>
                    </div>
                    {/if}
                    {if $aItem.is_featured}
                    <!-- Featured -->
                    <div class="sticky-label-icon sticky-featured-icon">
                        <span class="flag-style-arrow"></span>
                        <i class="ico ico-diamond"></i>
                    </div>
                    {/if}
                </div>
            </a>
            <!-- when no image -->
        {else}
            <div class="item-icon">
                {if (isset($sView) && $sView == 'my') && $aItem.is_approved == INACTIVATE && !isset($bBlogView)}
                    <div class="sticky-label-icon sticky-pending-icon">
                        <span class="flag-style-arrow"></span>
                        <i class="ico ico-clock-o"></i>
                    </div>
                {/if}
                {if $aItem.is_sponsor}
                <!-- Sponsor -->
                <div class="sticky-label-icon sticky-sponsored-icon">
                    <span class="flag-style-arrow"></span>
                    <i class="ico ico-sponsor"></i>
                </div>
                {/if}
                {if $aItem.is_featured}
                <!-- Featured -->
                <div class="sticky-label-icon sticky-featured-icon">
                    <span class="flag-style-arrow"></span>
                    <i class="ico ico-diamond"></i>
                </div>
                {/if}
            </div>
        {/if}
        <div class="item-inner">
            {if !isset($bBlogView)}
                {if isset($sView) && $sView == 'my'}
                    {if $aItem.post_status == BLOG_STATUS_DRAFT}
                        <!-- Draft mark-->
                        <span class="blog_status_draft">{_p var='draft_info'}</span>
                    {/if}
                {/if}
                <!-- title -->
                <div class="item-title">
                    <a href="{permalink module='blog' id=$aItem.blog_id title=$aItem.title}" id="js_blog_edit_inner_title{$aItem.blog_id}" class="link ajax_link" itemprop="url">{$aItem.title|clean}</a>
                </div>
                <!-- author -->
                <div class="item-author dot-separate">
                    <span class="item-author-info">{_p var='by_full_name' full_name=$aItem|user:'':'':50:'':'author'}</span>
                    <span>{_p var='on'} {$aItem.time_stamp|convert_time:'core.global_update_time'}<span>{plugin call='blog.template_block_entry_date_end'}</span></span>
                </div>
            {/if}
            <!-- description -->
            <div class="item-desc item_content item_view_content">
                {if isset($bBlogView)}
                    {$aItem.text|parse|highlight:'search'|split:500}
                {else}
                    {if !empty($aItem.text)}
                        {if !empty($iShorten)}
                            {$aItem.text|striptag|stripbb|highlight:'search'|split:500|shorten:$iShorten:'...'}
                        {else}
                            {$aItem.text|striptag|stripbb|highlight:'search'|split:500}
                        {/if}
                    {/if}
                {/if}
            </div>
            
            {if !isset($bBlogView)}
                <div class="total-view">
                    <span>
                        {$aItem.total_view|short_number} {if $aItem.total_view == 1}{_p var='view_lowercase'}{else}{_p var='views_lowercase'}{/if}
                    </span>
                    <span>
                        {$aItem.total_like|short_number} {if $aItem.total_like == 1}{_p var='like_lowercase'}{else}{_p var='likes_lowercase'}{/if}
                    </span>
                </div>
                <!-- dropdown -->
                {if !empty($aItem.permission_enable)}
                <div class="item-option blog-button-option">
                    <div class="dropdown">
                        <a class="btn dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            <i class="ico ico-gear-o"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right" id="js_blog_entry_options_{$aItem.blog_id}">
                            {template file='blog.block.link'}
                        </ul>
                    </div>
                </div>
                {/if}
            {/if}
            <!-- others -->
            {if isset($bBlogView) && $aItem.total_attachment}
                {module name='attachment.list' sType=blog iItemId=$aItem.blog_id}
            {/if}
            {plugin call='blog.template_block_entry_text_end'}
            {plugin call='blog.template_block_entry_end'}
        </div>
    </div>
</div>
