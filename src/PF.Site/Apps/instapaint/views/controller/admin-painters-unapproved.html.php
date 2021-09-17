{if !count($aItems)}
{if !PHPFOX_IS_AJAX}
<div class="extra_info">

    <div class="alert alert-info" role="alert"><i class="far fa-frown"></i> No unapproved painters found.</div>
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
        <th scope="col">Last login</th>
    </tr>
    </thead>
    {/if}
    <tbody>
    {foreach from=$aItems name=aItem item=aItem}
    <tr style="cursor: pointer" onclick="top.location.href='/{$aItem.user_name}/'">
        <td style="width: 4%">{$aItem.number}</td>
        <td style="width: 32%">{$aItem.full_name}</td>
        <td style="width: 32%">{$aItem.joined|convert_time}</td>
        <td style="width: 32%">{$aItem.last_login|convert_time}</td>
    </tr>
    {/foreach}
    </tbody>
</table>
{pager}
{/if}
