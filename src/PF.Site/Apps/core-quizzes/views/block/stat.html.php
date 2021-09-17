<?php
	defined('PHPFOX') or exit('NO DICE!'); 
?>

{if count($aQuizTakers)}
	<div class="item-container quizzes-app taken-recent">
		{foreach from=$aQuizTakers key=iLatestUser item=aQuizTaker}
			<article>
				<div class="item-outer">
					<div class="item-media">
                        {if isset($aQuizTaker.user_info)}
                            {img user=$aQuizTaker.user_info suffix='_50_square' max_width=50 max_height=50}
                        {else}
                            {img user=$aQuizTaker suffix='_50_square' max_width=50 max_height=50}
                        {/if}
	                </div>
	                <div class="item-inner">
	                    <div class="item-percent fw-bold">
	                    	{if (Phpfox::getParam('quiz.show_percentage_in_track'))}{$aQuizTaker.iSuccessPercentage}%{else}{$aQuizTaker.iUserCorrectAnswers} / {$aQuizTaker.total_correct}{/if}
	                    	<a class="item-view" href="{permalink module='quiz' id=$aQuizTaker.user_info.quiz_id title=$aQuizTaker.user_info.title}results/id_{if isset($aQuizTaker.user_info)}{$aQuizTaker.user_info.user_id}{else}{$aQuizTaker.user_id}{/if}/">{_p var='view'}</a>
	                    </div>
	                    <div class="item-author">
	                    	{_p var='by'}&nbsp;{if isset($aQuizTaker.user_info)}{$aQuizTaker.user_info|user}{else}{$aQuizTaker|user}{/if}
	                    </div>
	                </div>
				</div>
			</article>
		{/foreach}
	</div>
{else}
	<div class="extra_info">
		{_p var='be_the_first_to_answer_this_quiz'}
	</div>
{/if}