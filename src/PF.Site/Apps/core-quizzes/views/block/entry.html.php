<?php
	defined('PHPFOX') or exit('NO DICE!'); 
?>

{if isset($bIsViewingQuiz)}
	<div class="item_info pr-3">
	    {img user=$aQuiz suffix='_50_square'}
	    <div class="item_info_author">
	        <div>{_p var='by'} {$aQuiz|user:'':'':50}</div>
	        <div class="time">{$aQuiz.time_stamp|convert_time}</div>
	    </div>
	</div>

	<div class="item-comment mb-2">
        {if $aQuiz.view_id == 0}
            <div>
               {module name='feed.mini-feed-action'}
           </div>
       
       <span class="item-total-view">
           <span><b>{$aQuiz.total_view|short_number}</b> {if $aQuiz.total_view == 1}{_p('view')}{else}{_p('views_lowercase')}{/if}</span>
           <span><b>{$aQuiz.total_play|short_number}</b> {if $aQuiz.total_play == 1}{_p('quiz_total_play')}{else}{_p('quiz_total_plays')}{/if}</span>
       </span>
       {/if}
    </div>

	{if $aQuiz.hasPermission}
        <div class="item_bar">
            <div class="dropdown">
                <span role="button" data-toggle="dropdown" class="item_bar_action">
                    <i class="ico ico-gear-o"></i>
                </span>
                <ul class="dropdown-menu dropdown-menu-right">
                    {template file='quiz.block.link'}
                </ul>
            </div>
        </div>
	{/if}

	<div class="item-content">
        {if !empty($aQuiz.image_path)}
            <div class="item-media mr-2">
                <span style="background-image: url('{img thickbox=true server_id=$aQuiz.server_id title=$aQuiz.title path='quiz.url_image' file=$aQuiz.image_path suffix='' return_url=true}');"></span>
            </div>
        {/if}
        <div class="item_view_content">
            {$aQuiz.description|parse|shorten:200:'feed.view_more':true|split:55|max_line}
        </div>
    </div>

	{if (isset($aQuiz.view_id) && $aQuiz.view_id == 1)}
        {template file='core.block.pending-item-action'}
	{/if}
{/if}
		
<div id="js_quiz_{$aQuiz.quiz_id}" class="{if isset($phpfox.iteration.quizzes)} {if is_int($phpfox.iteration.quizzes/2)}row1{else}row2{/if}{if $phpfox.iteration.quizzes == 1} row_first{/if}{/if}">
	<div id="js_message_{$aQuiz.quiz_id}" style="display: none;"></div>	
	<div class="item_content">
        {if $aQuiz.total_attachment}
            {module name='attachment.list' sType=quiz iItemId=$aQuiz.quiz_id}
        {/if}
		{if isset($bShowResults) && $bShowResults}		
			{template file='quiz.block.result'}
		{elseif isset($bShowUsers) && $bShowUsers}
            <div class="quiz_user_title text-uppercase">{_p var='member_results'} <span>({$aQuiz.total_play|short_number})</span></div>
			<div class="quiz_user_lists js_quiz_user_lists" id="quiz_taken_by">
                {foreach from=$aQuiz.aTakenBy name=users item=aUser}
					<div class="quiz_user_list">
						<div class="quiz_user_list_left">
							<div class="quiz_user_list_number fw-bold">{$aUser.index}</div>
							<div class="quiz_user_image">
								{img user=$aUser.user_info suffix='_50_square' max_width=50 max_height=50}
								<div class="quiz_user_inner">
									{$aUser.user_info|user}
									<time>{$aUser.time_stamp|date:'core.global_update_time'}</time>
								</div>
							</div>
						</div>
						<div class="quiz_user_list_right">
							<div class="quiz_percentage fw-bold">{if (Phpfox::getParam('quiz.show_percentage_in_results'))}{$aUser.iSuccessPercentage}%{else}{$aUser.total_correct}/{$aUser.iTotalCorrectAnswers}{/if}</div>
							<div class="quiz_button ml-2"><a href="{permalink module='quiz' id=$aQuiz.quiz_id title=$aQuiz.title}results/id_{$aUser.user_info.user_id}/" id="quiz_inner_title_{$aQuiz.quiz_id}" class="button btn btn-default">{_p var='view_results'}</a></div>
						</div>
					</div>
				{foreachelse}
				    <div class="t_left">{_p var='be_the_first_to_answer_this_quiz'}</div>
			    {/foreach}
                {pager}
			</div>
		{else}
			{if isset($bIsViewingQuiz)}
				{if isset($aQuiz.question)}
					<form class="quiz-form" name="js_form_answer" method="post" action="{permalink module='quiz' id=$aQuiz.quiz_id title=$aQuiz.title}answer/">
							<div class="quiz_questions mb-3" >
								{foreach from=$aQuiz.question key=iQuestionId name=questions item=aQuestion}
									<div class="quiz_questions_inner {if isset($aAnswers) && !isset($aAnswers[$iQuestionId])}bg-danger{/if}">
										<div class="quiz_questions_nummberlist">{$phpfox.iteration.questions}</div>
										<div class="quiz_answers">
											<div class="quiz_answers_title fw-bold mb-1">{$aQuestion.question}</div>
											{foreach from=$aQuestion.answer key=iAnswerId name=answers item=sAnswer}
												<div class="quiz_answer">
													<div class="radio">
														<label>
															<input class="checkbox" {if !$bCanAnswer}disabled{/if} name="val[answer][{$iQuestionId}]" value="{$iAnswerId}" style="vertical-align: middle;" type="radio" {if isset($aAnswers[$iQuestionId]) && $aAnswers[$iQuestionId] == $iAnswerId}checked{/if}>
															<i class="ico ico-circle-o"></i>
															<span title="{$sAnswer}">{$sAnswer}</span>
														</label>
													</div>
												</div>
											{/foreach}
										</div>
									</div>
								{/foreach}
							</div>
						{if isset($aQuiz.view_id) && $aQuiz.view_id != 1 && $bCanAnswer}
							<button class="btn btn-primary">{_p var='submit_your_answers'}</button>
						{/if}
					</form>
				{/if}
			{/if}
		{/if}
		{if !isset($bIsViewingQuiz) && isset($aQuiz.aFeed)}
			{module name='feed.comment' aFeed=$aQuiz.aFeed}
		{/if}
	</div>		
</div>
