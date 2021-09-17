<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="main_break">
	<form class="form" method="post" action="{url link='user.password.request'}" id="js_request_password_form">
		<div class="form-group">
            <label for="email">{_p var='email'}</label>
            <input class="form-control" type="text" name="val[email]" id="email" value="" size="40" />
		</div>
		{if Phpfox::isModule('captcha')}{module name='captcha.form' sType='lostpassword'}{/if}
		<div class="form-group">
			<input type="submit" value="{_p var='request_new_password'}" class="btn btn-danger" />
		</div>	
	</form>
</div>