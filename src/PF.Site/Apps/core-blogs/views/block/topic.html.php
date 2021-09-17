<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="blog_popular_topic">
    <ul class="clearfix">
        {foreach from=$aHotTags item=aHotTag}
        <li>
            <a href="{$aHotTag.tag_url}">#{$aHotTag.tag_text|clean|shorten:55:'...'|split:20}</a>
        </li>
        {/foreach}
    </ul>
</div>
