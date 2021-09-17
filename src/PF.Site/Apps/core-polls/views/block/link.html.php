<?php
/**
 *
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		phpFox
 * @package  		Poll
 *
 */

defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $aPoll.canApprove}
    <li><a href="#" class="" onclick="$(this).hide(); $('#js_item_bar_approve_image').show(); $.ajaxCall('poll.moderatePoll','iResult=0&amp;iPoll={$aPoll.poll_id}&amp;inline=true'); return false;"><i class="ico ico-check-square-alt mr-1"></i>{_p var='approve'}</a></li>
{/if}
{if ($aPoll.canEdit)}
    {if !isset($bDesign) || $bDesign == false}
        <li>
            <a href="{url link='poll.add' id=$aPoll.poll_id}">
                <i class="ico ico-pencilline-o mr-1"></i>{_p var='edit'}
            </a>
        </li>
        <li>
            <a href="{url link='poll.design' id=$aPoll.poll_id}">
                <i class="ico ico-color-palette mr-1"></i>{_p var='design'}
            </a>
        </li>
    {/if}
{/if}
{if $aPoll.canSponsorInFeed}
    <li>
        {if $aPoll.iSponsorInFeedId === true}
        <a href="{url link='ad.sponsor' where='feed' section='poll' item=$aPoll.poll_id}">
            <i class="ico ico-sponsor mr-1"></i>{_p var='sponsor_in_feed'}
        </a>
        {else}
        <a href="#" onclick="$.ajaxCall('ad.removeSponsor', 'type_id=poll&item_id={$aPoll.poll_id}', 'GET'); return false;">
            <i class="ico ico-sponsor mr-1"></i>{_p var="unsponsor_in_feed"}
        </a>
        {/if}
    </li>
{/if}
{if $aPoll.canFeature}
    <li><a id="js_feature_{$aPoll.poll_id}"{if $aPoll.is_featured} style="display:none;"{/if} href="#" title="{_p var='feature'}" onclick="$('#js_featured_phrase_{$aPoll.poll_id}').hide(); $(this).hide(); $('#js_unfeature_{$aPoll.poll_id}').show(); $.ajaxCall('poll.feature', 'poll_id={$aPoll.poll_id}&amp;type=1'); return false;"><i class="ico ico-diamond mr-1"></i>{_p var='feature'}</a></li>
    <li><a id="js_unfeature_{$aPoll.poll_id}"{if !$aPoll.is_featured} style="display:none;"{/if} href="#" title="{_p var='un_feature'}" onclick="$('#js_featured_phrase_{$aPoll.poll_id}').show(); $(this).hide(); $('#js_feature_{$aPoll.poll_id}').show(); $.ajaxCall('poll.feature', 'poll_id={$aPoll.poll_id}&amp;type=0'); return false;"><i class="ico ico-diamond-o mr-1"></i>{_p var='unfeature'}</a></li>
{/if}
{if $aPoll.canSponsor}
    <li>
        <a href="#" id="js_sponsor_{$aPoll.poll_id}" onclick="$('#js_sponsor_phrase_{$aPoll.poll_id}').hide(); $('#js_sponsor_{$aPoll.poll_id}').hide();$('#js_unsponsor_{$aPoll.poll_id}').show();$.ajaxCall('poll.sponsor','poll_id={$aPoll.poll_id}&type=0', 'GET'); return false;" style="{if $aPoll.is_sponsor != 1}display:none;{/if}">
        <i class="ico ico-sponsor mr-1"></i>{_p var='unsponsor_this_poll'}
        </a>
        <a href="#" id="js_unsponsor_{$aPoll.poll_id}" onclick="$('#js_sponsor_phrase_{$aPoll.poll_id}').show(); $('#js_sponsor_{$aPoll.poll_id}').show();$('#js_unsponsor_{$aPoll.poll_id}').hide();$.ajaxCall('poll.sponsor','poll_id={$aPoll.poll_id}&type=1', 'GET'); return false;" style="{if $aPoll.is_sponsor == 1}display:none;{/if}">
        <i class="ico ico-sponsor mr-1"></i>{_p var='sponsor_this_poll'}
        </a>
    </li>
{elseif $aPoll.canPurchaseSponsor}
    <li>
        <a href="{permalink module='ad.sponsor' id=$aPoll.poll_id}section_poll/">
            <i class="ico ico-sponsor mr-1"></i>{_p var='sponsor_this_poll'}
        </a>
    </li>
{/if}
{if !isset($bIsCustomPoll)}
    {if $aPoll.canDelete}
        {if !isset($bDesign) || $bDesign == false}
            <li class="item_delete">
                <a {if isset($bIsViewingPoll)}href="{url link='poll' delete=$aPoll.poll_id}" class="sJsConfirm"{else}href="#" onclick="deletePoll({$aPoll.poll_id}); return false;"{/if} data-message="{_p('are_you_sure_you_want_to_delete_this_poll')}">
                    <i class="ico ico-trash-alt-o mr-1"></i>{_p var='delete'}
                </a>
            </li>
        {/if}
    {/if}
{/if}
{plugin call='poll.template_block_entry_links_main'}