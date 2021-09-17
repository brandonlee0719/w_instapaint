<?php

defined('PHPFOX') or exit('NO DICE!');

?>
{if count($aPackages)}
{if Phpfox::isUser()}
<div class="title">{_p var='your_membership_status'}: {$aGroup.title|convert}</div>
{/if}
<div class="item-membership-container">
    {foreach from=$aPackages item=aPackage name=packages}
    <div class="membership-item">
        <div class="item-outer">
            <div class="item-image">
                <a class="item-media-src" href="#" onclick="{if Phpfox::isUser()}tb_show('{_p var='select_payment_gateway' phpfox_squote=true}', $.ajaxBox('subscribe.upgrade', 'height=400&amp;width=400&amp;id={$aPackage.package_id}'));{else}$('#js_subscribe_package_id').val('{$aPackage.package_id}'); tb_remove(); {/if} return false;">
                    {if !empty($aPackage.image_path)}
                    <span style="background-image: url({img server_id=$aPackage.server_id title=$aPackage.title path='subscribe.url_image' file=$aPackage.image_path suffix='_120' max_width='120' max_height='120' return_url=true})"></span>
                    {else}
                    {img server_id=0 title=$aPackage.title file=$sDefaultImagePath max_width='120' max_height='120'}
                    {/if}
                </a>
            </div>

            <div class="item-inner">
                <div class="item-block-title">{$aPackage.title|convert|clean}</div>
                {if !empty($aPackage.description)}
                <div class="item-desc">
                    {$aPackage.description|convert|shorten:250:'more':true}
                </div>
                {/if}
                <div class="item-price">
                    <div class="item-price-first">
                    <span class="item-name">
                                {if $aPackage.recurring_period}
                                    {if $aPackage.recurring_period == 1}
                                        {_p var='first_month'}
                                    {elseif $aPackage.recurring_period == 2}
                                        {_p var='first_quarter'}
                                    {elseif $aPackage.recurring_period == 3}
                                        {_p var='first_biannual'}
                                    {elseif $aPackage.recurring_period == 4}
                                        {_p var='first_annual'}
                                    {/if}
                                {else}
                                    {_p var='one_time_pay'}
                                {/if}
                                :
                            </span>
                        <span class="item-cost">
                                {if isset($aPackage.default_cost) && $aPackage.default_cost != '0.00'}
                                    {$aPackage.default_cost|currency:$aPackage.default_currency_id}
                                {elseif isset($aPackage.price)}
                                    {foreach from=$aPackage.price item=sCurrency name=iCost}
                                        <span class="subscription-price">{$sCurrency.cost|currency:$sCurrency.currency_id}</span>
                                    {/foreach}
                                {else}
                                    {_p var='free'}
                                {/if}
                            </span>
                    </div>
                    {if $aPackage.recurring_period}
                    <div class="item-price-recurring">
                    <span class="item-name">
                                {if $aPackage.recurring_period == 1}
                                    {_p var='monthly'}
                                {elseif $aPackage.recurring_period == 2}
                                    {_p var='quarterly'}
                                {elseif $aPackage.recurring_period == 3}
                                    {_p var='biannualy'}
                                {elseif $aPackage.recurring_period == 4}
                                    {_p var='annually'}
                                {/if}
                                :
                            </span>
                        <span class="item-cost">
                                {$aPackage.default_currency_id|currency_symbol}{$aPackage.default_recurring_cost_no_phrase|number_format:2}
                            </span>
                    </div>
                    {/if}
                </div>
                <a class="btn btn-primary btn-sm" href="#" onclick="{if Phpfox::isUser()}tb_show('{_p var='select_payment_gateway' phpfox_squote=true}', $.ajaxBox('subscribe.upgrade', 'height=400&amp;width=400&amp;id={$aPackage.package_id}'));{else}$('#js_subscribe_package_id').val('{$aPackage.package_id}'); js_box_remove(this); {/if} return false;">{if Phpfox::isUser() || (isset($bIsOnSignup) && $bIsOnSignup)}{_p var='select'}{else}{_p var='select'}{/if}</a>
            </div>
        </div>
    </div>
    {/foreach}
</div>
{if count($aPackages) >= 2}
<a href="{url link='subscribe.compare'}" target="_blank" class="manage_subscriptions no_ajax">{_p var='compare_subscription_packages'}</a><br />
{/if}
{else}
<div class="extra_info">
    {_p var='no_packages_available'}
</div>
{/if}
{if Phpfox::isUser() && count($aPurchases)}
<div class="title">{_p var='recent_orders'}</div>
{foreach from=$aPurchases item=aPurchase name=purchases}
<div class="{if is_int($phpfox.iteration.purchases/2)}row1{else}row2{/if}{if $phpfox.iteration.purchases == 1} row_first{/if}">
    {if $aPurchase.status == 'completed'}
    <span class="item_action_active">{_p var='active'}</span>
    {elseif $aPurchase.status == 'cancel'}
    <span class="item_action_cancel">{_p var='canceled'}</span>
    {elseif $aPurchase.status == 'pending'}
    <span class="item_action_pending_payment">{_p var='pending_payment'}</span>
    {else}
    <span class="item_action_pending_action">{_p var='pending_action'}</span>
    {/if} - <a href="{url link='subscribe.view' id=$aPurchase.purchase_id}">{_p var='order_purchase_id' purchase_id=$aPurchase.purchase_id} ({$aPurchase.title|convert|clean})</a>
    {if empty($aPurchase.status)}
    (<a href="#" onclick="tb_show('{_p var='select_payment_gateway' phpfox_squote=true}', $.ajaxBox('subscribe.upgrade', 'height=400&amp;width=400&amp;purchase_id={$aPurchase.purchase_id}')); return false;">{_p var='upgrade_now'}</a>)
</div>
{/if}
<div class="extra_info">
    {$aPurchase.time_stamp|date:'core.global_update_time'}
</div>
</div>
{/foreach}

<a href="{url link='subscribe.list'}" class="manage_subscriptions">{_p var='manage_subscriptions'}</a>

{/if}