<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
<form class="form-search" method="get" action="{url link='admincp.ad'}">
<div class="panel panel-default">
	<div class="panel-body">
        <div class="row">
            <div class="form-group col-sm-3">
                <label for="status">{_p var='type'}</label>
                {$aFilters.status}
            </div>
            <div class="form-group col-sm-3">
                <label for="">{_p var='display'}</label>
                {$aFilters.display}
            </div>
            <div class="form-group col-sm-2">
                <label for="">{_p var='sort_by'}</label>
                {$aFilters.sort}
            </div>
            <div class="form-group col-sm-2">
                <label>&nbsp;</label>
                {$aFilters.sort_by}
            </div>
            <div class="form-btn-group">
                <label>&nbsp;</label>
                <div><input type="submit" name="search[submit]" value="{_p var='submit'}" class="btn btn-primary" /></div>
            </div>
        </div>

    </div>
</div>
</form>

<div class="block_content">
	{if $iPendingCount > 0 && $sView != 'pending'}
	<div class="message">
		{_p var='there_are_pending_ads_that_require_your_attention_view_all_pending_ads_a_href_link_here_a' link=$sPendingLink}
	</div>
	{/if}
	{if count($aAds)}
    <form class="form" method="post" action="{url link='admincp.ad'}">
    <div class="panel panel-default">
    <div class="table-responsive">
        <table class="table table-admin">
            <thead>
                <tr>
                    <th style="width:20px;"></th>
                    <th style="width:30px;">{_p var='id'}</th>
                    <th>{_p var='name'}</th>
                    <th>{_p var='status'}</th>
                    <th class="t_center">{_p var='views'}</th>
                    <th class="t_center">{_p var='clicks'}</th>
                    <th class="t_center" style="width:50px;">{_p var='active'}</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$aAds key=iKey item=aAd}
                <tr class="{if is_int($iKey/2)} tr{else}{/if}{if $aAd.is_custom && $aAd.is_custom == '2'} is_checked{/if}">
                    <td class="t_center">
                        <a href="#" class="js_drop_down_link" title="{_p var='manage'}"></a>
                        <div class="link_menu">
                            <ul class="dropdown-menu">
                                {if $aAd.is_custom == '2'}
                                <li><a href="{url link='admincp.ad' approve=$aAd.ad_id}">{_p var='approve'}</a></li>
                                <li><a href="{url link='admincp.ad' deny=$aAd.ad_id}" data-message="{_p var='are_you_sure' phpfox_squote=true}" class="sJsConfirm">{_p var='deny'}</a></li>
                                <li><a href="{url link='admincp.ad' delete=$aAd.ad_id}" data-message="{_p var='are_you_sure' phpfox_squote=true}" class="sJsConfirm">{_p var='delete'}</a></li>
                                {else}
                                <li><a href="{url link='admincp.ad' delete=$aAd.ad_id}" data-message="{_p var='are_you_sure' phpfox_squote=true}" class="sJsConfirm">{_p var='delete'}</a></li>
                                {/if}
                            </ul>
                        </div>
                    </td>
                    <td class="t_center">{$aAd.ad_id}</td>
                    <td>{$aAd.name|clean|convert}</td>
                    <td>{$aAd.status}</td>
                    <td class="t_center">{if $aAd.is_custom == '2' || $aAd.is_custom == '1'}N/A{else}{$aAd.count_view}{/if}</td>
                    <td class="t_center">{$aAd.count_click}</td>
                    <td class="t_center">
                        {if $aAd.is_custom == '2' || $aAd.is_custom == '1'}
                        {_p var='n_a'}
                        {else}
                        <div class="js_item_is_active"{if !$aAd.is_active} style="display:none;"{/if}>
                            <a href="#?call=ad.updateAdActivity&amp;id={$aAd.ad_id}&amp;active=0" class="js_item_active_link" title="{_p var='deactivate'}"></a>
                        </div>
                        <div class="js_item_is_not_active"{if $aAd.is_active} style="display:none;"{/if}>
                            <a href="#?call=ad.updateAdActivity&amp;id={$aAd.ad_id}&amp;active=1" class="js_item_active_link" title="{_p var='activate'}"></a>
                        </div>
                        {/if}
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    </div>
    </form>
	{pager}
	{else}
	<p class="alert alert-empty">
	{if $bIsSearch}
		{_p var='no_search_results_were_found'}
	{else}
		{_p var='no_ads_have_been_created'}
	{/if}
	</p>
	{/if}
</div>