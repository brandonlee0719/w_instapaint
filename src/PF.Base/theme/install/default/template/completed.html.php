<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="completed_message">
    {if $bIsUpgrade}
    Successfully upgraded to phpFox version {$sUpgradeVersion}.
    {else}
    Successfully installed phpFox {$sUpgradeVersion}.
    {/if}
</div>