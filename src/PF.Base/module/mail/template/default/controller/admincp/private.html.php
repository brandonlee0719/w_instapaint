<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package  		Module_Mail
 * @version 		$Id: private.html.php 4742 2012-09-24 10:38:10Z Raymond_Benc $
 */

?>
<form class="form" method="post" action="{url link='admincp.mail.private'}">
    <div class="panel panel-default">
        <div class="panel-body">
            {_p var='member_search'}
        </div>
        <div class="panel-footer">
            <div class="form-group">
                <label for="">{_p var='search'}</label>
                {filter key='keyword'}
                <p class="help-block"{if Phpfox::getParam('mail.threaded_mail_conversation')} style="display:none;"{/if}>
                    {_p var='within'}: {filter key='type'}
                </p>
            </div>
            <div class="form-group">
                <label for="">{_p var='user_group'}</label>
                {filter key='group'}
            </div>
            <div class="form-group">
                <label for="">{_p var='message_sender'}</label>
                {filter key='sender'}
            </div>
            <input type="submit" name="search[submit]" value="{_p var='submit'}" class="btn btn-primary" />
        </div>
    </div>
</form>
{pager}

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">{_p var='messages_title'}</div>
    </div>
    <div class="table-responsive">
        <table class="table table-admin" id="js_drag_drop">
            <thead>
            <tr>
                <th class="w30"></th>
                {if !Phpfox::getParam('mail.threaded_mail_conversation')}
                <th>{_p var='from'}</th>
                <th>{_p var='to'}</th>
                <th>{_p var='subject'}</th>
                {/if}
                <th>Conversation</th>
                <th>{_p var='sent'}</th>
            </tr>
            </thead>
            <tbody>
            {foreach from=$aMessages name=messages key=iKey item=aMessage}
            <tr>
                <td class="t_center">
                    <a href="#" class="js_drop_down_link" title="Manage"></a>
                    <div class="link_menu">
                        <ul class="dropdown-menu">
                            <li><a href="#" onclick="tb_show('', $.ajaxBox('mail.readMessage', 'id={if Phpfox::getParam('mail.threaded_mail_conversation')}{$aMessage.thread_id}{else}{$aMessage.mail_id}{/if}&amp;height=400&amp;width=600')); return false;">{_p var='read_message'}</a></li>
                            <li><a href="#" onclick="$Core.jsConfirm({l}message:'{_p var='are_you_sure' phpfox_squote=true}'{r}, function(){l} $.ajaxCall('mail.deleteMessage', 'id={if Phpfox::getParam('mail.threaded_mail_conversation')}{$aMessage.thread_id}{else}{$aMessage.mail_id}{/if}');{r}, function(){l}{r}); return false;">{_p var='delete_message'}</a></li>
                            {if !Phpfox::getParam('mail.threaded_mail_conversation')}
                            <li><a href="{url link='mail.compose' id=$aMessage.sender_user_id}"  title="{_p var='message_user'}">{_p var='message_sender'}</a></li>
                            <li><a href="{url link='mail.compose' id=$aMessage.receiver_user_id}"  title="{_p var='message_user'}">{_p var='message_receiver'}</a></li>
                            {/if}
                        </ul>
                    </div>
                </td>
                {if !Phpfox::getParam('mail.threaded_mail_conversation')}
                <td>{$aMessage|user:'sender_'}</td>
                <td>{$aMessage|user:'receiver_'}</td>
                <td><a href="#" onclick="tb_show('', $.ajaxBox('mail.readMessage', 'id={if Phpfox::getParam('mail.threaded_mail_conversation')}{$aMessage.thread_id}{else}{$aMessage.mail_id}{/if}&amp;height=400&amp;width=600')); return false;">{$aMessage.subject}</a></td>
                {else}
                <td>
                    {foreach from=$aMessage.users name=mailusers item=aMailUser}{if count($aMessage.users) == $phpfox.iteration.mailusers && count($aMessage.users) > 1} &amp; {else}{if $phpfox.iteration.mailusers != 1 && count($aMessage.users) != 2}, {/if}{/if}{$aMailUser|user}{/foreach}
                    <div class="extra_info">
                        {$aMessage.preview|strip_tags|shorten:40:'...'}
                    </div>
                </td>
                {/if}
                <td>{$aMessage.time_stamp|date}</td>
            </tr>
            {foreachelse}
            <tr><td colspan="5" style="text-align:center;">{_p var='no_messages_to_show'}</td></tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    <div class="panel-body">
        {pager}
    </div>
</div>
