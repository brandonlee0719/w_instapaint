<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

<form method="post" action="{url link='admincp.menu' parent=$iParentId}" class="form">
    {if $iParentId === 0}

    {foreach from=$aMenus key=sType item=aMenusSub}
    <div class="panel panel-default">
        <div class="panel-heading">Menu: <strong>{$sType}</strong></div>
        <table class="table table-bordered table-admin" id="js_drag_drop_{$sType}">
            <thead>
            <tr>
                <th></th>
                <th></th>
                <th>{_p var="name"}</th>
                <th>{_p var="url"}</th>
                <th>
                    {_p var="active"}
                </th>
            </tr>
            </thead>
            <tbody>
            {foreach from=$aMenusSub key=iKey item=aMenu}
            {template file='admincp.block.menu.entry'}
            {/foreach}
            </tbody>
        </table>
    </div>
    {/foreach}
    {/if}
</form>