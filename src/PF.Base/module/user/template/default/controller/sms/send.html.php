<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{if empty($bFail) }
{if $iStep==1}
<form method="post" class="form">
    <div class="form-group">
        <label for="verify_phone">
                {_p var='enter_your_phone'}</label>
        <p class="help-block">e.g +1 999 33 4455</p>
        <input id="verify_phone" class="form-control" type="tel" name="val[phone]" value="" required/>
    </div>
    {if $sEmail==''}
    <div class="form-group">
        <label for="verify_email">{_p var='enter_your_email'}</label>
        <p class="help-block">{_p var='enter_your_registered_email'}</p>
        <input id="verify_email" class="form-control" type="email" name="val[email]" value="" required/>
    </div>
    {/if}
    <div class="form-group">
        <input type="submit" name="val[publish]" value="{_p var='get_token'}" class="btn btn-primary">
    </div>
</form>
{literal}
<script>
    $Behavior.buildPhoneNumber = function(){
        if($("#mobile-phone").length){
            $("#mobile-phone").intlTelInput({
                allowDropdown: true,
                autoPlaceholder: true,
                autoHideDialCode: false,
            });
        }
    }
</script>
{/literal}
{/if}

{if $iStep==2 || $iStep==3}
<form method="post">
    <input type="hidden" name="val[phone]" value="{$sPhone}">
    <div class="form-group">
        <label>{_p var="enter_a_verification_code"}</label>
        <p class="help-block">{_p var='text_message_was_sent_to_to_phone'}</p>
        <input type="text" class="form-control" name="val[verify_sms_token]" value="" />
    </div>
    <div class="form-group">
        <input type="submit" name="val[publish]" value="{_p var='done'}" class="btn btn-primary">
        <input type="submit" name="val[resend_passcode]" value="{_p var='resend_passcode'}" class="btn">
    </div>
</form>
{/if}
{/if}