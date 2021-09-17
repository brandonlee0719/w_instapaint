<?php
    defined('PHPFOX') or exit('NO DICE!');
?>

<article>
    <div id="js_quiz_{$aQuiz.quiz_id}" class="item-outer {if empty($aQuiz.image_path)}no-photo{/if} {if ($aQuiz.is_sponsor && $aQuiz.is_featured) || ((isset($sView) && $sView == 'my' && $aQuiz.view_id) && $aQuiz.is_featured) || ($aQuiz.is_sponsor && (isset($sView) && $sView == 'my' && $aQuiz.view_id))}both-action{elseif $aQuiz.is_sponsor || $aQuiz.is_featured || (isset($sView) && $sView == 'my' && $aQuiz.view_id)}one-action{/if} ">
        <div class="item-media mr-2">
            {if !empty($aQuiz.image_path)}
                <a class="item-media-bg" href="{permalink module='quiz' id=$aQuiz.quiz_id title=$aQuiz.title}" itemprop="url"
               style="background-image: url('{img server_id=$aQuiz.server_id path='quiz.url_image' file=$aQuiz.image_path suffix='' return_url=true}')"></a>
            {/if}
            <div class="flag_style_parent">
                {if isset($sView) && $sView == 'my' && $aQuiz.view_id}
                <div class="sticky-label-icon sticky-pending-icon">
                    <span class="flag-style-arrow"></span>
                    <i class="ico ico-clock-o"></i>
                </div>
                {/if}
                {if $aQuiz.is_sponsor}
                <div class="sticky-label-icon sticky-sponsored-icon">
                    <span class="flag-style-arrow"></span>
                    <i class="ico ico-sponsor"></i>
                </div>
                {/if}
                {if $aQuiz.is_featured}
                <div class="sticky-label-icon sticky-featured-icon">
                    <span class="flag-style-arrow"></span>
                    <i class="ico ico-diamond"></i>
                </div>
                {/if}
            </div>
        </div>
        <div class="item-inner">
            <a href="{permalink module='quiz' id=$aQuiz.quiz_id title=$aQuiz.title}" class="item-title fw-bold" itemprop="url">{$aQuiz.title|clean}</a>
            <time><span>{_p var="By"}</span> {$aQuiz|user:'':'':15} {_p var="on"} {$aQuiz.time_stamp|convert_time:'core.global_update_time'}</time>
            <div class="item-statistic">
                <span>{$aQuiz.total_play|short_number} {if $aQuiz.total_play == 1}{_p('quiz_total_play')}{else}{_p('quiz_total_plays')}{/if}</span>
                <span>{if $aQuiz.total_view == 1}{_p('1_view')}{else}{$aQuiz.total_view|short_number} {_p('views_lowercase')}{/if}</span>
            </div>
        </div>
        {if !empty($bCanModerate)}
            <div class="moderation_row">
                <label class="item-checkbox">
                   <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aQuiz.quiz_id}" id="check{$aQuiz.quiz_id}" />
                   <i class="ico ico-square-o"></i>
               </label>
            </div>
        {/if}
        {if $aQuiz.hasPermission}
            <div class="item-option">
                <div class="dropdown">
                    <span role="button" class="row_edit_bar_action" data-toggle="dropdown">
                        <i class="ico ico-gear-o"></i>
                    </span>
                    <ul class="dropdown-menu dropdown-menu-right">
                        {template file='quiz.block.link'}
                    </ul>
                </div>
            </div>
        {/if}

        <div class="flag_style_parent hide">
            {if isset($sView) && $sView == 'my' && $aQuiz.view_id}
            <div class="sticky-label-icon sticky-pending-icon">
                <span class="flag-style-arrow"></span>
                <i class="ico ico-clock-o"></i>
            </div>
            {/if}
            {if $aQuiz.is_sponsor}
                <div class="sticky-label-icon sticky-sponsored-icon">
                    <span class="flag-style-arrow"></span>
                    <i class="ico ico-sponsor"></i>
                </div>
            {/if}
            {if $aQuiz.is_featured}
                <div class="sticky-label-icon sticky-featured-icon">
                    <span class="flag-style-arrow"></span>
                    <i class="ico ico-diamond"></i>
                </div>
            {/if}
        </div>
    </div>
</article>
