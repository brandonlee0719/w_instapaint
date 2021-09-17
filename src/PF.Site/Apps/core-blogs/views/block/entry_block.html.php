<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="blog-item">
    <div class="item-outer">
        {if !empty($aItem.image)}
            <!-- image -->
            <a class="item-media-src" href="{permalink module='blog' id=$aItem.blog_id title=$aItem.title}">
                <span style="background-image: url({$aItem.image})"></span>
            </a>
        {/if}
        <div class="item-inner">
            <!-- title -->
            <div class="item-title">
                <a href="{if empty($aItem.sponsor_id)}{permalink module='blog' id=$aItem.blog_id title=$aItem.title}{else}{url link='ad.sponsor' view=$aItem.sponsor_id}{/if}" title="{$aItem.title|clean}">{$aItem.title|clean}</a>
            </div>
            <!-- author -->
            <div class="item-author dot-separate">
                <span>{_p var='by_full_name' full_name=$aItem|user:'':'':50:'':'author'}</span>
            </div>
            <!-- description -->
            <div class="item-desc item_content">
                {$aItem.text|striptag|stripbb|highlight:'search'|split:500|shorten:100:'...'}
            </div>
            <div class="total-view">
                <span>
                    {$aItem.total_view|short_number} {if $aItem.total_view == 1}{_p var='view_lowercase'}{else}{_p var='views_lowercase'}{/if}
                </span>
            </div>
        </div>
    </div>
</div>
