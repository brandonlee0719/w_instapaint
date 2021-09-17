<?php

defined('PHPFOX') or exit('NO DICE!');

?>
{foreach from=$aInfos key=sPhrase item=sValue}
<div class="item">
    <div class="item-label">
        {$sPhrase}:
    </div>
    <div class="item-value">
        {$sValue}
    </div>
</div>
{/foreach}