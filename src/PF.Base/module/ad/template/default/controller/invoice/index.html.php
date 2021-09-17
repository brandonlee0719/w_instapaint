<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{if !count($aInvoices)}
<div class="extra_info">
	{_p var='you_do_not_have_any_invoices'}
</div>
{else}
<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>{_p var='id'}</th>
                <th>{_p var='status'}</th>
                <th>{_p var='price'}</th>
                <th>{_p var='date'}</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$aInvoices item=aInvoice}
            <tr>
                <td class="t_center">{$aInvoice.invoice_id}</td>
                <td>{$aInvoice.status_phrase}{if $aInvoice.status === null} (<a href="{if $aInvoice.is_sponsor != 1}{url link='ad.add.completed' id=$aInvoice.ad_id}{else}{url link='ad.sponsor' pay=$aInvoice.ad_id}{/if}">{_p var='pay_now'}</a>){/if}</td>
                <td>{$aInvoice.price|currency:$aInvoice.currency_id}</td>
                <td>{$aInvoice.time_stamp|date}</td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>
{/if}