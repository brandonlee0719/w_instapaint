<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="page_section_menu page_section_menu_header">
	<div>
		<ul class="nav nav-tabs nav-justified">
			<li{if empty($sView)} class="active"{/if}><a href="{url link='ad.manage-sponsor'}">{_p var='approved'}</a></li>
			<li{if $sView == 'pending'} class="active"{/if}><a href="{url link='ad.manage-sponsor' view='pending'}">{_p var='pending_approval'}</a></li>
			<li{if $sView == 'payment'} class="active"{/if}><a href="{url link='ad.manage-sponsor' view='payment'}">{_p var='pending_payment'}</a></li>
			<li class="last{if $sView == 'denied'} active{/if}"><a href="{url link='ad.manage-sponsor' view='denied'}">{_p var='denied'}</a></li>
		</ul>
	</div>
	<div class="clear"></div>
</div>

<div class="table-responsive">
    <table class="table table-admin">
        <thead>
            <tr>
                <th>{_p var='campaign'}</th>
                <th>{_p var='status'}</th>
                <th>{_p var='impressions'}</th>
                <th>{_p var='clicks'}</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$aAds name=ads item=aAd}
            <tr>
                <td><a href="{url link='ad.sponsor' view=$aAd.sponsor_id}"> {$aAd.campaign_name|clean|convert} </a>{if $aAd.is_custom == '1'}<a href="{url link='ad.sponsor' pay=$aAd.sponsor_id}">({_p var='pay_now'})</a>{/if}</td>
                <td class="t_center">{$aAd.status}</td>
                <td class="t_center">{$aAd.total_view}</td>
                <td class="t_center">{$aAd.total_click}</td>
            </tr>
            {foreachelse}
            <tr>
                <td colspan="5" id="no_ads_found">
                    {_p var='no_ads_found'}
                    {if empty($sView)}
                    {/if}
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>