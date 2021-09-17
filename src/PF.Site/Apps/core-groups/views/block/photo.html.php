<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="profiles_banner {if $aCoverPhoto !== false}has_cover{/if}">
    <div class="profiles_banner_bg">
        <div class="cover_bg"></div>
        {if $aCoverPhoto}
        <a href="{permalink module='photo' id=$aCoverPhoto.photo_id title=$aCoverPhoto.title}">
        {/if}
            <div class="cover" id="cover_bg_container">
                {if $aCoverPhoto}
                    {img server_id=$aCoverPhoto.server_id path='photo.url_photo' file=$aCoverPhoto.destination suffix='_1024' title=$aCoverPhoto.title class="visible-lg cover_photo"}
                    <span style="background-image: url({img server_id=$aCoverPhoto.server_id path='photo.url_photo' file=$aCoverPhoto.destination suffix='_1024' return_url=true})" class="hidden-lg" title="{$aCoverPhoto.title}"></span>
                {else}
                    <span style="background-image: url({img file=$sDefaultCoverPath return_url=true})"></span>
                {/if}
            </div>
        {if $aCoverPhoto}
        </a>
        {/if}
    </div>

    {if (\Phpfox::getUserParam('groups.can_edit_all_groups', 0) || $aPage.is_admin)}
    <div class="dropdown change-cover-block">
        <a role="button" data-toggle="dropdown" class=" btn btn-primary btn-gradient" id="js_change_cover_photo">
            <span class="ico ico-camera"></span>
        </a>

        <ul class="dropdown-menu">
            {if user('pf_group_add_cover_photo')}
            <li class="cover_section_menu_item">
                <a href="#"
                   onclick="$Core.box('profile.logo', 500, 'groups_id={$aPage.page_id}'); return false;">
                    {_p('upload_photo')}
                </a>
            </li>
            {if !empty($aPage.cover_photo_id)}
            <li class="cover_section_menu_item hidden-sm hidden-md hidden-xs">
                <a role="button" onclick="repositionCoverPhoto('groups',{$aPage.page_id})">
                    {_p('reposition')}
                </a>
            </li>
            <li class="cover_section_menu_item">
                <a href="#"
                   onclick="$.ajaxCall('groups.removeLogo', 'page_id={$aPage.page_id}'); return false;">
                    {_p('remove_cover_photo')}
                </a>
            </li>
            {/if}
            {/if}
        </ul>
    </div>
    {/if}

    <div class="profile-info-block groups-profile">
        <div class="profile-image">
            <div class="profile_image_holder">
                {if Phpfox::isModule('photo') && isset($aProfileImage) && $aProfileImage.photo_id}
                <a href="{permalink module='photo' id=$aProfileImage.photo_id title=$aProfileImage.title}">
                    <div class="img-wrapper">
                        {img server_id=$aPage.image_server_id title=$aPage.title path='pages.url_image' file=$aPage.pages_image_path suffix='_200_square' no_default=false max_width=200 time_stamp=true}
                    </div>
                </a>
                {else}
                    <div class="img-wrapper">
                    {img thickbox=true server_id=$aPage.image_server_id title=$aPage.title path='pages.url_image' file=$aPage.pages_image_path suffix='_200_square' no_default=false max_width=200 time_stamp=true}
                    </div>
                {/if}
                {if $bCanChangePhoto}
                <form class="" method="post" enctype="multipart/form-data" action="#">
                    <label title="{_p var='change_picture'}" for="upload_avatar" class="btn-primary btn-gradient" onclick="$('#groups-profile-photo').trigger('click');">
                        <span class="ico ico-camera"></span>
                    </label>
                    <input type="file" name="image" accept="image/*" id="groups-profile-photo" data-url="{url link='groups.photo'}" class="ajax_upload" data-custom="page_id={$aPage.page_id}"/>
                </form>
                {/if}
            </div>
        </div>
        <div class="profile-info">
            <div class="profile-extra-info ">
                <h1 title="{$aPage.title|clean}"><a>{$aPage.title|clean}</a></h1>
                <div class="profile-info-detail">
                    <span class="fw-bold">
                        {if $aPage.reg_method == 0}
                        <i class="fa fa-privacy fa-privacy-0"></i>&nbsp;{_p var='public_group'}
                        {elseif $aPage.reg_method == 1}
                        <i class="fa fa-unlock-alt" aria-hidden="true"></i>&nbsp;{_p var='closed_group'}
                        {elseif $aPage.reg_method == 2}
                        <i class="fa fa-lock" aria-hidden="true"></i>&nbsp;{_p var='secret_group'}
                        {/if}
                    </span>
                    -
                    <span class="fw-bold">
                        {if !empty($aPage.parent_category_link)}
                            <a href="{$aPage.parent_category_link}">
                            {if Phpfox::isPhrase($this->_aVars['aPage']['parent_category_name'])}
                                {_p var=$aPage.parent_category_name}
                            {else}
                                {$aPage.parent_category_name|convert}
                            {/if}
                            </a>
                        {/if}
                        {if !empty($aPage.parent_category_link) && !empty($aPage.category_link)}»{/if}
                        {if !empty($aPage.category_link)}
                            <a href="{$aPage.category_link}">
                            {if Phpfox::isPhrase($this->_aVars['aPage']['category_name'])}
                                {_p var=$aPage.category_name}
                            {else}
                                {$aPage.category_name|convert}
                            {/if}
                            </a>
                        {/if}
                    </span>
                    -
                    <span class="fw-bold">
                        {$aPage.total_like} {if $aPage.total_like != 1} {_p('Members')}{else}{_p('Member')}{/if}
                    </span>
                </div>
            </div>

            <div class="profile-actions">
                <div class="profile-action-block profiles-owner-actions">
                    {template file='groups.block.joinpage'}
                    
                    {if !empty($aSubPagesMenus)}
                    {foreach from=$aSubPagesMenus key=iKey name=submenu item=aSubMenu}
                    <a href="{url link=$aSubMenu.url)}" class="btn btn-success">
                        {if (isset($aSubMenu.title))}
                            {$aSubMenu.title}
                        {else}
                            {_p var=$aSubMenu.var_name}
                        {/if}
                    </a>
                    {/foreach}
                    {/if}

                    {if $bCanEdit || $bCanDelete}
                    <div class="dropdown">
                        <a class="btn" role="button" data-toggle="dropdown">
                            <i class="ico ico-dottedmore-o"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            {template file='groups.block.link'}
                        </ul>
                    </div>
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="profiles-menu">
    <ul data-component="menu">
        <div class="overlay"></div>
        {foreach from=$aGroupMenus item=aGroupMenu}
        <li>
            <a href="{$aGroupMenu.url}">
                {if (isset($aGroupMenu.menu_icon))}
                <span class="{$aGroupMenu.menu_icon}"></span>
                {else}
                <span class="ico ico-calendar-star-o"></span>
                {/if}
                <span>
                    {$aGroupMenu.phrase}
                </span>
            </a>
        </li>
        {/foreach}
        <li class="dropdown dropdown-overflow hide explorer">
            <a data-toggle="dropdown" role="button">
                <span class="ico ico-dottedmore-o"></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-right">
            </ul>
        </li>
    </ul>
    {plugin call='groups.block_photo_menus'}
</div>
<!-- Below css for reposition feature -->
<style type="text/css">
    .profiles_banner_bg .cover img.cover_photo {l}
        position: relative;
        left:0;
        top: { $iConverPhotoPosition }px;
    {r}
</style>
