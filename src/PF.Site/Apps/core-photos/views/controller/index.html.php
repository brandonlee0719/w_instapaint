<?php

defined('PHPFOX') or exit('NO DICE!');

?>
{if !PHPFOX_IS_AJAX}
    {if isset($bSpecialMenu) && $bSpecialMenu == true}
        {template file='photo.block.specialmenu'}
    {/if}
<div id="js_actual_photo_content">
    <div id="js_album_outer_content">
        {/if}
        {if count($aPhotos)}
            {if isset($bIsEditMode)}
                {if !PHPFOX_IS_AJAX}
                    <form class="form photo-app-manage" method="post" action="#" onsubmit="$('#js_photo_multi_edit_image').show(); $('#js_photo_multi_edit_submit').hide(); $(this).ajaxCall('photo.massUpdate'{if $bIsMassEditUpload}, 'is_photo_upload=1{/if}{if $bIsMassEdit}&mass_edit=1{/if}'); return false;">
                    <div class="clearfix item-photo-edit">
                {/if}
                        {foreach from=$aPhotos item=aForms}
                            {template file='photo.block.edit-photo'}
                        {/foreach}
                        {pager}
                {if !PHPFOX_IS_AJAX}
                    </div>
                    <div class="photo_table_clear">
                        <div id="js_photo_multi_edit_image" style="display:none;">
                            {img theme='ajax/add.gif'}
                        </div>
                        <div id="js_photo_multi_edit_submit" class="pull-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="ico ico-check-circle-alt hide mr-1"></i>
                                {_p var='update_photo_s'}
                            </button>
                        </div>
                    </div>
                </form>
                {/if}
            {else}
                {if !PHPFOX_IS_AJAX}
                    <div class="item-container photo-listing clearfix" id="photo_collection">
                {/if}
                        {foreach from=$aPhotos item=aForms}
                            {template file="photo.block.photo_entry"}
                        {/foreach}
                        {pager}
                {if !PHPFOX_IS_AJAX}
                    </div>
                {/if}
            {/if}
            {if $bShowModerator}
                {moderation}
            {/if}
        {else}
            {if !PHPFOX_IS_AJAX}
                <div class="extra_info">
                    {_p var='no_photos_found'}
                </div>
            {/if}
        {/if}
        {if !PHPFOX_IS_AJAX}
    </div>
</div>
{/if}