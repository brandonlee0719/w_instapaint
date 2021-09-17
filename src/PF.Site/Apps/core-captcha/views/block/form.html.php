<?php

defined('PHPFOX') or exit('NO DICE!');

?>
{if isset($bCaptchaPopup) && $bCaptchaPopup}
<div id="js_captcha_load_for_check">
    <form method="post" action="#" id="js_captcha_load_for_check_submit" class="form">
        {/if}
        <div class="form-group">
            {if $sCaptchaType =='default'}
            <div class="captcha_title">{_p var='captcha_challenge'}</div>
            <div class="go_left">
                <a href="#" onclick="$('#js_captcha_process').html($.ajaxProcess('{_p var='refreshing_image' phpfox_squote=true}')); $('#js_captcha_image').ajaxCall('captcha.reload', 'sId=js_captcha_image&amp;sInput=image_verification'); return false;"><img src="{$sImage}" alt="{_p var='reload_image'}" id="js_captcha_image" class="captcha" title="{_p var='click_refresh_image'}" /></a>
            </div>
            <a href="#" onclick="$('#js_captcha_process').html($.ajaxProcess('{_p var='refreshing_image' phpfox_squote=true}')); $('#js_captcha_image').ajaxCall('captcha.reload', 'sId=js_captcha_image&amp;sInput=image_verification'); return false;" title="{_p var='click_refresh_image' phpfox_squote=true}">{img theme='misc/reload.gif' alt='Reload'}</a>
            <span id="js_captcha_process"></span>
            <div class="clear"></div>
            <div class="captcha_form">
                <input class="form-control" type="text" name="val[image_verification]" size="10" id="image_verification" />
                <div class="help-block">
                    {_p var='type_verification_code_above'}
                </div>
            </div>
            <script type="text/javascript">
              $Behavior.loadImageVerification = function(){l}
              $('#image_verification').attr('autocomplete', 'off');
              {r}
            </script>
            {elseif $sCaptchaType == 'qrcode'}
            <div class="captcha_title">{_p var='captcha_challenge'}</div>
            <div class="">
                <a type="button">
                    <img src="{$sImage}" class="captcha"/>
                </a>
            </div>
            <div class="">
                <div class="captcha_extra_info">{_p var='captcha_qrcode_challenge'}</div>
                <input class="form-control" type="text" name="val[image_verification]" size="10" id="image_verification" />
                <div class="captcha_extra_info">
                    {_p var='type_verification_code_above'}
                </div>
            </div>
            {elseif $sCaptchaType == 'recaptcha'}
            {literal}
            <script type="text/javascript">
              $Behavior.onLoadEvents = function () {
                $Core.loadRecaptchaApi();
              }
            </script>
            {/literal}
            <div class="g-recaptcha" data-sitekey="{$sRecaptchaPublicKey}"></div>
            {/if}

        </div>
        {if isset($bCaptchaPopup) && $bCaptchaPopup}
        <div class="form-group">
            <input type="submit" value="{_p var='submit'}" class="btn btn-primary" />
            <input type="button" value="{_p var='cancel'}" class="btn btn-default" onclick="$('#js_captcha_load_for_check').hide();isAddingComment = false;" />
        </div>
    </form>
</div>
{/if}