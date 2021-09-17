<?php 
    defined('PHPFOX') or exit('NO DICE!');
?>
<div class="user_tooltip_cover" style="background-image:url('{$aUser.cover_photo_link}')"></div>
    <div class="user_tooltip_inner">
    <div class="user_tooltip_image">
        {img user=$aUser suffix='_50_square' max_width=50 max_height=50}
    </div>
    <div class="user_tooltip_info">
        {plugin call='user.template_block_tooltip_1'}

        <div class="user_tooltip_info_up">
            <a href="{url link=$aUser.user_name}" class="user_tooltip_info_user">
                {if Phpfox::getParam('user.display_user_online_status')}
                <div class="user_tooltip_status">
                    {if $aUser.is_online}
                    <span class="user_is_online" title="{_p var='online'}"><i class="fa fa-circle js_hover_title"></i></span>
                    {else}
                    <span class="user_is_offline" title="{_p var='offline'}"><i class="fa fa-circle js_hover_title"></i></span>
                    {/if}
                </div>
                {/if}
                {$aUser.full_name|clean}
            </a>

            {plugin call='user.template_block_tooltip_3'}

            {if $bIsPage}
                <ul>
                    <li>{$aUser.page.category_name|convert}</li>
                    <li>
                        {if $aUser.page.page_type == '1'}
                            {if $aUser.page.total_like == 1}
                                {_p var='1_member'}
                            {elseif $aUser.page.total_like > 1}
                                {_p var='total_members' total=$aUser.page.total_like|number_format}{/if}
                        {else}
                            {if $aUser.page.total_like == 1}
                                {_p var='1_person_likes_this'}
                            {elseif $aUser.page.total_like > 1}
                                {_p var='total_people_like_this' total=$aUser.page.total_like|number_format}
                            {/if}
                        {/if}
                    </li>
                </ul>
            {else}
                {if $aUser.total_friend > 0}
                <div class="top-info total-friends">
                    {if $aUser.total_friend == 1}
                        {_p var='total_friend' total=$aUser.total_friend}
                    {else}
                        {_p var='total_friends' total=$aUser.total_friend}
                    {/if}
                </div>
            {/if}
        </div>
            <ul class="bottom-info">
                {if $aUser.location}
                <li><span>{_p var='lives_in'}</span> {$aUser.location}</li>
                {/if}

                {if $aUser.gender_name}
                <li>{$aUser.gender_name}</li>
                {/if}

                {if !empty($aUser.birthdate_display) }
                <li>
                    <span>{_p var='birthday'}:</span>
                    {foreach from=$aUser.birthdate_display key=sAgeType item=sBirthDisplay}
                        {if $aUser.dob_setting == '2'}
                            {_p var='age_years_old' age=$sBirthDisplay}
                        {else}
                            {if $aUser.dob_setting != '3'}
                                {$sBirthDisplay}
                            {/if}
                        {/if}
                    {/foreach}
                </li>
                {/if}

                {if !empty($aUser.relationship)}
                <li><span>{_p var='relationship'}:</span> {$aUser.relationship}</li>
                {/if}

                {if !empty($aUser.joined)}
                <li><span>{_p var='registered_at'}</span> {$aUser.joined|convert_time}</li>
                {/if}

                {if !empty($iInfoCount)}
                <li class="no-info">
                    {_p var='no_information_found'}
                </li>
                {/if}
            </ul>

            {plugin call='user.template_block_tooltip_5'}
        {/if}

        {plugin call='user.template_block_tooltip_2'}

    </div>
</div>
{if $aUser.user_id != Phpfox::getUserId() && !$bIsPage}

{if $iMutualTotal > 0}
<ul class="user_tooltip_mutual mutual-friends-list">
    {if $iMutualTotal == 1}
    {_p var='1_mutual_friend'}:
    {else}
    {_p var='total_mutual_friends' total=$iMutualTotal}:
    {/if}
    {foreach from=$aMutualFriends key=iKey item=aMutualFriend}
    <li id="js_user_name_link_{$aMutualFriend.user_name}">
        <a href="{url link=$aMutualFriend.user_name}">{$aMutualFriend.full_name}</a>
    </li>
    {/foreach}
    {if $iRemainFriends > 0}
    <span>{_p var='and'}</span>
    <a href="#" class="user_viewmore" onclick="$Core.box('friend.getMutualFriends', 450, 'user_id={$aUser.user_id}');return false;">{if $mutual_remain == 1}{_p var='1_other'}{else}{_p var='total_others' total=$iRemainFriends}{/if}</a>
    {/if}
</ul>
{/if}
<div class="user_tooltip_action_user">
    {if $aUser.is_friend && $aUser.is_friend === 2}
    <div class="friend_request_sent">
        <span class="ico ico-arrow-right mr-1"></span>{_p var='friend_request_sent'}
    </div>
    {/if}
	<ul>
        {if Phpfox::isUser() && Phpfox::isModule('friend') && Phpfox::getUserParam('friend.can_add_friends') && empty($aUser.is_friend)}
		<li><a class="btn btn-primary btn-sm btn-icon" href="#" onclick="$(this).closest('.js_user_tool_tip_holder').hide();return $Core.addAsFriend('{$aUser.user_id}');" title="{_p var='add_to_friends'}"><span class="ico ico-user1-plus-o"></span>{_p var='add_friends'}</a></li>
		{/if}
		{if $bShowBDay}
        <li><a class="btn btn-default btn-sm btn-icon" href="{url link=$aUser.user_name}"><span class="ico ico-birthday-cake-alt mr-1"></span>{_p var='birthday_wishes'}</a></li>
        {if Phpfox::isModule('mail') && User_Service_Privacy_Privacy::instance()->hasAccess('' . $aUser.user_id . '', 'mail.send_message')}
        <li class="item-tooltip-viewmore-button dropup">
            <a href="" data-toggle="dropdown" class=""><span class="ico ico-dottedmore-o"></span></a>
            <ul class="dropdown-menu dropdown-menu-right">
                <li><a  href="#" onclick="$Core.composeMessage({left_curly}user_id: {$aUser.user_id}{right_curly});$(this).closest('.js_user_tool_tip_holder').hide(); return false;"><span class="ico ico-comment-o"></span>{_p var='send_message'}</a></li>
            </ul>
        </li>
        {/if}
		{else}
        {if Phpfox::isModule('mail') && User_Service_Privacy_Privacy::instance()->hasAccess('' . $aUser.user_id . '', 'mail.send_message')}
        <li><a class="btn btn-default btn-sm" href="#" onclick="$Core.composeMessage({left_curly}user_id: {$aUser.user_id}{right_curly});$(this).closest('.js_user_tool_tip_holder').hide(); return false;">{_p var='send_message'}</a></li>
        {/if}
        {/if}
	</ul>
</div>
{/if}
