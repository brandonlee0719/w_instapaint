<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{assign var='bSlideForm' value=1}

{if $bAllowRegistration}
<div class="js-slide-visitor-form sign-up" data-title="{ _p var='sign_up' }">
    {template file='user.controller.register'}
</div>
{/if}

<div class="js-slide-visitor-form sign-in" {if $bAllowRegistration}style="display: none"{/if} data-title="{ _p var='login_title' }">
    {template file='user.controller.login'}
</div>