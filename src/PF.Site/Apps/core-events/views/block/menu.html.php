<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: menu.html.php 3737 2011-12-09 07:50:12Z Raymond_Benc $
 */

defined('PHPFOX') or exit('NO DICE!');

?>
{if $aEvent.canApprove || $aEvent.canEdit}
    {if $aEvent.canApprove}
        <li>
            <a href="#" class="" onclick="$(this).hide(); $('#js_item_bar_approve_image').show(); $.ajaxCall('event.approve', 'inline=true&amp;event_id={$aEvent.event_id}', 'POST'); return false;"><span class="ico ico-check-square-alt mr-1"></span>{_p var='approve'}</a>
        </li>
    {/if}
    {if $aEvent.canEdit}
        <li><a href="{url link='event.add' id=$aEvent.event_id}"><span class="ico ico-pencilline-o mr-1"></span>{_p var='edit_event'}</a></li>
    {/if}
    <li role="separator" class="divider"></li>
{/if}
{if $aEvent.canSponsorInFeed || $aEvent.canInvite || $aEvent.canEdit || $aEvent.canFeature || $aEvent.canSponsor || $aEvent.canPurchaseSponsor}
    {if $aEvent.canSponsorInFeed}
    <li>
        {if $aEvent.iSponsorInFeedId === true}
        <a href="{url link='ad.sponsor' where='feed' section='event' item=$aEvent.event_id}">
            <span class="ico ico-sponsor mr-1"></span>{_p var='sponsor_in_feed'}
        </a>
        {else}
        <a href="#" onclick="$.ajaxCall('ad.removeSponsor', 'type_id=event&item_id={$aEvent.event_id}', 'GET'); return false;">
            <span class="ico ico-sponsor mr-1"></span>{_p var="Unsponsor In Feed"}
        </a>
        {/if}
    </li>
    {/if}

    {if $aEvent.canInvite}
        <li><a href="{url link='event.add.invite' id=$aEvent.event_id tab='invite'}"><span class="ico ico-user-man-plus"></span>{_p var='invite_people_to_come'}</a></li>
    {/if}
    {if $aEvent.canEdit}
        {if $aEvent.canMassEmail}
            <li><a href="{url link='event.add.email' id=$aEvent.event_id tab='email'}"><span class="ico ico-comment-o"></span>{_p var='mass_email_guests'}</a></li>
        {/if}
        <li><a href="{url link='event.add.manage' id=$aEvent.event_id tab='manage'}"><span class="ico ico-user-couple"></span>{_p var='manage_guest_list'}</a></li>
    {/if}

    {if $aEvent.canFeature}
        <li id="js_feature_{$aEvent.event_id}"{if $aEvent.is_featured} style="display:none;"{/if}><a href="#" title="{_p var='feature_this_event'}" onclick="$(this).parent().hide(); $('#js_unfeature_{$aEvent.event_id}').show(); $(this).parents('.js_event_parent:first').find('.js_featured_event').show(); $.ajaxCall('event.feature', 'event_id={$aEvent.event_id}&amp;type=1'); return false;"> <span class="ico ico-diamond-o mr-1"></span>{_p var='feature'}</a></li>
        <li id="js_unfeature_{$aEvent.event_id}"{if !$aEvent.is_featured} style="display:none;"{/if}><a href="#" title="{_p var='un_feature_this_event'}" onclick="$(this).parent().hide(); $('#js_feature_{$aEvent.event_id}').show(); $(this).parents('.js_event_parent:first').find('.js_featured_event').hide(); $.ajaxCall('event.feature', 'event_id={$aEvent.event_id}&amp;type=0'); return false;"> <span class="ico ico-diamond-o mr-1"></span>{_p var='Unfeature'}</a></li>
    {/if}

    {if $aEvent.canSponsor}
        <li id="js_event_sponsor_{$aEvent.event_id}" {if $aEvent.is_sponsor}style="display:none;"{/if}>
            <a href="#" onclick="$.ajaxCall('event.sponsor', 'event_id={$aEvent.event_id}&type=1', 'GET'); return false;"><span class="ico ico-sponsor mr-1"></span>{_p var='sponsor_this_event'}</a>
        </li>
        <li id="js_event_unsponsor_{$aEvent.event_id}" {if !$aEvent.is_sponsor}style="display:none;"{/if}>
            <a href="#" onclick="$.ajaxCall('event.sponsor', 'event_id={$aEvent.event_id}&type=0', 'GET'); return false;"><span class="ico ico-sponsor mr-1"></span>{_p var='unsponsor_this_event'}</a>
        </li>
    {elseif $aEvent.canPurchaseSponsor}
        {if $aEvent.is_sponsor == 1}
        <li>
            <a href="#" onclick="$.ajaxCall('event.sponsor', 'event_id={$aEvent.event_id}&type=0', 'GET'); return false;">
                <span class="ico ico-sponsor mr-1"></span>{_p var='unsponsor_this_event'}
            </a>
        </li>
        {else}
        <li>
            <a href="{permalink module='ad.sponsor' id=$aEvent.event_id title=$aEvent.title section=event}">
                <span class="ico ico-sponsor mr-1"></span>{_p var='sponsor_this_event'}
            </a>
        </li>
        {/if}
    {/if}
    <li role="separator" class="divider"></li>
{/if}
{if $aEvent.canDelete}
    <li class="item_delete"><a href="javascript:void(0);" data-id="{$aEvent.event_id}" data-is-detail="{if isset($bIsDetail) && $bIsDetail}1{else}0{/if}" data-message="{_p('are_you_sure_you_want_to_delete_this_event_permanently')}" onclick="$Core.event.deleteEvent($(this));"><span class="ico ico-trash-o mr-1"></span>{_p var='delete_event'}</a></li>
{/if}

{plugin call='event.template_block_entry_links_main'}