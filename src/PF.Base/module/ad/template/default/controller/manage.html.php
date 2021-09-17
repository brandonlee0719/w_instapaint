<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="page_section_menu page_section_menu_header">
	<div>
		<ul class="nav nav-tabs nav-justified">
			<li{if empty($sView)} class="active"{/if}><a href="{url link='ad.manage'}">{_p var='approved'}</a></li>
			<li{if $sView == 'pending'} class="active"{/if}><a href="{url link='ad.manage' view='pending'}">{_p var='pending_approval'}</a></li>
			<li{if $sView == 'payment'} class="active"{/if}><a href="{url link='ad.manage' view='payment'}">{_p var='pending_payment'}</a></li>
			<li class="last{if $sView == 'denied'} active{/if}"><a href="{url link='ad.manage' view='denied'}">{_p var='denied'}</a></li>
		</ul>
	</div>
	<div class="clear"></div>
</div>

{if $bNewPurchase}
<div class="message">
	{_p var='thank_you_for_your_purchase_your_ad_is_currently_pending_approval'}
</div>
{/if}
<div class="table-responsive">
    <table class="table table-admin">
        <thead>
            <tr>
                <th>{_p var='campaign'}</th>
                <th>{_p var='status'}</th>
                <th>{_p var='impressions'}</th>
                <th>{_p var='clicks'}</th>
                <th class="w50">{_p var='active'}</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$aAllAds name=ads item=aAd}
            <tr>
                <td><a href="{if $aAd.is_custom == '1'}{url link='ad.add.completed' id=$aAd.ad_id}{else}{url link='ad.add' id=$aAd.ad_id}{/if}">{$aAd.name|clean}</a></td>
                <td>{$aAd.status}</td>
                <td>{$aAd.count_view}</td>
                <td>{$aAd.count_click}</td>
                <td class="on_off">
                    {if empty($sView)}
                    <div class="js_item_is_active {if !$aAd.is_active}hide{/if}">
                        <a href="#?call=ad.updateAdActivityUser&amp;id={$aAd.ad_id}&amp;active=0" class="js_item_active_link" title="{_p var='pause_this_campaign'}">{img theme='misc/bullet_green.png' alt=''}</a>
                    </div>
                    <div class="js_item_is_not_active {if $aAd.is_active}hide{/if}">
                        <a href="#?call=ad.updateAdActivityUser&amp;id={$aAd.ad_id}&amp;active=1" class="js_item_active_link" title="{_p var='continue_this_campaign'}">{img theme='misc/bullet_red.png' alt=''}</a>
                    </div>
                    {else}
                    {_p var='n_a'}
                    {/if}
                </td>
            </tr>
            {foreachelse}
            <tr>
                <td colspan="5">
                    <div class="extra_info">
                        {_p var='no_ads_found'}
                    </div>
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>