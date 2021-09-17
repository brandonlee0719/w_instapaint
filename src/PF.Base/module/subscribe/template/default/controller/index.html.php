<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if Phpfox::getParam('subscribe.enable_subscription_packages')}
    {if !empty($aPackages)}
        <div class="item-container membership-package-listing owl-carousel membership-package-slider" id="collection-packages">
            {foreach from=$aPackages item=aPackage name=packages}
                <div class="item membership-item">
                    <div class="item-outer">
                        <div class="item-title">{$aPackage.title|convert|clean}</div>
                        <div class="item-image">
                            {if !empty($aPackage.image_path)}
                            <span style="background-image: url({img server_id=$aPackage.server_id title=$aPackage.title path='subscribe.url_image' file=$aPackage.image_path suffix='_120' max_width='120' max_height='120' return_url=true})"></span>
                            {else}
                            <span style="background-image: url({img server_id=0 title=$aPackage.title file=$sDefaultImagePath max_width='120' max_height='120' return_url=true})"></span>
                            {/if}
                        </div>
                        {if $aPackage.show_price}
                        <div class="item-price-info">
                            {if isset($aPackage.default_cost) && $aPackage.default_cost != '0.00'}
                                {if isset($aPackage.default_recurring_cost)}
                                    {$aPackage.default_recurring_cost}
                                {else}
                                    <div class="subscription-price">
                                        {$aPackage.default_cost|currency:$aPackage.default_currency_id}
                                    </div>
                                {/if}
                            {elseif isset($aPackage.price)}
                                {foreach from=$aPackage.price item=sCurrency name=iCost}
                                    <span>{$sCurrency.currency_id}: <span class="subscription-price">{$sCurrency.cost}</span></span>
                                {/foreach}
                            {else}
                            <div class="subscription-price">
                                {_p var='free'}
                            </div>
                            {/if}
                        </div>
                        {/if}
                        {if !empty($aPackage.description)}
                            <div class="item-desc-info">
                                {$aPackage.description|convert|shorten:250:'more':true}
                            </div>
                            {/if}
                            <a class="btn btn-primary" href="#" onclick="{if Phpfox::isUser()}tb_show('{_p var='select_payment_gateway' phpfox_squote=true}', $.ajaxBox('subscribe.upgrade', 'height=400&amp;width=400&amp;id={$aPackage.package_id}'));{else}$('#js_subscribe_package_id').val('{$aPackage.package_id}'); tb_remove(); {/if} return false;">{if Phpfox::isUser() || (isset($bIsOnSignup) && $bIsOnSignup)}{_p var='purchase'}{else}{_p var='purchase'}{/if}</a>
                    </div>
                </div>
            {/foreach}
        </div>
        {assign var=aPackages value=$aComparePackages}
        {template file='subscribe.block.compare'}
    {else}
        <div class="extra_info">
            {_p var='no_packages_available'}
        </div>
    {/if}
{else}
<div class="extra_info">
    {_p var='the_feature_or_section_you_are_attempting_to_use_is_not_permitted_with_your_membership_level'}
</div>
{/if}

