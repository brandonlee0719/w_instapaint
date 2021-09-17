<?php
/**
 *
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		phpFox
 * @package  		Poll
 *
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if !count($aPolls)}
    {if !PHPFOX_IS_AJAX}
        <div class="extra_info">
            {_p var='no_polls_found'}
        </div>
    {/if}
{else}
	{if !PHPFOX_IS_AJAX}<div class="item-container poll-app main">{/if}
	{foreach from=$aPolls item=aPoll key=iKey name=polls}
		<article id="js_poll_id_{$aPoll.poll_id}">
			<div class="item-outer {if empty($aPoll.image_path)}no-photo{/if} {if ($aPoll.is_sponsor && $aPoll.is_featured) || ((isset($sView) && $sView == 'my' && (isset($aPoll.view_id) && $aPoll.view_id == 1)) && $aPoll.is_featured) || ($aPoll.is_sponsor && (isset($sView) && $sView == 'my' && (isset($aPoll.view_id) && $aPoll.view_id == 1)))}both-action{elseif $aPoll.is_sponsor || $aPoll.is_featured || (isset($sView) && $sView == 'my' && (isset($aPoll.view_id) && $aPoll.view_id == 1))}one-action{/if}">
                <div class="item-media mr-2">
                    {if !empty($aPoll.image_path)}
                        <a class="item-media-bg" href="{permalink module='poll' id=$aPoll.poll_id title=$aPoll.question}" itemprop="url"
                       style="background-image: url('{img server_id=$aPoll.server_id path='poll.url_image' file=$aPoll.image_path suffix='' return_url=true}')"></a>
                    {/if}
                    <div class="flag_style_parent">
                        {if isset($sView) && $sView == 'my' && (isset($aPoll.view_id) && $aPoll.view_id == 1)}
                        <div class="sticky-label-icon sticky-pending-icon" title="{_p var='pending'}">
                            <span class="flag-style-arrow"></span>
                            <i class="ico ico-clock-o"></i>
                        </div>
                        {/if}
                        {if $aPoll.is_sponsor}
                        <div class="sticky-label-icon sticky-sponsored-icon" title="{_p var='sponsored'}">
                            <span class="flag-style-arrow"></span>
                            <i class="ico ico-sponsor"></i>
                        </div>
                        {/if}
                        {if $aPoll.is_featured}
                        <div class="sticky-label-icon sticky-featured-icon" title="{_p var='featured'}">
                            <span class="flag-style-arrow"></span>
                            <i class="ico ico-diamond"></i>
                        </div>
                        {/if}
                        {if !$aPoll.canVotesWithCloseTime}
                        <div class="sticky-label-icon sticky-closed-icon" title="{_p var='closed'}">
                            <span class="flag-style-arrow"></span>
                            <i class="ico ico-warning"></i>
                        </div>
                        {/if}
                    </div>
                </div>
                <div class="item-inner">
                    <a href="{permalink module='poll' id=$aPoll.poll_id title=$aPoll.question}" class="item-title fw-bold" itemprop="url">{$aPoll.question|clean}</a>
                    <time><span>{_p var='by'}</span> {$aPoll|user:'':'':15} {_p var="on"} {$aPoll.time_stamp|convert_time:'core.global_update_time'}</time>
                    <div class="item-statistic">
                        <span>{$aPoll.total_votes|short_number} {if $aPoll.total_votes == 1}{_p('poll_total_vote')}{else}{_p('poll_total_votes')}{/if}</span>
                        <span>{$aPoll.total_view|short_number} {if $aPoll.total_view == 1}{_p('view_lowercase')}{else}{_p('views_lowercase')}{/if}</span>
                    </div>
                </div>

                {if $bShowModerator}
                    <div class="moderation_row">
                        <label class="item-checkbox">
                           <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aPoll.poll_id}" id="check{$aPoll.poll_id}" />
                           <i class="ico ico-square-o"></i>
                       </label>
                    </div>
                {/if}

                {if $aPoll.hasPermission}
                    <div class="item-option">
                        <div class="dropdown">
                            <span role="button" class="row_edit_bar_action" data-toggle="dropdown">
                                <i class="ico ico-gear-o"></i>
                            </span>
                            <ul class="dropdown-menu dropdown-menu-right">
                                {template file='poll.block.link'}
                            </ul>
                        </div>
                    </div>
                {/if}
                <div class="flag_style_parent hide">
                    {if isset($sView) && $sView == 'my' && (isset($aPoll.view_id) && $aPoll.view_id == 1)}
                    <div class="sticky-label-icon sticky-pending-icon" title="{_p var='pending'}">
                        <span class="flag-style-arrow"></span>
                        <i class="ico ico-clock-o"></i>
                    </div>
                    {/if}
                    {if $aPoll.is_sponsor}
                        <div class="sticky-label-icon sticky-sponsored-icon" title="{_p var='sponsored'}">
                            <span class="flag-style-arrow"></span>
                            <i class="ico ico-sponsor"></i>
                        </div>
                    {/if}
                    {if $aPoll.is_featured}
                        <div class="sticky-label-icon sticky-featured-icon" title="{_p var='featured'}">
                            <span class="flag-style-arrow"></span>
                            <i class="ico ico-diamond"></i>
                        </div>
                    {/if}
                    {if !$aPoll.canVotesWithCloseTime}
                        <div class="sticky-label-icon sticky-closed-icon" title="{_p var='closed'}">
                            <span class="flag-style-arrow"></span>
                            <i class="ico ico-warning"></i>
                        </div>
                    {/if}
                </div>
		    </div>
		</article>
	{/foreach}

	{pager}
	{if !PHPFOX_IS_AJAX && $bShowModerator}
	    {moderation}
	{/if}
	{if !PHPFOX_IS_AJAX}</div>{/if}
{/if}