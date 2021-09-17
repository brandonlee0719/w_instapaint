<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="item-container with-video video-sponsored">
	<div class="sticky-label-icon sticky-sponsored-icon">
        <span class="flag-style-arrow"></span>
        <i class="ico ico-sponsor"></i>
    </div>
    <div class="videos-list">
    {foreach from=$aVideos item=aItem}
        {template file='v.block.entry_block'}
    {/foreach}
</div>
</div>
