<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: profile.html.php 5077 2012-12-13 09:05:45Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $aUser.user_id != Phpfox::getUserId()}
<div class="pages_view_sub_menu">
	<ul>
		{if $aUser.user_id != Phpfox::getUserId()}<li><a href="#?call=report.add&amp;height=220&amp;width=400&amp;type=user&amp;id={$aUser.user_id}" class="inlinePopup" title="{_p var='report_this_user'}">{_p var='report_this_user'}</a></li>{/if}
		{if Phpfox::getUserParam('user.can_block_other_members') && isset($aUser.user_group_id) && Phpfox::getUserGroupParam('' . $aUser.user_group_id . '', 'user.can_be_blocked_by_others')}
		<li><a href="#?call=user.block&amp;height=120&amp;width=400&amp;user_id={$aUser.user_id}" class="inlinePopup js_block_this_user" title="{if $bIsBlocked}{_p var='unblock_this_user'}{else}{_p var='block_this_user'}{/if}">{if $bIsBlocked}{_p var='unblock_this_user'}{else}{_p var='block_this_user'}{/if}</a></li>
		{/if}
        {if isset($bShowRssFeedForUser)}
        <li><a href="{url link=''$aUser.user_name'.rss'}" class="no_ajax_link">{_p var='subscribe_via_rss'}</a></li>
        {/if}
	</ul>
</div>
{/if}