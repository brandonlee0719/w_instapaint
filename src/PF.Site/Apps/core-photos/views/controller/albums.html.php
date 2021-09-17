<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: albums.html.php 6060 2013-06-14 09:28:36Z Miguel_Espinoza $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 
?>
{if !PHPFOX_IS_AJAX && isset($bSpecialMenu) && $bSpecialMenu == true}
    {template file='photo.block.specialmenu'}
{/if}
{if count($aAlbums)}
    {if !PHPFOX_IS_AJAX}
        <section class="photo-albums">
            <div class="item-container" id="album-collection">
    {/if}
                {foreach from=$aAlbums item=aAlbum name=albums}
                    {template file="photo.block.album_entry"}
                {/foreach}
                {pager}
    {if !PHPFOX_IS_AJAX}
            </div>
        </section>
    {/if}
    {if $bShowModerator}
        {moderation}
    {/if}
{else}
    {if !PHPFOX_IS_AJAX}
    <div class="extra_info">
        {_p var='no_albums_found'}
    </div>
{/if}
{/if}