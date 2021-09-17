<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}" id="js_user_{$aUser.user_id}">
    <td>
    {if $aUser.user_group_id == ADMIN_USER_ID && Phpfox::getUserBy('user_group_id') != ADMIN_USER_ID}

    {else}
    <input type="checkbox" name="id[]" class="checkbox" value="{$aUser.user_id}" id="js_id_row{$aUser.user_id}" />

    {/if}
    </td>
    <td>
        <a href="#" class="js_drop_down_link" title="{_p var='manage'}"></a>
        <div class="link_menu">
            <ul class="dropdown-menu">
                {if $aUser.user_group_id == ADMIN_USER_ID && Phpfox::getUserBy('user_group_id') != ADMIN_USER_ID}

                {else}
                <li><a href="{url link='admincp.user.add' id=$aUser.user_id}">{_p var='edit_user'}</a></li>
                {/if}
                <li><a href="#" onclick="tb_show('{_p var='statistics_of_user' user_name=$aUser.full_name}',$.ajaxBox('user.getUserStatistic','height=500&amp;width=400&amp;&amp;iUser={$aUser.user_id}'));return false;">{_p var='statistics'}</a></li>
                {if $aUser.view_id == '1'}
                <li class="js_user_pending_{$aUser.user_id}">
                    <a href="" onclick="$.ajaxCall('user.userPending', 'type=1&amp;user_id={$aUser.user_id}'); return false;">
                        {_p var='approve_user'}
                    </a>
                </li>
                <li class="js_user_pending_{$aUser.user_id}">
                    <a href="" onclick="tb_show('{_p var='deny_user' phpfox_squote=true}', $.ajaxBox('user.showDenyUser', 'height=240&amp;width=400&amp;iUser={$aUser.user_id}'));return false;">
                        {_p var='deny_user'}
                    </a>
                </li>
                <!-- onclick="" -->
                {/if}
                <li class="js_feature_{$aUser.user_id}">{if !isset($aUser.is_featured) || $aUser.is_featured < 0}<a href="#" onclick="$.ajaxCall('user.feature', 'user_id={$aUser.user_id}&amp;feature=1'); return false;">{_p var='feature_user'}{else}<a href="#" onclick="$.ajaxCall('user.feature', 'user_id={$aUser.user_id}&amp;feature=0'); return false;">{_p var='unfeature_user'}{/if}</a></li>
                {if (isset($aUser.pendingMail) && $aUser.pendingMail != '') || (isset($aUser.unverified) && $aUser.unverified > 0)}
                <li class="js_verify_email_{$aUser.user_id}"><a href="#" onclick="$.ajaxCall('user.verifySendEmail', 'iUser={$aUser.user_id}'); return false;">{_p var='resend_verification_mail'}</a></li>
                <li class="js_verify_email_{$aUser.user_id}"><a href="#" onclick="$.ajaxCall('user.verifyEmail', 'iUser={$aUser.user_id}'); return false;">{_p var='verify_this_user'}</a></li>
                {/if}
                {if $aUser.user_group_id == ADMIN_USER_ID && Phpfox::getUserBy('user_group_id') == ADMIN_USER_ID}

                {else}
                <li id="js_ban_{$aUser.user_id}">
                    {if $aUser.is_banned}
                    <a role="button" onclick="$.ajaxCall('user.ban', 'user_id={$aUser.user_id}&amp;type=0'); return false;">{_p var='un_ban_user'}</a>
                    {else}
                    <a class="popup" href="{url link='admincp.user.ban' user=$aUser.user_id}">
                        {_p var='ban_user'}
                    </a>
                    {/if}
                </li>
                {/if}

                {if Phpfox::getUserParam('user.can_delete_others_account')}
                {if $aUser.user_group_id == ADMIN_USER_ID && Phpfox::getUserBy('user_group_id') != ADMIN_USER_ID}
                {else}
                <li><a href="#" onclick="tb_show('{_p var='delete_user' phpfox_squote=true}', $.ajaxBox('user.deleteUser', 'height=240&amp;width=400&amp;iUser={$aUser.user_id}'));return false;" title="{_p var='delete_user_full_name' full_name=$aUser.full_name|clean}">{_p var='delete_user'}</a></li>
                {/if}
                {/if}
                {if Phpfox::getUserParam('user.can_member_snoop')}
                <li><a href="{url link='admincp.user.snoop' user=$aUser.user_id}" >{_p var='log_in_as_this_user'}</a></li>
                {/if}
            </ul>
        </div>
    </td>
    <td>#{$aUser.user_id}</td>
    {if isset($bShowFeatured) && $bShowFeatured == 1}
    <td class="drag_handle"><input type="hidden" name="val[ordering][{$aUser.user_id}]" value="{$aUser.featured_order}" /></td>
    {/if}


    <td>{img user=$aUser suffix='_50_square' max_width=50 max_height=50}</td>
    <td>{$aUser|user}</td>
    <td><a href="mailto:{$aUser.email}">{if (isset($aUser.pendingMail) && $aUser.pendingMail != '')} {$aUser.pendingMail} {else} {$aUser.email} {/if}</a>{if isset($aUser.unverified) && $aUser.unverified > 0} <span class="js_verify_email_{$aUser.user_id}" onclick="$.ajaxCall('user.verifyEmail', 'iUser={$aUser.user_id}');">{_p var='verify'}</span>{/if}</td>
    <td>
        {if ($aUser.status_id == 1)}
        <div class="js_verify_email_{$aUser.user_id}">{_p var='pending_email_verification'}</div>
        {/if}
        {if Phpfox::getParam('user.approve_users') && $aUser.view_id == '1'}
        <span id="js_user_pending_group_{$aUser.user_id}">{_p var='pending_approval'}</span>
        {elseif $aUser.view_id == '2'}
        {_p var='not_approved'}
        {else}
        {$aUser.user_group_title|convert}
        {/if}
    </td>
    <td>
        {if $aUser.last_activity > 0}
        {$aUser.last_activity|date:'core.profile_time_stamps'}
        {/if}
    </td>
    <td>
        {if !empty($aUser.last_ip_address)}
        <div class="">
            <a href="{url link='admincp.core.ip' search=$aUser.last_ip_address_search}" title="{_p var='view_all_the_activity_from_this_ip'}">{$aUser.last_ip_address}</a>
        </div>
        {/if}
    </td>
</tr>