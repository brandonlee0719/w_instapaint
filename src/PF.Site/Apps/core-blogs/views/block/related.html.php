<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="item-container with-blog">
    {foreach from=$aBlogs item=aItem}
        {template file='blog.block.entry_block'}
    {/foreach}
</div>
