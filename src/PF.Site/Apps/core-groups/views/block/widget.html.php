<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{foreach from=$aWidgetBlocks item=aWidgetBlock}
<div class="block">
    <div class="title">{$aWidgetBlock.title|clean}</div>
    <div class="content">
        {$aWidgetBlock.text|parse|shorten:'300':'view_more':true|split:30|max_line}
    </div>
</div>
{/foreach}