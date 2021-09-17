<?php

defined('PHPFOX') or exit('NO DICE!');

?>
<div class="block">
    <div class="title">
        {_p var='friends'}
    </div>
    <div class="content user_rows_block_content" id="js_block_content_profile_friend">
        <div class="user_rows_mini">
            {foreach from=$aFriends item=aUser}
            {template file='user.block.rows'}
            {/foreach}
        </div>
    </div>

    {if $aSubject.total_friend > count($aFriends)}
    <div class="bottom">
        <a class="btn btn-block btn-default" href="{url link=''$aSubject.user_name'.friend'}">
            {_p var='view_all_friends' total=$aSubject.total_friend }
        </a>
    </div>
    {/if}
</div>


{foreach from=$aFriendLists item=aLists}
<div class="block">
    <div class="title">
        {$aLists.name|clean}
    </div>
    <div class="content" id="js_block_content_profile_friend">
        <div class="user_rows_mini">
            {foreach from=$aLists.friends item=aUser}
            {template file='user.block.rows'}
            {/foreach}
        </div>
    </div>

    {if $aLists.friends_total > count($aLists.friends)}
    <div class="bottom">
        <a class="btn btn-block btn-default" href="{url link=''$aSubject.user_name'.friend' list=$aLists.list_id}">
            {_p var='view_all_friends' total=$aLists.friends_total }
        </a>
    </div>
    {/if}
</div>
{/foreach}