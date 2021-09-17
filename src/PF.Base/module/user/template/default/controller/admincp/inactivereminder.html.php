<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<form method="get" action="{url link='admincp.user.inactivereminder'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            {_p var='inactive_member_reminder'}
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>{_p var='show_users_who_have_not_logged_in_for'}:</label>
                <input type="text" id="inactive_days" name="day" size="3" value="{$iDays}"> {_p var='days'}
            </div>

            <div class="form-group">
                <label>{_p var='this_feature_uses_the_language'}</label>
            </div>
            <hr />
            <div class="form-group">
                <input type="submit" value="{_p var='get_inactive_members'}" class="btn btn-primary" />
                <input type="button" value="{_p var='process_mailing_job_to_all_inactive_members'}" class="btn btn-primary" id="btnSendAll"/>
            </div>
        </div>
    </div>
    <div class="block_content">
            {if $aUsers}
            <div class="table-responsive">
                <table class="table table-admin">
                    <thead>
                        <tr>
                            <th class="w20">
                                {if !PHPFOX_IS_AJAX}<input type="checkbox" name="val[id]" value="" id="js_check_box_all" class="main_checkbox" />{/if}
                            </th>
                            <th class="w20"></th>
                            <th {table_sort class="w60 centered" asc="u.user_id asc" desc="u.user_id desc" query="search[sort]"}>{_p var='id'}</th>
                            <th>{_p var='photo'}</th>
                            <th {table_sort class="centered" asc="u.full_name asc" desc="u.full_name desc" query="search[sort]"}>
                                {_p var='display_name'}
                            </th>
                            <th>{_p var='email_address'}</th>
                            <th>{_p var='group'}</th>
                            <th {table_sort class="centered" asc="u.last_activity asc" desc="u.last_activity desc" query="search[sort]"}>
                                {_p var='last_activity'}</th>
                        </tr>
                    </thead>
                    <tbody>
                    {foreach from=$aUsers name=users key=iKey item=aUser}
                        <tr class="{if empty($aUser.in_process)}checkRow{else}process_mail{/if}{if is_int($iKey/2)} tr{else}{/if}" id="js_user_{$aUser.user_id}">
                            <td>
                                {if !isset($aUser.in_process) || empty($aUser.in_process)}
                                    <input type="checkbox" name="id[]" class="checkbox" value="{$aUser.user_id}" id="js_id_row{$aUser.user_id}" />
                                {/if}
                            </td>
                            <td>
                                {if !isset($aUser.in_process) || empty($aUser.in_process)}
                                    <a href="#" class="js_drop_down_link" title="{_p var='manage'}"></a>
                                    <div class="link_menu">
                                        <ul class="dropdown-menu">
                                            <li><a href="#?call=user.addInactiveJob&amp;id={$aUser.user_id}" class="js_item_active_link">{_p var='process_mailing_job'}</a></li>
                                        </ul>
                                    </div>
                                {/if}
                            </td>
                            <td>{$aUser.user_id}</td>
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
                                {if !empty($aUser.last_ip_address)}
                                <div class="">
                                    (<a href="{url link='admincp.core.ip' search=$aUser.last_ip_address_search}" title="{_p var='view_all_the_activity_from_this_ip'}">{$aUser.last_ip_address}</a>)
                                </div>
                                {/if}
                            </td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>

            {pager}
            {else}
            <div class="alert alert-empty">
                {_p var="No members found."}
            </div>
            {/if}

            <div class="table_hover_action">
                <input type="submit" name="resend-verify" value="{_p var='process_mailing_job_to_selected'}" class="btn sJsCheckBoxButton disabled" disabled="disabled" />
            </div>
    </div>
</form>
