<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: list.html.php 1339 2009-12-19 00:37:55Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if $bIsSearching && !count($aPurchases)}
<div class="alert alert-empty">
	{_p var='could_not_find_any_purchase_orders_with_your_search_criteria'}
</div>
{/if}

<form class="form-search" method="post" action="{url link='admincp.subscribe.list'}">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="form-group col-sm-4">
                    <label for="status">{_p var='status'}</label>
                    {filter key='status'}
                </div>
                <div class="form-group col-sm-4">
                    <label for="sort">{_p var='sort_results_by'}</label>
                    {filter key='sort'}
                </div>
                <div class="form-group col-sm-4">
                    <label>
                    &nbsp;
                    </label>
        <div><input type="submit" value="{_p var='update'}" class="btn btn-primary" />
            {if $bIsSearching}
            <input type="submit" value="{_p var='reset'}" class="btn btn-danger" name="search[reset]" />
            {/if}</div>
                </div>
            </div>

        </div>
    </div>
</form>


{if count($aPurchases)}
<br />

{pager}
<div class="panel panel-default">
    <div class="panel-body">
        {_p var='orders'}
    </div>
    <div class="panel-footer">
        <div class="table-responsive">
            <table class="table" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th class="w30"></th>
                        <th class="t_center" style="width:100px;">{_p var='order_id'}</th>
                        <th>{_p var='package'}</th>
                        <th>{_p var='user'}</th>
                        <th class="t_center" style="width:100px;">{_p var='price'}</th>
                        <th class="w300">{_p var='status'}</th>
                        <th>{_p var='time'}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$aPurchases key=iKey item=aPurchase}
                    <tr>
                        <td class="t_center">
                            <a href="#" class="js_drop_down_link" title="{_p var='manage'}"></a>
                            <div class="link_menu">
                                <ul class="dropdown-menu">
                                    <li><a href="{url link='admincp.subscribe.list' delete={$aPurchase.purchase_id}" class="sJsConfirm" data-message="{_p var='are_you_sure' phpfox_squote=true}">{_p var='delete_order'}</a></li>
                                </ul>
                            </div>
                        </td>
                        <td class="t_center">{$aPurchase.purchase_id}</td>
                        <td><a href="{url link='admincp.subscribe.add' id=$aPurchase.package_id}">{$aPurchase.title|convert|clean}</a></td>
                        <td>{$aPurchase|user}</td>
                        <td class="t_center">
                            {if isset($aPurchase.default_cost) && $aPurchase.default_cost != '0.00'}
                            {if isset($aPurchase.default_recurring_cost)}
                                {$aPurchase.default_recurring_cost}
                            {else}
                                {$aPurchase.default_cost|currency:$aPurchase.default_currency_id}
                            {/if}
                            {else}
                            {_p var='free'}
                            {/if}
                        </td>
                        <td>
                            <a href="#" class="form_select_active">
                                {if $aPurchase.status == 'completed'}
                                {_p var='active'}
                                {elseif $aPurchase.status == 'cancel'}
                                {_p var='canceled'}
                                {elseif $aPurchase.status == 'pending'}
                                {_p var='pending_payment'}
                                {else}
                                {_p var='pending_action'}
                                {/if}
                            </a>
                            <ul class="form_select">
                                <li><a href="#?call=subscribe.updatePurchase&amp;status=completed&amp;purchase_id={$aPurchase.purchase_id}">{_p var='active'}</a></li>
                                <li><a href="#?call=subscribe.updatePurchase&amp;status=cancel&amp;purchase_id={$aPurchase.purchase_id}">{_p var='canceled'}</a></li>
                                <li><a href="#?call=subscribe.updatePurchase&amp;status=pending&amp;purchase_id={$aPurchase.purchase_id}">{_p var='pending_payment'}</a></li>
                                <li><a href="#?call=subscribe.updatePurchase&amp;status=&amp;purchase_id={$aPurchase.purchase_id}">{_p var='pending_action'}</a></li>
                            </ul>
                        </td>
                        <td>{$aPurchase.time_stamp|date}</td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    </div>
</div>

{pager}
{else}
{if !$bIsSearching}
<p class="alert alert-empty">
	{_p var='no_purchase_orders'}
</p>
{/if}
{/if}