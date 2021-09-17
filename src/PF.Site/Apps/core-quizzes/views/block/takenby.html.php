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
{/foreach}
{pager}
