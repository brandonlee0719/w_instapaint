<div class="quizzes-app feed {if empty($aQuiz.image_path)}no-photo{/if}">
	<div class="quizzes-media">
        {if !empty($aQuiz.image_path)}
            <a class="item-media-bg" href="{permalink title=$aQuiz.title id=$aQuiz.quiz_id module='quiz'}"
               style="background-image: url({img server_id=$aQuiz.server_id path='quiz.url_image' file=$aQuiz.image_path suffix='' return_url=true})">
            </a>
        {/if}
		<span class="quizzes-vote-number {if $aQuiz.total_votes > 99}more{/if}"><b>{$aQuiz.total_play|short_number}</b>{if $aQuiz.total_play == 1}{_p('quiz_total_play')}{else}{_p('quiz_total_plays')}{/if}</span>
	</div>
	<div class="quizzes-inner pl-2 pr-2">
		<a href="{permalink title=$aQuiz.title id=$aQuiz.quiz_id module='quiz'}" class="quizzes-title fw-bold">{$aQuiz.title|clean}</a>
		<div class="quizzes-description item_view_content">{$aQuiz.description|stripbb|feed_strip|split:55|max_line}</div>
	</div>
</div>