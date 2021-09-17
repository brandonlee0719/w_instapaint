<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<form class='form-search' method="get" action="{url link='admincp.ad.invoice'}">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="form-group col-sm-3">
                    <label for="status">{_p var='status'}</label>
                    {$aFilters.status}
                </div>
                <div class="form-group col-sm-2">
                    <label for="display">{_p var='display'}</label>
                    {$aFilters.display}
                </div>
                <div class="form-group col-sm-2">
                    <label for="sort">{_p var='sort_by'}</label>
                    {$aFilters.sort}
                </div>
                <div class="form-group col-sm-2">
                    <label for="sort_by">&nbsp;</label>
                    {$aFilters.sort_by}
                </div>
                <div class="form-group">
                    <label>&nbsp;</label>
                    <div><input type="submit" name="search[submit]" value="{_p var='submit'}" class="btn btn-primary" />
                        <input type="submit" name="search[reset]" value="{_p var='reset'}" class="btn btn-danger" /></div>
                </div>
            </div>
        </div>
    </div>
</form>

{if !count($aInvoices)}
<div class="alert alert-empty">
    {_p var='there_are_no_invoices'}
</div>
{else}
<div class="panel panel-default">
    <div class="table-responsive">
        <table class="table table-admin">
            <thead>
            <tr>
                <th class="w30"></th>
                <th>{_p var='id'}</th>
                <th>{_p var='status'}</th>
                <th>{_p var='price'}</th>
                <th>{_p var='date'}</th>
            </tr>
            </thead>
            <tbody>
            {foreach from=$aInvoices key=iKey item=aInvoice}
            <tr>
                <td class="t_center">{$aInvoice.invoice_id}</td>
                <td>
                    <a href="{url link='admincp.ad.invoice' delete=$aInvoice.invoice_id}" data-message="{_p var='are_you_sure' phpfox_squote=true}" class="sJsConfirm">{_p var='delete'}</a>
                </td>
                <td>{$aInvoice.status_phrase}</td>
                <td>{$aInvoice.price|currency:$aInvoice.currency_id}</td>
                <td>{$aInvoice.time_stamp|date}</td>
            </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>
{pager}
{/if}