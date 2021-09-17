<?php 
	defined('PHPFOX') or exit('NO DICE!'); 
?>

{if !count($aInvoices)}
		<div class="extra_info">
			{_p var='you_do_not_have_any_invoices'}
		</div>
		{else}
		<div class="market-app invoice">
			<div class="table-responsive">
				<table class="table">
					<thead>
						<tr>
							<th>{_p var='id'}</th>
							<th>{_p var='status'}</th>
							<th class="text-center">{_p var='date'}</th>
							<th class="text-center">{_p var='price'}</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$aInvoices item=aInvoice}
							<tr>
								<td>{$aInvoice.invoice_id}</td>
								<td>{$aInvoice.status_phrase}{if $aInvoice.status === null || $aInvoice.status == 'pending'}{/if}</td>
								<td class="text-center date">{$aInvoice.time_stamp|date}</td>
								<td class="text-center">{$aInvoice.price|currency:$aInvoice.currency_id}</td>
								<td class="text-right"><a class="fw-bold" href="{url link='marketplace.purchase' invoice=$aInvoice.invoice_id}">{_p var='pay_now'}</a></td>
							</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		</div>
{/if}