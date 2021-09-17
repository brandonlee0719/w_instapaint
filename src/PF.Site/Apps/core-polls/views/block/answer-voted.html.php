<?php
    defined('PHPFOX') or exit('NO DICE!');
?>
{if !$bIsPaging}
    <div class="js_poll_answer_voted">
        {/if}
            {if count($aAnswerVote)}
                <div class="poll-app poll-result-title"><i class="ico ico-check-circle-alt"></i>{if $bIsVoted}<strong>{_p var='you'}</strong>{/if}{if $iTotalVotes > 0 && $bIsVoted}<span>{_p var='and'}</span>{/if}{if $iTotalVotes > 0}<strong>{$iTotalVotes} {if $iTotalVotes == 1}{_p('person_lower')}{else}{_p('persons_lower')}{/if}</strong>{/if}<span>{_p var='voted_for'}</span>{$sAnswer}</div>
                <div class="item-container poll-app">
                    {foreach from=$aAnswerVote item=aVote}
                        <article>
                            <div class="item-outer">
                                <div class="item-media mr-1">
                                    {img user=$aVote suffix='_50_square'}
                                </div>
                                <div class="item-inner">
                                    <div class="poll-text-overflow">{$aVote|user:'':'':50}</div>
                                    <time>{_p var='voted_at'} {$aVote.time_stamp|date:'core.global_update_time'}</time>
                                </div>
                            </div>
                        </article>
                    {/foreach}
                </div>
                {pager}
            {else}
                {_p var='no_votes_to_show'}
            {/if}
        {if !$bIsPaging}
    </div>
{/if}