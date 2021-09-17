<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{foreach from=$aWidgetBlocks item=aWidgetBlock}
<div class="block">
	<div class="title">{$aWidgetBlock.title|clean}</div>
	<div class="content table-responsive">
        {$aWidgetBlock.text|parse|shorten:'300':'view_more':true}
    </div>
</div>
{/foreach}