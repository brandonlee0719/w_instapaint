<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: pager.html.php 3588 2011-11-28 08:28:21Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if isset($aPager) && $aPager.totalPages > 1}
	<div class="pager_outer">
       <ul class="pagination">
            {if !isset($bIsMiniPager)}
            <li class="pager_total hide">{_p var='page_x_of_x' current=$aPager.current total=$aPager.totalPages}</li>
            {/if}
            {if isset($aPager.firstUrl)}<li class="first"><a {if $sAjax}href="{$aPager.firstUrl}" onclick="$(this).parent().parent().parent().parent().find('.sJsPagerDisplayCount').html($.ajaxProcess('{_p var='loading'}')); $.ajaxCall('{$sAjax}', 'page={$aPager.firstAjaxUrl}{$aPager.sParams}'); $Core.addUrlPager(this); return false;"{else}href="{$aPager.firstUrl}"{/if}>{_p var='first'}</a></li>{/if}
            {if isset($aPager.prevUrl)}<li><a {if $sAjax}href="{$aPager.prevUrl}" onclick="$(this).parent().parent().parent().parent().find('.sJsPagerDisplayCount').html($.ajaxProcess('{_p var='loading'}')); $.ajaxCall('{$sAjax}', 'page={$aPager.prevAjaxUrl}{$aPager.sParams}'); $Core.addUrlPager(this); return false;"{else}href="{$aPager.prevUrl}"{/if}>{_p var='previous'}</a></li>{/if}
			{foreach from=$aPager.urls key=sLink name=pager item=sPage}
				<li {if !isset($aPager.firstUrl) && $phpfox.iteration.pager == 1} class="first"{/if}><a {if $sAjax}href="{$sLink}" onclick="{if $sLink}$(this).parent().parent().parent().parent().find('.sJsPagerDisplayCount').html($.ajaxProcess('{_p var='loading'}')); $.ajaxCall('{$sAjax}', 'page={$sPage}{$aPager.sParams}'); $Core.addUrlPager(this);{/if} return false;{else}href="{if $sLink}{$sLink}{else}javascript:void(0);{/if}{/if}"{if $aPager.current == $sPage} class="active"{/if}>{$sPage}</a></li>
			{/foreach}
            {if isset($aPager.nextUrl)}<li><a {if $sAjax}href="{$aPager.nextUrl}" onclick="$(this).parent().parent().parent().parent().find('.sJsPagerDisplayCount').html($.ajaxProcess('{_p var='loading'}')); $.ajaxCall('{$sAjax}', 'page={$aPager.nextAjaxUrl}{$aPager.sParams}'); $Core.addUrlPager(this); return false;"{else}href="{$aPager.nextUrl}"{/if}>{_p var='next'}</a></li>{/if}
            {if isset($aPager.lastUrl)}<li><a {if $sAjax}href="{$aPager.lastUrl}" onclick="$(this).parent().parent().parent().parent().find('.sJsPagerDisplayCount').html($.ajaxProcess('{_p var='loading'}')); $.ajaxCall('{$sAjax}', 'page={$aPager.lastAjaxUrl}{$aPager.sParams}'); $Core.addUrlPager(this); return false;"{else}href="{$aPager.lastUrl}"{/if}>{_p var='last'}</a></li>{/if}
        </ul>
	</div>
{/if}