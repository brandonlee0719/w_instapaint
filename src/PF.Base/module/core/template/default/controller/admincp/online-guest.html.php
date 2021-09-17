<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: online-guest.html.php 3622 2011-11-30 12:34:24Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if count($aGuests)}
<div class="table-responsive">
    <table class="table table-admin">
        <thead>
            <tr>
                <th>{_p var='ip_address'}</th>
                <th>{_p var='user_agent'}</th>
                <th class="t_center" style="width:70px;">{_p var='banned'}</th>
                <th>{_p var='last_activity'}</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$aGuests key=iKey item=aGuest}
            <tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                <td><a href="{url link='admincp.core.ip' search=$aGuest.ip_address_search}" title="{_p var='view_all_the_activity_from_this_ip'}">{$aGuest.ip_address|clean}</a></td>
                <td>{$aGuest.user_agent|clean}</td>
                <td class="on_off">
                    <div class="js_item_is_active"{if !$aGuest.ban_id} style="display:none;"{/if}>
                        <a href="#?call=ban.ip&amp;ip={$aGuest.ip_address}&amp;active=0" class="js_item_active_link" title="{_p var='unban'}"></a>
                    </div>
                    <div class="js_item_is_not_active"{if $aGuest.ban_id} style="display:none;"{/if}>
                        <a href="#?call=ban.ip&amp;ip={$aGuest.ip_address}&amp;active=1" class="js_item_active_link" title="{_p var='ban'}"></a>
                    </div>
                </td>
                <td>{$aGuest.last_activity|date:'core.global_update_time'}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>
{pager}
{else}
<div class="alert alert-empty">
	{_p var='no_guests_online'}
</div>
{/if}