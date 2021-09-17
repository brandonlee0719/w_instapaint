<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="item-container page-listing page-home in" id="core-pages-all">
    {foreach from=$aPagesList item=aPage}
    <article class="page-item" data-url="{$aPage.url}" data-uid="pages_{$aPage.page_id}" id="pages_{$aPage.page_id}">
        <div class="item-outer">
            {if $bIsModerator}
                <div class="moderation_row">
                    <label class="item-checkbox">
                        <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aPage.page_id}" id="check{$aPage.page_id}" />
                        <i class="ico ico-square-o"></i>
                    </label>
                </div>
            {/if}
            <div class="page-photo">
                <a href="{$aPage.url}">
                {img server_id=$aPage.profile_server_id title=$aPage.title path='pages.url_image' file=$aPage.image_path suffix='_200_square' max_width='200' max_height='200' is_page_image=true}
                </a>
                <div class="item-icon">
                    <div class="item-icon-liked btn-default" title="Liked" {if !$aPage.is_liked}style="display:none"{/if}>
                        <span class="ico ico-check"></span>
                    </div>
                    <div class="item-icon-like btn-primary btn-gradient" role="button" data-app="core_pages" data-action-type="click" data-action="like_page" data-id="{$aPage.page_id}" {if $aPage.is_liked}style="display:none"{/if}>
                        <span class="ico ico-plus"></span>
                    </div>
                </div>
                <!-- desc grid view mode -->
                <a class="item-desc" href="{$aPage.url}">
                    <div class="item-desc-main">{$aPage.text_parsed|striptag|stripbb}</div>
                </a>
            </div>
            {if ($sView == 'my' && $aPage.view_id == 1)}
            <div class="item-icon-pending">
                <div class="sticky-label-icon sticky-pending-icon">
                    <span class="flag-style-arrow"></span>
                    <i class="ico ico-clock-o"></i>
                </div>
            </div>
            {/if}
            <div class="item-inner">
                <div class="item-title">
                    <a href="{$aPage.url}" class="link pages_title">{$aPage.title|clean}</a>
                </div>

                <div class="item-number-like">
                    {if $aPage.total_like != 1}
                    {_p var='pages_total_likes', total=$aPage.total_like}
                    {else}
                    {_p var='pages_total_like', total=$aPage.total_like}
                    {/if}
                </div>

                {if $aPage.canApprove || $aPage.canEdit || $aPage.canDelete}
                <!-- please condition when show -->
                <div class="item-option page-button-option">
                    <div class="dropdown">
                        <a class="btn dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            <i class="ico ico-gear-o"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right" id="js_page_entry_options_{$aPage.page_id}">
                            {template file='pages.block.link-listing'}
                        </ul>
                    </div>
                </div>
                {/if}
            </div>
        </div>
    </article>
    {/foreach}
</div>