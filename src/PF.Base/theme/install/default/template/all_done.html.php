<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if $bIsUpgrade}
<h1>Upgrade Completed</h1>
{else}
<h1>Installation Completed</h1>
{/if}

{if $errors}
<div class="help-block">
    Some apps can't be installed. You should try installing them again in AdminCP later.
</div>
<div id="errors" class="alert alert-warning">
    {$errors}
</div>
{/if}
<div class="help-block">
    Your phpFox is {if $bIsUpgrade} upgraded {else} installed {/if} successfully. <br/>
    phpFox Team
</div>