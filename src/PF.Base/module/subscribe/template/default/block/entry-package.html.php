<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="membership-item">
    <div class="item-outer">
        <div class="item-block-title">{$aPackage.title|convert|clean}</div>
        <div class="item-block-main">
            <div class="item-inner">
                <div class="item-image">
                    <a class="item-media-src" href="#" onclick="{if Phpfox::isUser()}tb_show('{_p var='select_payment_gateway' phpfox_squote=true}', $.ajaxBox('subscribe.upgrade', 'height=400&amp;width=400&amp;id={$aPackage.package_id}'));{else}$('#js_subscribe_package_id').val('{$aPackage.package_id}'); tb_remove(); {/if} return false;">
                        {if !empty($aPackage.image_path)}
                        <span style="background-image: url({img server_id=$aPackage.server_id title=$aPackage.title path='subscribe.url_image' file=$aPackage.image_path suffix='_120' max_width='120' max_height='120' return_url=true})"></span>
                        {else}
                        {img server_id=0 title=$aPackage.title file=$sDefaultImagePath max_width='120' max_height='120'}
                        {/if}
                    </a>
                </div>
                <div class="item-desc">
                    {if !empty($aPackage.description)}
                    <div class="item-desc-info">
                        {$aPackage.description|convert|shorten:250:'more':true}
                    </div>
                    {/if}
                </div>
            </div>
            <div class="item-price">
                {if $aPackage.show_price}
                <div class="p_top_4 item-price-info">
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
                <a class="btn btn-primary" href="#" onclick="{if Phpfox::isUser()}tb_show('{_p var='select_payment_gateway' phpfox_squote=true}', $.ajaxBox('subscribe.upgrade', 'height=400&amp;width=400&amp;id={$aPackage.package_id}'));{else}$('#js_subscribe_package_id').val('{$aPackage.package_id}'); tb_remove(); {/if} return false;">{if Phpfox::isUser() || (isset($bIsOnSignup) && $bIsOnSignup)}{_p var='purchase'}{else}{_p var='purchase'}{/if}</a>
            </div>
        </div>
    </div>
</div>
