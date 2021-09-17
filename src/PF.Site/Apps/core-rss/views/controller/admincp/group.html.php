<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{if !count($aGroups)}
<div class="alert alert-danger">
    {_p var='no_groups_found'}
</div>
{else}
<div class="panel panel-default">
    <div class="table-responsive flex-sortable">
        <table class="table table-bordered" id="_sort" data-sort-url="{url link='rss.admincp.group.order'}">
            <thead>
                <tr>
                    <th class="w30"></th>
                    <th class="w30"></th>
                    <th>{_p var='title'}</th>
                    <th class="t_center w60">{_p var='active'}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$aGroups key=iKey item=aGroup}
                <tr class="{if is_int($iKey/2)} tr{else}{/if}" data-sort-id="{$aGroup.group_id}">
                    <td class="t_center w30">
                        <i class="fa fa-sort"></i>
                    </td>
                    <td class="t_center">
                        <a href="#" class="js_drop_down_link" title="Manage"></a>
                        <div class="link_menu">
                            <ul>
                                <li><a href="{url link='admincp.rss.add-group' group_id={$aGroup.group_id}" class="popup">{_p var='edit_group'}</a></li>
                                <li><a href="{url link='admincp.rss.group' delete={$aGroup.group_id}" class="sJsConfirm" data-message="{_p var='are_you_sure' phpfox_squote=true}">{_p var='delete_group'}</a></li>
                            </ul>
                        </div>
                    </td>
                    <td class="td-flex">{_p var=$aGroup.name_var}</td>
                    <td class="t_center w60">
                        <div class="js_item_is_active" style="{if !$aGroup.is_active}display:none;{/if}">
                            <a href="#?call=rss.updateGroupActivity&amp;id={$aGroup.group_id}&amp;active=0" class="js_item_active_link" title="{_p var='deactivate'}"></a>
                        </div>
                        <div class="js_item_is_not_active" style="{if $aGroup.is_active}display:none;{/if}">
                            <a href="#?call=rss.updateGroupActivity&amp;id={$aGroup.group_id}&amp;active=1" class="js_item_active_link" title="{_p var='activate'}"></a>
                        </div>
                    </td>
                </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
</div>
{/if}
