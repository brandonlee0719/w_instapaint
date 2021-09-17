<?php
    defined('PHPFOX') or exit('NO DICE!');
?>

{if $aQuiz.canApprove}
    <li><a href="javascript:void()" class="" onclick="$(this).hide(); $.ajaxCall('quiz.approve','iQuiz={$aQuiz.quiz_id}&amp;inline=true'); return false;"><i class="ico ico-check-square-alt mr-1"></i>{_p var='approve'}</a></li>
{/if}
{if ($aQuiz.canEdit)}
    <li><a href="{url link='quiz.add' id=$aQuiz.quiz_id}"><i class="ico ico-pencilline-o mr-1"></i>{_p var='edit'}</a></li>
{/if}
{if $aQuiz.canSponsorInFeed}
<li>
    {if $aQuiz.iSponsorInFeedId === true}
        <a href="{url link='ad.sponsor' where='feed' section='quiz' item=$aQuiz.quiz_id}">
            <i class="ico ico-sponsor mr-1"></i>{_p var='sponsor_in_feed'}
        </a>
    {else}
        <a href="javascript:void()" onclick="$.ajaxCall('ad.removeSponsor', 'type_id=quiz&item_id={$aQuiz.quiz_id}', 'GET'); return false;">
            <i class="ico ico-sponsor mr-1"></i>{_p var="Unsponsor In Feed"}
        </a>
    {/if}
</li>
{/if}
{if $aQuiz.canFeature}
    <li><a id="js_feature_{$aQuiz.quiz_id}"{if $aQuiz.is_featured} style="display:none;"{/if} href="javascript:void()" title="{_p var='feature'}" onclick="$('#js_featured_phrase_{$aQuiz.quiz_id}').hide(); $(this).hide(); $('#js_unfeature_{$aQuiz.quiz_id}').show(); $.ajaxCall('quiz.feature', 'quiz_id={$aQuiz.quiz_id}&amp;type=1'); return false;"><i class="ico ico-diamond mr-1"></i>{_p var='feature'}</a></li>
    <li><a id="js_unfeature_{$aQuiz.quiz_id}"{if !$aQuiz.is_featured} style="display:none;"{/if} href="javascript:void()" title="{_p var='un_feature'}" onclick="$('#js_featured_phrase_{$aQuiz.quiz_id}').show(); $(this).hide(); $('#js_feature_{$aQuiz.quiz_id}').show(); $.ajaxCall('quiz.feature', 'quiz_id={$aQuiz.quiz_id}&amp;type=0'); return false;"><i class="ico ico-diamond-o mr-1"></i>{_p var='unfeature'}</a></li>
{/if}
{if $aQuiz.canSponsor}
    <li>
            <a href="javascript:void()" id="js_sponsor_{$aQuiz.quiz_id}" class="{if $aQuiz.is_sponsor != 1}hide{/if}" onclick="$('#js_sponsor_phrase_{$aQuiz.quiz_id}').hide(); $('#js_sponsor_{$aQuiz.quiz_id}').hide();$('#js_unsponsor_{$aQuiz.quiz_id}').show();$.ajaxCall('quiz.sponsor','quiz_id={$aQuiz.quiz_id}&type=0', 'GET'); return false;">
                <i class="ico ico-sponsor mr-1"></i>{_p var='unsponsor_this_quiz'}
            </a>
            <a href="javascript:void()" id="js_unsponsor_{$aQuiz.quiz_id}" class="{if $aQuiz.is_sponsor == 1}hide{/if}" onclick="$('#js_sponsor_phrase_{$aQuiz.quiz_id}').show(); $('#js_sponsor_{$aQuiz.quiz_id}').show();$('#js_unsponsor_{$aQuiz.quiz_id}').hide();$.ajaxCall('quiz.sponsor','quiz_id={$aQuiz.quiz_id}&type=1', 'GET'); return false;">
                <i class="ico ico-sponsor mr-1"></i>{_p var='sponsor_this_quiz'}
            </a>
    </li>
{elseif $aQuiz.canPurchaseSponsor}
    <li>
        <a href="{permalink module='ad.sponsor' id=$aQuiz.quiz_id}section_quiz/">
            <i class="ico ico-sponsor mr-1"></i>{_p var='sponsor_this_quiz'}
        </a>
    </li>
{/if}
{if ($aQuiz.user_id == Phpfox::getUserId())}
    <li><a href="{permalink module='quiz' id=$aQuiz.quiz_id title=$aQuiz.title}results/""><i class="ico ico-eye mr-1"></i>{_p var='view_results'}</a></li>
{/if}
{if $aQuiz.canDelete}
    <li class="item_delete">
        <a href="javascript:void()" onclick="return $Core.quiz_moderate.deleteQuiz({$aQuiz.quiz_id}, '{if isset($bIsViewingQuiz) && $bIsViewingQuiz}viewing{else}browsing{/if}')"><i class="ico ico-trash-alt-o mr-1"></i>{_p var='delete'}</a>
    </li>
{/if}
{plugin call='quiz.template_block_entry_links_main'}