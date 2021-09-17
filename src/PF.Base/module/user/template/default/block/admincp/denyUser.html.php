<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
<div id="sFeedbackDeny" class="public_message"></div>
{_p var='you_are_about_to_deny_user', link=$aUser.link user_name=$aUser.user_name}

<div class="table form-group">
	<div class="table_above">
		{_p var='subject'}:
	</div>
	<div class="table_below">
        <input type="text" size="30" id="denySubject" name="denySubject" value="{left_curly}phrase var='user.deny_mail_subject'{right_curly}">
	</div>
	<div class="clear">	</div>
</div>
<div class="table form-group">
	<div class="table_above">
		{_p var='message'}:
	</div>
	<div class="table_below">
        <textarea name="denyMessage" id="denyMessage" cols="30" rows="3">{left_curly}phrase var='user.deny_mail_message'{right_curly}</textarea>
	</div>
	<div class="clear"></div>
</div>
<div class="table_clear">
	<a href="#" onclick="$.ajaxCall('user.denyUser', 'sSubject='+$('#denySubject').val()+'&sMessage='+$('#denyMessage').val()+'&iUser={$aUser.user_id}&doReturn=0');return false;">
		{_p var='deny_and_send_email'}</a>
	- <a href="#" onclick="$.ajaxCall('user.denyUser', 'sSubject='+$('#denySubject').val()+'&sMessage='+$('#denyMessage').val()+'&iUser={$aUser.user_id}&doReturn=1');return false;">{_p var='deny_without_email'}</a>
	{_p var='or'}
    <input type="button" onclick="tb_remove();" value="{_p var='cancel'}" class="btn btn-default">
</div>
