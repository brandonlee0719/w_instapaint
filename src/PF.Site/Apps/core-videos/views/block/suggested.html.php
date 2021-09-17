<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="item-container with-video video-suggested">
	<div class="videos-list">
    {foreach from=$aVideos item=aItem}
        {template file='v.block.entry_block'}
    {/foreach}
</div>
</div>
