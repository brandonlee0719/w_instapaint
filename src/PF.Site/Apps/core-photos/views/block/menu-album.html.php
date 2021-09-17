<?php
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $aAlbum.canEdit}
    <li>
        <a href="{url link='photo.edit-album' id=$aAlbum.album_id}" id="js_edit_this_album"><i class="ico ico ico-pencilline-o mr-1"></i>{_p var='edit_this_album'}</a>
    </li>
{/if}

{if $aAlbum.canUpload}
    <li>
        <a href="{if (empty($aAlbum.module_id))}{url link='photo.add' album=$aAlbum.album_id}{else}{url link='photo.add' module=$aAlbum.module_id item=$aAlbum.group_id album=$aAlbum.album_id}{/if}"><i class="ico ico-upload-alt mr-1"></i>{_p var='upload_photos_to_album'}</a>
    </li>
{/if}

{if $aAlbum.canDelete}
    <li class="item_delete">
        <a data-is-detail="{if isset($bIsAlbumDetail)}1{else}0{/if}" data-id="{$aAlbum.album_id}" href="javascript:void(0);" id="js_delete_this_album" data-message="{_p('are_you_sure_you_want_to_delete_this_album_permanently')}" onclick="$Core.Photo.deleteAlbumPhoto($(this));"><i class="ico ico-trash-o mr-1"></i>{_p var='delete_this_album'}</a>
    </li>
{/if}

{plugin call='photo.template_block_menu_album'}