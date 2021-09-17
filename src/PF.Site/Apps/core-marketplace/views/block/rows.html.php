<?php
    defined('PHPFOX') or exit('NO DICE!');
?>
<article class="{if $aListing.is_sponsor}is_sponsored {/if}{if $aListing.is_featured}is_featured {/if}" id="js_mp_item_holder_{$aListing.listing_id}">
    <div class="item-outer">
        <div class="item-media">
            <a href="{$aListing.url}" class="mp_listing_image"
            style="background-image: url(
                {if !empty($aListing.image_path)}
                    {img server_id=$aListing.server_id title=$aListing.title path='marketplace.url_image' file=$aListing.image_path  suffix='_400_square' return_url=true}
                {else}
                    {param var='marketplace.marketplace_default_photo'}
                {/if}
            )" >
            </a>
            <div class="flag_style_parent hide">
                {if isset($sListingView) && $sListingView == 'my' && $aListing.view_id == 1}
                <div class="sticky-label-icon sticky-pending-icon">
                    <span class="flag-style-arrow"></span>
                    <i class="ico ico-clock-o"></i>
                </div>
                {/if}
                {if $aListing.is_sponsor}
                <div class="sticky-label-icon sticky-sponsored-icon">
                    <span class="flag-style-arrow"></span>
                    <i class="ico ico-sponsor"></i>
                </div>
                {/if}
                {if $aListing.is_featured}
                <div class="sticky-label-icon sticky-featured-icon">
                    <span class="flag-style-arrow"></span>
                    <i class="ico ico-diamond"></i>
                </div>
                {/if}
            </div>
            {if $bShowModerator}
                <div class="moderation_row">
                    <label class="item-checkbox">
                       <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aListing.listing_id}" id="check{$aListing.listing_id}" />
                       <i class="ico ico-square-o"></i>
                   </label>
                </div>
            {/if}
            <div class="item-info">
                {img user=$aListing suffix='_50_square'}
                <div class="item-info-author ml-1">
                    <div>{_p var="By"} {$aListing|user:'':'':50}</div>
                    <div>{$aListing.time_stamp|convert_time}</div>
                </div>
            </div>
        </div>
        <div class="item-inner">
            <div class="item-price">
                {if $aListing.price == '0.00'}
                    <span class="free">{_p var='free'}</span>
                {else}
                    {$aListing.price|currency:$aListing.currency_id}
                {/if}
            </div>
            <div class="market-text-overflow item-title">
                <a href="{$aListing.url}">
                    {$aListing.title|clean|shorten:100:'...'|split:25}
                </a>
                {if $aListing.view_id == '2'}
                    <span class="marketplace_item_sold">({_p var='sold'})</span>
                {/if}
            </div>
            {if isset($aListing.categories) && is_array($aListing.categories) && count($aListing.categories)}
            <div class="item-location market-text-overflow">
                {_p var='category'}:
                {$aListing.categories|category_display}
            </div>
            {/if}
            <div class="item-location market-text-overflow">
                {_p var='location'}:
                <a class="js_hover_title" href="{url link='marketplace' location=$aListing.country_iso}">
                        {$aListing.country_iso|location}
                    <span class="js_hover_info">
                        {if !empty($aListing.city)} {$aListing.city|clean} &raquo; {/if}
                        {if !empty($aListing.country_child_id)} {$aListing.country_child_id|location_child} &raquo; {/if}
                        {$aListing.country_iso|location}
                    </span>
                </a>
            </div>
            <div class="item-statistic">
                <span>
                    <span class="count">{$aListing.total_like|short_number}</span>
                    {if $aListing.total_like == 1}{_p var='like__l'}{else}{_p var='likes__l'}{/if}
                </span>
                <span>&nbsp;.&nbsp;</span>
                <span>
                    <span class="count">{$aListing.total_view|short_number}</span>
                    {if $aListing.total_view == 1}{_p var='view__l'}{else}{_p var='views__l'}{/if}
                </span>
            </div>
            {if $aListing.hasPermission}
                <div class="item-option">
                    <div class="dropdown">
                        <span role="button" class="row_edit_bar_action" data-toggle="dropdown">
                            <i class="ico ico-gear-o"></i>
                        </span>
                        <ul class="dropdown-menu dropdown-menu-right">
                            {template file='marketplace.block.menu'}
                        </ul>
                    </div>
                </div>
            {/if}
        </div>
    </div>
</article>