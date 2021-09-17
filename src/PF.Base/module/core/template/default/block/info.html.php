<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
{foreach from=$aInfos key=sPhrase item=sValue}
<div class="info">
    <div class="info_left">
        {$sPhrase}:
    </div>
    <div class="info_right">
        {$sValue}
    </div>
</div>
{/foreach}