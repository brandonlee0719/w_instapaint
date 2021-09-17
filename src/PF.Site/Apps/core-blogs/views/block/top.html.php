<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{if count($aTopBloggers)}
<ul>
{foreach from=$aTopBloggers item=aTopBlogger key=index}
	<li>
        <div class="blog-row checkRow table_row {if $index == 0}blogger-1st{/if}">
            {if $index == 0 }
            {if !empty($aTopBlogger.cover_destination) || !empty($aTopBlogger.cover_default)}
            <div class="blogger-cover">
                <span style="background-image: url({if empty($aTopBlogger.cover_default)}{img return_url=true server_id=$aTopBlogger.cover_server_id path='photo.url_photo' file=$aTopBlogger.cover_destination suffix='_200'}{else}{$aTopBlogger.cover_default}{/if})"></span>
            </div>
            {else}
            <div class="blogger-cover no-image-cover">
                <span></span> 
            </div>
            {/if}
            <div class="blogger-info">
            <div class="blog-image">
                    <div class="blog-image-holder">
                        {img user=$aTopBlogger suffix='_50_square'}
                    </div>
                </div>
            <div class="blog-title">
                <div class="blog-title-inner-holder">
                    <header>
                        {$aTopBlogger|user}
                        {if $bDisplayBlogCount}
                        <div class="blog-user-details">
                            <time>{$aTopBlogger.top_total} {if $aTopBlogger.top_total == 1}{_p var='blog__l'}{else}{_p var='blogs__l'}{/if}</time>
                        </div>
                        {/if}
                    </header>
                </div>
            </div>
            </div>
            {else}
            <div class="blog-image">
                <div class="blog-image-holder">
                    {img user=$aTopBlogger suffix='_50_square'}
                </div>
            </div>
            
            <div class="blog-title">
                <div class="blog-title-inner-holder">
                    <header>
                        {$aTopBlogger|user}
                        {if $bDisplayBlogCount}
                        <div class="blog-user-details">
                            <time>{$aTopBlogger.top_total} {if $aTopBlogger.top_total == 1}{_p var='blog__l'}{else}{_p var='blogs__l'}{/if}</time>
                        </div>
                        {/if}
                    </header>
                </div>
            </div>
            {/if}
        </div>
    </li>
{/foreach}
</ul>
{/if}
