<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">{_p var='default_user_groups'}</div>
    </div>
    <table class="table table-admin">
        <thead>
        <tr>
            <th class="w20"></th>
            <th>{_p var='title'}</th>
            <th class="w100">{_p var='users'}</th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$aGroups.special key=iKey item=aGroup}
        <tr>
            <td class="t_center">
                {if Phpfox::getUserParam('user.can_edit_user_group') || Phpfox::getUserParam('user.can_manage_user_group_settings')}
                <a href="#" class="js_drop_down_link" title="Manage"></a>
                <div class="link_menu">
                    <ul class="dropdown-menu">
                        {if Phpfox::getUserParam('user.can_manage_user_group_settings')}
                        <li><a href="{url link='admincp.user.group.add' group_id=$aGroup.user_group_id setting='true' module='core'}">{_p var='manage_user_settings'}</a></li>
                        {/if}
                        {if Phpfox::getUserParam('user.can_edit_user_group')}
                        <li><a href="{url link='admincp.user.group.add' group_id=$aGroup.user_group_id}" class="popup">{_p var='edit_user_group'}</a></li>
                        {/if}
                        <li><a href="{url link='admincp.user.group.activitypoints' id=$aGroup.user_group_id}" class="popup">{_p var='manage_activity_points'}</a></li>
                    </ul>
                </div>
                {/if}
            </td>
            <td>{$aGroup.title|convert|clean}</td>
            <td>{if $aGroup.user_group_id == 3}N/A{else}{$aGroup.total_users}{/if}</td>
        </tr>
        {/foreach}
        </tbody>
    </table>
</div>
{if isset($aGroups.custom)}
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">{_p var='custom_user_groups'}</div>
    </div>
    <div class="table-responsive">
        <table class="table table-admin">
            <thead>
            <tr>
                <th class="w20"></th>
                <th>{_p var='title'}</th>
                <th class="w100">{_p var='users'}</th>
            </tr>
            </thead>
            <tbody>
            {foreach from=$aGroups.custom key=iKey item=aGroup}
            <tr>
                <td class="t_center">
                    {if Phpfox::getUserParam('user.can_edit_user_group') || Phpfox::getUserParam('user.can_manage_user_group_settings') || Phpfox::getUserParam('user.can_delete_user_group')}
                    <a href="#" class="js_drop_down_link" title="Manage"></a>
                    <div class="link_menu">
                        <ul class="dropdown-menu">
                            {if Phpfox::getUserParam('user.can_manage_user_group_settings')}
                            <li><a href="{url link='admincp.user.group.add' group_id=$aGroup.user_group_id setting='true'}">{_p var='manage_user_settings'}</a></li>
                            {/if}
                            {if Phpfox::getUserParam('user.can_edit_user_group')}
                            <li><a href="{url link='admincp.user.group.add' group_id=$aGroup.user_group_id}" class="popup">{_p var='edit_user_group'}</a></li>
                            {/if}
                            {if !$aGroup.is_special && Phpfox::getUserParam('user.can_delete_user_group')}
                            <li><a href="{url link='admincp.user.group.delete' id=$aGroup.user_group_id}">{_p var='delete'}</a></li>
                            {/if}
                            <li><a href="{url link='admincp.user.group.activitypoints' id=$aGroup.user_group_id}" class="popup">{_p var='manage_activity_points'}</a></li>
                        </ul>
                    </div>
                    {/if}
                </td>
                <td>{$aGroup.title|convert|clean}</td>
                <td>{if $aGroup.user_group_id == 3}{_p var='n_a'}{else}{$aGroup.total_users}{/if}</td>
            </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>
{/if}