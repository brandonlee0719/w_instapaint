<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if !count($aBlogs)}
    {if !PHPFOX_IS_AJAX}
        <div class="extra_info">
            {_p var='no_blogs_found'}
        </div>
    {/if}
{else}
    {if !PHPFOX_IS_AJAX}
        <div class="item-container with-blog blog-listing" id="container-blog">
    {/if}
    {foreach from=$aBlogs name=blog item=aItem}
        {template file='blog.block.entry'}
    {/foreach}
    {pager}
    {if $bShowModerator}
        {moderation}
    {/if}
    {if !PHPFOX_IS_AJAX}
        </div>
    {/if}
{/if}
