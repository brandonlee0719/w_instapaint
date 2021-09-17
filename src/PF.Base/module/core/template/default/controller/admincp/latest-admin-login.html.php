<div class="table-responsive">
    <table class="table table-admin">
        <thead>
            <tr>
                <th>{_p var='user'}</th>
                <th>{_p var='ip_address'}</th>
                <th class="text-center">{_p var='attempt'}</th>
                <th>{_p var='time_stamp'}</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$aUsers key=iKey item=aUser}
            <tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                <td>{$aUser|user}</td>
                <td>{$aUser.ip_address}</td>
                <td class="text-center">
                    <a href="#" title="{_p var='view_attempt'}: {$aUser.attempt}" onclick="tb_show('{_p var='admincp_login_log' phpfox_squote=true}', $.ajaxBox('core.admincp.viewAdminLogin', 'height=410&amp;width=600&amp;login_id={$aUser.login_id}')); return false;">
                    {if $aUser.is_failed}
                        <i class="fa fa-check text-danger"></i>
                    {else}
                        <i class="fa fa-check text-success"></i>
                    {/if}
                    </a>
                </td>
                <td>{$aUser.time_stamp|date:'core.extended_global_time_stamp'}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>
{pager}