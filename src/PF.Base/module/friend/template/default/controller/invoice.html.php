<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package 		Phpfox
 * @version 		$Id: controller.html.php 64 2009-01-19 15:05:54Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if !count($aInvoices)}
<div class="extra_info">
	{_p var='you_do_not_have_any_invoices'}
</div>
{else}
<div class="table-responsive">
    <table class="table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th>{_p var='id'}</th>
                <th>{_p var='status'}</th>
                <th>{_p var='price'}</th>
                <th>{_p var='date'}</th>
                <th>{_p var='sent_to'}</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$aInvoices item=aInvoice}
            <tr>
                <td class="t_center">{$aInvoice.invoice_id}</td>
                <td>{$aInvoice.invoice_status}</td>
                <td>{$aInvoice.price|currency:$aInvoice.currency_id}</td>
                <td>{$aInvoice.time_stamp|date}</td>
                <td>{$aInvoice|user}</td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>
{/if}