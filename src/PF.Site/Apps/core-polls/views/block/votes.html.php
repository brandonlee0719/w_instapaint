<?php
    defined('PHPFOX') or exit('NO DICE!');
?>

{if !$bIsPaging}
    <div class="item-container js_poll_view_results poll-app view-all">
        {/if}
            {if count($aVotes)}
                {foreach from=$aVotes name=votes item=aResult}
                    <article>
                        <div class="item-outer">
                            <div class="item-media mr-1">
                                {img user=$aResult suffix='_50_square' max_width=50 max_height=50}
                            </div>
                            <div class="item-inner">
                                <div class="item-author">
                                    {$aResult|user}&nbsp;{if $aResult.total_votes == 1}{_p var='voted_total_option' total=$aResult.total_votes|short_number}{else}{_p var='voted_total_options' total=$aResult.total_votes|short_number}{/if}:
                                    <span>
                                        {$aResult.answer|shorten:150:'feed.view_more':true|split:55|max_line}
                                    </span>
                                </div>
                                <time>{$aResult.time_stamp|date:'core.global_update_time'}</time>
                            </div>
                        </div>
                    </article>
                {/foreach}
                {pager}
            {/if}
        {if !$bIsPaging }
    </div>
{/if}