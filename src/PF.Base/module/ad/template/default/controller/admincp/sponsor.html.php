<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<form class="form-search" method="post" action="{url link='admincp.ad.sponsor'}">
<div class="panel panel-default">
	<div class="panel-body">
        <div class="row">
            <div class="form-group col-sm-3">
                <label for="">{_p var='type'}</label>
                {$aFilters.status}
            </div>
            <div class="form-group col-sm-2">
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
            <div class="form-group">
                <label>&nbsp;</label>
                <div>
                    <input type="submit" name="search[submit]" value="{_p var='submit'}" class="btn btn-primary" />
                    <input type="submit" name="search[reset]" value="{_p var='reset'}" class="btn btn-danger" />
                </div>
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
    <form method="post" action="{url link='admincp.ad'}">
    <div class="panel panel-default">

                <div class="table-responsive">
                    <table class="table table-admin">
                        <thead>
                            <tr>
                                <th class="w20"></th>
                                <th class="w30">{_p var='id'}</th>
                                <th>{_p var='campaign_name'}</th>
                                <th>{_p var='user'}</th>
                                <th>{_p var='status'}</th>
                                <th>{_p var='views'}</th>
                                <th>{_p var='clicks'}</th>
                                <th style="width:50px;">{_p var='active'}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach from=$aAds key=iKey item=aAd}
                            <tr class="{if is_int($iKey/2)} tr{else}{/if}{if $aAd.is_custom && $aAd.is_custom == '2'} is_checked{/if}">
                                <td class="t_center">
                                    <a href="#" class="js_drop_down_link" title="{_p var='manage'}"></a>
                                    <div class="link_menu">
                                        <ul class="dropdown">
                                            {if $aAd.is_custom == '2'}
                                            <li><a href="{url link='admincp.ad.sponsor' approve=$aAd.sponsor_id}">{_p var='approve'}</a></li>
                                            <li><a href="{url link='admincp.ad.sponsor' deny=$aAd.sponsor_id}" data-message="{_p var='are_you_sure' phpfox_squote=true}" class="sJsConfirm">{_p var='deny'}</a></li>
                                            {/if}
                                            <li><a href="{url link='admincp.ad.sponsor' delete=$aAd.sponsor_id}" data-message="{_p var='are_you_sure' phpfox_squote=true}" class="sJsConfirm">{_p var='delete'}</a></li>
                                        </ul>
                                    </div>
                                </td>
                                <td class="t_center">{$aAd.sponsor_id}</td>
                                <td><a href="{url link='ad.sponsor' view=$aAd.sponsor_id}">{$aAd.campaign_name|clean|convert}</a></td>
                                <td><a href="{url link=''$aAd.user_name'}">{$aAd|user}</a></td>
                                <td>{$aAd.status}</td>
                                <td class="t_center">{if $aAd.is_custom == '2' || $aAd.is_custom == '1'}N/A{else}{$aAd.count_view}{/if}</td>
                                <td class="t_center">{if $aAd.is_custom == '2' || $aAd.is_custom == '1'}N/A{else}{$aAd.count_click}{/if}</td>
                                <td class="t_center">
                                    {if $aAd.is_custom == '2' || $aAd.is_custom == '1'}
                                    {_p var='n_a'}
                                    {else}
                                    <div class="js_item_is_active"{if !$aAd.is_active} style="display:none;"{/if}>
                                        <a href="#?call=ad.updateSponsorActivity&amp;id={$aAd.sponsor_id}&amp;active=0" class="js_item_active_link" title="{_p var='deactivate'}"></a>
                                    </div>
                                    <div class="js_item_is_not_active"{if $aAd.is_active} style="display:none;"{/if}>
                                        <a href="#?call=ad.updateSponsorActivity&amp;id={$aAd.sponsor_id}&amp;active=1" class="js_item_active_link" title="{_p var='activate'}"></a>
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
	<div class="alert alert-empty">
	{if $bIsSearch}
		{_p var='no_search_results_were_found'}
	{else}
		{_p var='no_ads_have_been_created'}
	{/if}
	</div>
	{/if}
</div>