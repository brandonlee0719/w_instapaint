{if !count($aItems)}
{if !PHPFOX_IS_AJAX}
<div class="extra_info">

    <div class="alert alert-info" role="alert"><i class="far fa-frown"></i> No approval requests found.</div>
</div>
{/if}
{else}
<table class="table table-bordered table-hover {if PHPFOX_IS_AJAX}pager-table{/if}">
    {if !PHPFOX_IS_AJAX}
    <thead>
    <tr>
        <th scope="col">#</th>
        <th scope="col">Painter name</th>
        <th scope="col">Sign-up date</th>
        <th scope="col">Request date</th>
    </tr>
    </thead>
    {/if}
    <tbody>
    {foreach from=$aItems name=aItem item=aItem}
    <tr style="cursor: pointer" onclick="top.location.href='/admin-dashboard/painters/approval-request/{$aItem.approval_request_id}/'">
        <td style="width: 5%">{$aItem.number}</td>
        <td style="width: 19%">{$aItem.full_name}</td>
        <td style="width: 19%">{$aItem.joined|convert_time}</td>
        <td style="width: 19%">{$aItem.request_timestamp|convert_time}</td>
    </tr>
    {/foreach}
    </tbody>
</table>
{pager}
{/if}
