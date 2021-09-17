<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div id="content_attachment_{$aRow.attachment_id}" class="is-active content_attachment">
    <div id="file-icon-wrapper" class="icon-wrapper">
        {if $aRow.is_image}
        <i class="fa fa-file-image-o" aria-hidden="true"></i>
        {elseif $aRow.is_video}
        <i class="fa fa-file-video-o" aria-hidden="true"></i>
        {else}
        <i class="fa fa-file" aria-hidden="true"></i>
        {/if}
    </div>

    <div id="details">
        <span id="title-area">
            <b>{_p var="File name"}:</b>
            <a id="file-link" class="no_ajax" tabindex="0" role="link" href="{url link='attachment.download' id= $aRow.attachment_id}">
                {$aRow.file_name}
            </a>
        </span>
        <span id="title-area">
            <b>{_p var="By"}:</b>
            <a id="file-link" class="no_ajax" tabindex="0" role="link" href="{url link=$aRow.user_name}">
                {$aRow.full_name}
            </a>
        </span>

        {$aRow.using}

        <div id="description">{$aRow.description}</div>
    </div>

    <div id="remove-wrapper" class="icon-wrapper">
        <span onclick="tb_show('{_p var='Notice'}', $.ajaxBox('attachment.deleteAttachment', 'height=400&amp;width=600&amp;TB_inline=1&amp;call=attachment.deleteAttachment&amp;type=delete&amp;item_id={$aRow.attachment_id}'));" id="remove" title="{_p var='Remove from list'}" role="button">
            <i class="fa fa-times" aria-hidden="true"></i></span>
    </div>
</div>