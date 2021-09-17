<?php

defined('PHPFOX') or exit('NO DICE!');

?>
<div class="block_listing_inline">
    <ul class="user_rows_mini core-friend-block friend-online-block">
        {foreach from=$aLatestUsers name=latestusers key=iLatestUser item=aLatestUser}
        <li class="user_rows">
            <div class="user_rows_image">
                {img user=$aLatestUser suffix='_50_square' max_width=32 max_height=32 class='js_hover_title _size__32'}
            </div>
        </li>
        {/foreach}
    </ul>
    <div class="clear"></div>
</div>
