<?php
/**
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		phpFox
 * @package  		Quiz
 * @version 		4.5.3
 *
 */
defined('PHPFOX') or exit('NO DICE!'); 
?>

{$sCreateJs}

<div class="main_break"></div>
<div style="display:none;" id="hiddenQuestion">
	<div id="js_quiz_layout_default">
		{template file="quiz.block.question"}
	</div>
</div>
<form method="post" action="{$sFormAction}" id="js_add_quiz_form" name="js_add_quiz_form" {if $bShowTitle}onsubmit="{$sGetJsForm}"{/if} {if Phpfox::getUserParam('quiz.can_upload_picture')}enctype="multipart/form-data"{/if}>
	<div id="js_custom_privacy_input_holder">
	{if isset($aQuiz.quiz_id)}
		{module name='privacy.build' privacy_item_id=$aQuiz.quiz_id privacy_module_id='quiz'}	
	{/if}
	</div>	
	{if isset($aQuiz.quiz_id)}
	  <input type="hidden" name="val[quiz_id]"  value="{$aQuiz.quiz_id}" />
	  <input type="hidden" name="quiz_id"  value="{$aQuiz.quiz_id}" />
	{/if}
    <input type="hidden" name="val[attachment]" class="js_attachment" value="{value type='input' id='attachment'}" />
	{if !$bShowTitle}<div style="display:none;">{/if}
		<div class="form-group">
            <label>{required}{_p var='title'}:</label>
            <input class="form-control close_warning" type="text" name="val[title]" value="{value type='input' id='title'}" id="title" maxlength="150" size="40" />
		</div>
		<div class="form-group">
            <label>{required}{_p var='description'}:</label>
			{editor id='description'}
		</div>
		
	{if !$bShowTitle}<div style="display:none;">{/if}
		{if Phpfox::getUserParam('quiz.can_upload_picture')}
            {if !empty($aQuiz.current_image) && !empty($aQuiz.quiz_id)}
                {module name='core.upload-form' type='quiz' current_photo=$aQuiz.current_image id=$aQuiz.quiz_id}
            {else}
                {module name='core.upload-form' type='quiz' }
            {/if}
		{/if}

		{if !$bShowTitle}
	</div>
	{/if}
<!--{* end of IF bShowTitle *}		-->
		
	{if !$bShowTitle}</div>{/if}
	{if !$bShowQuestions}<div style="display:none">{/if}
		<div id="js_quiz_container">
			{if isset($aQuiz.questions)}
			{foreach from=$aQuiz.questions item=Question name=question}
			{template file="quiz.block.question"}
			{/foreach}
			{else}
<!--			{* Not Editing *}-->
			{/if}			
		</div>
		
		<div class="quiz_add_new_question text-right form-group">
			<a href="#" id="js_add_question"><i class="ico ico-plus mr-1"></i>{_p var='add_another_question'}</a>
		</div>		
		
	{if !$bShowQuestions}</div>{/if}
	
		{if Phpfox::isModule('privacy')}
		<div class="form-group">
			<label>{_p var='privacy'}:</label>
			{module name='privacy.form' privacy_name='privacy' privacy_info='quiz.control_who_can_see_this_quiz' default_privacy='quiz.default_privacy_setting'}
		</div>
		{if Phpfox::isModule('comment')}
		<div class="table form-group-follow hidden">
			<div class="table_left">
				{_p var='comment_privacy'}:
			</div>
			<div class="table_right">	
				{module name='privacy.form' privacy_name='privacy_comment' privacy_info='quiz.control_who_can_comment_on_this_quiz' privacy_no_custom=true}
			</div>			
		</div>
		{/if}				
		{/if}	
	
<div class="table_clear">
			<ul class="table_clear_button">
				<li><input id="js_quiz_submit_button" type="submit" value="{if isset($aQuiz.quiz_id)}{_p var='update'}{else}{_p var='submit'}{/if}" class="button btn-primary"/></li>
			</ul>
			<div class="clear"></div>
		</div>
</form>
