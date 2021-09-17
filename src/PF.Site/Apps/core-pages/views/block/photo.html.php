<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="profiles_banner {if $aCoverPhoto !== false}has_cover{/if}">
    <div class="profiles_banner_bg">
        <div class="cover_bg"></div>
        {if $aCoverPhoto}
        <a href="{permalink module='photo' id=$aCoverImage.photo_id title=$aCoverImage.title}">
        {/if}
            <div class="cover" id="cover_bg_container">
                {if $aCoverPhoto}
                    {img server_id=$aCoverPhoto.server_id path='photo.url_photo' file=$aCoverPhoto.destination suffix='_1024' title=$aCoverPhoto.title class="visible-lg cover_photo"}
                    <span style="background-image: url({img server_id=$aCoverPhoto.server_id path='photo.url_photo' file=$aCoverPhoto.destination suffix='_1024' return_url=true})" class="hidden-lg"></span>
                {else}
                    <span style="background-image: url({img file=$sDefaultCoverPath return_url=true})"></span>
                {/if}
            </div>
        {if $aCoverPhoto}
        </a>
        {/if}
    </div>

    {if $bCanChangeCover}
    <div class="dropdown change-cover-block">
        <a role="button" data-toggle="dropdown" class=" btn btn-primary btn-gradient" id="js_change_cover_photo">
            <span class="ico ico-camera"></span>
        </a>

        <ul class="dropdown-menu">
            <li class="cover_section_menu_item">
                <a href="{url link='pages.'$aPage.page_id}photo">
                    {_p var='choose_from_photos'}
                </a>
            </li>
            <li class="cover_section_menu_item">
                <a href="#" onclick="$Core.box('profile.logo', 500, 'page_id={$aPage.page_id}'); return false;">
                    {_p var='upload_photo'}
                </a>
            </li>
            {if !empty($aPage.cover_photo_id)}
            <li class="cover_section_menu_item hidden-sm hidden-md hidden-xs">
                <a role="button" onclick="repositionCoverPhoto('pages',{$aPage.page_id})">
                    {_p var='reposition'}
                </a>
            </li>
            <li class="cover_section_menu_item">
                <a href="#" onclick="$.ajaxCall('pages.removeLogo', 'page_id={$aPage.page_id}'); return false;">
                    {_p var='remove_cover_photo'}
                </a>
            </li>
            {/if}
        </ul>
    </div>
    {/if}

    <div class="profile-info-block">
        <div class="profile-image">
            <div class="profile_image_holder">
                {if Phpfox::isModule('photo') && isset($aProfileImage) && $aProfileImage.photo_id}
                <a href="{permalink module='photo' id=$aProfileImage.photo_id title=$aProfileImage.title}">
                    <div class="img-wrapper">
                        {img server_id=$aPage.image_server_id title=$aPage.title path='pages.url_image' file=$aPage.pages_image_path suffix='_200_square' no_default=false max_width=200 time_stamp=true}
                    </div>
                </a>
                {else}
                {img thickbox=true server_id=$aPage.image_server_id title=$aPage.title path='pages.url_image' file=$aPage.pages_image_path suffix='_200_square' no_default=false max_width=200 time_stamp=true}
                {/if}

                {if $bCanChangePhoto}
                <form method="post" enctype="multipart/form-data" action="#" class="">
                    <label for="upload_avatar" class="btn-primary btn-gradient" onclick="$('#pages-profile-photo').trigger('click');">
                        <span class="ico ico-camera"></span>
                    </label>
                    <input type="file" name="image" accept="image/*" id="pages-profile-photo" data-url="{url link='pages.photo'}" class="ajax_upload" data-custom="page_id={$aPage.page_id}"/>
                </form>
                {/if}
            </div>
        </div>

        <div class="profile-info">
            <div class="profile-extra-info">
                <h1 title="{$aPage.title|clean}"><a>{$aPage.title|clean}</a></h1>
                <div class="profile-info-detail">
                    {if $aPage.parent_category_name}
                    <a href="{$aPage.type_link}" class="fw-bold">
                        {if Phpfox::isPhrase($this->_aVars['aPage']['parent_category_name'])}
                        {_p var=$aPage.parent_category_name}
                        {else}
                        {$aPage.parent_category_name|convert}
                        {/if}
                    </a> »
                    {/if}
                    {if $aPage.category_name}
                    <a href="{$aPage.category_link}" class="fw-bold">
                        {if Phpfox::isPhrase($this->_aVars['aPage']['category_name'])}
                        {_p var=$aPage.category_name}
                        {else}
                        {$aPage.category_name|convert}
                        {/if}
                    </a> -
                    {/if}
                    <span class="fw-bold">
                        {$aPage.total_like} {if $aPage.total_like >= 2} {_p var='likes'}{else}{_p var='like'}{/if}
                    </span>
                </div>
            </div>

            <div class="profile-actions">
                {if (Phpfox::getUserParam('pages.can_edit_all_pages') || $aPage.is_admin)}
                <div class="profile-action-block profiles-owner-actions">
                    {if isset($aSubPagesMenus) && count($aSubPagesMenus)}
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
                </div>
                {/if}
                <div class="profile-action-block profiles-viewer-actions">
                    {template file='pages.block.joinpage'}

                    {if !$aPage.is_admin && Phpfox::getUserParam('pages.can_claim_page') && empty($aPage.claim_id)}
                    <a href="#?call=contact.showQuickContact&amp;height=600&amp;width=600&amp;page_id={$aPage.page_id}" class="btn btn-icon btn-round btn-default inlinePopup js_claim_page" title="{_p var='claim_page'}">
                        <span class="ico ico-compose-alt"></span>
                        {_p var='claim_page'}
                    </a>
                    {/if}

                    {if $bCanEdit || $bCanDelete}
                    <div class="dropdown">
                        <a class="btn" role="button" data-toggle="dropdown">
                            <span class="ico ico-dottedmore-o"></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            {template file='pages.block.link'}
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
        {foreach from=$aPageMenus item=aPageMenu}
        <li>
            <a href="{$aPageMenu.url}">
                {if (isset($aPageMenu.menu_icon))}
                <span class="{$aPageMenu.menu_icon}"></span>
                {else}
                <span class="ico ico-calendar-star-o"></span>
                {/if}
                <span>
                    {$aPageMenu.phrase}
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
    {plugin call='pages.block_photo_menus'}
</div>
{if isset($iCoverPhotoPosition)}
<style type="text/css">
	.profiles_banner_bg .cover img.cover_photo
	{l}
	position: relative;
	left: 0;
	top: {if empty($iCoverPhotoPosition)}0{else}{$iCoverPhotoPosition}px{/if};
	{r}
</style>
{/if}