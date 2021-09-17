{literal}

<style>
    .edit-icon {
        display: none;
    }
    tr:hover .edit-icon {
        display: initial;
    }
</style>
{/literal}

{if !count($aItems)}
    {if !PHPFOX_IS_AJAX}
    <div class="extra_info">
        <div class="alert alert-info" role="alert"><i class="far fa-frown"></i> No approved painters found.</div>
    </div>
    {/if}
{else}
    <table class="table table-bordered table-hover {if PHPFOX_IS_AJAX}pager-table{/if}">
        {if !PHPFOX_IS_AJAX}
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Painter name</th>
            <th scope="col">Request date</th>
            <th scope="col">Approval date</th>
            <th scope="col">Last login</th>
            <th scope="col">Approved by</th>
            <th scope="col">Daily jobs limit</th>
        </tr>
        </thead>
        {/if}
        <tbody>
            {foreach from=$aItems name=aItem item=aItem}
            <tr style="cursor: pointer">
                <td style="width: 5%" onclick="top.location.href='/{$aItem.user_name}/'">{$aItem.number}</td>
                <td style="width: 16%" onclick="top.location.href='/{$aItem.user_name}/'">{$aItem.full_name}</td>
                <td style="width: 16%" onclick="top.location.href='/{$aItem.user_name}/'">{$aItem.request_timestamp|convert_time}</td>
                <td style="width: 16%" onclick="top.location.href='/{$aItem.user_name}/'">{$aItem.approved_timestamp|convert_time}</td>
                <td style="width: 16%" onclick="top.location.href='/{$aItem.user_name}/'">{$aItem.last_login|convert_time}</td>
                <td style="width: 16%" onclick="top.location.href='/{$aItem.user_name}/'">{$aItem.approver_full_name}</td>
                <td style="width: 15%; text-align: center;" onclick="top.location.href='/admin-dashboard/painters/set-daily-limit/{$aItem.user_id}/'">{$aItem.daily_limit} <i class="fas fa-edit edit-icon"></i></td>
            </tr>
            {/foreach}
        </tbody>
    </table>
    {pager}
{/if}
