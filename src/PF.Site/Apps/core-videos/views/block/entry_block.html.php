<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="video-item">
    <div class="item-outer">
            <!-- image -->
            <a class="item-media-src" href="{$aItem.link}">
                <span class="image_load" data-src="{$aItem.image_path}"></span>
                <div class="item-video-icon"><span class="ico ico-play"></span></div>
            </a>
        <div class="item-inner">
            <!-- title -->
            <div class="item-title">
                <a href="{$aItem.link}" id="js_video_edit_inner_title{$aItem.video_id}" class="link ajax_link" itemprop="url">{$aItem.title|clean}</a>
            </div>
            <!-- author -->
            <div class="item-info">
                {if !empty($aItem.duration)}
                <div class="item-video-length">{$aItem.duration}</div>
                {/if}
                <div class="item-author dot-separate">
                    <span class="item-author-info">{_p var='by_full_name' full_name=$aItem|user:'':'':50:'':'author'}</span>
                </div>
            </div>
            <div class="total-view">
                <span>
                    {$aItem.total_view|short_number} {if $aItem.total_view == 1}{_p var='view_lowercase'}{else}{_p var='views_lowercase'}{/if}
                </span>
            </div>
        </div>
    </div>
</div>
