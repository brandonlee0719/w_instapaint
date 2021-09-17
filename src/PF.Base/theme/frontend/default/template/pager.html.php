<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author			Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: pager.html.php 5844 2013-05-09 08:00:59Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>

{if !isset($sPagingMode)}
{assign var='sPagingMode' value='loadmore'}
{/if}

{if !empty($bPopup)}
{if !empty($aPager.nextAjaxUrl)}
<div class="js_pager_popup_view_more_link">
	<a href="{$aPager.nextUrl}" class="button btn-small no_ajax_link" onclick="$.ajaxCall('{$sAjax}', 'page={$aPager.nextAjaxUrl}{$aPager.sParamsAjax}', 'GET'); return false;">
		{if !empty($aPager.icon)}
		{img theme=$aPager.icon class='v_middle'}
		{/if}
		{if !empty($aPager.phrase)}{$aPager.phrase}{else}{_p var='view_more'}{/if}
	</a>
</div>
{/if}
{elseif $sPagingMode == 'loadmore'}
<div class="js_pager_view_more_link">
	{if !empty($bIsAdminCp) && Phpfox::isAdminPanel() && empty($aAjaxPaging)}
	<div class="pager_view_more_holder">
		<div class="pager_view_more_link">
			{if !empty($aPager.nextAjaxUrl)}
			<a href="{$aPager.nextUrl}" class="pager_view_more no_ajax_link" onclick="$.ajaxCall('{$sAjax}', 'page={$aPager.nextAjaxUrl}{$aPager.sParamsAjax}', 'GET'); return false;">
				{if !empty($aPager.icon)}
				{img theme=$aPager.icon class='v_middle'}
				{/if}
				{if !empty($aPager.phrase)}{$aPager.phrase}{else}{_p var='view_more'}{/if}
				<span>{_p var='displaying_of_total' displaying=$aPager.displaying total=$aPager.totalRows}</span>
			</a>
			{/if}
		</div>
	</div>
	{elseif !empty($aAjaxPaging)}
    <div class="js_pager_buttons" data-block="{$aAjaxPaging.block}" data-content-container="{$aAjaxPaging.container}">
        <a class="ajax-paging" {if !empty($aPager.rel)}rel="{$aPager.rel}"{/if} data-params="{$aAjaxPaging.sParam}&page={$iNextPage}&type=loadmore" href="http://ajax-paging">
            {_p var='load_more'}
        </a>
    </div>
    {else}
	<a href="{$sNextUrl}" class="next_page" data-paging="{if isset($sPagingVar)}{$sPagingVar}{/if}">
		<i class="fa fa-spin fa-circle-o-notch"></i>
		<span>{_p var='load_more'}</span>
	</a>
	{/if}
</div>
{elseif !empty($aPagers)}
<div class="js_pager_buttons" {if !empty($aAjaxPaging)}data-block="{$aAjaxPaging.block}" data-content-container="{$aAjaxPaging.container}"{/if}>
    <ul class="pagination">
        {foreach from=$aPagers item=aPager}
        <li class="page-item {if !empty($aPager.attr)}{$aPager.attr}{/if}">
            {if !empty($aPager.attr) && ($aPager.attr == 'disabled')}
            <a class="page-link" href="javascript:void(0);" {if !empty($aPager.rel)}rel="{$aPager.rel}"{/if}>{$aPager.label}</a>
            {else}
            <a class="page-link{if !empty($aAjaxPaging)} ajax-paging{/if}" {if !empty($aPager.rel)}rel="{$aPager.rel}"{/if} {if !empty($aAjaxPaging)}data-params="{$aAjaxPaging.sParam}&page={$aPager.page_number}" href="http://ajax-paging"{else}href="{$aPager.link}"{/if}>{$aPager.label}</a>
            {/if}
        </li>
        {/foreach}
    </ul>
</div>
{/if}