<?php
	defined('PHPFOX') or exit('NO DICE!'); 
?>

<div class="item-container quizzes-widget-block quizzes-app">
	<div class="sticky-label-icon sticky-sponsored-icon r-2">
	    <span class="flag-style-arrow"></span>
	   <i class="ico ico-sponsor"></i>
	</div>
	{foreach from=$aSponsorQuizzes item=aQuiz}
		{template file='quiz.block.mini'}
	{/foreach}
</div>