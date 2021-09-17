<?php 
defined('PHPFOX') or exit('NO DICE!'); 
?>

{item name='CreativeWork'}
	<meta itemprop="dateCreated" content="{$aThread.time_stamp|micro_time}" />
	<meta itemprop="interactionCount" content="Replies:{$aThread.total_post|short_number}" />
	<meta itemprop="interactionCount" content="Views:{$aThread.total_view|short_number}" />
	<div class="js_selector_class_{$aThread.thread_id} item-outer
	            {if $aThread.is_closed}
	                closed
                {/if}
                {if $aThread.order_id == 1}
                    sticky
                {elseif $aThread.order_id == 2 && !defined('PHPFOX_IS_GROUP_VIEW')}
                    sponsored
                {/if}
    ">
        <div class="section-left">
            <div class="item-media">
                {img user=$aThread suffix='_50_square'}
            </div>
            <div class="section-left-inner">
                <div class="item-title forum-text-overflow">
                    <a href="{if isset($aThread.sponsor_id)}{url link='ad.sponsor' view=$aThread.sponsor_id}{else}{permalink module='forum.thread' id=$aThread.thread_id title=$aThread.title}{/if}" itemprop="url">
                        {if $aThread.is_closed}
                            <b class="forum-app-highlight closed">[{_p var='closed'}]</b>
                        {/if}
                        {if $aThread.order_id == 1}
                            <b class="forum-app-highlight sticky">[{_p var='sticky'}]</b>
                        {elseif $aThread.order_id == 2 && !defined('PHPFOX_IS_GROUP_VIEW')}
                            <b class="forum-app-highlight sponsored">[{_p var='sponsored'}]</b>
                        {/if}

                        {if isset($sView) && $sView == 'my-thread' && $aThread.view_id == 1}
                        <span class="pending-label">{_p('pending_label')}</span>
                        {/if}
                        {$aThread.title|clean|split:40|shorten:100:'...'}
                    </a>
                </div>
                
                <div class="item-author-post">
                    <span>{_p var="by"} </span>
                    {if isset($aThread.cache_name) && $aThread.cache_name}
                        <span class="user_profile_link_span"><a href="#">{$aThread.cache_name|clean}</a></span>
                    {else}
                        {$aThread|user}
                    {/if}
                    <span> {_p var='on'} </span>
                    <time class="item-time">{$aThread.time_stamp|convert_time:'core.global_update_time'}</time>
                </div>
            </div>
        </div>
        <div class="section-right">
            <div class="item-author-rep">
                <div>{_p var='last_replied'}</div>
                {if isset($aThread.last_post)}
                    {$aThread.last_post|user}
                {/if}
                <div>{$aThread.last_post.time_stamp|date:'core.global_update_time'}</div>
            </div>
            <ul class="item-statistic">
                <li class="text-center">
                    <div class="number">{$aThread.total_post|short_number}</div>
                    <div class="text">{if $aThread.total_post == 1}{_p var='reply'}{else}{_p var='replies'}{/if}</div>
                </li>
                <li class="text-center">
                    <div class="number">{$aThread.total_view|short_number}</div>
                    <div class="text">{if $aThread.total_view == 1}{_p var='view'}{else}{_p var='views'}{/if}</div>
                </li>
            </ul>
            {if $aThread.hasPermission}
                <div class="item-option">
                    <div class="dropdown">
                        <span role="button" class="row_edit_bar_action" data-toggle="dropdown">
                            <i class="ico ico-gear-o"></i>
                        </span>
                        <ul class="dropdown-menu dropdown-menu-right">
                            {template file='forum.block.menu'}
                        </ul>
                    </div>
                </div>
            {/if}
        </div>
        {if (!isset($bResult) || !$bResult) && $bShowModerator}
            <div class="moderation_row">
                <label class="item-checkbox">
                   <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aThread.thread_id}" id="check{$aThread.thread_id}" />
                   <i class="ico ico-square-o"></i>
               </label>
            </div>
        {/if}
	</div>
{/item}