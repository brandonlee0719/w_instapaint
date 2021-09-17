<h1>Checking File Permission</h1>

<table class="check-requirements table">
    {foreach from=$aErrors item=error}
    <tr class="text-danger has-error">
        <td>
            {$error}
        </td>
        <td width="40">
            <i class="fa fa-remove text-danger"></i>
        </td>
    </tr>
    {/foreach}
</table>
