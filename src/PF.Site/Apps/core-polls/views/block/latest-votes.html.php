<?php
    defined('PHPFOX') or exit('NO DICE!');
?>

<div class="item-container poll-app lasted-vote">
    {foreach from=$aVotes name=votes item=aResult}
        <article>
            <div class="item-outer">
                <div class="item-media">
                    {img user=$aResult suffix='_50_square' max_width=50 max_height=50}
                </div>
                <div class="item-inner">
                    <div class="item-author">
                        {$aResult|user:'':'':15}&nbsp;{if $aResult.total_votes == 1}{_p var='voted_total_option' total=$aResult.total_votes|short_number}{else}{_p var='voted_total_options' total=$aResult.total_votes|short_number}{/if}:
                        <span>
                            {$aResult.answer|clean}
                        </span>
                    </div>
                    <time>{$aResult.time_stamp|date:'core.global_update_time'}</time>
                </div>
            </div>
        </article>
    {/foreach}
</div>