<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="hidden" id="current_user_avatar">
    {img user=$aUser suffix='_50_square' class="img-responsive" title=$aUser.full_name}
</div>
<div class="panel-body msg_container_base shoutbox-container">
    {foreach from=$aShoutboxes key=sKey value=aShoutbox}
    <div class="row msg_container {if $aShoutbox.type=='s'} base_sent {else} base_receive {/if}" id="shoutbox_message_{$aShoutbox.shoutbox_id}" data-value="{$aShoutbox.shoutbox_id}">
        <div class="msg_container_row shoutbox-item {if $aShoutbox.type=='s'} item-sent {else} item-receive {/if}">
            {if $aIsAdmin || $iUserId == $aShoutbox.user_id}
            <button type="button" class="close" data-toggle="shoutbox-dismiss" data-value="{$aShoutbox.shoutbox_id}"><i class="ico ico-close-circle" aria-hidden="true"></i></button>
            {/if}
            <div class="item-outer {if $aIsAdmin || $iUserId == $aShoutbox.user_id}can-delete{/if}">
                <div class="item-media-source">
                    {img user=$aShoutbox suffix='_50_square' width=32 height=32 class="img-responsive" title=$aShoutbox.full_name}
                </div>
                <div class="item-inner">  
                    <div class="title_avatar item-shoutbox-body {if $aShoutbox.type=='r'} msg_body_receive {elseif $aShoutbox.type=='s'} msg_body_sent {/if} " title="{$aShoutbox.full_name}" data-toggle="tooltip"> 
                    <div class=" item-title">
                        <a href="{url link=$aShoutbox.user_name}" title="{$aShoutbox.full_name}">
                            {$aShoutbox.full_name}
                        </a>
                    </div>
                    <div class="messages_body item-message">
                        <div class="item-message-info ">
                            {$aShoutbox.text}
                        </div>
                    </div>
                    </div>
                    <span class=" item-time message_convert_time" data-id="{$aShoutbox.timestamp}">{$aShoutbox.timestamp|convert_time}</span>
                </div>   
            </div>
            
            
        </div>
    </div>
    {/foreach}
</div>
{if $bCanShare}
<div class="panel-footer">
    <div class="input-group">
        <textarea rows='1' data-toggle="shoutbox" data-name="text" maxlength="255" id="shoutbox_text_message_field" type="text" class="form-control chat_input" placeholder="{_p var='write_message'}"/></textarea>
        <!-- <span class="input-group-btn">
            <button data-name="shoutbox-submit" class="btn btn-primary" id="btn-chat"><i class="fa fa-arrow-right" aria-hidden="true"></i></button>
        </span> -->
    </div>
    <div class="item-footer-sent">
        <div class="item-count"><span id="pf_shoutbox_text_counter">0</span>/255</div>
        <span class="item-btn-sent">
            <button data-name="shoutbox-submit" class="btn btn-primary btn-xs" id="btn-chat"><i class="ico ico-paperplane" aria-hidden="true"></i></button>
        </span>
    </div>
</div>
{/if}
<input type="hidden" value="{$sModuleId}" data-toggle="shoutbox" data-name="parent_module_id">
<input type="hidden" value="{$iItemId}" data-toggle="shoutbox" data-name="parent_item_id">
<!--error-->
<div id="shoutbox_error" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{_p var="Error"}</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    {_p var="type_something_to_chat"}
                </div>
            </div>
        </div>

    </div>
</div>
