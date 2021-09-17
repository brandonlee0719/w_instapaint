<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if Phpfox::isUser() && Phpfox::isModule('friend') && Phpfox::getUserParam('friend.can_add_friends')}
<ul class="list-unstyled">
    {if !$is_friend}
    <li><a class="btn btn-sm {if $type == 'string'}btn-default{else}btn-success{/if}" href="#" onclick="return $Core.addAsFriend('{$user_id}');" title="{_p var='add_to_friends'}">
            {if $type == 'string'}
            <i class="fa fa-plus"></i>
            {_p var='add_as_friend'}
            {else}
                {if $requested}
                <i class="fa fa-check"></i>
                {else}
                <i class="fa fa-user-plus"></i>
                {/if}
            {/if}
        </a></li>
    {/if}
</ul>
{/if}

{if $show_extra}
<div class="friend-info">
    {if $mutual_count == 0}
        {if !empty($aUserFriendFeed)}
            {if $aUserFriendFeed.total_friend == 1}
                {_p var='total_friend' total=$aUserFriendFeed.total_friend}
            {else}
                {_p var='total_friends' total=$aUserFriendFeed.total_friend}
            {/if}
        {else}
            {if $aUser.total_friend == 1}
                {_p var='total_friend' total=$aUser.total_friend}
            {else}
                {_p var='total_friends' total=$aUser.total_friend}
            {/if}
        {/if}
    {else}
        {if $mutual_count == 1}
            {_p var='1_mutual_friend'}
        {else}
            {_p var='total_mutual_friends' total=$mutual_count}
        {/if}
    {/if}
</div>
{/if}