<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if !count($aInvoices)}
<div class="alert alert-empty">
	{_p var='no_invoices_found'}
</div>
{else}
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">{_p var='manage_invoices'}</div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>{_p var='id'}</th>
                    <th>{_p var='status'}</th>
                    <th>{_p var='price'}</th>
                    <th>{_p var='created'}</th>
                    <th>{_p var='paid'}</th>
                    <th>{_p var='sent_to'}</th>
                    <th>{_p var='sent_from'}</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$aInvoices item=aInvoice}
                <tr>
                    <td class="t_center">{$aInvoice.invoice_id}</td>
                    <td>{$aInvoice.status}</td>
                    <td>
                        {if $aInvoice.price == '0.00'}
                            {_p var='free'}
                        {else}
                            {$aInvoice.price|currency:$aInvoice.currency_id}
                        {/if}
                    </td>
                    <td>{$aInvoice.time_stamp_created|date:'core.extended_global_time_stamp'}</td>
                    <td>{if $aInvoice.time_stamp_paid > 0 }{$aInvoice.time_stamp_paid|date:'core.extended_global_time_stamp'}{/if}</td>
                    <td>{$aInvoice.to|user}</td>
                    <td>{$aInvoice.from|user}</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>
{/if}
