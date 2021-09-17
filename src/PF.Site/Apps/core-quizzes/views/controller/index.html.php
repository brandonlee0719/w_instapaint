<?php 
	defined('PHPFOX') or exit('NO DICE!'); 
?>

{if count($aQuizzes)}
	{if !PHPFOX_IS_AJAX}<div class="item-container quizzes-app main">{/if}
		{foreach from=$aQuizzes name=quizzes item=aQuiz}
		    {template file='quiz.block.rows'}
		{/foreach}
	{pager}
	{if !PHPFOX_IS_AJAX}</div>{/if}
	{if $bCanModerate}
	    {moderation}
	{/if}
	{else}
		{if !PHPFOX_IS_AJAX}
			<div class="extra_info">
				{_p var='no_quizzes_found'}
			</div>
		{/if}
{/if}
