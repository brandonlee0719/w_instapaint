{if count($aItems)}
<div class="panel panel-default">
    <table class="table table-admin">
        <tbody>
        {foreach from=$aItems item=aItem}
        <tr>
            <td>{$aItem.message}</td>
            <td class="w100"><a class="btn btn-xs btn-success" target="{if isset($aItem.target)}{$aItem.target}{else}_blank{/if}" href="{$aItem.link}">Continue</a></td>
        </tr>
        {/foreach}
        </tbody>
    </table>
</div>
{else}
{_p var="We have no alerts now."}
{/if}