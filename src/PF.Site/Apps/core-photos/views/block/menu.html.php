<?php 
defined('PHPFOX') or exit('NO DICE!');

?>
{if $aForms.canApprove}
<li><a href="#" onclick="$(this).hide(); $('#js_item_bar_approve_image').show(); $.ajaxCall('photo.approve', 'id={$aForms.photo_id}{if !isset($bIsDetail)}&amp;inline=true{/if}'); return false;" title="{_p var='approve'}"><i class="ico ico-check-square-alt mr-1"></i>{_p var='approve'}</a></li>
{/if}

{if $aForms.canEdit}
    <li><a href="#" onclick="if ($Core.exists('.js_box_image_holder_full')) {l} js_box_remove($('.js_box_image_holder_full').find('.js_box_content')); {r} $Core.box('photo.editPhoto', 700, 'photo_id={$aForms.photo_id}'); $('#js_tag_photo').hide();return false;"><i class="ico ico-pencilline-o mr-1"></i>{_p var='edit_this_photo'}</a></li>
{/if}

{if $aForms.canSponsorInFeed}
    <li>
        {if $aForms.iSponsorInFeedId === true}
            <a href="{url link='ad.sponsor' where='feed' section='photo' item=$aForms.photo_id}">
                <i class="ico ico-sponsor mr-1"></i>{_p var='sponsor_in_feed'}
            </a>
        {else}
            <a href="#" onclick="$.ajaxCall('ad.removeSponsor', 'type_id=photo&item_id={$aForms.photo_id}', 'GET'); return false;">
                <i class="ico ico-sponsor mr-1"></i>{_p var="Unsponsor In Feed"}
            </a>
        {/if}
    </li>
{/if}

{if $aForms.canSponsor}
    <li id="js_sponsor_{$aForms.photo_id}" class="" style="{if $aForms.is_sponsor}display:none;{/if}">
        <a href="#" onclick="$('#js_sponsor_{$aForms.photo_id}').hide();$('#js_unsponsor_{$aForms.photo_id}').show();$.ajaxCall('photo.sponsor','photo_id={$aForms.photo_id}&type=1'); return false;">
            <i class="ico ico-sponsor mr-1"></i>{_p var='sponsor_this_photo'}
        </a>
    </li>
    <li id="js_unsponsor_{$aForms.photo_id}" class="" style="{if $aForms.is_sponsor != 1}display:none;{/if}">
        <a href="#" onclick="$('#js_sponsor_{$aForms.photo_id}').show();$('#js_unsponsor_{$aForms.photo_id}').hide();$.ajaxCall('photo.sponsor','photo_id={$aForms.photo_id}&type=0'); return false;">
            <i class="ico ico-sponsor mr-1"></i>{_p var='unsponsor_this_photo'}
        </a>
    </li>
{elseif $aForms.canPurchaseSponsor}
    {if $aForms.is_sponsor == 1}
    <li>
        <a href="#" onclick="$('#js_sponsor_{$aForms.photo_id}').show();$('#js_unsponsor_{$aForms.photo_id}').hide();$.ajaxCall('photo.sponsor','photo_id={$aForms.photo_id}&type=0'); return false;">
            <i class="ico ico-sponsor mr-1"></i>{_p var='unsponsor_this_photo'}
        </a>
    </li>
    {else}
    <li>
        <a href="{permalink module='ad.sponsor' id=$aForms.photo_id}section_photo/">
            <i class="ico ico-sponsor mr-1"></i>{_p var='sponsor_this_photo'}
        </a>
    </li>
    {/if}
{/if}

{if $aForms.canFeature}
    <li id="js_photo_feature_{$aForms.photo_id}">
        {if $aForms.is_featured}
            <a href="#" title="{_p var='un_feature_this_photo'}" onclick="$.ajaxCall('photo.feature', 'photo_id={$aForms.photo_id}&amp;type=0', 'GET'); return false;"><i class="ico ico-diamond-o mr-1"></i>{_p var='un_feature'}</a>
        {else}
            <a href="#" title="{_p var='feature_this_photo'}" onclick="$.ajaxCall('photo.feature', 'photo_id={$aForms.photo_id}&amp;type=1', 'GET'); return false;"><i class="ico ico-diamond mr-1"></i>{_p var='feature'}</a>
        {/if}
    </li>
{/if}

{plugin call='photo.template_block_menu'}

{if $aForms.canDelete}
    {if defined('PHPFOX_IS_THEATER_MODE')}
        <li class="item_delete"><a href="#" onclick="$Core.jsConfirm({l}{r}, function() {l} $.ajaxCall('photo.deleteTheaterPhoto', 'photo_id={$aForms.photo_id}'); {r}, function(){l}{r}); return false;">{_p var='delete_this_photo'}</a></li>
    {else}
        <li class="item_delete">
            <a href="javascript:void(0);" data-message={if isset($iAvatarId) && $iAvatarId == $aForms.photo_id}"{_p('are_you_sure_you_want_to_delete_this_photo_permanently_this_will_delete_your_current_profile_picture_also')}"{elseif isset($iCover) && $iCover == $aForms.photo_id}"{_p('are_you_sure_you_want_to_delete_this_photo_permanently_this_will_delete_your_current_cover_photo_also')}"{else}"{_p('are_you_sure_you_want_to_delete_this_photo_permanently')}"{/if} data-id="{$aForms.photo_id}" data-is-detail="{if isset($bIsDetail) && $bIsDetail && !isset($bIsAlbumDetail)}1{else}0{/if}" onclick="$Core.Photo.deletePhoto($(this));"><i class="ico ico-trash-alt-o mr-1"></i>{_p var='delete_this_photo'}</a>
        </li>
    {/if}
{/if}

{if isset($aCallback) && ($aCallback.module_id == 'pages' || $aCallback.module_id == 'groups')}
    <li>
        <a href="#" onclick="$Core.Photo.setCoverPhoto({$aForms.photo_id},{$aCallback.item_id},'{$aCallback.module_id}'); return false;" >
            {if isset($aCallback.set_default_phrase)}
                <i class="ico ico-photo mr-1"></i>{$aCallback.set_default_phrase}
            {else}
                <i class="ico ico-photo mr-1"></i>{_p var='set_as_page_s_cover_photo'}
            {/if}
        </a>
    </li>
{/if}
