<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="item-container with-video video-featured">
	<div class="sticky-label-icon sticky-featured-icon">
        <span class="flag-style-arrow"></span>
        <i class="ico ico-diamond"></i>
    </div>
    <div class="videos-list">
    {foreach from=$aVideos item=aItem}
        {template file='v.block.entry_block'}
    {/foreach}
	</div>
</div>
