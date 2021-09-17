<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
<div id="js_error_pages_login_user"></div>
<form method="post" action="#" onsubmit="$('#js_error_pages_login_user').show(); $(this).ajaxCall('pages.logBackUser'); return false;">
	<div class="table form-group">
		<div class="table_left">
			{_p var='password'}:
		</div>
		<div class="table_right">
			<input type="password" name="password" value="" size="40" autocomplete="off" />
			<div class="extra_info">
				{_p var='enter_the_password_used_to_log_into_the_account'}"{$aGlobalProfilePageLogin.full_name|clean}".
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<div class="table_clear">
		<input type="submit" class="button" value="{_p var='login'}" />
	</div>
</form>