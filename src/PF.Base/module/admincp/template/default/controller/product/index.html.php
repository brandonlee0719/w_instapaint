<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

{if count($aProducts)}
<form method="post" action="{url link='admincp.product'}" class="form">
    <div class="panel panel-default">
	    <table class="table table-admin">
            <thead>
                <tr>
                    <th class="w20"></th>
                    <th>{_p var='Product'}</th>
                    <th>{_p var='Version'}</th>
                    <th class="text-center">{_p var='Action'}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$aProducts key=iKey item=aProduct}
                <tr>
                    <td class="text-center">
                        <a href="#" class="js_drop_down_link" title="{_p var='manage'}"></a>
                        <div class="link_menu">
                            <ul class="dropdown-menu">
                                <li><a href="{url link='admincp.product.add' id=$aProduct.product_id}">{_p var='edit'}</a></li>
                                <li><a href="{url link='admincp.product.file' export=$aProduct.product_id extension='xml'}">{_p var='export'}</a></li>
                                <li><a href="{url link='admincp.product' delete=$aProduct.product_id}"  data-message="{_p var='are_you_sure' phpfox_squote=true}" class="sJsConfirm">{_p var='delete'}</a></li>
                            </ul>
                        </div>
                    </td>
                    <td>{$aProduct.title}</td>
                    <td>
                        {if $aProduct.version}
                        {$aProduct.version}
                        {else}
                        {_p var='n_a'}
                        {/if}
                        </td>
                    <td class="text-center">
                    {if isset($aProduct.upgrade_version)}
                        <a href="{url link='admincp.product' upgrade=$aProduct.product_id}" class="action_link">
                            {_p var='upgrade_upgrade_version' upgrade_version=$aProduct.upgrade_version}</a>
                    {else}
                        {_p var='n_a'}
                    {/if}
                    </td>
                </tr>
                {/foreach}
            </tbody>
	    </table>
    </div>
</form>
{else}
<div class="alert alert-empty" role="alert">
    {_p var='no_modules_have_been_installed'}.
</div>
{/if}