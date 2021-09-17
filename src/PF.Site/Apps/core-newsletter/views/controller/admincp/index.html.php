<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if count($aNewsletters)}
<div class="panel panel-default">
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="w20"></th>
                    <th>{_p var='subject'}</th>
                    <th>{_p var='user'}</th>
                    <th>{_p var='added'}</th>
                    <th>{_p var='process__l'}</th>
                    <th class="w120">{_p var='status__l'}</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$aNewsletters item=aNewsletter key=iKey name=newsletters}
            <tr id="js_newsletter_{$aNewsletter.newsletter_id}">
            <td class="t_center">
                <a class="js_drop_down_link" title="Manage"></a>
                <div class="link_menu">
                    <ul class="dropdown-menu">
                        {if $aNewsletter.state == CORE_NEWSLETTER_STATUS_DRAFT}
                            <li><a href="{url link='admincp.newsletter.add' newsletter_id=$aNewsletter.newsletter_id}">{_p var='edit_newsletter'}</a></li>
                            <li><a href="{url link='admincp.newsletter.add' job=$aNewsletter.newsletter_id}" title="{_p var='process_newsletter'}">{_p var='process_newsletter'}</a></li>
                        {/if}
                        {if $aNewsletter.state == CORE_NEWSLETTER_STATUS_COMPLETED}
                            <li><a href="{url link='admincp.newsletter.add' job=$aNewsletter.newsletter_id}" title="{_p var='resend_newsletter'}">{_p var='resend_newsletter'}</a></li>
                        {/if}
                            <li><a class="popup" href="{url link='admincp.newsletter.view' id=$aNewsletter.newsletter_id}" title="{_p var='view_newsletter'}">{_p var='view_newsletter'}</a></li>
                        {if $aNewsletter.state != CORE_NEWSLETTER_STATUS_IN_PROGRESS}
                            <li><a class="sJsConfirm" data-message="{_p var='are_you_sure_you_want_to_delete_this_newsletter_permanently' phpfox_squote=true}" href="{url link='admincp.newsletter' delete={$aNewsletter.newsletter_id}" title="{_p var='delete_newsletter_subject' subject=$aNewsletter.subject|clean}">{_p var='delete_newsletter'}</a></li>
                        {else}
                            <li><a href="{url link='admincp.newsletter.add' job=$aNewsletter.newsletter_id}" title="{_p var='reprocess_newsletter'}">{_p var='reprocess_newsletter'}</a></li>
                            <li><a class="sJsConfirm" data-message="{_p var='are_you_sure_you_want_to_stop_this_newsletter_now' phpfox_squote=true}" href="{url link='admincp.newsletter' stop={$aNewsletter.newsletter_id}" title="{_p var='stop_newsletter_subject' subject=$aNewsletter.subject|clean}">{_p var='stop_newsletter'}</a></li>
                        {/if}
                    </ul>
                </div>
            </td>
            <td>{$aNewsletter.subject}</td>
            <td>{$aNewsletter|user}</td>
            <td>{$aNewsletter.time_stamp|date:'core.global_update_time'}</td>
            <td>{if $aNewsletter.state == CORE_NEWSLETTER_STATUS_DRAFT}{_p var='none'}{else}{$aNewsletter.total_sent}/{$aNewsletter.total_users} {_p var='sent_emails'}{/if}</td>
            <td>
                {if $aNewsletter.state == CORE_NEWSLETTER_STATUS_DRAFT}
                    <span class="label label-info">{_p var='not_started'}</span>
                {elseif $aNewsletter.state == CORE_NEWSLETTER_STATUS_IN_PROGRESS}
                    <span class="label label-warning">{_p var='sending'}</span>
                {else}
                    <span class="label label-success">{_p var='completed'}</span>
                {/if}
            </td>
            </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>
{else}
    <div class="alert alert-danger">
        {_p var='no_newsletters_to_show'}
    </div>
{/if}
