<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright        [PHPFOX_COPYRIGHT]
 * @author           Raymond_Benc
 * @package          Phpfox
 * @version          $Id: template-notification.html.php 2838 2011-08-16 19:09:21Z Raymond_Benc $
 */

defined('PHPFOX') or exit('NO DICE!');

?>

{if Phpfox::isUser()}
<nav class="pull-right">
    <ul class="list-inline header-right-menu">
        {if Phpfox::getUserBy('profile_page_id') > 0}
        {else}
        <li class="pl-5" id="hd-request">
            <a role="button" title="{_p('friend_requests')}"
               data-toggle="dropdown"
               class="btn-abr"
               data-panel="#request-panel-body"
               data-url="{url link='friend.panel'}">
                <i class="fa fa-user-plus"></i>
                <span id="js_total_new_friend_requests"></span>
            </a>
            <div class="dropdown-panel">
                <div class="dropdown-panel-body" id="request-panel-body"></div>
            </div>
        </li>
        <li class="pl-5" id="hd-notification">
            <a role="button" title="{_p('notifications')}"
               class="btn-abr"
               data-panel="#notification-panel-body"
               data-toggle="dropdown"
               data-url="{url link='notification.panel'}">
                <i class="fa fa-bell"></i>
                <span id="js_total_new_notifications"></span>
            </a>
            <div class="dropdown-panel">
                <div class="dropdown-panel-body" id="notification-panel-body"></div>
            </div>
        </li>
        <li class="pl-5" id="hd-message">
            <a role="button" title="{_p('messages')}"
               class="btn-abr"
               data-toggle="dropdown"
               data-panel="#message-panel-body"
               data-url="{url link='mail.panel'}">
                <i class="fa fa-comment"></i>
                <span id="js_total_new_messages"></span>
            </a>
            <div class="dropdown-panel">
                <div class="dropdown-panel-body" id="message-panel-body"></div>
            </div>
        </li>
        {/if}
        <li class="pl-0" id="hd-cof">
            <a href="#" title="{_p('account')}"
               class="btn-abr"
               data-toggle="dropdown"
               type="button"
               aria-haspopup="true"
               aria-expanded="false">
                <i class="fa fa-cog"></i>
            </a>

            {if Phpfox::getUserBy('profile_page_id') > 0}
            <ul class="dropdown-menu dropdown-menu-right dont-unbind">
                <li class="header_menu_user_link_page">
                    <a href="#" onclick="$.ajaxCall('pages.logBackIn'); return false;">
                            <i class="fa fa-reply" aria-hidden="true"></i>
                            {_p var='log_back_in_as_global_full_name'
                            global_full_name=$aGlobalProfilePageLogin.full_name|clean}
                    </a>
                </li>
                <li>
                    <a href="{url link='pages.add' id=$iGlobalProfilePageId}">
                        <i class="fa fa-cog"></i>
                        {_p var='edit_page'}
                    </a>
                </li>
            </ul>
            {else}
            <ul class="dropdown-menu dropdown-menu-right dont-unbind">
                {if Phpfox::isModule('pages') && Phpfox::getUserParam('pages.can_add_new_pages')}
                <li>
                    <a href="#" onclick="$Core.box('pages.login', 400); return false;">
                        <i class="fa fa-flag"></i>
                        {_p var='login_as_page'}
                    </a>
                </li>
                {/if}
                <li role="presentation">
                    <a href="{url link='user.setting'}" class="no_ajax">
                        <i class="fa fa-cog"></i>
                        {_p var='account_settings'}
                    </a>
                </li>
                <li role="presentation">
                    <a href="{url link='user.profile'}" class="no_ajax">
                        <i class="fa fa-edit"></i>
                        {_p var='edit_profile'}
                    </a>
                </li>
                <li role="presentation">
                    <a href="{url link='friend'}" class="no_ajax">
                        <i class="fa fa-group"></i>
                        {_p var='manage_friends'}
                    </a>
                </li>
                <li role="presentation">
                    <a href="{url link='user.privacy'}" class="no_ajax">
                        <i class="fa fa-shield"></i>
                        {_p var='privacy_settings'}
                    </a>
                </li>
                {plugin call='core.template-notification-custom'}
                {if Phpfox::isAdmin() }
                <li class="divider"></li>
                <li role="presentation">
                    <a href="{url link='admincp'}" class="no_ajax">
                        <i class="fa fa-diamond"></i>
                        {_p var='menu_admincp'}
                    </a>
                </li>
                {/if}
                {plugin call='core.template_block_notification_dropdown_menu'}
                <li class="divider"></li>
                <li role="presentation">
                    <a href="{url link='user.logout'}" class="no_ajax logout">
                        <i class="fa fa-toggle-off"></i>
                        {_p var='logout'}
                    </a>
                </li>
            </ul>
            {/if}
        </li>
        <li class="pl-5" id="hd-user">
            {img user=$aGlobalUser suffix='_50_square'}
        </li>
    </ul>
</nav>
{else}
<div class="guest_login_small pull-right" data-component="guest-actions">
    <a class="btn btn01 btn-success test text-uppercase {if Phpfox::canOpenPopup('login')}popup{else}no_ajax{/if}"
       rel="hide_box_title" role="link" href="{url link='login'}">
        <i class="fa fa-sign-in"></i> {_p var='sign_in'}
    </a>
    {if Phpfox::getParam('user.allow_user_registration') && !Phpfox::getParam('user.invite_only_community')}
    <a class="btn btn02 btn-warning text-uppercase {if Phpfox::canOpenPopup('user.register')}popup{else}no_ajax{/if}"
       rel="hide_box_title" role="link" href="{url link='user.register'}">
        {_p var='sign_up'}
    </a>
    {/if}
</div>
{/if}
