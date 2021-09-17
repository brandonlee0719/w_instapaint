<?php

defined('PHPFOX') or exit('NO DICE!');

?>
<div class="event-mini-block-container">
    {foreach from=$events item=aEvent name=event}
        {template file='event.block.mini-entry'}
    {/foreach}
</div>