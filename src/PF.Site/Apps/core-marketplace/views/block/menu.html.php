<?php
    defined('PHPFOX') or exit('NO DICE!');
?>

{if $aListing.canApprove}
<li>
    <a href="#" onclick="$(this).hide(); $.ajaxCall('marketplace.approve', 'inline=true&amp;listing_id={$aListing.listing_id}'); return false;"><i class="ico ico-check-square-alt mr-1"></i>{_p var='approve'}</a>
</li>
{/if}
{if $aListing.canEdit}
<li><a href="{url link='marketplace.add' id=$aListing.listing_id}"><i class="ico ico-pencilline-o mr-1"></i>{_p var='edit_listing'}</a></li>
<li><a href="{url link='marketplace.add.customize' id=$aListing.listing_id tab='customize'}"><i class="ico ico-photos-alt"></i>{_p var='manage_photos'}</a></li>
<li><a href="{url link='marketplace.add.invite' id=$aListing.listing_id tab='invite'}"><i class="ico ico-envelope"></i>{_p var='send_invitations'}</a></li>
<li><a href="{url link='marketplace.add.manage' id=$aListing.listing_id tab='manage'}"><i class="ico ico-inbox-full"></i>{_p var='manage_invites'}</a></li>
{/if}
{if $aListing.canSponsorInFeed}
<li>
    {if $aListing.iSponsorInFeedId === true}
    <a href="{url link='ad.sponsor' where='feed' section='marketplace' item=$aListing.listing_id}">
        <i class="ico ico-sponsor"></i>{_p var='sponsor_in_feed'}
    </a>
    {else}
    <a href="#"
       onclick="$.ajaxCall('ad.removeSponsor', 'type_id=marketplace&item_id={$aListing.listing_id}', 'GET'); return false;">
        <i class="ico ico-sponsor"></i>{_p var="Unsponsor In Feed"}
    </a>
    {/if}
</li>
{/if}

{if $aListing.canFeature}
<li class="js_marketplace_is_feature" {if $aListing.is_featured} style="display:none;" {/if}><a href="#" onclick="$('#js_featured_phrase_{$aListing.listing_id}').show(); $.ajaxCall('marketplace.feature', 'listing_id={$aListing.listing_id}&amp;type=1', 'GET'); $(this).parent().hide(); $(this).parents('ul:first').find('.js_marketplace_is_un_feature').show(); return false;"><i class="ico ico-diamond"></i>{_p
    var='feature'}</a></li>
<li class="js_marketplace_is_un_feature" {if !$aListing.is_featured} style="display:none;" {/if}><a href="#" onclick="$('#js_featured_phrase_{$aListing.listing_id}').hide(); $.ajaxCall('marketplace.feature', 'listing_id={$aListing.listing_id}&amp;type=0', 'GET'); $(this).parent().hide(); $(this).parents('ul:first').find('.js_marketplace_is_feature').show(); return false;"><i class="ico ico-diamond"></i>{_p
    var='un_feature'}</a></li>
{/if}
{if $aListing.canSponsor}
<li id="js_sponsor_{$aListing.listing_id}">
	{if $aListing.is_sponsor}
		<a href="#" onclick="$('#js_sponsor_phrase_{$aListing.listing_id}').hide(); $.ajaxCall('marketplace.sponsor','listing_id={$aListing.listing_id}&type=0', 'GET'); return false;"><i class="ico ico-sponsor mr-1"></i>{_p var='unsponsor_this_listing'}</a>
	{else}
		<a href="#" onclick="$('#js_sponsor_phrase_{$aListing.listing_id}').show(); $.ajaxCall('marketplace.sponsor','listing_id={$aListing.listing_id}&type=1', 'GET'); return false;"><i class="ico ico-sponsor mr-1"></i>{_p var='sponsor_this_listing'}</a>
	{/if}
</li>
{elseif $aListing.canPurchaseSponsor}
    {if $aListing.is_sponsor == 1}
    <li>
        <a href="#" onclick="$('#js_sponsor_phrase_{$aListing.listing_id}').hide(); $.ajaxCall('marketplace.sponsor','listing_id={$aListing.listing_id}&type=0', 'GET'); return false;">
            <i class="ico ico-sponsor mr-1"></i>{_p var='unsponsor_this_listing'}
        </a>
    </li>
    {else}
    <li>
        <a href="{permalink module='ad.sponsor' id=$aListing.listing_id}section_marketplace/">
            <i class="ico ico-sponsor mr-1"></i>{_p var='sponsor_this_listing'}
        </a>
    </li>
    {/if}
{/if}
{if $aListing.canDelete}
<li class="item_delete"><a href="javascript:void(0)" data-id="{$aListing.listing_id}" data-is-detail="{if isset($bIsDetail) && $bIsDetail}1{else}0{/if}" data-message="{_p('are_you_sure_you_want_to_delete_this_listing_permanently')}" onclick="$Core.marketplace.deleteListing($(this));"><i class="ico ico-trash-alt-o"></i>{_p
        var='delete_listing'}</a></li>
{/if}

{plugin call='marketplace.template_block_entry_links_main'}