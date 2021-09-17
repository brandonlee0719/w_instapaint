<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

<div id="admincp_login">
	<form method="post" action="{url link='current'}" class="form">
		<div class="adminp_login_body">
            <h3 class="admin_login_title">{_p var='admincp_login'}</h3>
            <div class="clearfix">
                {error}
                <div class="form-group">
                    <label for="admincp_login_email">{_p var='email'}</label>
                    <input required class="form-control" id="admincp_login_email" type="text" name="val[email]" value="{value id='email' type='input'}" placeholder="{_p var='email'}" />
                </div>
                <div class="form-group">
                    <label for="admincp_login_password">{_p var='password'}</label>
                    <input required type="password" id="admincp_login_password" name="val[password]" class="form-control" value="{value id='password' type='input'}" placeholder="{_p var='password'}" size="40" autocomplete="off"/>
                </div>
                <div class="form-group">
                    <button type="submit" id="admincp_btn_login" class="btn btn-danger">{_p var='login'}</button>
                    <a href="{url link=''}" class="no_ajax btn btn-link pull-right">{_p var='back_to_site'}</a>
                </div>
            </div>
		</div>
	</form>
</div>