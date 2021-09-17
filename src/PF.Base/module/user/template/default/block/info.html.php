<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if $sLocation}
<div class="user-location" title="{$sLocation}">
    {$sLocation}
</div>
{/if}

{if $sGender}
<div class="user-gender" title="{$sGender}">
    {$sGender}
</div>
{/if}

{if $sJoined}
<div class="user-joined" title="{_p var='joined'}: {$sJoined}">
    {_p var='joined'}: {$sJoined}
</div>
{/if}
