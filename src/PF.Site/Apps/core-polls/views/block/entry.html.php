<?php
    defined('PHPFOX') or exit('NO DICE!'); 
?>

{if isset($bIsViewingPoll)}
    <div class="item_info pr-3">
        {img user=$aPoll suffix='_50_square'}
        <div class="item_info_author">
            <div>{_p var='by'} {$aPoll|user:'':'':50}</div>
            <div>{$aPoll.time_stamp|convert_time}</div>
        </div>
    </div>

    <div class="item-comment mb-2">
        {if $aPoll.view_id == 0 && !isset($bIsCustomPoll)}
            <div>
               {module name='feed.mini-feed-action'}
           </div>
       
       <span class="item-total-view">
           <span class="vote" 
               {if isset($aPoll.answer) && count($aPoll.answer) && !isset($bDesign) && $aPoll.total_votes > 0 && ((Phpfox::getUserParam('poll.can_view_user_poll_results_own_poll') && $aPoll.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('poll.can_view_user_poll_results_other_poll')) && (Phpfox::getUserParam('privacy.can_view_all_items') || $aPoll.hide_vote != '1' || ($aPoll.hide_vote == '1' && Phpfox::getUserId() == $aPoll.user_id))}
                onclick="$Core.box('poll.pageVotes', 1000, 'poll_id={$aPoll.poll_id}'); return false;"
               {/if}><b>{$aPoll.total_votes|short_number}</b> {if $aPoll.total_votes == 1}{_p('poll_total_vote')}{else}{_p('poll_total_votes')}{/if}</span>
           <span><b>{$aPoll.total_view|short_number}</b> {if $aPoll.total_view == 1}{_p('view_lowercase')}{else}{_p('views_lowercase')}{/if}</span>
       </span>
       {/if}
    </div>
    {if !$aPoll.canVotesWithCloseTime}
        <div class="alert alert-info">
            {_p var='voting_for_this_poll_was_closed'}
        </div>
    {elseif $aPoll.close_time}
        <div class="alert alert-info">
            {_p var='voting_for_this_poll_available_until'}: {$aPoll.close_time|date:'feed.feed_display_time_stamp'}
        </div>
    {/if}
    {if isset($bIsCustomPoll)}
        <div class="poll-title mt-2 mb-1 fw-bold">
           {$aPoll.question|clean}
       </div>
    {/if}
	{if isset($bIsViewingPoll) && (isset($aPoll.view_id) && !isset($bDesign) && $aPoll.view_id == 1)}
        {template file='core.block.pending-item-action'}
	{/if}
    
    <div class="item-content mb-3 mt-1">
        {if !empty($aPoll.image_path)}
            <div class="item-media mr-2">
                <span style="background-image: url('{img server_id=$aPoll.server_id path='poll.url_image' file=$aPoll.image_path suffix='' return_url=true}');"></span>
            </div>
        {/if}
        <div class="item_view_content">
            {$aPoll.description|parse|shorten:500:'feed.view_more':true|split:55|max_line}
        </div>
    </div>

    {if $aPoll.total_attachment}
        {module name='attachment.list' sType=poll iItemId=$aPoll.poll_id}
    {/if}

	{if (isset($aPoll.hasPermission) && $aPoll.hasPermission) || (!isset($bIsCustomPoll) && (isset($aPoll.user_id) && $aPoll.user_id == Phpfox::getUserId() && Phpfox::getUserParam('poll.poll_can_delete_own_polls')))}
        <div class="item_bar">
            <div class="dropdown">
                <span role="button" data-toggle="dropdown" class="item_bar_action">
                    <i class="ico ico-gear-o"></i>
                </span>
                <ul class="dropdown-menu dropdown-menu-right">
                    {template file='poll.block.link'}
                </ul>
            </div>
        </div>
	{/if}

{/if}

{if isset($aPoll)}
<div id="poll_holder_{$aPoll.poll_id}"{if !isset($bIsViewingPoll)} class="{if isset($aPoll.view_id) && (!isset($bDesign) && $aPoll.view_id == 1 && (Phpfox::getUserParam('poll.poll_can_moderate_polls') || ($aPoll.user_id == Phpfox::getUserId())))} {/if}{if isset($bIsViewingPoll) || (isset($bDesign) && $bDesign)}row1 row_first{else}{if isset($phpfox.iteration.polls) && is_int($phpfox.iteration.polls/2)}row1{else}row2{/if}{if isset($phpfox.iteration.polls) && $phpfox.iteration.polls == 1} row_first{/if}{/if}"{/if}>
	
	<div class="vote_holder_{$aPoll.poll_id}">		
		
		{if !isset($bIsViewingPoll)}
            <div class="row_title">

                <div class="row_title_image">
                    {img user=$aPoll suffix='_50_square' max_width=50 max_height=50}
                    {if !isset($bDesign) && (Phpfox::getUserParam('poll.poll_can_moderate_polls') || $aPoll.bCanEdit
                    || (isset($aPoll.user_id) && $aPoll.user_id == Phpfox::getUserId() && Phpfox::getUserParam('poll.poll_can_delete_own_polls')))}
                    <div class="row_edit_bar_parent">
                        <div class="row_edit_bar">
                            <a role="button" class="row_edit_bar_action" data-toggle="dropdown">
                                <i class="fa fa-action"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right">
                                {template file='poll.block.link'}
                            </ul>
                        </div>
                    </div>
                    {/if}
                    {if !isset($bDesign) && Phpfox::getUserParam('poll.poll_can_moderate_polls')}
                    <a href="#{$aPoll.poll_id}" class="moderate_link" rel="poll">{_p var='moderate'}</a>
                    {/if}
                </div>

                <div class="row_title_info">
                    <header>
                        <h1><span id="poll_view_{$aPoll.poll_id}"><a href="{permalink module='poll' id=$aPoll.poll_id title=$aPoll.question}" id="poll_inner_title_{$aPoll.poll_id}" class="link">{$aPoll.question|clean|shorten:55:'...'|split:40}</a></span></h1>
                        <div class="row_header">
                            <ul>
                                <li>{$aPoll.time_stamp|convert_time}</li>
                                <li>{_p var='by'} {$aPoll|user}</li>
                            </ul>
                        </div>
                    </header>
		{/if}

            {if $aPoll.view_id == 0 || isset($bDesign)}
                <div class="item_content">
                    <div id="js_poll_results_{$aPoll.poll_id}">
                        {template file='poll.block.vote'}
                    </div>

                </div>
            {/if}

	{if !isset($bIsViewingPoll) && isset($aPoll.aFeed)}
	    {module name='feed.comment' aFeed=$aPoll.aFeed}
	{/if}	
	
        {if !isset($bIsViewingPoll)}
                </div>
                <div class="clear"></div>
            </div>
        {/if}
	</div>	
	
</div>	
{/if}