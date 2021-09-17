<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="attachment-row" id="js_attachment_id_{$aRow.attachment_id}">
    <div id="js_attachment_id_content_{$aRow.attachment_id}" class="item-outer">
        <div class="attachment-image {if !$aRow.is_image && !$aRow.is_video}attachment-image-icon{/if}">
            {if $aRow.is_image}
            <a href="{img server_id=$aRow.server_id title=$aRow.description path='core.url_attachment' file=$aRow.destination max_width='attachment.attachment_max_thumbnail' max_height='attachment.attachment_max_thumbnail' return_url=true}" class="thickbox item-media-src" onclick="$.ajaxCall('attachment.updateCounter', 'item_id={$aRow.attachment_id}'); $('#attachment_counter_{$aRow.attachment_id}').text(parseInt($('#attachment_counter_{$aRow.attachment_id}').text())+1);">
                <span style="background-image: url({img server_id=$aRow.server_id title=$aRow.description path='core.url_attachment' file=$aRow.destination suffix='_thumb' max_width='attachment.attachment_max_thumbnail' max_height='attachment.attachment_max_thumbnail' return_url=true})"></span>
            </a>
            {elseif $aRow.is_video}
            <a href="#" class="play_link" onclick="$.ajaxCall('attachment.playVideo', 'attachment_id={$aRow.attachment_id}', 'GET'); return false;"><span class="play_link_img">{_p var='play'}</span>{img server_id=$aRow.server_id title=$aRow.description path='core.url_attachment' file=$aRow.video_image_destination suffix='_120' max_width='attachment.attachment_max_thumbnail' max_height='attachment.attachment_max_thumbnail'}</a>
            {else}
            <span class="ico ico-file-zip-o"></span>
            {/if}
        </div>
        <div class="attachment-body">
            {if !empty($aRow.description)}
            <div class="attachment_body_description">
                {$aRow.description}
            </div>
            {/if}
            {if $aRow.link_id}
                {module name='link.display' link_id=$aRow.link_id attachment=true}
            {else}
                <div class="attachment-row-title">
                    {if $aRow.is_video}
                    <a href="#" class="attachment_row_link no_ajax_link item-title" onclick="$.ajaxCall('attachment.playVideo', 'attachment_id={$aRow.attachment_id}', 'GET'); return false;">{$aRow.file_name}</a> <span class="item-info">({if !empty($aRow.video_duration)}{$aRow.video_duration}, {/if}{_p var='views' total=$aRow.counter})</span>
                    {else}
                    <a href="{url link='attachment.download' id=''$aRow.attachment_id''}" class="attachment_row_link no_ajax_link item-title">{$aRow.file_name}</a> <span class="item-info">{$aRow.using}</span>
                    {/if}
                </div>
            {/if}
            {if (Phpfox::getUserParam('attachment.delete_own_attachment') && $aRow.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('attachment.delete_user_attachment')}
            <div class="attachment-row-actions">
                <a role="button" onclick="$Core.jsConfirm({l}{r}, function(){l}$.ajaxCall('attachment.deleteAttachment', 'height=400&amp;width=600&amp;TB_inline=1&amp;call=attachment.deleteAttachment&amp;type=delete&amp;item_id={$aRow.attachment_id}'){r}, function(){l}{r});" id="remove" class="js_hover_title">
                    <span class="ico ico-trash-alt-o"></span>
                    <span class="js_hover_info">{_p var='delete_file'}</span>
                </a>
            </div>
            {/if}
        </div>
    </div>
</div>
