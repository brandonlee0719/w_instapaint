<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<ul class="dropdown-menu dropdown-menu-right">
    {if $aFeed.type_id == "user_status" && ((Phpfox::getUserParam('feed.can_edit_own_user_status') && $aFeed.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('feed.can_edit_other_user_status'))}
    <li class=""><a href="#" class="" onclick="tb_show('{_p var='edit_your_post'}', $.ajaxBox('feed.editUserStatus', 'height=400&amp;width=600&amp;id={$aFeed.feed_id}')); return false;">
        <span class="ico ico-pencilline-o"></span> {_p var='edit'}</a>
    </li>
    {/if}
    {if ($aFeed.type_id == 'pages_comment' || $aFeed.type_id == 'groups_comment') && $aFeed.parent_user_id != 0 && ($aFeed.user_id == Phpfox::getUserId() || (defined('PHPFOX_PAGES_ITEM_TYPE') && Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->isAdmin($this->_aVars['aFeed']['parent_user_id'])))}
    <li class=""><a href="#" class="" onclick="tb_show('{_p var='edit_your_post'}', $.ajaxBox('feed.editUserStatus', 'height=400&amp;width=600&amp;id={$aFeed.feed_id}&amp;module=pages')); return false;">
        <span class="ico ico-pencilline-o"></span> {_p var='edit'}</a>
    </li>
    {/if}
    {if $aFeed.type_id == 'feed_comment' && $aFeed.user_id == Phpfox::getUserId()}
    <li class=""><a href="#" class="" onclick="tb_show('{_p var='edit_your_post'}', $.ajaxBox('feed.editUserStatus', 'height=400&amp;width=600&amp;id={$aFeed.feed_id}')); return false;">
        <span class="ico ico-pencilline-o"></span> {_p var='edit'}</a>
    </li>
    {/if}
    {if $aFeed.type_id == 'event_comment' && $aFeed.user_id == Phpfox::getUserId()}
    <li class=""><a href="#" class="" onclick="tb_show('{_p var='edit_your_post'}', $.ajaxBox('feed.editUserStatus', 'height=400&amp;width=600&amp;id={$aFeed.feed_id}&amp;module=event')); return false;">
        <span class="ico ico-pencilline-o"></span> {_p var='edit'}</a>
    </li>
    {/if}

    {if Phpfox::isModule('report') && $aFeed.type_id=='user_status' && $aFeed.user_id != Phpfox::getUserId()  && !User_Service_Block_Block::instance()->isBlocked(null, $aFeed.user_id)}
    <li class=""><a href="#?call=report.add&height=210&width=400&type=user_status&id={$aFeed.item_id}" class="inlinePopup" title="{_p var='report'}">
            <span class="ico ico-warning-o"></span> {_p var='report'}</a></li>
    {/if}

	{if !empty($feed_entry_be) && (defined('PHPFOX_FEED_CAN_DELETE') || (Phpfox::getUserParam('feed.can_delete_own_feed') && $aFeed.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('feed.can_delete_other_feeds') || (!defined('PHPFOX_IS_PAGES_VIEW') && isset($aFeed.parent_user_id) && (int)$aFeed.parent_user_id === Phpfox::getUserId() && Phpfox::getUserParam('comment.can_delete_comments_posted_on_own_profile')))}
	<li class="item-delete"><a href="#" class="" onclick="$Core.jsConfirm({l}{r}, function(){l}$.ajaxCall('feed.delete', 'height=400&amp;width=600&amp;TB_inline=1&amp;call=feed.delete&amp;type=delete&amp;id={$aFeed.feed_id}{if isset($aFeedCallback.module)}&amp;module={$aFeedCallback.module}&amp;item={$aFeedCallback.item_id}{/if}&amp;type_id={$aFeed.type_id}'){r}, function(){l}{r}); return false;">
			<span class="ico ico-trash-alt-o"></span> {_p var='delete'}</a></li>
	{/if}

	{assign var=empty value=true}
	
	{if Phpfox::isModule('report') && isset($sFeedType) && isset($aFeed.report_module)  && !User_Service_Block_Block::instance()->isBlocked(null, $aFeed.user_id)}
		{assign var=empty value=false}
		<li><a href="#?call=report.add&amp;height=100&amp;width=400&amp;type={$aFeed.report_module}&amp;id={$aFeed.item_id}" class="inlinePopup activity_feed_report" title="{$aFeed.report_phrase}">
				<span class="ico ico-warning-o"></span>
				{_p var='report'}</a>
		</li>
	{/if}

	{plugin call='feed.template_block_entry_2'}

	{plugin call='core.template_block_comment_border_new'}

</ul>
{if $empty}
<input type="hidden" class="comment_mini_link_like_empty" value="1" />
{/if}