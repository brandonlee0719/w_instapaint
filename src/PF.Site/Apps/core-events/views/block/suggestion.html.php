<?php
/**
 * [PHPFOX_HEADER]
 *
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<div class="event-suggestion-container event-mini-block-container">
{foreach from=$aSuggestion item=aEvent name=suggest}
    {template file='event.block.mini-entry'}
{/foreach}
</div>