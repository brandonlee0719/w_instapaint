<?php
	defined('PHPFOX') or exit('NO DICE!'); 
?>

{if isset($aQuiz)}	
	<div class="item_view quizzes-app">
		{template file='quiz.block.entry'}

	    {addthis url=$aQuiz.bookmark title=$aQuiz.title description=$sShareDescription}
	    
		<div id="js_comment_module" {if $aQuiz.view_id == 1}style="display:none;" class="js_moderation_on"{/if}>
			<div class="item-detail-feedcomment">{module name='feed.comment'}</div>
		</div>	
	</div>
{else}
	<div class="extra_info">
		{_p var='the_link_that_brought_you_here_is_wrong'}
		<ul class="action">
			<li><a href="{$sRealLink}">{_p var='click_here_to_get_the_quiz_from_the_real_owner'}</a></li>
		</ul>
	</div>
{/if}