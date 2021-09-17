<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: entry.html.php 1339 2009-12-19 00:37:55Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="info_holder">
    <div class="info form-group">
        <div class="info_left">
            {_p var='purchase_id'}:
        </div>
        <div class="info_right">
            <a href="{url link='subscribe.view' id=$aPurchase.purchase_id}">{$aPurchase.purchase_id}</a>
        </div>
    </div>
    <div class="info form-group">
        <div class="info_left">
            {_p var='membership'}:
        </div>
        <div class="info_right">
            {if $aPurchase.status == "completed"}
                {$aPurchase.s_title|convert|clean}
            {else}
                {$aPurchase.f_title|convert|clean}
            {/if}
        </div>
    </div>
    <div class="info form-group">
        <div class="info_left">
            {_p var='status'}:
        </div>
        <div class="info_right">
            {if $aPurchase.status == 'completed'}
            <span class="item_action_active">{_p var='active'}</span>
            {elseif $aPurchase.status == 'cancel'}
            <span class="item_action_cancel">{_p var='canceled'}</span>
            {elseif $aPurchase.status == 'pending'}
            <span class="item_action_pending_payment">{_p var='pending_payment'}</span>
            {else}
            <span class="item_action_pending_action">{_p var='pending_action'}</span>
            {/if}
        </div>
    </div>
    <div class="info form-group">
        <div class="info_left">
            {_p var='price'}:
        </div>
        <div class="info_right">
            {if isset($aPurchase.default_cost) && $aPurchase.default_cost != '0.00'}
                {if isset($aPurchase.default_recurring_cost)}
                    {$aPurchase.default_recurring_currency_id|currency_symbol}{$aPurchase.default_recurring_cost}
                {else}
                    {$aPurchase.default_cost|currency:$aPurchase.default_currency_id}
                {/if}
            {else}
            {_p var='free'}
            {/if}
        </div>
    </div>
    <div class="info form-group">
        <div class="info_left">
            {_p var='activated_date'}:
        </div>
        <div class="info_right">
            {$aPurchase.time_purchased|convert_time}
        </div>
    </div>
    <div class="info form-group">
        <div class="info_left">
            {_p var='type'}:
        </div>
        <div class="info_right">
            {$aPurchase.type}
        </div>
    </div>
    <div class="info form-group">
        <div class="info_left">
            {_p var='expiry_date'}:
        </div>
        <div class="info_right">
            {if $aPurchase.recurring_period == 0}
            {_p var='no_expiration_date'}
            {else}
            {$aPurchase.expiry_date|convert_time}
            {/if}
        </div>
    </div>
</div>
{if empty($aPurchase.status)}
<div class="t_right">
	<ul class="item_menu">
		<li><a href="#?call=subscribe.upgrade&amp;height=400&amp;width=400&amp;purchase_id={$aPurchase.purchase_id}" class="inlinePopup" title="{_p var='select_payment_gateway'}">{_p var='upgrade'}</a></li>
	</ul>
</div>
{/if}