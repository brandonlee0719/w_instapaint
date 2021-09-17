<?php 
    defined('PHPFOX') or exit('NO DICE!');
?>

{if !PHPFOX_IS_AJAX && isset($bSpecialMenu) && $bSpecialMenu == true}
    {template file='music.block.specialmenu'}
{/if}
{if count($aSongs)}
    {if ! PHPFOX_IS_AJAX }<div class="item-container music-listing">{/if}
        {foreach from=$aSongs name=songs item=aSong}
            {template file='music.block.rows'}
        {/foreach}
        {if $bShowModerator}
            {moderation}
        {/if}
        {pager}
    {if ! PHPFOX_IS_AJAX }</div>{/if}
{else}
    {if ! PHPFOX_IS_AJAX }
        <div class="extra_info">
            {_p var='no_songs_found'}
        </div>
    {/if}
{/if}
