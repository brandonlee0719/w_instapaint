<table class="table table-hover">
    <thead>
        <tr>
            <th>{_p var='Admin'}</th>
            <th>{_p var='ip_address'}</th>
            <th>{_p var='last_login'}</th>
        </tr>
    </thead>
    <tbody>
    {foreach from=$aLastAdmins name=lastadmins item=aLastAdmin}
        <tr>
            <td>
                {$aLastAdmin|user}
            </td>
            <td>{$aLastAdmin.ip_address}</td>
            <td>{$aLastAdmin.time_stamp|date:'core.extended_global_time_stamp'}</td>
        </tr>
    {/foreach}
    </tbody>
</table>