<?php 
    defined('PHPFOX') or exit('NO DICE!'); 
?>

{if $aAlbum.canEdit}
    <li><a href="{url link='music.album' id=$aAlbum.album_id}"><i class="ico ico-pencilline-o mr-1"></i>{_p var='edit'}</a></li>
    <li><a href="{url link='music.album.manage' id=$aAlbum.album_id}"><i class="ico ico-music-album mr-1"></i>{_p var='manage_songs'}</a></li>
    {if $aAlbum.canAddSong}
    <li><a href="{url link='music.album.track' id=$aAlbum.album_id}"><i class="ico ico-upload-alt mr-1"></i>{_p var='upload_new_song'}</a></li>
    {/if}
{/if}

{if $aAlbum.canFeature}
    <li><a id="js_feature_{$aAlbum.album_id}"{if $aAlbum.is_featured} style="display:none;"{/if} href="#" title="{_p var='feature_this_album'}" onclick="$(this).hide(); $('#js_unfeature_{$aAlbum.album_id}').show(); $(this).parents('.js_album_parent:first').addClass('row_featured').find('.js_featured_album').show(); $.ajaxCall('music.featureAlbum', 'album_id={$aAlbum.album_id}&amp;type=1'); return false;"><i class="ico ico-diamond mr-1"></i>{_p var='feature'}</a></li>
    <li><a id="js_unfeature_{$aAlbum.album_id}"{if !$aAlbum.is_featured} style="display:none;"{/if} href="#" title="{_p var='un_feature_this_album'}" onclick="$(this).hide(); $('#js_feature_{$aAlbum.album_id}').show(); $(this).parents('.js_album_parent:first').removeClass('row_featured').find('.js_featured_album').hide(); $.ajaxCall('music.featureAlbum', 'album_id={$aAlbum.album_id}&amp;type=0'); return false;"><i class="ico ico-diamond-o mr-1"></i>{_p var='unfeature'}</a></li>
{/if}

{if $aAlbum.canSponsor}
    <li>
        <a href='#' onclick="$.ajaxCall('music.sponsorAlbum','album_id={$aAlbum.album_id}&type={if $aAlbum.is_sponsor == 1}0{else}1{/if}');return false;">
            {if $aAlbum.is_sponsor == 1}
                <i class="ico ico-sponsor mr-1"></i>{_p var='unsponsor_this_album'}
            {else}
                <i class="ico ico-sponsor mr-1"></i>{_p var='sponsor_this_album'}
            {/if}
        </a>
    </li>
{elseif $aAlbum.canPurchaseSponsor}
    {if $aAlbum.is_sponsor}
    <li>
        <a href='#' onclick="$.ajaxCall('music.sponsorAlbum','album_id={$aAlbum.album_id}&type=0');return false;">
            <i class="ico ico-sponsor mr-1"></i>{_p var='unsponsor_this_album'}
        </a>
    </li>
    {else}
    <li>
        <a href="{permalink module='ad.sponsor' id=$aAlbum.album_id}section_music_album/">
            <i class="ico ico-sponsor mr-1"></i>{_p var='encourage_sponsor_album'}
        </a>
    </li>
    {/if}
{/if}

{if $aAlbum.canDelete}
    <li class="item_delete"><a href="{url link='music.browse.album' id=$aAlbum.album_id}" class="sJsConfirm" data-message="{_p var='are_you_sure_this_will_delete_all_tracks_that_belong_to_this_album_and_cannot_be_undone' phpfox_squote=true}"><i class="ico ico-trash-alt-o mr-1"></i>{_p var='delete'}</a></li>
{/if}