<?php
    defined('PHPFOX') or exit('NO DICE!');
?>

<article>
    <div class="item-outer">
        {if !empty($aPoll.image_path)}
            <div class="item-media">
                <a class="item-media-bg" href="{permalink title=$aPoll.question id=$aPoll.poll_id module='poll'}"
                   style="background-image: url({img server_id=$aPoll.server_id path='poll.url_image' file=$aPoll.image_path suffix='' return_url=true})">
                </a>
            </div>
        {/if}
        <div class="item-inner">
            <a class="item-title" href="{if isset($aPoll.sponsor_id)}{url link='ad.sponsor' view=$aPoll.sponsor_id}{else}{permalink module='poll' id=$aPoll.poll_id title=$aPoll.question}{/if}">{$aPoll.question|clean}</a>
            <div class="item-extra">
                <div class="item-author poll-text-overflow">{_p var="By"} {$aPoll|user}</div>
                <div class="item-vote">{$aPoll.total_votes|short_number} {if $aPoll.total_votes == 1}{_p('poll_total_vote')}{else}{_p('poll_total_votes')}{/if}</div>
            </div>
        </div>
    </div>
</article>