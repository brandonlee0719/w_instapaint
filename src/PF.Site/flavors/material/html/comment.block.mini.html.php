<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
	<div id="js_comment_{$aComment.comment_id}" class="js_mini_feed_comment comment_mini js_mini_comment_item_{$aComment.item_id} {if isset($aComment.children) && count($aComment.children.comments)}has-replies{/if}">
        {if ((Phpfox::getUserParam('comment.delete_own_comment') && Phpfox::getUserId() == $aComment.user_id)
                || Phpfox::getUserParam('comment.delete_user_comment')
                || (defined('PHPFOX_IS_USER_PROFILE') && isset($aUser.user_id) && $aUser.user_id == Phpfox::getUserId() && Phpfox::getUserParam('comment.can_delete_comments_posted_on_own_profile'))
                || (defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE') && Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->isAdmin('' . $aPage.page_id . '')))
            || (Phpfox::getUserParam('comment.can_delete_comment_on_own_item')
                && isset($aFeed)
                && isset($aFeed.feed_link)
                && $aFeed.user_id == Phpfox::getUserId())
            || ((Phpfox::getUserParam('comment.edit_own_comment')&& Phpfox::getUserId() == $aComment.user_id)
                || Phpfox::getUserParam('comment.edit_user_comment'))
        }
        <div class="item-options-holder comment-options-holder">
            <a role="button" data-toggle="dropdown" href="#" class="item-options">
				<span class="ico ico-angle-down"></span>
			</a>
            <ul class="dropdown-menu dropdown-menu-right">
                {if (Phpfox::getUserParam('comment.edit_own_comment') && Phpfox::getUserId() == $aComment.user_id) || Phpfox::getUserParam('comment.edit_user_comment')}
                <li>
                    <a href="inline#?type=text&amp;&amp;simple=true&amp;id=js_comment_text_{$aComment.comment_id}&amp;call=comment.updateText&amp;comment_id={$aComment.comment_id}&amp;data=comment.getText" class="quickEdit">
						<span class="ico ico-pencilline-o mr-1"></span> {_p var='edit'}
					</a>
                </li>
                {/if}
				{if Phpfox::isModule('report') && Phpfox::getUserParam('report.can_report_comments')}
					{if $aComment.user_id != Phpfox::getUserId() && !User_Service_Block_Block::instance()->isBlocked(null, $aComment.user_id)}
						<li>
							<a href="#?call=report.add&amp;height=210&amp;width=400&amp;type=comment&amp;id={$aComment.comment_id}" class="inlinePopup" title="{_p var='report_a_comment'}">
								<span class="ico ico-warning-o mr-1"></span>
								{_p var='report'}
							</a>
						</li>
					{/if}
				{/if}	
                {if (Phpfox::getUserParam('comment.delete_own_comment') && Phpfox::getUserId() == $aComment.user_id) || Phpfox::getUserParam('comment.delete_user_comment') || (defined('PHPFOX_IS_USER_PROFILE') && isset($aUser.user_id) && $aUser.user_id == Phpfox::getUserId() && Phpfox::getUserParam('comment.can_delete_comments_posted_on_own_profile'))
                || (defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE') && Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->isAdmin('' . $aPage.page_id . ''))
                }
                <li class="item-delete">
                    <a href="#" onclick="$Core.jsConfirm({left_curly}message:'{_p var='are_you_sure' phpfox_squote=true}'{right_curly}, function(){left_curly}$.ajaxCall('comment.InlineDelete', 'type_id={$aComment.type_id}&amp;comment_id={$aComment.comment_id}{if defined('PHPFOX_IS_THEATER_MODE')}&photo_theater=1{/if}{if !$aComment.parent_id}&item_id={$aComment.item_id}{/if}', 'GET');{right_curly},function(){left_curly}{right_curly}); return false;">
                        <span class="ico ico-trash-alt-o  mr-1"></span> {_p var='delete'}
                    </a>
                </li>
                {elseif Phpfox::getUserParam('comment.can_delete_comment_on_own_item')&& isset($aFeed) && isset($aFeed.feed_link) && $aFeed.user_id == Phpfox::getUserId()}
                <li>
                    <a href="{$aFeed.feed_link}ownerdeletecmt_{$aComment.comment_id}/" class="sJsConfirm">
                        <span class="ico ico-trash-alt-o  mr-1"></span> {_p var='delete'}
                    </a>
                </li>
                {/if}
            </ul>
        </div>
        {/if}

		<div class="comment_mini_image">
            {img user=$aComment suffix='_50_square' max_width=40 max_height=40}
		</div>
		<div class="comment_mini_content">
			{$aComment|user:'':'':30}<div id="js_comment_text_{$aComment.comment_id}" class="comment_mini_text {if $aComment.view_id == '1'}row_moderate{/if}">{$aComment.text|feed_strip|shorten:'300':'comment.view_more':true|split:30|max_line}</div>
			<div class="comment_mini_action">
				<ul>
					{module name='like.link' like_type_id='feed_mini' like_owner_id=$aComment.user_id like_item_id=$aComment.comment_id like_is_liked=$aComment.is_liked like_is_custom=true}
					
					
					<span class="total-like js_like_link_holder" {if $aComment.total_like == 0}style="display:none"{/if}>
						<span onclick="return $Core.box('like.browse', 450, 'type_id=feed_mini&amp;item_id={$aComment.comment_id}');">
							<span class="js_like_link_holder_info">
								{$aComment.total_like}
							</span>
						</span>
					</span>
					

					{if Phpfox::getParam('comment.comment_is_threaded') && Phpfox::getUserParam('feed.can_post_comment_on_feed')}
						{if (isset($aComment.iteration) && $aComment.iteration >= Phpfox::getParam('comment.total_child_comments')) || (isset($bForceNoReply) && $bForceNoReply) && !User_Service_Block_Block::instance()->isBlocked(null, $aComment.user_id)}
						
						{else}
							<li><a href="#" class="js_comment_feed_new_reply" rel="{$aComment.comment_id}">{_p var='reply'}</a></li>
						{/if}
					{/if}					


					{if Phpfox::getUserParam('comment.can_moderate_comments') && $aComment.view_id == '1'}
						<li>
							<a href="#" onclick="$('#js_comment_text_{$aComment.comment_id}').removeClass('row_moderate'); $(this).remove(); $.ajaxCall('comment.moderateSpam', 'id={$aComment.comment_id}&amp;action=approve&amp;inacp=0'); return false;">{_p var='approve'}</a>
						</li>
					{/if}
					<li class="comment_mini_entry_time_stamp">{if isset($aComment.unix_time_stamp)}{$aComment.unix_time_stamp|convert_time:'comment.comment_time_stamp'}{else}{$aComment.time_stamp|convert_time:'comment.comment_time_stamp'}{/if}</li>
				</ul>
			</div>
		</div>		
		<div id="js_comment_form_holder_{$aComment.comment_id}" class="js_comment_form_holder"></div>

		<div id="js_comment_mini_child_holder_{$aComment.comment_id}" class="comment_mini_child_holder{if isset($aComment.children) && $aComment.children.total > 0} comment_mini_child_holder_padding{/if}">
			{if isset($aComment.children) && $aComment.children.total > 0}
				<div class="comment_mini_child_view_holder" id="comment_mini_child_view_holder_{$aComment.comment_id}">
					<a href="#" onclick="$.ajaxCall('comment.viewAllComments', 'comment_type_id={$aComment.type_id}&amp;item_id={$aComment.item_id}&amp;comment_id={$aComment.comment_id}', 'GET'); return false;">
						+{$aComment.children.total|number_format} {_p var='replies'}
						<span class="ico ico-angle-down"></span>
						</a>
				</div>
			{/if}
			<div id="js_comment_children_holder_{$aComment.comment_id}" class="comment_mini_child_content">				
				{if isset($aComment.children) && count($aComment.children.comments)}
					{foreach from=$aComment.children.comments item=aCommentChilded}
						{module name='comment.mini' comment_custom=$aCommentChilded}
					{/foreach}
				{else}
					<div id="js_feed_like_holder_{$aComment.comment_id}"> </div>
				{/if}			
				
			</div>
		</div>		
	</div>
{if isset($bForceNoReply) && !$bForceNoReply && !empty($bIsAjaxAdd)}
<script>
    $Core.updateCommentCounter('{$aComment.type_id}',{$aComment.item_id}, '+');
</script>
{/if}