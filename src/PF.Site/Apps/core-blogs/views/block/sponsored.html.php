<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="item-container with-blog">
    <div class="sticky-label-icon sticky-sponsored-icon">
	    <span class="flag-style-arrow"></span>
	    <i class="ico ico-sponsor"></i>
	</div>
    {foreach from=$aSponsorBlogs item=aItem}
        {assign var=title_clean value=$aItem.title|clean}
        {template file='blog.block.entry_block'}
    {/foreach}
</div>
