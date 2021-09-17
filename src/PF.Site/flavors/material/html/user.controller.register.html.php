<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{literal}
<script type="text/javascript">
    $Behavior.termsAndPrivacy = function()
    {
        $('#js_terms_of_use').click(function()
        {
            {/literal}
            tb_show('{_p var='terms_of_use' phpfox_squote=true phpfox_squote=true phpfox_squote=true phpfox_squote=true phpfox_squote=true phpfox_squote=true}', $.ajaxBox('page.view', 'height=410&width=600&title=terms'));
            {literal}
            return false;
        });

        $('#js_privacy_policy').click(function()
        {
            {/literal}
            tb_show('{_p var='privacy_policy' phpfox_squote=true phpfox_squote=true phpfox_squote=true phpfox_squote=true phpfox_squote=true phpfox_squote=true}', $.ajaxBox('page.view', 'height=410&width=600&title=policy'));
            {literal}
            return false;
        });



        $(document).ready(function () {
            function getParameterByName(name, url) {
                if (!url) url = window.location.href;
                name = name.replace(/[\[\]]/g, "\\$&");
                var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                    results = regex.exec(url);
                if (!results) return null;
                if (!results[2]) return '';
                return decodeURIComponent(results[2].replace(/\+/g, " ").replace('/', ''));
            }

            var partialOrderId = getParameterByName('partial_order_id');
            var userType = getParameterByName('user_type');

            // If partial order update URL query string:
            if (partialOrderId) {
                $('#js_form').attr( 'action', $('#js_form').attr('action') + '?partial_order_id=' + partialOrderId );

                // Set client as default user type:
                $('#custom_field_2').val(1);

                // Show message:
                $('#partial-order-message').css('display', 'block');

            } else if (userType == 'painter') {
                $('#js_form').attr( 'action', $('#js_form').attr('action') + '?user_type=' + userType );

                // Set client as default user type:
                $('#custom_field_2').val(2);
                // Set title:
                $('.header-page-title > a').html('Sign up (as painter)');

                // Show message:
                $('#sign-up-as-client-message').css('display', 'block');

            } else if (userType == 'client') {
                $('#js_form').attr( 'action', $('#js_form').attr('action') + '?user_type=' + userType );

                // Set client as default user type:
                $('#custom_field_2').val(1);

                // Set title:
                $('.header-page-title > a').html('Sign up (as client)');

                // Show message:
                $('#sign-up-as-painter-message').css('display', 'block');
            } else {
                $('div#js_register_step2').css('display', 'block');
            }

        });
    }
</script>

<style>
    div#js_register_step2 {
        display: none;
    }
</style>
{/literal}

{if !empty($message)}
<div class="extra_info message">{$message}</div>
{/if}

