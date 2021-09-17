<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if !$bIsPaging && empty($aFriends)}
    <div class="alert alert-danger">{_p var='you_have_no_friend_online_now'}</div>
{else}
    {if !$bIsPaging}
    <div class="browse-online-container label_flow">
    {/if}
    {foreach from=$aFriends item=aFriend name=friend}
        <div id="js_row_like_{$aFriend.user_id}" style="position:relative;" class="{if is_int($phpfox.iteration.friend/2)}row1{else}row2{/if}{if $phpfox.iteration.friend == 1} row_first{/if}">
            <div class="go_left" style="text-align:center;">
                {img user=$aFriend suffix='_50_square' max_width=50 max_height=50}
            </div>
            <div>
                {$aFriend|user:'':'':30}
            </div>
            <div class="clear"></div>
        </div>
    {/foreach}

    {if $hasPagingNext}
    {pager}
    {/if}

    {if !$bIsPaging}
    </div>
    {/if}
{/if}