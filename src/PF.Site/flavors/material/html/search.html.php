<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{if !defined('PHPFOX_IS_FORCED_404') && !empty($aSearchTool) && is_array($aSearchTool)}
	<div class="header_bar_menu">
		{if isset($aSearchTool.search)}
		<div class="header_bar_search">
			<form id="form_main_search" class="" method="GET" action="{$aSearchTool.search.action|clean}" onbeforesubmit="$Core.Search.checkDefaultValue(this,\'{$aSearchTool.search.default_value}\');">
				<div class="hidden">
					{if (isset($aSearchTool.search.hidden))}
					{$aSearchTool.search.hidden}
					{/if}
				</div>
				<div class="header_bar_search_holder form-group has-feedback">
					<div class="header_bar_search_inner">
						<div class="input-group" style="width: 100%">

							<input type="search" class="form-control" name="search[{$aSearchTool.search.name}]" value="{if isset($aSearchTool.search.actual_value)}{$aSearchTool.search.actual_value|clean}{/if}" placeholder="{$aSearchTool.search.default_value}" />
							<a class="form-control-feedback" data-cmd="core.search_items">
								<i class="ico ico-search-o"></i>
							</a>
						</div>
					</div>
				</div>
				<div id="js_search_input_holder">
					<div id="js_search_input_content right">
						{if isset($sModuleForInput)}
						{module name='input.add' module=$sModuleForInput bAjaxSearch=true}
						{/if}
					</div>
				</div>
			</form>
		</div>
		{/if}

        {if !empty($aBreadCrumbTitle)}
        <h1 class="header-page-title {if empty($aBreadCrumbTitle[2])}item-title{/if}">
            <a href="{ $aBreadCrumbTitle[1]}" class="ajax_link" rel="nofollow">{ $aBreadCrumbTitle[0] }</a>
        </h1>
        {/if}

		{if isset($aSearchTool.filters) && count($aSearchTool.filters)}
		<div class="header-filter-holder">
			{foreach from=$aSearchTool.filters key=sSearchFilterName item=aSearchFilters name=fkey}
			{if !isset($aSearchFilters.is_input) && count($aSearchFilters.data)}
			<div class="filter-options">
				<a class="dropdown-toggle" data-toggle="dropdown">
					<!--<span class="">{$sSearchFilterName}:</span>-->
					<span>{if isset($aSearchFilters.active_phrase)}{$aSearchFilters.active_phrase}{else}{$aSearchFilters.default_phrase}{/if}</span>
					<span class="ico ico-caret-down"></span>
				</a>

				<ul class="dropdown-menu {if $phpfox.iteration.fkey < 2}{else}dropdown-menu-left{/if} dropdown-menu-limit dropdown-line">
					{foreach from=$aSearchFilters.data item=aSearchFilter}
					<li>
						<a href="{$aSearchFilter.link}" class="ajax_link {if isset($aSearchFilter.is_active)}active{/if}" rel="nofollow">
						{$aSearchFilter.phrase}
						</a>
					</li>
					{/foreach}
					{if (isset($aSearchFilters.default))}
					<li class="divider"></li>
					<li><a href="{$aSearchFilters.default.url}" class="is_default" rel="nofollow">{$aSearchFilters.default.phrase}</a></li>
					{/if}
				</ul>
			</div>
			{/if}
			{/foreach}
		</div>
		{/if}
	</div>
{elseif !empty($aBreadCrumbTitle)}
    <h1 class="header-page-title {if empty($aBreadCrumbTitle[2])}item-title{/if} {if isset($aTitleLabel.total_label) && $aTitleLabel.total_label > 0}header-has-label-{$aTitleLabel.total_label}{/if}">
        <a href="{ $aBreadCrumbTitle[1]}" class="ajax_link">{ $aBreadCrumbTitle[0] }</a>
        {if isset($aTitleLabel) && isset($aTitleLabel.type_id) && isset($aTitleLabel.label) && count($aTitleLabel.label)}
        <div class="{$aTitleLabel.type_id}-icon">
            {foreach from=$aTitleLabel.label key=sKey item=aLabel}
            <div class="sticky-label-icon title-label sticky-{$sKey}-icon" title="{_p var=$sKey}">
                <span class="ico ico-{$aLabel.icon_class}"></span>
                <span class="{if isset($aLabel.title_class)}{$aLabel.title_class}{/if}">{$aLabel.title}</span>
            </div>
            {/foreach}
        </div>
        {/if}
        {if !empty($aPageExtraLink)}
        <div class="view_item_link">
            <a href="{$aPageExtraLink.link}" class="page_section_menu_link" title="{$aPageExtraLink.phrase}" rel="nofollow">
                <span>{$aPageExtraLink.phrase}</span>
            </a>
        </div>
        {/if}
    </h1>
{/if}