{if Phpfox_Module::instance()->getFullControllerName() == 'user.register' && Phpfox::isModule('invite')}
<div class="block">
    <div class="content">
        <div id="partial-order-message" style="font-size: 18px; margin-bottom: 20px; display: none;" class="alert alert-success"><strong>Your draft order has been created!</strong> Please sign up to complete payment.</div>
        <div id="sign-up-as-painter-message" style="font-size: 14px; margin-bottom: 20px; display: none;" class="alert alert-info">Not a customer? <a href="/user/register/?user_type=painter">Click here to sign up as painter.</a></div>
        <div id="sign-up-as-client-message" style="font-size: 14px; margin-bottom: 20px; display: none;" class="alert alert-info">Not a painter? <a href="/user/register/?user_type=client">Click here to sign up as customer.</a></div>
{/if}
    {if Phpfox::isModule('invite') && Invite_Service_Invite::instance()->isInviteOnly()}
        <div class="sign-up-invitation">
            <img src="{param var='core.path_actual'}PF.Site/flavors/material/assets/images/sign-up-invitation.jpg" alt="">

            <p class="help-block">
                {_p var='ssitetitle_is_an_invite_only_community_enter_your_email_below_if_you_have_received_an_invitation' sSiteTitle=$sSiteTitle}
            </p>


            <form method="post" class="form" action="{url link='user.register'}">
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-group-addon">
                            <span class="ico ico-at-sign"></span>
                        </div>
                        <input type="text" id="invite_email" class="form-control" placeholder="{_p var='your_email'}" name="val[invite_email]" value="" />
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">{_p var='submit_to_sign_up'}</button>
                </div>
            </form>
        </div>
        {else}
        {if isset($sCreateJs)}
            {$sCreateJs}
        {/if}
        <div id="js_registration_process" class="t_center" style="display:none;">
            <div class="p_top_8">
                {img theme='ajax/add.gif' alt=''}
            </div>
        </div>

        <div id="js_signup_error_message"></div>

        {if Phpfox::getParam('user.allow_user_registration')}
        <!-- <ul class="signin_signup_tab clearfix">
            <li><a class="keepPopup" rel="hide_box_title visitor_form" href="{url link='login'}">{_p var='sign_in'}</a></li>
            <li class="active"><a rel="hide_box_title visitor_form" href="javascript:void(0)">{_p var='sign_up'}</a></li>
        </ul> -->
        <div id="js_registration_holder">
            <form method="post" class="form" action="{url link='user.register'}" id="js_form" enctype="multipart/form-data">
                {token}
                <div id="js_signup_block">
                    {if isset($bIsPosted) || !Phpfox::getParam('user.multi_step_registration_form')}
                        <div>
                            {template file='user.block.register.step1'}
                            {template file='user.block.register.step2'}
                        </div>
                    {else}
                        {template file='user.block.register.step1'}
                    {/if}
                </div>

                {plugin call='user.template_controller_register_pre_captcha'}

                {if Phpfox::isModule('captcha') && Phpfox::getParam('user.captcha_on_signup')}
                <div id="js_register_capthca_image"{if Phpfox::getParam('user.multi_step_registration_form') && !isset($bIsPosted)} style="display:none;"{/if}>
                    {module name='captcha.form'}
                </div>
                {/if}

                {if Phpfox::getParam('user.new_user_terms_confirmation')}
                <div id="js_register_accept" class="register-accept-block form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="val[agree]" id="agree" value="1" {value type='checkbox' id='agree' default='1'}/>
                            {_p var='i_have_read_and_agree_to_the_a_href_id_js_terms_of_use_terms_of_use_a_and_a_href_id_js_privacy_policy_privacy_policy_a'}
                        </label>
                    </div>
                </div>
                {/if}

                <div class="form-button-group">
                    <div class="form-group">
                        {if isset($bIsPosted) || !Phpfox::getParam('user.multi_step_registration_form')}
                            <button type="submit" class="btn btn-primary" id="js_registration_submit">{_p var='sign_up_button'}</button>
                        {else}
                            <input type="button" value="{_p var='sign_up_button'}" class="btn btn-success text-uppercase" id="js_registration_submit" onclick="$Core.registration.submitForm();" />
                        {/if}
                    </div>
                    <div class="form-group already-member">
                        {_p var='i_m_already_member'}
                        {if !empty($bSlideForm)}
                            <a href="javascript:void(0);" class="js-slide-btn">{_p var='sign_in_now'}</a>
                        {else}
                            <a class="keepPopup" rel="hide_box_title visitor_form" href="{url link='login'}">{_p var='sign_in_now'}</a>
                        {/if}
                    </div>
                </div>

                {plugin call='user.template.register_header_set_var'}
                {if isset($bCustomLogin)}
                <div class="form-button-group form-login-custom-fb">
                    <div class="custom-fb-or"><span>{_p var='or'}</span></div>
                    <div class="custom_fb">
                        {plugin call='user.template_controller_register_block__end'}
                    </div>
                </div>
                {/if}
            </form>
        </div>
        {/if}
    {/if}

    {if Phpfox_Module::instance()->getFullControllerName() == 'user.register'}
    </div>
</div>
{/if}
