<?php
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if isset($bSingleMode)}
<div class="clearfix">
    <form class="form" method="post" action="#" onsubmit="$(this).ajaxCall('photo.updatePhoto'); return false;">
        <div class="hidden"><input type="hidden" name="photo_id" value="{$aForms.photo_id}" /></div>
        <div class="hidden"><input type="hidden" name="val{if isset($aForms.photo_id)}[{$aForms.photo_id}]{/if}[photo_id]" id="photo_id" value="{$aForms.photo_id}" /></div>
        <div class="hidden"><input type="hidden" name="val{if isset($aForms.photo_id)}[{$aForms.photo_id}]{/if}[album_id]" value="{$aForms.album_id}" /></div>
        <div class="hidden"><input type="hidden" name="val{if isset($aForms.photo_id)}[{$aForms.photo_id}]{/if}[server_id]" value="{$aForms.server_id}" /></div>
        <div id="js_custom_privacy_input_holder">
            {if $aForms.album_id == '0' && $aForms.group_id == '0'}
                {module name='privacy.build' privacy_item_id=$aForms.photo_id privacy_module_id='photo'}
            {else}
                <div><input type="hidden" name="val{if isset($aForms.photo_id)}[{$aForms.photo_id}]{/if}[privacy]" value="{$aForms.privacy}" /></div>
            {/if}
        </div>
        {if $bIsInline}
            <div class="hidden"><input type="hidden" name="inline" value="1" /></div>
        {/if}
{/if}
        <div id="photo_edit_item_id_{$aForms.photo_id}" class="photo-edit-item">
            <div class="photo-edit-item-inner">
                <div class="item-media hide">
                    <span class="item-media-bg" style="background-image: url({img server_id=$aForms.server_id path='photo.url_photo' file=$aForms.destination suffix='_500' title=$aForms.title return_url=true})"></span>
                    {if !isset($bSingleMode)}
                        <span class="item-delete hide">
                            <i class="ico ico-close"></i>
                            <input type="checkbox" name="val[{$aForms.photo_id}][delete_photo]" value="{$aForms.photo_id}" class="v_middle" />
                        </span>
                    {/if}
                    <span class="item-allow-download hide {if !$aForms.allow_download}active{/if}">
                        <i class="ico ico-download-off-alt"></i>
                    </span>
                </div>
                {if isset($bSingleMode)}
                    <div class="photo_edit_wrapper">
                        <div class="item-inner">
                            {if count($aAlbums)}
                            <div class="form-group">
                                <label for="{$aForms.photo_id}">{_p var='move_to'}</label>
                                <select id="{$aForms.photo_id}" name="val[{$aForms.photo_id}][move_to]" class="form-control">
                                    <option value="">{_p var='select'}:</option>
                                    {foreach from=$aAlbums item=aAlbum}
                                    {if $aForms.module_id == $aAlbum.module_id}
                                    <option value="{$aAlbum.album_id}">{if $aAlbum.profile_id > 0}{_p var='profile_pictures'}{elseif $aAlbum.cover_id > 0}{_p var='cover_photo'}{else}{$aAlbum.name|translate|clean}{/if}</option>
                                    {/if}
                                    {/foreach}
                                </select>
                            </div>
                            {/if}

                            {template file='photo.block.form'}

                            {if $aForms.album_id == '0' && $aForms.group_id == '0'}
                            <div class="photo_edit_input">
                                <div class="form-group form-group-follow">
                                    <label for="">{_p var='privacy'}</label>
                                    <div id="js_custom_privacy_input_holder_{$aForms.photo_id}">{if isset($bIsEditMode)}
                                        {module name='privacy.build' privacy_item_id=$aForms.photo_id privacy_module_id='photo' privacy_array=$aForms.photo_id}
                                        {else}
                                        {module name='privacy.build' privacy_item_id=$aForms.photo_id privacy_module_id='photo'}
                                        {/if}</div>
                                    {if isset($bIsEditMode)}
                                    {module name='privacy.form' privacy_name='privacy' privacy_info='photo.control_who_can_see_this_photo' privacy_array=$aForms.photo_id privacy_custom_id='js_custom_privacy_input_holder_'$aForms.photo_id''}
                                    {else}
                                    {module name='privacy.form' privacy_name='privacy' privacy_info='photo.control_who_can_see_this_photo'}
                                    {/if}
                                </div>
                            </div>
                            {/if}
                            <input type="submit" value="{_p var='update'}" class="btn btn-primary" />
                        </div>
                    </div>
                {else}
                    {template file='photo.block.mass-edit-item'}
                {/if}
            </div>
        </div>
{if isset($bSingleMode)}
    </form>
</div>
{/if}

