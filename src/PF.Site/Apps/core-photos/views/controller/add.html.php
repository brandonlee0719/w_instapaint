<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright       [PHPFOX_COPYRIGHT]
 * @author          phpFox
 * @package         phpFox
 * @version         $Id: add.html.php 6934 2013-11-22 14:26:35Z Fern $
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<div id="js_upload_error_message"></div>
<div id="js_photo_form_holder" class="uploader-photo-fix-height">
    <form class="form" id="js_photo_form">
        <input type="hidden" name="val[timestamp]" value="{$iTimestamp}"/>
        {if $sModuleContainer}
        <div><input type="hidden" name="val[callback_module]" value="{$sModuleContainer}"/></div>
        {/if}
        {if $iItem}
        <div><input type="hidden" name="val[callback_item_id]" value="{$iItem}"/></div>
        <div><input type="hidden" name="val[group_id]" value="{$iItem}"/></div>
        <div><input type="hidden" name="val[parent_user_id]" value="{$iItem}"/></div>
        {/if}
        {plugin call='photo.template_controller_upload_form'}

        {module name='core.upload-form' type='photo' }

        {if Phpfox::getUserParam('photo.can_create_photo_album')}
        <div class="form-group " id="album_table" >
            <div class="item-album mr-1" id="js_photo_albums" {if !count($aAlbums)} style="display:none;"{/if}>
                <label for="js_photo_album_select" id="js_photo_album_select_label">{_p var='photo_album'}</label>
                <span>
                    <select class="form-control" name="val[album_id]" id="js_photo_album_select">
                        <option value="">{_p var='select_an_album'}:</option>
                        {foreach from=$aAlbums item=aAlbum}
                        <option value="{$aAlbum.album_id}" {if $iAlbumId== $aAlbum.album_id} selected{/if}>{$aAlbum.name|clean}</option>
                        {/foreach}
                    </select>
                </span>
            </div>
            <button onclick="$Core.box('photo.newAlbum', 500, 'module={$sModuleContainer}&amp;item={$iItem}'); return false;" type="button" class="btn btn-primary text-capitalize"><i class="ico ico-plus mr-1"></i>{_p var='create_new_album'}</button>
        </div>
        <input type="hidden" id="js_new_album" name="val[new_album]">
        {/if}

        {if (!$sModuleContainer
        || ($sModuleContainer == 'groups' && Phpfox::getParam('photo.display_photo_album_created_in_group'))
        || ($sModuleContainer == 'pages' && Phpfox::getParam('photo.display_photo_album_created_in_page'))) &&
        Phpfox::getParam('photo.allow_photo_category_selection') &&
        Phpfox::getService('photo.category')->hasCategories()}
            <div class="form-group">
                <label for="category">{_p('photo_s_category')}</label>
                {module name='photo.drop-down'}
            </div>
        {/if}

        <div class="{if $iAlbumId}hidden{/if} form-group" id="js_photo_privacy_holder">
            <div id="js_custom_privacy_input_holder"></div>
            {if $sModuleContainer}
                <div><input type="hidden" id="privacy" name="val[privacy]" value="0"/></div>
            {else}
                {if Phpfox::isModule('privacy')}
                <div class="form-group form-group-follow">
                    <label for="">{_p var='photo_s_privacy'}</label>
                    {module name='privacy.form' privacy_name='privacy' privacy_info='photo.control_who_can_see_these_photo_s' default_privacy='photo.default_privacy_setting'}
                </div>
                {/if}
            {/if}
        </div>
    </form>
    <div id="js_photo_done_upload" style="display: none;">
        <button class="btn btn-primary">{_p('done')}</button>
    </div>
</div>
