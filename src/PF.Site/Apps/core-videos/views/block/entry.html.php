<?php
defined('PHPFOX') or exit('NO DICE!');

?>
<div id="js_video_item_{$aItem.video_id}" class="video-item moderation_row js_video_parent"  data-uid="{$aItem.video_id}" >
    <div class="item-outer">
            <!-- moderate_checkbox -->
        <div class="{if !empty($bShowModerator)} moderation_row{/if}">
            {if !empty($bShowModerator)}
        <label class="item-checkbox">
            <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aItem.video_id}" id="check{$aItem.video_id}" />
            <i class="ico ico-square-o"></i>
        </label>
        {/if}
        </div>
        <!-- image -->
        <a class="item-media-src" href="{$aItem.link}">
            <span class="image_load" data-src="{$aItem.image_path}"></span>
            <div class="item-icon">
                {if isset($sView) && $sView == 'my' && $aItem.view_id != 0}
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

        <div class="item-inner">
            <!-- please show length video time -->
            {if !empty($aItem.duration)}
                <div class="item-video-length"><span>{$aItem.duration}</span></div>
            {/if}
            <!--  avatar user show when all video and hide when my vieo -->
            {if isset($sView) && $sView != 'my'}
                <div class="item-video-avatar">{img user=$aItem suffix='_50_square'}</div>
            {/if}
                
                <!-- title -->
                <div class="item-title">
                    <a href="{$aItem.link}" id="js_video_edit_inner_title{$aItem.video_id}" class="link ajax_link" itemprop="url">
                        {$aItem.title|clean}
                    </a>
                </div>
                <!-- author -->
                <div class="item-author dot-separate">
                    {if isset($sView) && $sView != 'my'}<span class="item-author-info">{_p var='by_full_name' full_name=$aItem|user:'':'':50:'':'author'}</span>{/if}
                    <span>{_p var='on'} {$aItem.time_stamp|convert_time:'core.global_update_time'}</span>
                </div>
            
            {if !isset($bVideoView)}
                <div class="total-view">
                    <span>
                        {$aItem.total_view} {if $aItem.total_view == 1}{_p var='view_lowercase'}{else}{_p var='views_lowercase'}{/if}
                    </span>
                    <span>.</span>
                    <span>
                        {$aItem.total_like} {if $aItem.total_like == 1}{_p var='like_lowercase'}{else}{_p var='likes_lowercase'}{/if}
                    </span>
                </div>
                <!-- dropdown -->
                {if $aItem.hasPermission}
                <div class="item-option video-button-option">
                    <div class="dropdown">
                        <a class="btn dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            <i class="ico ico-gear-o"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right" id="js_video_entry_options_{$aItem.video_id}">
                            {template file='v.block.menu'}
                        </ul>
                    </div>
                </div>
                {/if}
            {/if}
        </div>
    </div>
</div>