{literal}
{/literal}
<div class="alert alert-info" role="alert"><i class="fas fa-ticket-alt"></i> In this section you can create, edit, and delete discounts for all packages or specific ones.</div>
{if !count($aItems)}
{if !PHPFOX_IS_AJAX}
<div class="extra_info">

    <div class="alert alert-info" role="alert"><i class="far fa-frown"></i> No discounts found.</div>
</div>
{/if}
{else}
<table class="table table-bordered table-hover {if PHPFOX_IS_AJAX}pager-table{/if}">
    {if !PHPFOX_IS_AJAX}
    <thead>
    <tr>
        <th scope="col">#</th>
        <th scope="col">Name</th>
        <th scope="col">Details</th>
        <th scope="col">Type</th>
        <th scope="col">Expires on</th>
    </tr>
    </thead>
    {/if}
    <tbody>
    {foreach from=$aItems name=aItem item=aItem}
    <tr style="cursor: pointer" onclick="top.location.href='/admin-dashboard/discounts/edit/{$aItem.discount_id}/'">
        <td style="width: 4%">{$aItem.number}</td>
        <td style="width: 24%">{$aItem.name}</td>
        <td style="width: 24%">{$aItem.details}</td>
        <td style="width: 24%">{if $aItem.coupon_code}Coupon <small>Code: {$aItem.coupon_code}</small>{else}Sale{/if}</td>
        <td style="width: 24%">{if $aItem.expiration_timestamp}{$aItem.expiration_timestamp|convert_time}{else}No expiration{/if}</td>
    </tr>
    {/foreach}
    </tbody>
</table>
{pager}
{/if}
