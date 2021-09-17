<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if !empty($bShowCategories)}
{if count($aCategories)}
{foreach from=$aCategories item=aCategory}
{if $aCategory.pages}
<div class="block_clear">
    <div class="item-group-title-block">
        <a class="item-title" href="{$aCategory.link}">
            {_p var=$aCategory.name}
        </a>
        <a href="#item-collapse-group-grid-{$aCategory.type_id}" data-toggle="collapse" class="item-collapse-icon"><span class="ico ico-angle-right"></span></a>
    </div>
    <div class="content clearfix">
        <div class="group-listing item-container group-home collapse in" id="item-collapse-group-grid-{$aCategory.type_id}">
            {foreach from=$aCategory.pages item=aPage}
            <article class="group-item" data-url="{$aPage.link}" data-uid="groups_{$aPage.page_id}" id="groups_{$aPage.page_id}">
                <div class="item-outer">
                    {if !empty($bShowModeration)}
                    <div class="moderation_row">
                        <label class="item-checkbox">
                            <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aPage.page_id}" id="check{$aPage.page_id}" />
                            <i class="ico ico-square-o"></i>
                        </label>
                    </div>
                    {/if}

                    <div class="group-photo">
                        <a href="{$aPage.link}">
                        {img server_id=$aPage.profile_server_id title=$aPage.title path='pages.url_image' file=$aPage.image_path suffix='_200_square' max_width='200' max_height='200' is_page_image=true}
                        </a>
                        <div class="item-icon">
                            <div class="item-icon-joined btn-default" title="{_p var='joined'}" {if empty($aPage.is_liked)}style="display:none"{/if}>
                                <span class="ico ico-check"></span>
                            </div>
                            <div class="item-icon-joined btn-default" {if !empty($aPage.is_liked) || !$aPage.joinRequested}style="display:none"{/if}>
                                <span class="ico ico-sandclock-goingon-o"></span>
                            </div>
                            <div class="item-icon-join btn-primary btn-gradient" role="button" data-app="core_groups" data-id="{$aPage.page_id}" data-action="join_group" data-is-closed="{ $aPage.reg_method == 1 }" data-action-type="click" {if !empty($aPage.is_liked) || $aPage.joinRequested}style="display:none"{/if}>
                                <span class="ico ico-plus"></span>
                            </div>
                        </div>
                        <!-- desc grid view mode -->
                        <a href="{$aPage.link}" class="item-desc">
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
                            <a href="{$aPage.link}" class="link pages_title ">{$aPage.title|clean}</a>
                        </div>

                        <div class="item-number-member">
                            {if $aPage.total_like != 1}
                            {_p var='groups_total_members', total=$aPage.total_like}
                            {else}
                            {_p var='groups_total_member', total=$aPage.total_like}
                            {/if}
                        </div>
                        {if $aPage.bShowItemActions}
                        <!-- please condition when show -->
                        <div class="item-option group-button-option">
                            <div class="dropdown">
                                <a class="btn dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <i class="ico ico-gear-o"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right" id="js_group_entry_options_{$aPage.page_id}">
                                    {template file='groups.block.link-listing'}
                                </ul>
                            </div>
                        </div>
                        {/if}
                    </div>
                </div>
            </article>
            {/foreach}

            {if isset($aCategory.remain_pages) && $aCategory.remain_pages}
            <article class="group-item">
                <a class="remain_groups" href="{url link='groups.category'}{$aCategory.type_id}/{_p var=$aCategory.name}/{if $sView}view_{$sView}{/if}" title="{_p var=$aCategory.name}">
                    <span class="group-item-viewall"
                          {if !empty($aCategory.image_path)}
                              style="background: url({img server_id=$aCategory.image_server_id path='core.path_actual' file=$aCategory.image_path suffix='_200' return_url=true})"
                          {else}
                              style="background-image: url('{img path='core.path_actual' file='PF.Site/Apps/core-groups/assets/img/default-category/default_category.png' return_url=true}')"
                          {/if}>
                        {if $aCategory.image_path}
                        <div class="overlay"></div>
                        {/if}
                        <span class="group-item-view-all">{_p var='view_all'}</span>
                        <span class="group-item-remain-number">+{$aCategory.remain_pages}</span>
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
<div class="extra_info">
    {_p var='no_groups_found' }
