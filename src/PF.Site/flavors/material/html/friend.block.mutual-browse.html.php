<?php

defined('PHPFOX') or exit('NO DICE!');

?>
{if !$iPage && !count($aFriends) && !$bIsPaging}
<div class="extra_info">
    {_p var='no_mutual_friends_found'}.
</div>
{else}
    {if !$bIsPaging}
    <div class="js_friend_mutual_container popup-user-total-container">
    {/if}
        {foreach from=$aFriends name=friends item=aFriend}
        <div class="mutual-friend-item popup-user-item">
            <div class="item-outer ">
                <div class="item-media">
                    {img user=$aFriend suffix='_50_square' max_width=50 max_height=50}
                </div>
                <div class="item-name">
                    {$aFriend|user}
                </div>
                <div class="clear"></div>
            </div>
        </div>
        {/foreach}
        {if $hasPagingNext}
            {pager}
        {/if}
    {if !$bIsPaging}
    </div>
    {/if}
{/if}