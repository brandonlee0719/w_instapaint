<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{if count($aPlacements)}
<div class="panel panel-default table-responsive">
    <table class="table table-admin">
        <thead>
            <tr>
                <th class="w30"></th>
                <th>{_p var='title'}</th>
                <th class="w50">{_p var='active'}</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$aPlacements key=iKey item=aPlacement}
            <tr class="{if is_int($iKey/2)} tr{else}{/if}">
                <td>
                    <a href="#" class="js_drop_down_link" title="{_p var='manage'}"></a>
                    <div class="link_menu">
                        <ul class="dropdown-menu">
                            <li><a href="{url link='admincp.ad.placement.add' id=$aPlacement.plan_id}">{_p var='edit'}</a></li>
                            <li><a href="{url link='admincp.ad.placement' delete=$aPlacement.plan_id}" data-message="{_p var='are_you_sure' phpfox_squote=true}" class="sJsConfirm">{_p var='delete'}</a></li>
                        </ul>
                    </div>
                </td>
                <td>{$aPlacement.title|clean}</td>
                <td class="t_center">
                    <div class="js_item_is_active"{if !$aPlacement.is_active} style="display:none;"{/if}>
                        <a href="#?call=ad.updateAdPlacementActivity&amp;id={$aPlacement.plan_id}&amp;active=0" class="js_item_active_link" title="{_p var='deactivate'}"></a>
                    </div>
                    <div class="js_item_is_not_active"{if $aPlacement.is_active} style="display:none;"{/if}>
                        <a href="#?call=ad.updateAdPlacementActivity&amp;id={$aPlacement.plan_id}&amp;active=1" class="js_item_active_link" title="{_p var='activate'}"></a>
                    </div>
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>
{else}
<div class="alert alert-empty">
	{_p var='no_placements_found'}
</div>
{/if}