<div class="item-inner">
    <div class="photo_edit_wrapper">
        <div class="photo_edit_holder dropdown {if $aForms.album_id > 0}success{/if}">
                            <span class="dropdown-toggle hide" data-toggle="dropdown">
                                <i class="ico ico-photos"></i>
                            </span>
            {if count($aAlbums)}
            <div class="form-group dropdown-menu pf-dropdown-not-hide-photo">
                <label for="{$aForms.photo_id}">{_p var='move_to'}</label>
                <select id="{$aForms.photo_id}" name="val[{$aForms.photo_id}][move_to]" class="form-control" onchange="$Core.Photo.toggleEditAction(this,'album');" data-album_id="{$aForms.album_id}">
                    <option value="">{_p var='select'}:</option>
                    {foreach from=$aAlbums item=aAlbum}
                    {if $aForms.module_id == $aAlbum.module_id && $aForms.album_id != $aAlbum.album_id}
                    <option value="{$aAlbum.album_id}">{if $aAlbum.profile_id > 0}{_p var='profile_pictures'}{elseif $aAlbum.cover_id > 0}{_p var='cover_photo'}{else}{$aAlbum.name|translate|clean}{/if}</option>
                    {/if}
                    {/foreach}
                </select>
            </div>
            {/if}
        </div>

        <div class="item-categories dropdown hide {if $aForms.category_list != ''}success{/if}">
                            <span class="dropdown-toggle" data-toggle="dropdown">
                                <i class="ico ico-folder-o"></i>
                            </span>
            <div class="item-categories-inner dropdown-menu pf-dropdown-not-hide-photo">
                <div class="fw-bold item-categories-title">
                    {_p var="Categories:"}
                    <span data-dismiss="dropdown">
                        <i class="ico ico-close"></i>
                    </span>
                </div>
                {if (!$aForms.module_id
                || ($aForms.module_id == 'groups' && Phpfox::getParam('photo.display_photo_album_created_in_group'))
                || ($aForms.module_id == 'pages' && Phpfox::getParam('photo.display_photo_album_created_in_page')))}
                {if Phpfox::getService('photo.category')->hasCategories()}
                <div class="form-group item-category">
                    <div class="table_right js_category_list_holder">
                        {if isset($aForms.photo_id)}<div class="js_photo_item_id" style="display:none;">{$aForms.photo_id}</div>{/if}
                        {if isset($aForms.category_list)}<div class="js_photo_active_items" style="display:none;">{$aForms.category_list}</div>{/if}
                        {module name='photo.drop-down'}
                    </div>
                </div>
                {/if}
                {/if}
            </div>
        </div>

        <div class="form-group item-mature hide dropdown">
            <span class="dropdown-toggle" data-toggle="dropdown">
                <i class="ico ico-dottedmore-vertical"></i>
            </span>
            <div class="dropdown-menu pf-dropdown-not-hide-photo">
                {if Phpfox::getUserParam('photo.can_add_mature_images')}
                <label class="fw-bold">{_p var='mature_content'}</label>
                <div class="pb-1">
                    <label>
                        <input type="radio" name="val{if isset($aForms.photo_id)}[{$aForms.photo_id}]{/if}[mature]" value="2" {if isset($aForms) && $aForms.mature == 2} checked {/if}>
                        <i class="ico ico-circle-o mr-1"></i>
                        {_p var='yes_strict'}
                    </label>
                    <label>
                        <input type="radio" name="val{if isset($aForms.photo_id)}[{$aForms.photo_id}]{/if}[mature]" value="1" {if isset($aForms) && $aForms.mature == 1} checked {/if}>
                        <i class="ico ico-circle-o mr-1"></i>
                        {_p var='yes_warning'}
                    </label>
                    <label>
                        <input type="radio" name="val{if isset($aForms.photo_id)}[{$aForms.photo_id}]{/if}[mature]" value="0" {if isset($aForms) && $aForms.mature == 0} checked {/if}>
                        <i class="ico ico-circle-o mr-1"></i>
                        {_p var='no'}
                    </label>
                </div>
                {/if}
                {if !isset($bIsEditMode) && $aForms.album_id > 0}
                    <div class="divider"></div>
                    <div class="album-set-as-cover-action py-1">
                        <label>
                            <input type="radio" name="val[set_album_cover]" value="{$aForms.photo_id}" class=""{if $aForms.is_cover} checked="checked"{/if} />
                            <i class="ico ico-circle-o mr-1"></i>
                            {_p var='set_as_the_album_cover'}
                        </label>
                    </div>
                {/if}
                <div class="divider"></div>
                <div class="disable-download">
                    <label>
                        <input type="checkbox" name="val[{$aForms.photo_id}][allow_download]" value="1" {value type='checkbox' id='allow_download' default=1} onclick="$Core.Photo.toggleEditAction(this,'download');"/>
                        <i class="ico ico-square-o mr-1"></i>
                        {_p var='download_enabled'}
                    </label>
                </div>
            </div>
        </div>

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
    </div>
    <div class="form-group item-title page hide">
        <input placeholder="{_p var='Title'}" title="{_p var='Title'}" id="title" type="text" name="val{if isset($aForms.photo_id)}[{$aForms.photo_id}]{/if}[title]" value="{if isset($aForms.title)}{$aForms.title|clean}{else}{value type='input' id='title'}{/if}" size="30" maxlength="150" onfocus="this.select();" class="form-control" />
    </div>
    <div class="form-group item-description page hide">
        <textarea title="{_p var='description'}" rows="4" id="{if isset($aForms.photo_id)}{$aForms.photo_id}{/if}description" placeholder="{_p var='Description'}" name="val{if isset($aForms.photo_id)}[{$aForms.photo_id}]{/if}[description]" class="form-control">{if isset($aForms.description)}{$aForms.description|clean}{else}{value type='input' id='description'}{/if}</textarea>
    </div>
    {if Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_tag_support')}
    <div class="form-group item-topic page hide">
        <input type="text" class="topic" name="val{if $aForms.photo_id}[{$aForms.photo_id}]{/if}[tag_list]" value="{value type='input' id='tag_list'}" size="30" placeholder="{_p var='topics_separate_multiple_topics_with_commas'}" title="{_p var='topics_separate_multiple_topics_with_commas'}"/>
    </div>
    {/if}
    <div class="item-delete-bg text-capitalize text-center hide">
                        <span class="delete-reverse">
                            <i class="ico ico-reply"></i>
                        </span>
        {_p var="undo_delete"}
    </div>
</div>