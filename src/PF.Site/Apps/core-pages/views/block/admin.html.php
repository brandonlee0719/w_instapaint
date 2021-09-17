<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
<ul class="user_rows_mini core-friend-block">
{foreach from=$aPageAdmins name=pageadmins item=aPageAdmin}
    <li class="user_rows">
        <div class="user_rows_image">
            {img user=$aPageAdmin suffix='_120_square'}
        </div>
        <div class="user_rows_inner">
            {$aPageAdmin|user:'':'':40}
            {if $phpfox.iteration.pageadmins == 1}
            <div class="extra_info">
                {_p var='founder'}
            </div>
            {/if}
        </div>
        <div class="clear"></div>
    </li>
{/foreach}
</ul>