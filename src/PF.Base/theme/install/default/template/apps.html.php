<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<div id="client_details">
    <form method="post" action="#apps" id="js_form" enctype="multipart/form-data">
        <h1>Checking Compatible</h1>
        <div class="alert alert-warning">
            Incompatible apps might break your site.
        </div>
        <div id="errors" class="hide"></div>
        <input type="hidden" name="val[app]" value="apps">
        <input type="submit" value="Continue" class="hide" name="val[submit]"/>
        <table style="width:100%; text-align: center;" class="table-bordered table table-striped">
            <tr>
                <th>Name</th>
                <th>Compatible</th>
                <th>Status</th>
                <th class="text-center">Version</th>
                <th>Latest</th>
                <th>Upgrade</th>
            </tr>
            {foreach from=$apps key=sKey item=app}
            <tr class="{if isset($app.hide) and $app.hide == 1} hide {/if}">
                <td class="text-left">{$app.name}</td>
                <td class="text-center">
                    {$app.is_compatible}
                    {if $app.is_compatible == 'No'}
                    {/if}
                </td>
                <td>
                    {if $app.status}
                    Enable
                    {else}
                    Disabled
                    {/if}
                </td>
                <td>{$app.current_version}</td>
                <td>{$app.latest_version}</td>
                <td>
                    {if $app.bUpgradeAvailable}
                    {if $app.required}<i class="label label-info">required</i>{/if}
                    <label for="upgrade" {if $app.required}class="hide"{/if}><input type="checkbox" value="{$app.name}" name="val[upgrade][{$app.id}]" checked id="{$app.id}"/></label>
                    {/if}
                </td>
            </tr>
            {/foreach}
        </table>
        <textarea name="val[apps_serialized]" class="hide">{$apps_serialized}</textarea>
    </form>
</div>