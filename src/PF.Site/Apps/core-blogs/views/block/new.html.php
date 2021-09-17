<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if !count($aBlogs)}
<div class="help-block">
    {_p var='no_blogs_have_been_added_yet'}
    <ul class="action">
        <li><a href="{url link='blog.add'}">{_p var='be_the_first_to_add_a_blog'}</a></li>
    </ul>
</div>
{else}
{if !PHPFOX_IS_AJAX}
<div class="item-collections item-collections-2">
    {/if}
    {foreach from=$aBlogs name=blogs item=aBlog}
    <div class="{if is_int($phpfox.iteration.blogs/2)}row1{else}row2{/if}{if $phpfox.iteration.blogs == 1} row_first{/if}"{if $phpfox.iteration.blogs == 1} style="padding-top:0;"{/if}>
    <div class="go_left" style="width:52px;">
        {img user=$aBlog max_width=50 max_height=50 suffix='_50' class='v_middle'}
    </div>
    <div style="margin-left:54px;">
        <a href="{url link=''$aBlog.user_name'.blog.'$aBlog.title_url''}">{$aBlog.title|clean}</a>
        <div class="extra_info">
            {$aBlog.posted_on}
        </div>
    </div>
    <div class="clear"></div>
</div>
{/foreach}
{if !PHPFOX_IS_AJAX}
</div>
{/if}
{/if}
