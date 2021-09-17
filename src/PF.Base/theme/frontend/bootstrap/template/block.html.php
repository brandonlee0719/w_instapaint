<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{if !isset($sHidden)}{assign var='sHidden' value=''}{/if}

{if (isset($sHeader) && (!PHPFOX_IS_AJAX || isset($bPassOverAjaxCall) || isset($bIsAjaxLoader))) || (defined("PHPFOX_IN_DESIGN_MODE") && PHPFOX_IN_DESIGN_MODE) || (Phpfox::getService('theme')->isInDnDMode())}

<div class="{$sHidden} block{if (defined('PHPFOX_IN_DESIGN_MODE') || Phpfox::getService('theme')->isInDnDMode()) && (!isset($bCanMove) || (isset($bCanMove) && $bCanMove == true ) )} js_sortable{/if}{if isset($sCustomClassName)} {$sCustomClassName}{/if}"{if isset($sBlockBorderJsId)} id="js_block_border_{$sBlockBorderJsId}"{/if}{if defined('PHPFOX_IN_DESIGN_MODE') && Phpfox_Module::instance()->blockIsHidden('js_block_border_' . $sBlockBorderJsId . '')} style="display:none;"{/if} data-toggle="{$sToggleWidth}">
	{if !empty($sHeader) || (defined("PHPFOX_IN_DESIGN_MODE") && PHPFOX_IN_DESIGN_MODE) || (Phpfox::getService('theme')->isInDnDMode())}
		<div class="title {if defined('PHPFOX_IN_DESIGN_MODE') || Phpfox::getService('theme')->isInDnDMode()}js_sortable_header{/if}">
		{if isset($sBlockTitleBar)}
			{$sBlockTitleBar}
		{/if}
		{if (isset($aEditBar) && Phpfox::isUser())}
			<div class="js_edit_header_bar">
				<a href="#" title="{_p var='edit_this_block'}" onclick="$.ajaxCall('{$aEditBar.ajax_call}', 'block_id={$sBlockBorderJsId}{if isset($aEditBar.params)}{$aEditBar.params}{/if}'); return false;">
					<i class="fa fa-edit"></i>
				</a>
			</div>
		{/if}
			{if empty($sHeader)}
				{$sBlockShowName}
			{else}
				{$sHeader}
			{/if}
		</div>
	{/if}
	{if isset($aEditBar)}
	<div id="js_edit_block_{$sBlockBorderJsId}" class="edit_bar hidden"></div>
	{/if}
	{if isset($aMenu) && count($aMenu)}
	{unset var=$aMenu}
	{/if}
	<div class="content"{if isset($sBlockJsId)} id="js_block_content_{$sBlockJsId}"{/if}>
{/if}
		{layout_content}



{if (isset($sHeader) && (!PHPFOX_IS_AJAX || isset($bPassOverAjaxCall) || isset($bIsAjaxLoader))) || (defined("PHPFOX_IN_DESIGN_MODE") && PHPFOX_IN_DESIGN_MODE) || (Phpfox::getService('theme')->isInDnDMode())}
	</div>
	{if isset($aFooter) && count($aFooter)}
	<div class="bottom">
		{if count($aFooter) == 1}
			{foreach from=$aFooter key=sPhrase item=sLink name=block}
			{if $sLink == '#'}
			{img theme='ajax/add.gif' class='ajax_image'}
			{/if}
            {if is_array($sLink)}
            <a class="btn btn-block btn-default{if !empty($sLink.class)} {$sLink.class}{/if}" href="{if !empty($sLink.link)}{$sLink.link}{else}#{/if}" {if !empty($sLink.attr)}{$sLink.attr}{/if} id="js_block_bottom_link_{$phpfox.iteration.block}">{$sPhrase}</a>
            {else}
			<a class="btn btn-block btn-default" href="{$sLink}" id="js_block_bottom_link_{$phpfox.iteration.block}">{$sPhrase}</a>
            {/if}
			{/foreach}
		{else}
		<ul>
			{foreach from=$aFooter key=sPhrase item=sLink name=block}
				<li id="js_block_bottom_{$phpfox.iteration.block}"{if $phpfox.iteration.block == 1} class="first"{/if}>
					{if $sLink == '#'}
						{img theme='ajax/add.gif' class='ajax_image'}
					{/if}
                    {if is_array($sLink)}
                    <a {if !empty($sLink.class)}class="{$sLink.class}{/if}" href="{if !empty($sLink.link)}{$sLink.link}{else}#{/if}" {if !empty($sLink.attr)}{$sLink.attr}{/if} id="js_block_bottom_link_{$phpfox.iteration.block}">{$sPhrase}</a>
                    {else}
					<a href="{$sLink}" id="js_block_bottom_link_{$phpfox.iteration.block}">{$sPhrase}</a>
                    {/if}
				</li>
			{/foreach}
		</ul>
		{/if}
	</div>
	{/if}
</div>
{/if}
{unset var=$sHeader var2=$sComponent var3=$aFooter var4=$sBlockBorderJsId var5=$bBlockDisableSort var6=$bBlockCanMove var7=$aEditBar var8=$sDeleteBlock var9=$sBlockTitleBar var10=$sBlockJsId var11=$sCustomClassName var12=$aMenu}