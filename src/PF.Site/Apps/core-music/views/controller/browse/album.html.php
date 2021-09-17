<?php 
    defined('PHPFOX') or exit('NO DICE!'); 
?>

{if !PHPFOX_IS_AJAX && isset($bSpecialMenu) && $bSpecialMenu == true}
    {template file='music.block.specialmenu'}
{/if}

{if count($aAlbums)}
    {if ! PHPFOX_IS_AJAX }<div class="item-container albums-listing">{/if}
        {foreach from=$aAlbums name=albums item=aAlbum}
            {template file='music.block.album-rows'}
        {/foreach}
        {if $bShowModerator}
            {moderation}
        {/if}
        {pager}
    {if ! PHPFOX_IS_AJAX }</div>{/if}
{else}
    {if ! PHPFOX_IS_AJAX }
    <div class="extra_info">
        {_p var='no_albums_found'}
    </div>
    {/if}
{/if}
