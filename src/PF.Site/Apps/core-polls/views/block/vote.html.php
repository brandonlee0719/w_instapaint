<?php
    defined('PHPFOX') or exit('NO DICE!');
?>

{if !isset($aPoll.poll_is_in_feed) && (((isset($aPoll.voted)) || (!Phpfox::getUserParam('poll.can_vote_in_own_poll') && ($aPoll.user_id == Phpfox::getUserId())) || Phpfox::getUserParam('poll.view_poll_results_before_vote') || (isset($bDesign) && $bDesign)))}
<div class="votes poll_answer poll-app" id="vote_list_{$aPoll.poll_id}">
	{foreach from=$aPoll.answer item=aAnswer}
		<div id="js_answer_{$aAnswer.answer_id}" class="answers_container{if Phpfox::getUserParam('poll.highlight_answer_voted_by_viewer') && isset($aPoll.voted) && isset($aAnswer.voted) && $aAnswer.voted > 0} user_answered_this{/if}">
			<div class="poll_anwser_user">{$aAnswer.answer}</div>
			{if ((isset($aPoll.user_voted_this_poll) &&
				(($aPoll.user_voted_this_poll == false && Phpfox::getUserParam('poll.view_poll_results_before_vote')) ||
				($aPoll.user_voted_this_poll == true && Phpfox::getUserParam('poll.view_poll_results_after_vote')))) ||
				(!isset($aPoll.user_voted_this_poll)))
				|| ((isset($bDesign) && $bDesign))
			}
			{if !isset($bDesign)}
				<div class="extra_info percent mobile">
					{if isset($aAnswer.vote_percentage)}
						{$aAnswer.vote_percentage}%
					{else}
						{_p var='votes_0'}
					{/if}
				</div>
			{/if}
			<div class="poll_answer_container_temp mobile">
				<div class="poll_answer_container js_sample_outer" style="{if isset($aPoll.border) && ($aPoll.border != '')}border:1px solid #{$aPoll.border};{/if} {if isset($aPoll.background) && ($aPoll.background != '')}background:#{$aPoll.background};{/if}">
					<div class="poll_answer_percentage js_sample_percent percentage" style="{if isset($aPoll.percentage) && ($aPoll.percentage != '')}background-color:#{$aPoll.percentage};{/if}{if isset($bDesign) && $bDesign}width:40%;{else}{if isset($aAnswer.vote_percentage)}width:{$aAnswer.vote_percentage}{elseif !Phpfox::getUserParam('poll.can_vote_in_own_poll')}width:0{/if}%;{/if}">&nbsp;</div>
				</div>
			</div>
			{/if}

			{if !isset($bDesign)}
				<div class="extra_info text mobile">
					{if isset($aAnswer.vote_percentage)}
                        {if !isset($bDesign) && $aPoll.total_votes > 0 && isset($aPoll.canViewResult) && $aPoll.canViewResult}
                            {if isset($aPoll.canViewResultVote) && $aPoll.canViewResultVote}
                                {if count($aAnswer.some_votes) && (Phpfox::getUserParam('privacy.can_view_all_items') || ($aPoll.hide_vote == '1' && Phpfox::getUserId() == $aPoll.user_id) || $aPoll.hide_vote != '1')}
                                    <span onclick="tb_show('{_p var='poll_results'}',$.ajaxBox('poll.showAnswerVote','answer_id={$aAnswer.answer_id}')); return false;">
                                        {$aAnswer.total_votes|short_number} {if $aAnswer.total_votes == 1}{_p('poll_total_vote')}{else}{_p('poll_total_votes')}{/if}
                                    </span>
                                {/if}
                            {/if}
                        {/if}
					{/if}
				</div>
			{/if}
            <div class="vote-member mobile">
                {if !isset($bDesign) && $aPoll.total_votes > 0 && $aPoll.canViewResult}
	                {if $aPoll.canViewResultVote}
	                    {if count($aAnswer.some_votes) && (Phpfox::getUserParam('privacy.can_view_all_items') || ($aPoll.hide_vote == '1' && Phpfox::getUserId() == $aPoll.user_id) || $aPoll.hide_vote != '1')}
                            {foreach from=$aAnswer.some_votes item=aUserVote}
                                <div class="vote-member-inner">{img user=$aUserVote suffix='_50_square'}</div>
                            {/foreach}
                            <div class="vote-member-inner">
                                <span onclick="tb_show('{_p var='poll_results'}',$.ajaxBox('poll.showAnswerVote','answer_id={$aAnswer.answer_id}')); return false;">
                                    <i class="ico ico-dottedmore-o"></i>
                                </span>
                            </div>
                        {/if}
                    {/if}
                {/if}
            </div>
            <div class="hide">
                <div>
                    {if ((isset($aPoll.user_voted_this_poll) &&
                    (($aPoll.user_voted_this_poll == false && Phpfox::getUserParam('poll.view_poll_results_before_vote')) ||
                    ($aPoll.user_voted_this_poll == true && Phpfox::getUserParam('poll.view_poll_results_after_vote')))) ||
                    (!isset($aPoll.user_voted_this_poll)))
                    || ((isset($bDesign) && $bDesign))
                    }
                        {if !isset($bDesign)}
                            <div class="extra_info percent">
                                {if isset($aAnswer.vote_percentage)}
                                    {$aAnswer.vote_percentage}%
                                {else}
                                    {_p var='votes_0'}
                                {/if}
                            </div>
                        {/if}
                        <div class="poll_answer_container_temp">
                            <div class="poll_answer_container js_sample_outer" style="{if isset($aPoll.border) && ($aPoll.border != '')}border:1px solid #{$aPoll.border};{/if} {if isset($aPoll.background) && ($aPoll.background != '')}background:#{$aPoll.background};{/if}">
                                <div class="poll_answer_percentage js_sample_percent percentage" style="{if isset($aPoll.percentage) && ($aPoll.percentage != '')}background-color:#{$aPoll.percentage};{/if}{if isset($bDesign) && $bDesign}width:40%;{else}{if isset($aAnswer.vote_percentage)}width:{$aAnswer.vote_percentage}{elseif !Phpfox::getUserParam('poll.can_vote_in_own_poll')}width:0{/if}%;{/if}">&nbsp;</div>
                            </div>
                        </div>
                    {/if}
                </div>
                <div>
                    {if !isset($bDesign)}
                        <div class="extra_info text">
                            {if isset($aAnswer.vote_percentage)}
                                {if !isset($bDesign) && $aPoll.total_votes > 0 && isset($aPoll.canViewResult) && $aPoll.canViewResult}
                                    {if isset($aPoll.canViewResultVote) && $aPoll.canViewResultVote}
                                        {if count($aAnswer.some_votes) && (Phpfox::getUserParam('privacy.can_view_all_items') || ($aPoll.hide_vote == '1' && Phpfox::getUserId() == $aPoll.user_id) || $aPoll.hide_vote != '1')}
                                            <span onclick="tb_show('{_p var='poll_results'}',$.ajaxBox('poll.showAnswerVote','answer_id={$aAnswer.answer_id}')); return false;">
                                                {_p var='total_votes_votes' total_votes=$aAnswer.total_votes|short_number}
                                            </span>
                                        {/if}
                                    {/if}
                                {/if}
                            {/if}
                        </div>
                    {/if}
                    <div class="vote-member">
                        {if !isset($bDesign) && $aPoll.total_votes > 0 && $aPoll.canViewResult}
                            {if $aPoll.canViewResultVote}
                                {if count($aAnswer.some_votes) && (Phpfox::getUserParam('privacy.can_view_all_items') || ($aPoll.hide_vote == '1' && Phpfox::getUserId() == $aPoll.user_id) || $aPoll.hide_vote != '1')}
                                    {foreach from=$aAnswer.some_votes item=aUserVote}
                                        <div class="vote-member-inner">{img user=$aUserVote suffix='_50_square'}</div>
                                    {/foreach}
                                    <div class="vote-member-inner">
                                        <span onclick="tb_show('{_p var='poll_results'}',$.ajaxBox('poll.showAnswerVote','answer_id={$aAnswer.answer_id}')); return false;">
                                            <i class="ico ico-dottedmore-o"></i>
                                        </span>
                                    </div>
                                {/if}
                            {/if}
                        {/if}
                    </div>
                </div>
            </div>
		</div>
	{foreachelse}
			{_p var='no_answers_to_show'}
	{/foreach}