</div>
{/if}
{else}

{if count($aPages)}
{if $sView == 'my' && Phpfox::getUserBy('profile_page_id')}
<div class="message">
    {_p var='Note that Groups displayed here are groups created by the Group (!<< global_full_name >>!) and not by the parent user (!<< profile_full_name >>!).' global_full_name=$sGlobalUserFullName|clean profile_full_name=$aGlobalProfilePageLogin.full_name|clean}
</div>
{/if}
{if !PHPFOX_IS_AJAX }
<div class="item-container groups-items group-listing group-result">
{/if}
    {foreach from=$aPages item=aPage}
    <article class="group-item" data-url="{$aPage.link}" data-uid="groups_{$aPage.page_id}" id="groups_{$aPage.page_id}">
        <div class="item-outer">
            {if !empty($bShowModeration)}
            <div class="moderation_row">
                <label class="item-checkbox">
                    <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aPage.page_id}" id="check{$aPage.page_id}" />
                    <i class="ico ico-square-o"></i>
                </label>
            </div>
            {/if}
            <a class="group-photo" href="{$aPage.link}">
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
                    <a href="{$aPage.link}" class="link pages_title ">{$aPage.title|clean}</a>
                </div>
                <div class="item-info">
                    <span class="item-number-member">{if $aPage.total_like != 1}{_p var='groups_total_members' total=$aPage.total_like}{else}{_p var='groups_total_member' total=$aPage.total_like}{/if}</span>
                    {if $aPage.category_name}
                    <a href="{$aPage.category_link}" class="item-category">{_p var=$aPage.category_name}</a>
                    {else}
                    <a href="{$aPage.type_link}" class="item-category">{_p var=$aPage.type_name}</a>
                    {/if}
                </div>
                <div class="item-desc">
                    <div class="item-desc-main">{$aPage.text_parsed|striptag|stripbb}</div>
                </div>
                {if $aPage.bShowItemActions}
                <div class="item-option group-button-option">
                    <div class="dropdown">
                        <a class="btn dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            <i class="ico ico-gear-o"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right" id="js_page_entry_options_{$aPage.page_id}">
                            {template file='groups.block.link-listing'}
                        </ul>
                    </div>
                </div>
                {/if}
                <div class="item-group-icon-users">
                    <div class="item-icon">
                        {if !empty($aPage.is_liked)}
                        <a data-toggle="dropdown" class="btn btn-default btn-icon item-icon-joined">
                            <span class="ico ico-check"></span>
                            {_p var='joined'}
                            <span class="ml-1 ico ico-caret-down"></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>
                                <a role="button" onclick="$.ajaxCall('like.delete', 'type_id=groups&item_id={$aPage.page_id}&reload=1');">
                                    <span class="ico ico-close"></span>
                                    {_p var='unjoin'}
                                </a>
                            </li>
                        </ul>
                        {else}
                        <button class="btn btn-primary btn-gradient btn-icon item-icon-join" onclick="$(this).remove(); {if $aPage.reg_method == '1' && !isset($aPage.is_invited)} $.ajaxCall('groups.signup', 'page_id={$aPage.page_id}'); {else}$.ajaxCall('like.add', 'type_id=groups&amp;item_id={$aPage.page_id}&amp;reload=1');{/if} return false;">
                            <span class="ico ico-plus"></span>{_p var='join'}
                        </button>
                        {/if}
                    </div>
                    <!-- please show avatar user like page -->
                    {if $aPage.total_members > 0}
                    <div class="item-members">
                        {if ($aPage.total_members - 4) > 0}
                            {foreach from=$aPage.members item=aMember key=iKey}
                                {if $iKey < 3}
                                {img user=$aMember suffix='_50'}
                                {/if}
                            {/foreach}
                            <a href="{$aPage.link}members" class="item-members-viewall"><span>+{$aPage.remain_members}</span></a>
                        {else}
                            {foreach from=$aPage.members item=aMember}
                            {img user=$aMember suffix='_50'}
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

{if !empty($bShowModeration) }
{moderation}
{/if}

{else}
{if !PHPFOX_IS_AJAX }
<div class="extra_info">
    {_p var='no_groups_found' }
</div>
{/if}
{/if}
{/if}

{if !empty($bShowModeration)}
    {moderation}
{/if}