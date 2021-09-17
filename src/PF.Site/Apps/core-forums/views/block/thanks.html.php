<?php 

 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if !$bIsPaging}
    <div class="js_users_thank_post">
{/if}
    {if count($aThanks)}
        <div class="popup-user-total-container">
            {foreach from=$aThanks item=aThank}
                <div id="js_post_{$iPostId}_thank_{$aThank.user_id}" class="popup-user-item">
                    <div class="item-outer">
                        <div class="item-media">
                            {img user=$aThank suffix='_50_square' max_width=50 max_height=50}
                        </div>
                        <div class="item-name">
                            {$aThank|user:'':'':30}
                            {if ($aThank.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('forum.can_delete_thanks_by_other_users')}
                            <div>
                                <a class="forum_thank_delete_link" href="#" onclick="$.ajaxCall('forum.removeThanks', 'thank_id={$aThank.thank_id}&user_id={$aThank.user_id}&popup=true&post_id={$iPostId}');return false;">{_p var='delete'}</a>
                            </div>
                            {/if}
                        </div>
                    </div>
                </div>
            {/foreach}
        </div>
        {pager}
    {else}
        <div class="extra_info">
            {_p var='no_one_has_thanked_this_post'}
        </div>
    {/if}
{if !$bIsPaging}
    </div>
{/if}