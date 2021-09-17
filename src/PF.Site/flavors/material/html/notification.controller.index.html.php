<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
{if isset($bNoContent) && $bNoContent}
{else}
    {if count($aNotifications)}
    <div id="js_notification_holder">
        <ul class="notification_holder">
        {foreach from=$aNotifications name=notifications key=sDate item=aSubNotifications}
            <li class="notification_date">{$sDate}</li>
            {foreach from=$aSubNotifications item=aNotification}
                <li id="js_notification_{$aNotification.notification_id}" class="all-notification-item {if !$aNotification.is_read} is_new{/if}">
                    <div class="item-outer">
                        <div class="item-avatar">   
                            {img user=$aNotification max_width='50' max_height='50' suffix='_50_square' }
                        </div>
                        <div class="item-inner">
                            <a href="{$aNotification.link}" class="main_link{if !$aNotification.is_read} is_new{/if}">
                                {$aNotification.message}
                            </a>
                            <span class="extra_info">
                                {$aNotification.time_stamp|convert_time}
                            </span>
                            <a href=" {$aNotification.link}" class="main-link-bg-click" onclick="$.ajaxCall('notification.markAsRead', 'notification_id={$aNotification.notification_id}');"></a>
                            
                        </div>
                        <span class="notification_delete">
                            <a href="#" class="js_hover_title" onclick="$.ajaxCall('notification.delete', 'id={$aNotification.notification_id}'); return false;">
                                <span class="ico ico-close"></span>
                                <span class="js_hover_info">
                                    {_p var='delete_this_notification'}
                                </span>
                            </a>
                        </span>
                    </div>
                </li>
            {/foreach}
        {/foreach}
            {pager}
        </ul>
        {if !PHPFOX_IS_AJAX}
        <ul class="table_clear_button" id="js_notification_list_delete">
            <li><input type="button" value="{_p var='delete_all_notifications'}" class="button" onclick="$Core.processForm('#js_notification_list_delete'); $(this).ajaxCall('notification.removeAll', 'redirect=1'); return false;" /></li>
            <li class="table_clear_ajax"></li>
        </ul>
        {/if}
        <div class="clear"></div>
    </div>
    {/if}

    <div id="js_no_notifications"{if count($aNotifications)} style="display:none;"{/if}>
        <div class="extra_info">
            {_p var='no_new_notifications'}
        </div>
    </div>
{/if}