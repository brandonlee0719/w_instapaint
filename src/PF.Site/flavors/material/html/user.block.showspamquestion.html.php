<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
{foreach from=$aQuestions item=aQuestion}
	<div class="form-group">
		<label>
			{$aQuestion.question_phrase|convert}:
		</label>
		<div>
			{if isset($aQuestion.image_path) && !empty($aQuestion.image_path)}
                {img server_id=$aQuestion.server_id path='user.url_user_spam' class='img-responsive' file=$aQuestion.image_path}
            {/if}
			<div {if isset($aQuestion.image_path) && !empty($aQuestion.image_path)}class="mt-2"{/if}>
				<input type="text" name="val[spam][{$aQuestion.question_id}]" value="" placeholder="{_p var='your_answer'}" />
			</div>
		</div>
	</div>
{/foreach}