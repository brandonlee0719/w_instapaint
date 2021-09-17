<?php
	defined('PHPFOX') or exit('NO DICE!'); 
?>

<div class="item-container quizzes-widget-block quizzes-app">
	<div class="sticky-label-icon sticky-featured-icon r-2">
	    <span class="flag-style-arrow"></span>
	   <i class="ico ico-diamond"></i>
	</div>
	{foreach from=$aFeaturedQuizzes item=aQuiz}
		{template file='quiz.block.mini'}
	{/foreach}
</div>