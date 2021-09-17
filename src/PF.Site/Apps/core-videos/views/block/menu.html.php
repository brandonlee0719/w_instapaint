<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

{if $aItem.canApprove}
    <li class="js_video_approve_btn_{$aItem.video_id}">
        <a href="#" onclick="$.ajaxCall('v.approve', 'video_id={$aItem.video_id}', 'GET'); return false;">
            <span class="ico ico-check-square-alt mr-1"></span>{_p('approve')}
        </a>
    </li>
{/if}

{if $aItem.canEdit}
    <li>
        <a href="{url link="video.edit" id=""$aItem.video_id""}">
            <span class="ico ico-pencilline-o mr-1"></span>{_p('edit')}
        </a>
    </li>
{/if}
{if ($aItem.canApprove || $aItem.canEdit) && ($aItem.canSponsorInFeed || $aItem.canSponsor || $aItem.canPurchaseSponsor || $aItem.canFeature)}
<li role="separator" class="divider"></li>
{/if}

{if $aItem.canSponsorInFeed}
    <li>
        {if $aItem.iSponsorInFeedId === true}
        <a href="{url link='ad.sponsor' where='feed' section='v' item=$aItem.video_id}">
            <span class="ico ico-sponsor mr-1"></span>{_p var='sponsor_in_feed'}
        </a>
        {else}
        <a href="#" onclick="$.ajaxCall('ad.removeSponsor', 'type_id=v&item_id={$aItem.video_id}', 'GET'); return false;">
            <span class="ico ico-sponsor mr-1"></span>{_p var="Unsponsor In Feed"}
        </a>
        {/if}
    </li>
{/if}
{if $aItem.canSponsor}
    <li id="js_video_sponsor_{$aItem.video_id}" {if $aItem.is_sponsor}style="display:none;"{/if}>
        <a href="#" onclick="$.ajaxCall('v.sponsor', 'video_id={$aItem.video_id}&type=1', 'GET'); return false;">
            <span class="ico ico-sponsor mr-1"></span>{_p('sponsor_this_video')}
        </a>
    </li>
    <li id="js_video_unsponsor_{$aItem.video_id}" {if !$aItem.is_sponsor}style="display:none;"{/if}>
        <a href="#" onclick="$.ajaxCall('v.sponsor', 'video_id={$aItem.video_id}&type=0', 'GET'); return false;">
            <span class="ico ico-sponsor mr-1"></span>{_p('unsponsor_this_video')}
        </a>
    </li>
{elseif $aItem.canPurchaseSponsor}
    {if $aItem.is_sponsor == 1}
    <li>
        <a href="#" onclick="$.ajaxCall('v.sponsor', 'video_id={$aItem.video_id}&type=0', 'GET'); return false;">
            <span class="ico ico-sponsor mr-1"></span>{_p('unsponsor_this_video')}
        </a>
    </li>
    {else}
    <li>
        <a href="{permalink module='ad.sponsor' id=$aItem.video_id title=$aItem.title section=v}">
            <span class="ico ico-sponsor mr-1"></span>{_p('sponsor_this_video')}
        </a>
    </li>
    {/if}
{/if}

{if $aItem.canFeature}
    <li id="js_video_feature_{$aItem.video_id}" {if $aItem.is_featured}style="display:none;"{/if}>
        <a href="#" onclick="$.ajaxCall('v.feature', 'video_id={$aItem.video_id}&type=1', 'GET'); return false;">
            <span class="ico ico-diamond-o mr-1"></span>{_p('feature')}
        </a>
    </li>
    <li id="js_video_unfeature_{$aItem.video_id}" {if !$aItem.is_featured}style="display:none;"{/if}>
        <a href="#" onclick="$.ajaxCall('v.feature', 'video_id={$aItem.video_id}&type=0', 'GET'); return false;">
            <span class="ico ico-diamond-o mr-1"></span>{_p('un_feature')}
        </a>
    </li>
{/if}

{if $aItem.canDelete}
    <li role="separator" class="divider"></li>
    <li class="item_delete">
        {if !empty($iProfileId)}
            <a href="{url link='video' delete=$aItem.video_id view=$sView user_id=$iProfileId}" class="no_ajax_link sJsConfirm" data-message="{_p var='are_you_sure_you_want_to_delete_this_video_permanently' phpfox_squote=true}" phpfox_squote=true}');" title="{_p('delete_video')}">
                <span class="ico ico-trash-o mr-1"></span>{_p('Delete')}
            </a>
        {else}
            <a href="{url link='video' delete=$aItem.video_id view=$sView}" class="no_ajax_link sJsConfirm" data-message="{_p var='are_you_sure_you_want_to_delete_this_video_permanently' phpfox_squote=true}" phpfox_squote=true}');" title="{_p('delete_video')}">
                <span class="ico ico-trash-o mr-1"></span>{_p('Delete')}
            </a>
        {/if}
    </li>
{/if}