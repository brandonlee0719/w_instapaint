<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<div class="event-sponsored-container event-mini-block-container">
	<div class="sticky-label-icon sticky-sponsored-icon">
        <span class="flag-style-arrow"></span>
       <i class="ico ico-sponsor"></i>
    </div>
    <div class="event-mini-block-content">
{foreach from=$aSponsorEvents item=aEvent name=sponsor}
    {template file='event.block.mini-entry'}
{/foreach}
</div>
</div>