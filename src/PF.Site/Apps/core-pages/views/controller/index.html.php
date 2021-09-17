<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{if $bShowCategories}
    {if count($aCategories)}
        {foreach from=$aCategories item=aCategory}
            {if $aCategory.pages}
            <div class="block_clear">
                <div class="item-page-title-block">
                    <a class="item-title" href="{$aCategory.link}">
                        {if Phpfox::isPhrase($this->_aVars['aCategory']['name'])}
                        {_p var=$aCategory.name}
                        {else}
                        {$aCategory.name|convert}
                        {/if}
                    </a>
                    <a href="#item-collapse-page-grid-{$aCategory.type_id}" data-toggle="collapse" class="item-collapse-icon"><span class="ico ico-angle-right"></span></a>
                </div>
                <div class="content clearfix">
                    <div class="item-container page-listing page-home collapse in" id="item-collapse-page-grid-{$aCategory.type_id}">
                        {foreach from=$aCategory.pages item=aPage}
                        <article class="page-item" data-url="{$aPage.link}" data-uid="pages_{$aPage.page_id}" id="pages_{$aPage.page_id}">
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
                                    <a href="{$aPage.link}">
                                    {img server_id=$aPage.profile_server_id title=$aPage.title path='pages.url_image' file=$aPage.image_path suffix='_200_square' max_width='200' max_height='200' is_page_image=true}
                                    </a>
                                    <div class="item-icon">
                                        <div class="item-icon-liked btn-default" title="{_p var='liked'}" {if empty($aPage.is_liked)}style="display:none"{/if}>
                                            <span class="ico ico-check"></span>
                                        </div>
                                        <div class="item-icon-like btn-primary btn-gradient" role="button" data-app="core_pages" data-action-type="click" data-action="like_page" data-id="{$aPage.page_id}" {if !empty($aPage.is_liked)}style="display:none"{/if}>
                                            <span class="ico ico-plus"></span>
                                        </div>
                                    </div>
                                    <!-- desc grid view mode -->
                                    <a class="item-desc" href="{$aPage.link}">
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
                                        <a href="{$aPage.link}" class="link pages_title">{$aPage.title|clean}</a>
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

                        {if isset($aCategory.remain_pages) && $aCategory.remain_pages}
                        <article class="page-item">
                            <a class="remain_pages" href="{url link='pages.category'}{$aCategory.type_id}/{_p var=$aCategory.name}/{if $sView}view_{$sView}{/if}" title="{_p var=$aCategory.name}">
                                <span class=" page-item-viewall "
                                      {if !empty($aCategory.image_path)}
                                          style="background: url({img server_id=$aCategory.image_server_id path='core.path_actual' file=$aCategory.image_path suffix='_200' return_url=true})"
                                      {else}
                                          style="background-image: url('{img path='core.path_actual' file='PF.Site/Apps/core-pages/assets/img/default-category/default_category.png' return_url=true}')"
                                      {/if}
                                >
                                    {if $aCategory.image_path}
                                    <div class="overlay"></div>
                                    {/if}
                                    <span class="page-item-view-all">{_p var='view_all'}</span>
                                    <span class="page-item-remain-number">+{$aCategory.remain_pages}</span>
                                </span>
                            </a>
                        </article>
                        {/if}
                    </div>
                </div>
            </div>
            {/if}
        {/foreach}
    {/if}
    {if $iCountPage == 0}
    <p class="help-block">
        {_p var='no_pages_found'}
    </p>
    {/if}
{else}

{if count($aPages)}
{if $sView == 'my' && Phpfox::getUserBy('profile_page_id')}
<div class="message">
    {_p var='note_that_pages_displayed_here_are_pages_created_by_the_page' global_full_name=$sGlobalUserFullName|clean profile_full_name=$aGlobalProfilePageLogin.full_name|clean}
</div>
{/if}
{if !PHPFOX_IS_AJAX }
<div class="item-container pages-items page-listing page-result">
{/if}
	{foreach from=$aPages item=aPage}
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
            <a class="page-photo" href="{$aPage.url}">
                {img server_id=$aPage.profile_server_id title=$aPage.title path='pages.url_image' file=$aPage.image_path suffix='_200_square' max_width='200' max_height='200' is_page_image=true}
                {if ($sView == 'my' && $aPage.view_id == 1)}
                <div class="item-icon-pending">
                    <div class="sticky-label-icon sticky-pending-icon">
                        <span class="flag-style-arrow"></span>
                        <i class="ico ico-clock-o"></i>
                    </div>
                </div>
                {/if}
            </a>

            <div class="item-inner">
                <div class="item-title">
                    <a href="{$aPage.url}" class="link pages_title ">{$aPage.title|clean}</a>
                </div>
                <div class="item-info">
                <span class="item-number-like">{if $aPage.total_like != 1}{_p var='pages_total_likes', total=$aPage.total_like}{else}{_p var='pages_total_like', total=$aPage.total_like}{/if}</span>
                {if $aPage.category_name}
                <a href="{$aPage.category_link}" class="item-category">
                    {_p var=$aPage.category_name}
                </a>
                {else}
                <a href="{$aPage.type_link}" class="item-category">
                    {_p var=$aPage.type_name}
                </a>
                {/if}
                </div>
                
                <div class="item-desc">
                    <div class="item-desc-main">{$aPage.text_parsed|striptag|stripbb}</div>
                </div>
                {if $aPage.canApprove || $aPage.canEdit || $aPage.canDelete}
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
                <div class="item-group-icon-users">
                    <div class="item-icon">
                        {if !empty($aPage.is_liked)}
                        <a role="button" class="btn btn-default btn-icon item-icon-liked" data-toggle="dropdown">
                            <span class="ico ico-thumbup"></span>{_p var='liked'}<span class="ml-1 ico ico-caret-down"></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>
                                <a role="button" onclick="$.ajaxCall('like.delete', 'type_id=pages&item_id={$aPage.page_id}&reload=1');">
                                    <span class="mr-1 ico ico-thumbdown"></span>{_p var='unlike'}
                                </a>
                            </li>
                        </ul>
                        {else}
                        <button class="btn btn-primary btn-gradient btn-icon item-icon-like" onclick="$.ajaxCall('like.add', 'type_id=pages&item_id={$aPage.page_id}&reload=1');">
                            <span class="ico ico-thumbup-o"></span>{_p var='like'}
                        </button>
                        {/if}
                    </div>
                    <!-- please show avatar user like page -->
                    {if $aPage.total_members > 0}
                        <div class="item-members">
                            {if ($aPage.total_members - 4) > 0}
                                {foreach from=$aPage.members item=aMember key=iKey}
                                    {if $iKey < 3}
                                        {img user=$aMember suffix='_50_square'}
                                    {/if}
                                {/foreach}
                                <a href="{$aPage.url}members" class="item-members-viewall"><span>+{$aPage.remain_members}</span></a>
                            {else}
                                {foreach from=$aPage.members item=aMember}
                                    {img user=$aMember suffix='_50_square'}
                                {/foreach}
                            {/if}
                        </div>
                    {/if}
                </div>
            </div>
        </div>
    </article>
    {/foreach}
    {pager}
{if !PHPFOX_IS_AJAX }
</div>
{/if}

{else}
{if !PHPFOX_IS_AJAX }
<p class="help-block">
    {_p var='no_pages_found'}
</p>
{/if}
{/if}
{/if}
{if $bIsModerator}
{moderation}
{/if}
