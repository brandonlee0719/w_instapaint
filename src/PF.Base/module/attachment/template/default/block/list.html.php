<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{if count($aAttachments)}
{if empty($bIsGetAttachmentList)}
<div class="{if $bIsAttachmentNoHeader}attachment_holder{else}attachment_holder_view{/if}">
	{if !$bIsAttachmentNoHeader}
	<div class="attachment_header_holder">
		<div class="attachment_header">{_p var='attachments_headline'}</div>
	</div>
	{/if}
    <div class="attachment_list">
    {/if}
	{foreach from=$aAttachments key=iKey item=aAttachment}
		<div class="attachment-row" id="js_attachment_id_{$aAttachment.attachment_id}">
            <div id="js_attachment_id_content_{$aAttachment.attachment_id}" class="item-outer">
                <div class="attachment-image {if !$aAttachment.is_image && !$aAttachment.is_video}attachment-image-icon{/if}">
                    {if $aAttachment.is_image}
                    <a href="{img server_id=$aAttachment.server_id title=$aAttachment.description path='core.url_attachment' file=$aAttachment.destination max_width='attachment.attachment_max_thumbnail' max_height='attachment.attachment_max_thumbnail' return_url=true}" class="thickbox item-media-src" onclick="$.ajaxCall('attachment.updateCounter', 'item_id={$aAttachment.attachment_id}'); $('#attachment_counter_{$aAttachment.attachment_id}').text(parseInt($('#attachment_counter_{$aAttachment.attachment_id}').text())+1);">
                        <span style="background-image: url({img server_id=$aAttachment.server_id title=$aAttachment.description path='core.url_attachment' file=$aAttachment.destination suffix='_thumb' max_width='attachment.attachment_max_thumbnail' max_height='attachment.attachment_max_thumbnail' return_url=true})"></span>
                    </a>
                    {elseif $aAttachment.is_video}
                    <a role="button" class="play_link" onclick="$.ajaxCall('attachment.playVideo', 'attachment_id={$aAttachment.attachment_id}', 'GET'); return false;"><span class="play_link_img">Play</span>{img server_id=$aAttachment.server_id title=$aAttachment.description path='core.url_attachment' file=$aAttachment.video_image_destination suffix='_120' max_width='attachment.attachment_max_thumbnail' max_height='attachment.attachment_max_thumbnail'}</a>
                    {else}
                    <span class="ico ico-file-zip-o"></span>
                    {/if}
                </div>
                <div class="attachment-body">
                    {if !empty($aAttachment.description)}
                    <div class="attachment_body_description">
                        {$aAttachment.description}
                    </div>
                    {/if}
                    {if $aAttachment.link_id}
                    {module name='link.display' link_id=$aAttachment.link_id attachment=true}
                    {else}
                    <div class="attachment-row-title">
                        {if $aAttachment.is_video}
                        <a role="button" class="attachment_row_link no_ajax_link item-title" onclick="$.ajaxCall('attachment.playVideo', 'attachment_id={$aAttachment.attachment_id}', 'GET'); return false;">{$aAttachment.file_name}</a> <span class="item-info">({if !empty($aAttachment.video_duration)}{$aAttachment.video_duration}, {/if}{_p var='views' total=$aAttachment.counter})</span>
                        {else}
                        <a href="{url link='attachment.download' id=''$aAttachment.attachment_id''}" class="attachment_row_link no_ajax_link item-title">{$aAttachment.file_name}</a> <span class="item-info">{$aAttachment.file_size|filesize} . <span id="attachment_counter_{$aAttachment.attachment_id}">{$aAttachment.counter}</span> {_p var='views'}</span>
                        {/if}
                    </div>
                    <div class="attachment-row-actions">
                        {if $bIsAttachmentNoHeader}
                        <span class="js_attachment_remove_inline" style="display: none;">
                            <a role="button" onclick="$Core.Attachment.removeInline(this, {if $aAttachment.inline.path}'{$aAttachment.inline.path}'{else}{$aAttachment.attachment_id}{/if})" data-inline-path="{$aAttachment.inline.path}" class="js_hover_title">
                                <span class="ico ico-godown"></span>
                                <span class="js_hover_info">{_p var='remove_inline'}</span>
                            </a>
                        </span>
                        <span class="js_attachment_add_inline">
                            <a role="button" onclick="$Core.Attachment.insertInline(this, '{$aAttachment.inline.name}', '{$aAttachment.attachment_id}{if $aAttachment.is_image}:view{/if}', '{$aAttachment.inline.path}', '{$aAttachment.inline.url}', {if $aAttachment.is_image}true{else}false{/if})" class="js_hover_title">
                                <span class="ico ico-list-up"></span>
                                <span class="js_hover_info">{_p var='use_inline'}</span>
                            </a>
                        </span>
                        {/if}
                        {if (Phpfox::getUserParam('attachment.delete_own_attachment') && $aAttachment.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('attachment.delete_user_attachment')}
                        <a role="button" onclick="$Core.jsConfirm({left_curly}{right_curly},function(){left_curly}  $.ajaxCall('attachment.delete', 'id={$aAttachment.attachment_id}');{right_curly},function(){left_curly}{right_curly}); return false;" class="js_hover_title">
                            <span class="ico ico-trash-alt-o"></span>
                            <span class="js_hover_info">{_p var='delete_file'}</span>
                        </a>
                        {/if}
                    </div>
                    {/if}
                </div>
            </div>
		</div>
	{/foreach}
    {if empty($bIsGetAttachmentList)}
    </div>
</div>
{/if}
{/if}