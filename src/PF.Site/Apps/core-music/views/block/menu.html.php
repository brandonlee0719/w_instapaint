<?php 
    defined('PHPFOX') or exit('NO DICE!'); 
?>

{if $aSong.canApprove}
    <li><a href="#" class="" onclick="$(this).hide(); $('#js_item_bar_approve_image').show(); $.ajaxCall('music.approveSong', 'inline=true&amp;id={$aSong.song_id}', 'GET'); return false;"><i class="ico ico-check-square-alt mr-1"></i>{_p var='approve'}</a></li>
{/if}

{if $aSong.canEdit}
    <li><a href="{url link='music.upload' id=$aSong.song_id}"><i class="ico ico-pencilline-o mr-1"></i>{_p var='edit'}</a></li>
{/if}

{if $aSong.canSponsorInFeed}
    <li>
        {if $aSong.iSponsorInFeedId === true}
            <a href="{url link='ad.sponsor' where='feed' section='music_song' item=$aSong.song_id}">
                <i class="ico ico-sponsor mr-1"></i>
                {_p var='sponsor_in_feed'}
            </a>
        {else}
            <a href="#" onclick="$.ajaxCall('ad.removeSponsor', 'type_id=music_song&item_id={$aSong.song_id}', 'GET'); return false;">
                <i class="ico ico-sponsor mr-1"></i>
                {_p var="Unsponsor In Feed"}
            </a>
        {/if}
    </li>
{/if}

{if $aSong.canFeature}
    <li><a id="js_feature_{$aSong.song_id}"{if $aSong.is_featured} style="display:none;"{/if} href="#" title="{_p var='feature_this_song'}" onclick="$('#js_featured_phrase_{$aSong.song_id}').hide(); $(this).hide(); $('#js_unfeature_{$aSong.song_id}').show(); $(this).parents('.js_song_parent:first').addClass('row_featured').find('.js_featured_song').show(); $.ajaxCall('music.featureSong', 'song_id={$aSong.song_id}&amp;type=1'); return false;"><i class="ico ico-diamond mr-1"></i>{_p var='feature'}</a></li>
    <li><a id="js_unfeature_{$aSong.song_id}"{if !$aSong.is_featured} style="display:none;"{/if} href="#" title="{_p var='un_feature_this_song'}" onclick="$('#js_featured_phrase_{$aSong.song_id}').show(); $(this).hide(); $('#js_feature_{$aSong.song_id}').show(); $(this).parents('.js_song_parent:first').removeClass('row_featured').find('.js_featured_song').hide(); $.ajaxCall('music.featureSong', 'song_id={$aSong.song_id}&amp;type=0'); return false;"><i class="ico ico-diamond-o mr-1"></i>{_p var='unfeature'}</a></li>
{/if}
{if $aSong.canSponsor}
    <li>
        <a id="js_sponsor_{$aSong.song_id}" {if !$aSong.is_sponsor}style="display:none"{/if} href="#" onclick="$('#js_sponsor_phrase_{$aSong.song_id}').hide(); $('#js_sponsor_{$aSong.song_id}').hide();$('#js_unsponsor_{$aSong.song_id}').show();$.ajaxCall('music.sponsorSong','song_id={$aSong.song_id}&type=0', 'GET'); return false;">
        <i class="ico ico-sponsor mr-1"></i>{_p var='unsponsor_this_song'}
        </a>
        <a id="js_unsponsor_{$aSong.song_id}" {if $aSong.is_sponsor}style="display:none"{/if} href="#" onclick="$('#js_sponsor_phrase_{$aSong.song_id}').show(); $('#js_sponsor_{$aSong.song_id}').show();$('#js_unsponsor_{$aSong.song_id}').hide();$.ajaxCall('music.sponsorSong','song_id={$aSong.song_id}&type=1', 'GET'); return false;">
        <i class="ico ico-sponsor mr-1"></i>{_p var='sponsor_this_song'}
        </a>
    </li>
{elseif $aSong.canPurchaseSponsor}
    {if $aSong.is_sponsor}
    <li>
        <a href="#" onclick="$('#js_sponsor_phrase_{$aSong.song_id}').hide(); $('#js_sponsor_{$aSong.song_id}').hide();$('#js_unsponsor_{$aSong.song_id}').show();$.ajaxCall('music.sponsorSong','song_id={$aSong.song_id}&type=0', 'GET'); return false;">
            <i class="ico ico-sponsor"></i>
            {_p var='unsponsor_this_song'}
        </a>
    </li>
    {else}
    <li>
        <a href="{permalink module='ad.sponsor' id=$aSong.song_id}section_music_song/">
            <i class="ico ico-sponsor"></i>
            {_p var='sponsor_this_song'}
        </a>
    </li>
    {/if}
{/if}
{if $aSong.canDelete}
	<li class="item_delete"><a href="{url link='music.delete' id=$aSong.song_id}" class="sJsConfirm" data-message="{_p('are_you_sure_you_want_to_delete_this_song')}"><i class="ico ico-trash-alt-o mr-1"></i>{_p var='delete'}</a></li>
{/if}
{plugin call='music.template_block_entry_links_main'}