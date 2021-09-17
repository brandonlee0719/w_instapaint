<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>

{if $iFolder}
<div style="position:absolute; right:0px; top:-15px;">
	<a href="#" onclick="$Core.jsConfirm({l}message: '{_p var='are_you_sure'}'{r}, function(){l} $.ajaxCall('mail.deleteFolder', 'id={$iFolder}'); {r},function(){l}{r}); return false;">{_p var='delete_this_list'}</a>
</div>
{/if}
{if count($aMails)}
<div class="item-container all-message-container" id="collection-mails">
{foreach from=$aMails item=aMail name=mail}
    <article class="mail_holder{if !$bIsSentbox && !$bIsTrash && $aMail.viewer_is_new} mail_is_new{/if} " data-url="{url link='mail.thread' id=$aMail.thread_id}{if $bIsSentbox}view_sent/{/if}" id="js_message_{if Phpfox::getParam('mail.threaded_mail_conversation')}{$aMail.thread_id}{else}{$aMail.mail_id}{/if}">
        <div class=" moderation_row">
                <label class="item-checkbox">
                    <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{if Phpfox::getParam('mail.threaded_mail_conversation')}{$aMail.thread_id}{else}{$aMail.mail_id}{/if}"/>
                    <i class="ico ico-square-o"></i>
                </label>
            </div>
        <div class="item-outer">
                {if Phpfox::getParam('mail.threaded_mail_conversation')}
                <a href="{url link='mail.thread' id=$aMail.thread_id}{if $bIsSentbox}view_sent/{/if}" class="item-click">
                </a>
                {else}
                <a href="{url link='mail.view' id=$aMail.mail_id}" class="item-click">
                </a>
                {/if}
            <div class="notification-delete {if $aMail.viewer_is_new} is_new{/if}">
                <a href="#" class="js_hover_title noToggle" onclick="$.ajaxCall('mail.delete', 'id={$aMail.thread_id}', 'GET'); $(this).parents('li:first').slideUp(); return false;">
                    <span class="ico ico-inbox"></span>
                    <span class="js_hover_info">
                        {_p var='archive'}
                    </span>
                </a>
            </div>
            
            <div class="item-media-src" href="#">
                {if $aMail.user_id == Phpfox::getUserId()}
                    {img user=$aMail suffix='_50_square' max_width=50 max_height=50}
                {else}
                    {if (isset($aMail.user_id) && !empty($aMail.user_id))}
                        {img user=$aMail suffix='_50_square' max_width=50 max_height=50}
                    {/if}
                {/if}
            </div>
            <div class="item-inner">
                <div class="item-title-time">
                    <div class="item-title">
                    {if Phpfox::getParam('mail.threaded_mail_conversation')}
                    <a href="{url link='mail.thread' id=$aMail.thread_id}{if $bIsSentbox}view_sent/{/if}" class="mail_link">
                        {$aMail.thread_name}
                    </a>
                    {else}
                    <a href="{url link='mail.view' id=$aMail.mail_id}" class="mail_link">
                        {if $aMail.parent_id}{_p var='re'}: {/if}{$aMail.subject|shorten:35:'...'|clean}
                    </a>
                    {/if}
                </div>
                    <div class="item-time {if $bIsSentbox && isset($aMail.users_is_read) && count($aMail.users_is_read)}not-has-unread{/if}">
                        <ul>
                        <li>{$aMail.time_stamp|convert_time}</li>
                        {if !$bIsSentbox && !$bIsTrash}
                        <li class="js_mail_mark_read"{if !$aMail.viewer_is_new} style="display:none;"{/if}><a href="#" class="mail_read js_hover_title" onclick="$.ajaxCall('mail.toggleRead', 'id={if Phpfox::getParam('mail.threaded_mail_conversation')}{$aMail.thread_id}{else}{$aMail.mail_id}{/if}', 'GET'); $(this).parent().hide(); $(this).parents('ul:first').find('.js_mail_mark_unread').show(); $(this).parents('.mail_holder:first').removeClass('mail_is_new'); return false;"><span class="js_hover_info">{_p var='mark_as_read'}</span></a></li>
                        <li class="js_mail_mark_unread"{if $aMail.viewer_is_new} style="display:none;"{/if}><a href="#" class="mail_read js_hover_title" onclick="$.ajaxCall('mail.toggleRead', 'id={if Phpfox::getParam('mail.threaded_mail_conversation')}{$aMail.thread_id}{else}{$aMail.mail_id}{/if}', 'GET'); $(this).parent().hide(); $(this).parents('ul:first').find('.js_mail_mark_read').show(); $(this).parents('.mail_holder:first').addClass('mail_is_new'); return false;"><span class="js_hover_info">{_p var='mark_as_unread'}</span></a></li>
                        {/if}
                        
                    </ul>
                    </div>
                </div>
                {if !Phpfox::getParam('mail.threaded_mail_conversation')}
                <div class="item-author dot-separate">
                    {if $aMail.user_id == Phpfox::getUserId()}
                        <span>{_p var='to'}: {_p var='you'}</span>
                    {else}
                        {if $bIsSentbox}
                            <span>{_p var='to'}: {$aMail|user:'':'':50}</span>
                        {else}
                            <span>{_p var='from'}: {if empty($aMail.user_id)}{param var='core.site_title'}{else}{$aMail|user:'':'':50}{/if}</span>
                        {/if}
                    {/if}
                </div>
                {/if}
                {if Phpfox::getParam('mail.show_preview_message')}
                <div class="mail_preview item_view_content">
                    {if isset($aMail.last_user_id) && $aMail.last_user_id == Phpfox::getUserId()}<span class="ico ico-reply-o"></span> {/if}{$aMail.preview|cleanbb|clean}
                </div>
                {/if}
                
            </div>
        </div>
    </article>
{/foreach}
</div>
{elseif !PHPFOX_IS_AJAX}

<div class="extra_info mail_duplication_content">
	{_p var='no_messages_found_here'}
</div>
{/if}
<a role="button" class="item-mark-all-read mail_duplication_content" onclick="$.ajaxCall('mail.markallread', 'reload=1')" >
    <span class="ico ico-check-circle-alt"></span>
    {_p var='mark_all_read'}
</a>
{if $iTotalMessages}
{moderation}
{/if}
{pager}