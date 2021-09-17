<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: $
 */
 
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
                <li id="js_notification_{$aNotification.notification_id}" class="{if !$aNotification.is_read} is_new{/if}">
                    {if !empty($aNotification.icon)}
                        <img src="{$aNotification.icon}" alt="" class="v_middle" />
                    {/if}
                    <a href="{$aNotification.link}" class="main_link{if !$aNotification.is_read} is_new{/if}" onclick="$.ajaxCall('notification.markAsRead', 'notification_id={$aNotification.notification_id}');">
                        {$aNotification.message}
                    </a>
                    - <span class="extra_info">
                        {$aNotification.time_stamp|convert_time}
                    </span>
                    <span class="notification_delete">
                        &nbsp;&nbsp;-&nbsp;&nbsp;
                        <a href="#" class="js_hover_title" onclick="$.ajaxCall('notification.delete', 'id={$aNotification.notification_id}'); return false;">
                            {img theme='misc/delete.gif' class='v_middle'}
                            <span class="js_hover_info">
                                {_p var='delete_this_notification'}
                            </span>
                        </a>
                    </span>
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