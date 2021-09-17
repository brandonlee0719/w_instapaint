<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
{if count($aLikes)}
    {if !$bIsPaging}
    <div class="like-browse-container popup-user-total-container">
    {/if}
        {foreach from=$aLikes name=like item=aLike}
            <div id="js_row_like_{$aLike.user_id}"  class="like-browse-item popup-user-item">
                <div class="item-outer">
                    {if isset($bIsPageAdmin) && $bIsPageAdmin}
                        <div class="absolute-right">
                            <a href="#" onclick="$.ajaxCall('like.delete', 'delete_inline=1&type_id={$sItemType}&amp;item_id={$iItemId}&amp;force_user_id={$aLike.user_id}'); return false;" class="remove-btn"><i class="fa fa-times"></i></a>
                        </div>
                    {/if}
                    <div class="item-media">
                        {img user=$aLike suffix='_50_square' max_width=50 max_height=50}
                    </div>
                    <div class="item-name">
                        {$aLike|user}
                    </div>
                </div>
            </div>
        {/foreach}
        {if $hasPagingNext}
        {pager}
        {/if}
    {if !$bIsPaging}
    </div>
    {/if}
{else}
    {if !$bIsPaging}
    <div class="extra_info">
        {$sErrorMessage}
    </div>
    {/if}
{/if}
