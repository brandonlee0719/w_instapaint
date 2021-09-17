<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="form-group">
    <p class="help-block">
        {_p var='google_2step_verify_description'}
    </p>
    {if $sQRCodeUrl}
    <p class="help-block">
        {_p var='use_google_authencator_app_to_scan_this_qr_code'}
    </p>
    <div>
        <img src="{$sQRCodeUrl}" width="200" height="200" />
    </div>
    {/if}
</div>
{if $sQRCodeUrl ==''}
<form method="post" class="form">
    <div class="form-group">
        <label for="email">{_p var='enter_your_email'}</label>
        <input class="form-control" id="email" type="email" required name="val[email]" value="{$sEmail}" placeholder="{_p var='enter_your_email'}"/>
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-danger">
            {_p var='submit'}
        </button>
    </div>
</form>
{/if}