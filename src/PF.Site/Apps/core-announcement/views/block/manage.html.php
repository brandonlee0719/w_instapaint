<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            <a href="{url link='admincp.app' id='Core_Announcement'}">
                {_p var='manage_announcements'}
            </a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-admin">
            <thead>
                <tr>
                    <th class="w20"></th>
                    <th>{_p var='subject'}</th>
                    <th class="w140">{_p var='since'}</th>
                    <th class="w60">{_p var='active'}</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$aAnnouncements key=iKey item=aAnnouncement}
            <tr>
                <td class="t_center">
                    <a class="js_drop_down_link" title="Manage"></a>
                    <div class="link_menu">
                        <ul class="dropdown-menu">
                            <li><a href="{url link='admincp.announcement.add' announcement_id=$aAnnouncement.announcement_id}">{_p var='edit'}</a></li>
                            <li><a href="{url link='admincp.announcement.manage' delete=$aAnnouncement.announcement_id}" data-message="{_p var='are_you_sure_you_want_to_delete_this_announcement_permanently' phpfox_squote=true}" class="sJsConfirm">{_p var='delete'}</a></li>
                        </ul>
                    </div>
                </td>
                <td>{_p var=$aAnnouncement.subject_var}</td>
                <td class="t_center">{$aAnnouncement.start_date|date:'core.global_update_time'}</td>
                <td class="t_center">
                    <div class="js_item_is_active" style="{if !$aAnnouncement.is_active}display: none{/if}">
                        <a href="#?call=announcement.toggleActiveAnnouncement&amp;aid={$aAnnouncement.announcement_id}&amp;active=0" class="js_item_active_link" title="{_p var='deactivate'}"></a>
                    </div>
                    <div class="js_item_is_not_active" style="{if $aAnnouncement.is_active}display: none{/if}">
                        <a href="#?call=announcement.toggleActiveAnnouncement&amp;aid={$aAnnouncement.announcement_id}&amp;active=1" class="js_item_active_link" title="{_p var='activate'}"></a>
                    </div>
                </td>
            </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>
