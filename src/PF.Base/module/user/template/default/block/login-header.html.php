<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{if Phpfox_Module::instance()->getFullControllerName() != 'user.login'}
{plugin call='user.template.login_header_set_var'}
<div id="header_menu_login">
    {if isset($bCustomLogin)}
    <div id="header_menu_login_holder">
    {/if}
        <form method="post" action="{url link='user.login'}">
            <div class="header_menu_login_left">
                <div class="header_menu_login_label">{if Phpfox::getParam('user.login_type') == 'user_name'}{_p var='user_name'}{elseif Phpfox::getParam('user.login_type') == 'email'}{_p var='email'}{else}{_p var='email_or_user_name'}{/if}:</div>
                <div><input type="text" name="val[login]" value="" class="header_menu_login_input" tabindex="1" /></div>
                <div class="header_menu_login_sub">
                    <label><input type="checkbox" name="val[remember_me]" value="" checked="checked" tabindex="4" /> {_p var='keep_me_logged_in'}</label>
                </div>
            </div>
            <div class="header_menu_login_right">
                <div class="header_menu_login_label">{_p var='password'}:</div>
                <div><input type="password" name="val[password]" value="" class="header_menu_login_input" tabindex="2" autocomplete="off" /></div>
                <div class="header_menu_login_sub">
                    <a href="{url link='user.password.request'}">{_p var='forgot_your_password'}</a>
                </div>
            </div>
            <div class="header_menu_login_button">
                <input type="submit" value="{_p var='login_singular'}" tabindex="3" />
            </div>
        </form>
    {if isset($bCustomLogin)}
    </div>
    <div id="header_menu_login_custom">
        {if !Phpfox::getParam('user.force_user_to_upload_on_sign_up')}
            {_p var='or_login_with'}:
        {/if}
        {plugin call='user.template.login_header_custom'}
    </div>
    {/if}
</div>
{/if}