<?php

defined('PHPFOX') or exit('NO DICE!');

?>
{if !PHPFOX_IS_AJAX}
<div class="search-header-title-custom">
    <div class="item-title">{_p var='search_results'}</div>
    <div class="main_search_bar">
        <form class="form" method="get" action="{url link='search'}">
            <div class="input-group">
                <input type="text" name="q" placeholder="{_p var='Search'}" autocomplete="off" value="{if isset($sQuery)}{$sQuery|clean}{/if}" class="form-control main_search_bar_input">
                <div class="input-group-addon">
                    <button type="submit" class="btn" aria-hidden="true">
                        <span class="ico ico-search-o"></span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
{/if}

{if !empty($sQuery)}
<div class="search-keyword">
    <span class="item-title-keyword">{_p var='for_keyword'}:</span> <span class="item-keyword">{$sQuery}</span>
</div>
{else}
<div class="alert alert-danger">{_p var='provide_a_search_query'}</div>
{/if}

{if isset($aSearchResults) && count($aSearchResults)}
{if PHPFOX_IS_AJAX}
<div class="search_result_new"></div>
{/if}
<div class="search-listing" id="collection-search-results">
{foreach from=$aSearchResults item=aSearchResult}
    <article class="search-result" data-url="{$aSearchResult.item_link}">
        <div class="item-outer">
            <div class="item-inner">
                <div class="item-media-src" href="{$aSearchResult.item_link}">
                    {if isset($aSearchResult.profile_image)}
                        {img user=$aSearchResult.profile_image suffix='_50_square' max_width=50 max_height=50}
                    {else}
                        {img user=$aSearchResult suffix='_50_square' max_width=50 max_height=50}
                    {/if}
                </div>
                <div class="item-info">
                    <div class="item-title">
                        <a href="{$aSearchResult.item_link}" title="{$aSearchResult.item_title|clean}">{$aSearchResult.item_title|clean|shorten:'60':'...'}</a>
                    </div>
                    <div class="item-author dot-separate">
                        <span class="item-app">{$aSearchResult.item_name}</span>
                        <span>.</span>
                        <span>{$aSearchResult.item_time_stamp|convert_time}</span>
                    </div>
                </div>
            </div>
            {if isset($aSearchResult.item_display_photo)}
            <div class="item-image">
                <div class="item-media">
                    <a href="{$aSearchResult.item_link}">{$aSearchResult.item_display_photo}</a>    
                </div>
            </div>
            {/if}
            
        </div>
    </article>
{/foreach}
</div>
{if (count($aSearchResults) < $iTotalShow)}
    <div class="alert alert-info">
        {_p var='no_more_search_results_to_show'}
    </div>
{else}
<div id="feed_view_more">
    <a href="#" onclick="$(this).html($.ajaxProcess('{_p var='loading'}')); $.ajaxCall('search.viewMore', '{$sNextPage}', 'GET'); return false;" class="btn btn-primary btn-round btn-gradient global_view_more no_ajax_link">{_p var='view_more'}</a>
</div>
{/if}
{else}
{if PHPFOX_IS_AJAX}
{_p var='no_more_search_results_to_show'}
{else}
{_p var='no_search_results_found'}
{/if}
{/if}
{if !PHPFOX_IS_AJAX}
<div id="js_feed_content" class="js_feed_content"></div>
{/if}