</div>
	{if isset($aPoll.answer) && count($aPoll.answer) && !isset($bDesign) && $aPoll.total_votes > 0 && ((Phpfox::getUserParam('poll.can_view_user_poll_results_own_poll') && $aPoll.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('poll.can_view_user_poll_results_other_poll')) && (Phpfox::getUserParam('privacy.can_view_all_items') || $aPoll.hide_vote != '1' || ($aPoll.hide_vote == '1' && Phpfox::getUserId() == $aPoll.user_id))}
		<div class="mt-2 poll_answer_button">
			<button class="btn btn-default" onclick="$Core.box('poll.pageVotes', 1000, 'poll_id={$aPoll.poll_id}'); return false;">{_p var='view_results'}<b>{$aPoll.total_votes|short_number}&nbsp;{if $aPoll.total_votes == 1}{_p('poll_total_vote')}{else}{_p('poll_total_votes')}{/if}</b></button>
			{if isset($aPoll.voted) && Phpfox::getUserParam('poll.poll_can_change_own_vote') && $aPoll.canVotesWithCloseTime}
				<button class="btn btn-primary" onclick="$Core.poll.showFormForEditAgain({$aPoll.answer_id}, {$aPoll.poll_id}); return false;">{_p var='vote_again'}</button>
			{/if}
		</div>
	{/if}
{/if}

{if (!isset($aPoll.voted) || (isset($aPoll.voted) && Phpfox::getUserParam('poll.poll_can_change_own_vote'))) && (!(!Phpfox::getUserParam('poll.can_vote_in_own_poll') && ($aPoll.user_id == Phpfox::getUserId()))) && $aPoll.canVotesWithCloseTime}
<div id="vote_{$aPoll.poll_id}" class="clearfix"  {if isset($aPoll.voted) && Phpfox::getUserParam('poll.poll_can_change_own_vote')}style="display:none;"{/if}>
	<form class="poll_form" method="post" action="{url link='current'}" id="js_poll_form_{$aPoll.poll_id}">
		<div><input type="hidden" name="val[poll_id]" value="{$aPoll.poll_id}" /></div>
		{if isset($aPoll.voted)}
		<div><input type="hidden" name="val[vote_again]" value="1" /></div>
		{/if}
		<div class="poll_question">
			{if isset($aPoll.answer)}
			{foreach from=$aPoll.answer item=answer}
                {if !empty($answer.answer)}
                    {if $aPoll.is_multiple}
                        <div class="radio">
                            <label class="checkbox">
                                {if !isset($aPoll.poll_is_in_feed)}
                                <input class="checkbox js_poll_answer{if isset($iKey)}_{$iKey}{/if}" {if isset($aPoll.voted) && isset($aPoll.answer_id) && ($aPoll.answer_id == $answer.answer_id)}checked{/if} type="checkbox" name="val[answer][]" value="{$answer.answer_id}"/>{/if}
                                <i class="ico ico-square-o"></i>
                                <span title="{$answer.answer|clean}">{$answer.answer|clean|split:50|shorten:150:'...'}</span>
                            </label>
                        </div>
                    {else}
                        <div class="radio">
                            <label {if !isset($aPoll.poll_is_in_feed)}onclick="$('#js_submit_vote{if isset($iKey)}_{$iKey}{/if}').show(); $('.js_poll_answer{if isset($iKey)}_{$iKey}{/if}').prop('checked', false); $(this).find('.js_poll_answer{if isset($iKey)}_{$iKey}{/if}').prop('checked', true);"{/if}>{if !isset($aPoll.poll_is_in_feed)}
	                            <input class="checkbox js_poll_answer{if isset($iKey)}_{$iKey}{/if}" {if isset($aPoll.voted) && isset($aPoll.answer_id) && ($aPoll.answer_id == $answer.answer_id)}checked{/if} type="radio" name="val[answer]" value="{$answer.answer_id}" style="vertical-align:middle;" />{/if}
	                            <i class="ico ico-circle-o"></i>
	                            <span title="{$answer.answer|clean}">{$answer.answer|clean|split:50|shorten:150:'...'}</span>
                            </label>
                        </div>
                    {/if}
                {/if}
			{/foreach}
			{/if}
		</div>
		<div class="mt-2">
			<button class="button btn-primary" type="button" class="button_link" onclick="$Core.poll.submitPoll({if !Phpfox::getUserParam('poll.poll_can_change_own_vote')}true{else}false{/if}, {$aPoll.poll_id});">{_p var='submit_your_vote'}</button>
			{if isset($aPoll.voted)}
				<a href="javascript:void()" class="ml-1" onclick="$Core.poll.hideFormForEditAgain({$aPoll.poll_id}); return false;">{_p var='cancel'}</a>
			{/if}
		</div>
		<div class="js_poll_image_ajax" style="display:none;">
			{img theme='ajax/add.gif' class='v_middle'}
		</div>
	</form>
</div>
{/if}