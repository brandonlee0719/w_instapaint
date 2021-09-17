<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{$sCreateJs}
<div class="block">
    <div class="content">
        <ul class="signin_signup_tab clearfix">
            <li class="active"><a rel="hide_box_title" href="javascript:void(0)">{_p var='sign_in'}</a></li>
            {if Phpfox::getParam('user.allow_user_registration')}
            <li><a class="keepPopup" rel="hide_box_title" href="{url link='user.register'}">{_p var='sign_up'}</a></li>
            {/if}
        </ul>
        {plugin call='user.template_controller_login_block__start'}
        <form class="content" method="post" action="{url link="user.login"}" id="js_login_form" {if !empty($sGetJsForm)}onsubmit="{$sGetJsForm}"{/if}>
        <div class="table form-group">
            <div class="table_right">
                <input class="form-control" placeholder="{if Phpfox::getParam('user.login_type') == 'user_name'}{_p var='user_name'}{elseif Phpfox::getParam('user.login_type') == 'email'}{_p var='email'}{else}{_p var='email_or_user_name'}{/if}" type="{if Phpfox::getParam('user.login_type') == 'email'}email{else}text{/if}" name="val[login]" id="login" value="{$sDefaultEmailInfo}" size="40" autofocus/>
            </div>
            <div class="clear"></div>
        </div>


        <div class="form-group">
            <input class="form-control" placeholder="{_p var='password'}" type="password" name="val[password]" id="password" value="" size="40" autocomplete="off" />
        </div>

        {if $bEnable2StepVerification}
        <div class="form-group">
            <input class="form-control" placeholder="{_p var='passcode'}" type="text" name="val[passcode]" id="passcode" value="" size="40" />
            <p class="help-block">
                <a class="no_ajax" target="_blank" href="{url link='user.passcode'}">{_p var='dont_receive_passcode_how_to_get_it'}</a>
            </p>
        </div>
        {/if}

        {if Phpfox::isModule('captcha') && Phpfox::getParam('user.captcha_on_login')}
        <div id="js_register_capthca_image">
            {module name='captcha.form'}
        </div>
        {/if}

        {plugin call='user.template_controller_login_end'}

        <div class="form-group">
            <button id="_submit" type="submit" class="btn btn-primary text-uppercase">
                {_p var='sign_in'}
            </button>
            <div class="p_top_base checkbox">
                <ul class="clearfix">
                    <li><label><input type="checkbox" class="checkbox" name="val[remember_me]" value="" /> {_p var='remember'}</label></li>
                    <li><a class="no_ajax" href="{url link='user.password.request'}">{_p var='forgot_your_password'}</a></li>
                </ul>
            </div>

            {plugin call='user.template.login_header_set_var'}
            {if isset($bCustomLogin)}
            <div class="custom_login_fb">
                <div class="item-or-line"><span>{_p var='or'}</span></div>
                <div class="p_top_4">
                    {plugin call='user.template_controller_login_block__end'}
                </div>
            </div>
            {/if}
        </div>
        <input type="hidden" name="val[parent_refresh]" value="1" />
        </form>
        {if !PHPFOX_IS_AJAX}
        <script type="text/javascript">
            document.getElementById('js_login_form').getElementsByTagName("input")[0].focus()
        </script>
        {/if}
    </div>
</div>
