<?php
defined('PHPFOX') or exit('NO DICE!');

?>
<div class="blog-feed {if $sImageSrc}has-image{/if}">
    {if $sImageSrc}
    <div class="blog-feed-image">
        <span style="background-image: url({$sImageSrc})"></span>
    </div>
    {/if}
    <div class="blog-feed-info">
        <div class="blog-title"><a href="{$sLink}">{$aItem.title}</a></div>
        <div class="blog-info-general">
            <span class="blog-datetime">{$aItem.time_stamp|convert_time:'core.global_update_time'}</span>
            {if !empty($sCategory)}
            <span class="blog-catgory">{_p var='category'}: {$sCategory}</span>
            {/if}
            <span class="blog-views">{$aItem.total_view|short_number} {if $aItem.total_view == 1}{_p var='view_lowercase'}{else}{_p var='views_lowercase'}{/if}</span>
        </div>
        <div class="blog-content item_content">{$aItem.text|stripbb|feed_strip|split:55|max_line}</div>
    </div>
</div>