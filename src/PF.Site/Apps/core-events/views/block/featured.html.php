<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<div class="event-featured-container event-mini-block-container">
	<div class="sticky-label-icon sticky-featured-icon">
        <span class="flag-style-arrow"></span>
       <i class="ico ico-diamond"></i>
    </div>
    <div class="event-mini-block-content">
{foreach from=$aFeatured item=aEvent name=featured}
    {template file='event.block.mini-entry'}
{/foreach}
</div>
</div>