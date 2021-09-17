<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Mail
 * @version 		$Id: view.html.php 3369 2011-10-28 16:04:06Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div id="js_main_mail_thread_holder">
	<div class="mail_messages">
		{if count($aMessages) >= 10}
		<a href="javascript:void(0)" id="js_mail_thread_view_more" class="mail_view_more" rel="{$aThread.thread_id}">{_p var='view_more'}</a>
		{/if}
		<div id="mail-pf-loading-message">
			<span class="l-1"></span>
			<span class="l-2"></span>
			<span class="l-3"></span>
			<span class="l-4"></span>
			<span class="l-5"></span>
			<span class="l-6"></span>
		</div>
		<div id="mail_threaded_view_more_messages"></div>
		{foreach from=$aMessages name=messages item=aMail}
			{template file='mail.block.entry'}
		{/foreach}
		<div id="mail_threaded_new_message"></div>
		<div id="mail_threaded_new_message_scroll"></div>
	</div>

	{literal}
	<script>
		function mailOnSuccess(e, obj) {
			$(".mail_messages").animate({ scrollTop: $('.mail_messages')[0].scrollHeight}, 400);
		};

		$Behavior.loadMessages = function() {
			$('.mail_thread_form_holder_inner textarea').focus();
			$('.mail_messages').scrollTop($('.mail_messages')[0].scrollHeight);

			var m = $('.mail_thread .js_box_title');
			var names = m.html();
			m.html('<span class="js_hover_title">' + names + '<span class="js_hover_info">' + names + '</span></span>');

			delete $Behavior.loadMessages;
		};
	</script>
	{/literal}
	<div class="mail_thread_form_holder">
		<div class="mail_thread_form_holder_inner">
			{if $bCanReplyThread}
			{$sCreateJs}
			<form method="post" action="{url link='mail.thread' id=$aThread.thread_id}" id="js_form_mail" class="ajax_post" data-callback="mailOnSuccess">
                <div id="js_mail_error"></div>
				<div><input type="hidden" name="val[thread_id]" value="{$aThread.thread_id}" /></div>
				<div><input type="hidden" name="val[attachment]" class="js_attachment" value="{value type='input' id='attachment'}" /></div>
				<div id="js_compose_new_message" class=instant_message">
                {if \Phpfox::getUserParam('mail.can_add_attachment_on_mail')}
				    {editor id='message' enter=true simple=true}
                {else}
                    {editor id='message' enter=true simple=true can_attach_file=false}
                {/if}
				</div>
			</form>
			{else}
			<div class="message">{_p var='can_not_reply_due_to_privacy_settings'}</div>
			{/if}
		</div>
	</div>
</div>