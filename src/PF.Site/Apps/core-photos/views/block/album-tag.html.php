<?php
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $sView == 'block'}
    <div class="block_listing_inline album_tag">
	    <ul>
            {foreach from=$aTaggedUsers item=aTaggedUser}
                <li>{img user=$aTaggedUser suffix='_50_square' max_width=50 max_height=50 class='js_hover_title'}</li>
            {/foreach}
	    </ul>
	    <div class="clear"></div>
    </div>
{else}
    {if $iPage == 1}
    <div class="popup-user-total-container" id="js_album_tag_content">
    {/if}
        {foreach from=$aTaggedUsers item=aTaggedUser}
            <div class="popup-user-item">
                <div class="item-outer">
                    <div class="item-media">
                        {img user=$aTaggedUser suffix='_50_square' max_width=50 max_height=50}
                    </div>
                    <div class="item-name">
                        {$aTaggedUser|user:'':'':30}
                    </div>
                </div>
            </div>
        {/foreach}
        {pager}
    {if $iPage == 1}
    </div>
    {/if}
{/if}