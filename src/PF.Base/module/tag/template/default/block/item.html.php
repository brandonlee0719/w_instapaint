<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if isset($sTags) && !empty($sTags)}
{if $bIsInline}
 <span id="js_quick_edit_tag{$iItemId}">{if $bDontCleanTags}{$sTags}{else}{$sTags|clean|shorten:55:'...'|split:20}{/if}</span>
{else}
<div class="item_tag_holder">
	<span class="item_tag">
		{_p var='topics'}:
	</span>
	<span id="js_quick_edit_tag{$iItemId}"{if !empty($sMicroKeywords)} itemprop="{$sMicroKeywords}"{/if}>{foreach from=$aTags item=aTag name=tag}{if $phpfox.iteration.tag != 1}, {/if}<a href="{$aTag.tag_url}">{$aTag.tag_text|clean|shorten:55:'...'|split:20}</a>{/foreach}</span>
</div>
{/if}
{/if}
{unset var=$sTags}