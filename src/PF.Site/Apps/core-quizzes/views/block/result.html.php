<?php
	defined('PHPFOX') or exit('NO DICE!'); 
?>

<h3 class="quiz_result_title mt-3 pb-1 mb-2">
    {if Phpfox::getUserId() == $iUserResult && Phpfox::isUser()}
        <span>
            <span class="text-uppercase">{_p var='your_results'}:&nbsp;</span><span class="txt-success"> {_p var='correct__l'} {$aQuiz.total_correct}/{$aQuiz.iTotalCorrectAnswers} <b>({$aQuiz.iSuccessPercentage}%)</b></span>
        </span>
    {else}
        <span>
            {if isset($aQuiz.takerInfo)}
                <span class="text-capitalize">{_p var='quiz_results_by'}</span>{$aQuiz.takerInfo.userinfo|user}<span class="txt-success"> {$aQuiz.total_correct}/{$aQuiz.iTotalCorrectAnswers} <b>({$aQuiz.iSuccessPercentage}%)</b></span>
            {else}
                {_p var='quiz_results_percentage' percentage=$aQuiz.iSuccessPercentage}
            {/if}
        </span>
    {/if}
    <a href="{permalink module='quiz' id=$aQuiz.quiz_id title=$aQuiz.title}" class="quiz_back"><i class="ico ico-arrow-left mr-1"></i>{_p var='back_to_list'}</a>
</h3>
<div class="quiz_result_items">
	{foreach from=$aQuiz.results name=questions item=aQuestion}	
		<div class="quiz_result_item">
			<div class="quiz_result_number mr-2 pr-3 pl-1">{$phpfox.iteration.questions}</div>
			<div class="quiz_result_answer">
				<div class="fw-bold quiz_result_title mb-1">{$aQuestion.questionText}</div>
				<div>
                    {if $aQuestion.userAnswer == $aQuestion.correctAnswer}
					    <div class="correct_answer"><span class="correct_answer_title">{_p var='your_answer_not_dot'}:</span> {$aQuestion.userAnswerText} <span class="txt-success fw-bold status">- {_p var='correct__u'}</span></div>
                    {else}
					    <div class="incorrect_answer"><span class="correct_answer_title">{_p var='your_answer_not_dot'}:</span> {if $aQuestion.userAnswer == 0}<span class="txt-warning">{$aQuestion.userAnswerText}</span>{else}{$aQuestion.userAnswerText}{/if} {if $aQuestion.userAnswer > 0}<span class="txt-danger fw-bold status">- {_p var='incorrect__u'}</span>{/if}</div>
					    <div class="correct_answer"><span class="correct_answer_title">{_p var='correct_answer'}:</span> {$aQuestion.correctAnswerText}</div>
                    {/if}
				</div>
			</div>
		</div>
	{/foreach}	
</div>
