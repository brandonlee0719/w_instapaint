<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Comment
 * @version 		$Id: entry.html.php 2525 2011-04-13 18:03:20Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div id="js_comment{$aRow.comment_id}">
	<div class="valid_message" style="display:none;">
		{_p var='your_comment_has_successfully_added'}
	</div>
	<div class="comment_header p_relative{if !isset($bIsCommentAdminPanel) && ($aRow.view_id == 9 || $aRow.view_id == '1')} row_moderate{/if}">
		{if !isset($bIsCommentAdminPanel)}
		<div style="position:absolute;">
		{/if}
		{if $aRow.link}
			{_p var='x_wrote' user=$aRow link=$aRow.link date=$aRow.time_stamp}
		{else}
			{_p var='user_wrote_date' user=$aRow date=$aRow.time_stamp}
		{/if}
		{if isset($bIsCommentAdminPanel) && $aRow.item_name}
		<div class="extra_info">
			{_p var='item'}: {$aRow.item_name}
		</div>
		{/if}
		{if !isset($bIsCommentAdminPanel)}
		</div>		
		<div class="t_right">	
			<div id="js_comment_rating{$aRow.comment_id}" style="font-size:8pt;">
                {if isset($aRow.has_rating)}
                {module name='comment.rating' sRating=$aRow.rating iCommentId=$aRow.comment_id bHasRating=$aRow.has_rating iLastVote=$aRow.actual_rating iUserId=$aRow.user_id}
                {else}
                {module name='comment.rating' sRating=$aRow.rating iCommentId=$aRow.comment_id iUserId=$aRow.user_id}
                {/if}
            </div>
		</div>
		{/if}
	</div>
	
	<div class="comment_box p_relative">
		<div class="comment_outer">
			<div class="comment_content">
				<div class="p_4" id="js_comment_text{$aRow.comment_id}">
				{$aRow.text|parse|split:60}
				</div>
				{if Phpfox::getUserParam('core.can_view_update_info') && !empty($aRow.update_user)}
				<div class="p_4" id="js_update_text_comment_{$aRow.comment_id}">
					<i>{_p var='last_update_on_x_by_x' date=$aRow.update_time full_name=$aRow.update_user}</i>
				</div>
				{/if}				
			</div>	
		</div>
	
		<div class="comment_link">
			<ul>
			{if ($aRow.view_id == 9 || $aRow.view_id == '1') && Phpfox::getUserParam('comment.can_moderate_comments')}
				<li><a href="#" onclick="$('#js_comment{$aRow.comment_id}').find('.comment_header').removeClass('row_moderate'); $('#js_comment{$aRow.comment_id}').find('.js_comment_moderate').remove(); $.ajaxCall('comment.moderateSpam', 'id={$aRow.comment_id}&amp;action=approve&amp;inacp={if isset($bIsCommentAdminPanel)}1{else}0{/if}'); return false;" class="btn btn-xs btn-primary">{_p var='approve'}</a></li>
				<li><a href="#" onclick="$('#js_comment{$aRow.comment_id}').find('.comment_header').removeClass('row_moderate'); $('#js_comment{$aRow.comment_id}').find('.js_comment_moderate').remove(); $.ajaxCall('comment.moderateSpam', 'id={$aRow.comment_id}&amp;action=deny&amp;inacp={if isset($bIsCommentAdminPanel)}1{else}0{/if}'); return false;" class="btn btn-xs btn-danger">{_p var='deny'}</a></li>
			{/if}
			{if !isset($bIsCommentAdminPanel)}
			{if Phpfox::isModule('report') && Phpfox::getUserParam('report.can_report_comments')}
				{if $aRow.user_id != Phpfox::getUserId()}<li><a href="#?call=report.add&amp;height=210&amp;width=400&amp;type=comment&amp;id={$aRow.comment_id}" class="inlinePopup" title="{_p var='report_a_comment'}">{_p var='report'}</a></li>{/if}
			{/if}
			{if (Phpfox::getUserParam('comment.edit_own_comment') && Phpfox::getUserId() == $aRow.user_id) || Phpfox::getUserParam('comment.edit_user_comment')}
				<li><a href="inline#?type=text&amp;id=js_comment_text{$aRow.comment_id}&amp;call=comment.updateText&amp;comment_id={$aRow.comment_id}&amp;data=comment.getText" class="quickEdit">{_p var='edit'}</a></li>
			{/if}
			{if Phpfox::getUserParam('comment.can_post_comments') && $bCanPostOnItem}
			{if Phpfox::getParam('comment.comment_is_threaded')}
				<li><a href="#" onclick="$('#js_comment_form_{$aRow.comment_id}').show(); $('#js_comment_form_form_{$aRow.comment_id}').html($('#js_comment_form').html()); $('#js_comment{$aRow.comment_id}').find('.js_reply_comment:first').val('{$aRow.comment_id}'); $('#js_comment_form').hide(); addCommentReply('{$aRow.comment_id}'); return false;">{_p var='reply'}</a></li>
			{else}			
				<li><a href="#" onclick="$.ajaxCall('comment.getQuote', 'id={$aRow.comment_id}'); return false;">{_p var='quote'}</a></li>
			{/if}
			{/if}
			{if ( (Phpfox::getUserParam('comment.delete_own_comment') && Phpfox::getUserId() == $aRow.user_id))	|| Phpfox::getUserParam('comment.delete_user_comment') || (defined('PHPFOX_IS_USER_PROFILE') && isset($aUser.user_id) && $aRow.user_id == Phpfox::getUserId() && Phpfox::getUserParam('comment.can_delete_comments_posted_on_own_profile'))}
				<li><a href="#" onclick="$Core.jsConfirm({left_curly}message:'{_p var='are_you_sure' phpfox_squote=true}'{right_curly}, function(){left_curly} $.ajaxCall('comment.InlineDelete', 'type_id={$aRow.type_id}&amp;comment_id={$aRow.comment_id}'); {right_curly},function(){left_curly}{right_curly}); return false;" title="{_p var='delete_comment'}">{_p var='delete'}</a></li>
			{/if}		
			{/if}	
			</ul>			
		</div>	
	</div>
	<div id="js_comment_form_{$aRow.comment_id}">
		<form method="post" action="{url link='current'}" id="js_comment_form_form_{$aRow.comment_id}">
		
		</form>
	</div>
	<div id="js_comment_parent{$aRow.comment_id}"></div>
	{if isset($bChild) && $bChild && $aRow.child_total > 0}
	<div id="js_comment_parent_view_{$aRow.comment_id}">
		<div style="text-align:right; margin-bottom:8px;">
			<a href="#" onclick="$(this).parent().html($.ajaxProcess()); $.ajaxCall('comment.getChildren', 'comment_id={$aRow.comment_id}&amp;type={$sType}'); return false;">
                {_p var='view_replies_total_to_this_comment' total=$aRow.child_total}
            </a>
		</div>
	</div>
	{/if}	
</div>