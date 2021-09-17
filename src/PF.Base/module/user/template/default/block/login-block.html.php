<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{plugin call='user.template_controller_login_block__start'}
<form method="post" action="{url link="user.login"}">
	<div class="table form-group">
		<div class="table_right">
			<input placeholder="{if Phpfox::getParam('user.login_type') == 'user_name'}{_p var='user_name'}{elseif Phpfox::getParam('user.login_type') == 'email'}{_p var='email'}{else}{_p var='email_or_user_name'}{/if}" type="text" name="val[login]" id="js_email" value="" size="30" class="form-control" />
		</div>
	</div>


    <div class="form-group">
        <div class="input-group">
            <input placeholder="{_p var='password'}" type="password" name="val[password]" id="js_password" value="" size="30" class="form-control" autocomplete="off" />
            <span class="input-group-btn">
				<button class="btn btn-primary"><i class="fa fa-sign-in"></i></button>
			  </span>
        </div>
    </div>

	<div class="form-group">
		<div class="checkbox">
			<label><input type="checkbox" name="val[remember_me]" value="" class="checkbox" /> {_p var='remember'}</label>
		</div>
    </div>

    {plugin call='user.template.login_header_set_var'}
    {if isset($bCustomLogin)}
    <div class="form-group">
      {_p var='or_login_with'}:
        {plugin call='user.template_controller_login_block__end'}
    </div>
    {/if}
</form>
