<?php
    defined('PHPFOX') or exit('NO DICE!');
?>

<article>
    <div class="item-outer">
        {if !empty($aQuiz.image_path)}
            <div class="item-media">
                <a class="item-media-bg" href="{if isset($aQuiz.sponsor_id)}{url link='ad.sponsor' view=$aQuiz.sponsor_id}{else}{permalink module='quiz' id=$aQuiz.quiz_id title=$aQuiz.title}{/if}"
                   style="background-image: url({img server_id=$aQuiz.server_id path='quiz.url_image' file=$aQuiz.image_path suffix='' return_url=true})">
                </a>
            </div>
        {/if}
        <div class="item-inner">
            <a class="item-title" href="{if isset($aQuiz.sponsor_id)}{url link='ad.sponsor' view=$aQuiz.sponsor_id}{else}{permalink module='quiz' id=$aQuiz.quiz_id title=$aQuiz.title}{/if}">{$aQuiz.title|clean}</a>
            <div class="item-extra">
                <div class="item-author quizzes-text-overflow">{_p var="By"} {$aQuiz|user}</div>
                <div class="item-vote">{$aQuiz.total_play|short_number} {if $aQuiz.total_play == 1}{_p('quiz_total_play')}{else}{_p('quiz_total_plays')}{/if}</div>
            </div>
        </div>
    </div>
</article>