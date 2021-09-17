<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{if empty($no_button) && Phpfox::isUser() && Phpfox::isModule('friend') && Phpfox::getUserParam('friend.can_add_friends')}
    {if !$is_friend}
    <div class="btn-addfriend">
        <a class="btn btn-primary btn-gradient s-5 btn-round" href="#" onclick="return $Core.addAsFriend('{$user_id}');" title="{_p var='add_to_friends'}">
            {if $type == 'string'}
            <span class="ico ico-user1-plus-o"></span>
            {_p var='add_as_friend'}
            {else}
            <span class="ico ico-user1-plus-o"></span>
            {/if}
        </a>
    </div>
    {/if}
{/if}

{if $show_extra}
<div class="friend-info">
    <ul class="mutual-friends-list">
         {if $mutual_count == 0}
            {if !empty($aUserFriendFeed) && !empty($aUserFriendFeed.total_friend)}
                {if $aUserFriendFeed.total_friend == 1}{_p var='total_friend' total=$aUserFriendFeed.total_friend}{else}{_p var='total_friends' total=$aUserFriendFeed.total_friend}{/if}
            {elseif !empty($aUser.total_friend)}
                {if $aUser.total_friend == 1}{_p var='total_friend' total=$aUser.total_friend}{else}{_p var='total_friends' total=$aUser.total_friend}{/if}
            {/if}
        {else}
            {if $no_mutual_list}
                {if !empty($aUserFriendFeed) && !empty($aUserFriendFeed.user_id)}
                    <a href="#" onclick="$Core.box('friend.getMutualFriends', 450, 'user_id={$aUserFriendFeed.user_id}');return false;">
                {else}
                    <a href="#" onclick="$Core.box('friend.getMutualFriends', 450, 'user_id={$user_id}');return false;">
                {/if}
                {if $mutual_count == 1}
                    {_p var='1_mutual_friend'}
                {else}
                    {_p var='total_mutual_friends' total=$mutual_count}
                {/if}
                </a>
            {else}
                {if $mutual_count == 1}
                    {_p var='1_mutual_friend'}
                {else}
                    {_p var='total_mutual_friends' total=$mutual_count}
                {/if}
                :
                {foreach from=$mutual_list key=iKey item=aMutualFriend}
                <li class="user_profile_link_span" id="js_user_name_link_{$aMutualFriend.user_name}">
                    <a href="{url link=$aMutualFriend.user_name}">{$aMutualFriend.full_name}</a>
                </li>
                {/foreach}
                {if $mutual_remain > 0}
                    {_p var='and'}
                    <a href="#" onclick="$Core.box('friend.getMutualFriends', 450, 'user_id={if !empty($aUserFriendFeed) && !empty($aUserFriendFeed.user_id)}{$aUserFriendFeed.user_id}{else}{$user_id}{/if}');return false;">{if $mutual_remain == 1}{_p var='1_other'}{else}{_p var='total_others' total=$mutual_remain}{/if}</a>
                {/if}
            {/if}
        {/if}
    </ul>
</div>
{/if}