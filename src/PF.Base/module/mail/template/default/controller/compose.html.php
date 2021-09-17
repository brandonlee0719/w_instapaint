<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{$sCreateJs}

<div id="js_ajax_compose_error_message"></div>
<div>
	<form class="form" method="post" action="{url link='mail.compose'}" id="js_form_mail">

	{if isset($iPageId)}
		<div><input type="hidden" name="val[page_id]" value="{$iPageId}"></div>
		<div><input type="hidden" name="val[sending_message]" value="{$iPageId}"></div>
	{/if}

	{token}
	<div><input type="hidden" name="val[attachment]" class="js_attachment" value="{value type='input' id='attachment'}" /></div>
	{if isset($bIsThreadForward) && $bIsThreadForward}
	<div><input type="hidden" name="val[forwards]" value="{$sThreadsToForward}" /></div>
	<div><input type="hidden" name="val[forward_thread_id]" value="{$sForwardThreadId}" /></div>
	{/if}
	{if PHPFOX_IS_AJAX && isset($aUser.user_id)}
	<div><input type="hidden" name="id" value="{$aUser.user_id}" /></div>
	<div><input type="hidden" name="val[to][]" value="{$aUser.user_id}" /></div>
	{else}
		<div class="form-group">
            {module name='friend.search-small' input_name='val[to]'}
		</div>
	{/if}
		{if !Phpfox::getParam('mail.threaded_mail_conversation')}
		<div class="form-group">
            <input type="text" name="val[subject]" id="subject" value="{if isset($iPageId)}{_p var='claiming_page_title' title=$aPage.title}{else}{value type='input' id='subject'}{/if}" size="40" style="width:400px;" tabindex="1" class="form-control"/>
		</div>
		{/if}
		<div class="form-group">
			<div id="js_compose_new_message">
                {if \Phpfox::getUserParam('mail.can_add_attachment_on_mail')}
				    {editor id='message' enter=true}
                {else}
                    {editor id='message' enter=true can_attach_file=false}
                {/if}
			</div>
		</div>
		
		{if Phpfox::isModule('captcha') && Phpfox::getUserParam('mail.enable_captcha_on_mail')}
			{module name='captcha.form' sType='mail'}
		{/if}
		
		<div class="table_clear" style="position:relative;">
			{if !Phpfox::getParam('mail.threaded_mail_conversation')}
			<div class="checkbox" style="position:absolute; right:0;">
				<label><input type="checkbox" name="val[copy_to_self]" value="1" class="v_middle" />{_p var='send_a_copy_to_myself'}</label>
			</div>
			{/if}
			{if isset($iPageId)}
			<div id="js_mail_compose_submit">
				<ul class="table_clear_button">
					<li><input id="submit_btn" type="submit" value="{_p var='submit'}" class="btn btn-primary" onclick="return submitClaimrequest(this);"/></li>
					<li class="table_clear_ajax"></li>
				</ul>
				<div class="clear"></div>
			</div>
			{/if}			
		</div>
	</form>
</div>

{if isset($sMessageClaim)}
	<script type="text/javascript">
		$('#js_compose_new_message #message').html('{$sMessageClaim}');
	</script>
{/if}
{literal}
<script>
	function submitClaimrequest(obj) {
		$Core.processForm('#js_mail_compose_submit');
		$(obj).parents('form:first').ajaxCall('mail.composeProcess', 'type=claim-page');
		return false;
	} 
	
	$Ready(function() {
		if ($('#js_compose_new_message #message').length) {
			if (!$('#js_mail_search_friend_placement').length) {
				$('#js_compose_new_message #message').focus();
			}
			$('#js_compose_new_message #message').parents('form:first').submit(function() {
			    if ($(this).closest('.js_box_content').length > 0)
                {
                    $Core.processForm('#js_mail_compose_submit');
                    $(this).ajaxCall('mail.composeProcess');
                    return false;
                }
			});
		}
	});
</script>
{/literal}

{if PHPFOX_IS_AJAX}
{literal}
<script>
	$Core.loadInit();
</script>
{/literal}
{/if}