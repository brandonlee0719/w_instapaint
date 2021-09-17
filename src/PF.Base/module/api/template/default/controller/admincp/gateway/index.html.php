<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="table-responsive">
    <table class="table table-admin">
        <thead>
            <tr>
                <th class="w30"></th>
                <th>{_p var='title'}</th>
                <th class="t_center w100">{_p var='test_mode'}</th>
                <th class="t_center w100">{_p var='active'}</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$aGateways key=iKey item=aGateway}
            <tr>
                <td>
                    <a href="#" class="js_drop_down_link" title="Manage"></a>
                    <div class="link_menu">
                        <ul class="dropdown-menu">
                            <li><a href="{url link='admincp.api.gateway.add' id={$aGateway.gateway_id}" class="popup">{_p var='edit'}</a></li>
                        </ul>
                    </div>
                </td>
                <td>{$aGateway.title}</td>
                <td class="on_off">
                    <div class="js_item_is_active {if !$aGateway.is_test}hide{/if}">
                        <a href="#?call=api.updateGatewayTest&amp;gateway_id={$aGateway.gateway_id}&amp;active=0" class="js_item_active_link" title="{_p var='disable_test_mode'}"></a>
                    </div>
                    <div class="js_item_is_not_active {if $aGateway.is_test}hide{/if}">
                        <a href="#?call=api.updateGatewayTest&amp;gateway_id={$aGateway.gateway_id}&amp;active=1" class="js_item_active_link" title="{_p var='enable_test_mode'}"></a>
                    </div>
                </td>
                <td class="on_off">
                    <div class="js_item_is_active {if !$aGateway.is_active}hide{/if}">
                        <a href="#?call=api.updateGatewayActivity&amp;gateway_id={$aGateway.gateway_id}&amp;active=0" class="js_item_active_link" title="{_p var='deactivate'}"></a>
                    </div>
                    <div class="js_item_is_not_active {if $aGateway.is_active}hide{/if}">
                        <a href="#?call=api.updateGatewayActivity&amp;gateway_id={$aGateway.gateway_id}&amp;active=1" class="js_item_active_link" title="{_p var='activate'}"></a>
                    </div>
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>