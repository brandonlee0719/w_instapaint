<?php
defined('PHPFOX') or exit('NO DICE!');

?>
<div class="item-container with-blog">
    <div class="sticky-label-icon sticky-featured-icon">
        <span class="flag-style-arrow"></span>
        <i class="ico ico-diamond"></i>
    </div>
    {foreach from=$aFeaturedBlogs item=aItem}
        {template file='blog.block.entry_block'}
    {/foreach}
</div>
