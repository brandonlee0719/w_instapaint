<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="item_view">
    <div class="photo_tag_in_photo pt-2 js_tagged_section">
        <p>{_p var='With'}</p> <span id="js_photo_in_this_photo" class="ml-1"></span>
    </div>
    <div class="photos_actions mt-2 pb-1">
        <ul>
            {if (Phpfox::getUserParam('photo.can_tag_own_photo') && $aForms.user_id == Phpfox::getUserId() && Phpfox::getUserParam('photo.how_many_tags_on_own_photo') > 0) || (Phpfox::getUserParam('photo.can_tag_other_photos') && Phpfox::getUserParam('photo.how_many_tags_on_other_photo'))}
                <li class="photos_tag">
                    <a href="#" id="js_tag_photo" onclick="$('.js_tagged_section').addClass('edit'); $(this).parent().addClass('active');">
                        <span><i class="ico ico-price-tag"></i></span>
                        <b class="text-capitalize">{_p var="tag_friends"}</b>
                    </a>
                </li>
            {/if}
            {if $aForms.user_id == Phpfox::getUserId() || (Phpfox::getUserParam('photo.can_download_user_photos') && $aForms.allow_download)}
                <li class="photos_download">
                    <a href="{permalink module='photo' id=$aForms.photo_id title=$aForms.title action=download}" id="js_download_photo" class="no_ajax">
                        <span>
                            <i class="ico ico-download-alt"></i>
                        </span>
                        <b class="text-capitalize">{_p var="download"}</b>
                    </a>
                </li>
            {/if}
            {if (Phpfox::getUserParam('photo.can_edit_own_photo') && $aForms.user_id == Phpfox::getUserId()) || Phpfox::getUserParam('photo.can_edit_other_photo')}
                <li class="rotate-left">
                    <a href="#" onclick="$('#photo_view_ajax_loader').show(); $('#menu').remove(); $('#noteform').hide(); $('#js_photo_view_image').imgAreaSelect({left_curly} hide: true {right_curly}); $('#js_photo_view_holder').hide(); $.ajaxCall('photo.rotate', 'photo_id={$aForms.photo_id}&amp;photo_cmd=left&amp;currenturl=' + $('#js_current_page_url').html()); return false;">
                        <span><i class="ico ico-rotate-left"></i></span>
                        <b class="text-capitalize">{_p var='rotate_left'}</b>
                    </a>
                </li>
                <li class="rotate-right">
                    <a href="#" onclick="$('#photo_view_ajax_loader').show(); $('#menu').remove(); $('#noteform').hide(); $('#js_photo_view_image').imgAreaSelect({left_curly} hide: true {right_curly}); $('#js_photo_view_holder').hide(); $.ajaxCall('photo.rotate', 'photo_id={$aForms.photo_id}&amp;photo_cmd=right&amp;currenturl=' + $('#js_current_page_url').html()); return false;">
                        <span><i class="ico ico-rotate-right"></i></span>
                        <b class="text-capitalize">{_p var='rotate_right'}</b>
                    </a>
                </li>
            {/if}
            {if Phpfox::isUser() && ($iAvatarId != $aForms.photo_id)}
                <li>
                    <a href="#" class="photo_make_as_profile" data-processing="false" onclick="$.ajaxCallOne(this, 'photo.makeProfilePicture', 'photo_id={$aForms.photo_id}'); return false;">
                        <span>
                            <i class="ico ico-user-circle-o"></i>
                        </span>
                        <b class="text-capitalize">{_p var='make_profile_picture'}</b>
                    </a>
                </li>
            {/if}
            {if Phpfox::isUser() && ($iCover != $aForms.photo_id)}
                <li>
                    <a href="#" class="photo_make_as_cover" data-processing="false" onclick="$.ajaxCallOne(this, 'photo.makeCoverPicture', 'photo_id={$aForms.photo_id}'); return false;">
                        <span><i class="ico ico-photo"></i></span>
                        <b class="text-capitalize">{_p var='make_cover_photo'}</b>
                    </a>
                </li>
            {/if}
            {if isset($aCallback) && ($aCallback.module_id == 'pages' || $aCallback.module_id == 'groups')}
            <li>
                <a href="#" class="photo_make_as_cover" onclick="$Core.Photo.setCoverPhoto({$aForms.photo_id}, {$aCallback.item_id}, '{$aCallback.module_id}'); return false;" >
                    <span><i class="ico ico-photo"></i></span>
                    <b class="text-capitalize">
                        {if isset($aCallback.set_default_phrase)}
                        {$aCallback.set_default_phrase}
                        {else}
                        {_p var='set_as_page_s_cover_photo'}
                        {/if}
                    </b>
                </a>
            </li>
            {/if}
        </ul>
    </div>
    <div class="item-size mb-3">
        <div>
            <p class="text-uppercase">{_p var='file_size'}</p>
            <span>{$aForms.file_size|filesize}</span>
        </div>
        <div>
            <p class="text-uppercase">{_p var='dimension'}</p>
            <span>{$aForms.width} x {$aForms.height}</span>
        </div>
    </div>

    {if $aForms.description}
        <div class="item_description item_view_content mb-4">
            <p class="title text-uppercase ">{_p var="description"}</p>
            <span>
                {$aForms.description|parse|shorten:200:'feed.view_more':true|split:55|max_line}
            </span>
        </div>
    {/if}
    

	<div class="js_moderation_on">
        {if !empty($aForms.sCategories)}
        <div class="item-category pt-2">
            {_p var="Categories"}: {$aForms.sCategories}
        </div>
        {/if}
        {if Phpfox::isModule('tag') && isset($aForms.tag_list)}
            {module name='tag.item' sType='photo' sTags=$aForms.tag_list iItemId=$aForms.photo_id iUserId=$aForms.user_id}
        {/if}
        <div class="item-addthis mb-3 pt-2">{addthis url=$aForms.link title=$aForms.title description=$sShareDescription}</div>

        {if Phpfox::getParam('feed.enable_check_in') && Phpfox::getParam('core.google_api_key') != '' && isset($aForms.location_name)}
            <div class="activity_feed_location">
                <span class="activity_feed_location_at">{_p('at')} </span>
                <span class="js_location_name_hover activity_feed_location_name" {if isset($aForms.location_latlng) && isset($aForms.location_latlng.latitude)}onmouseover="$Core.Feed.showHoverMap('{$aForms.location_latlng.latitude}','{$aForms.location_latlng.longitude}', this);"{/if}>
                    <span class="ico ico-checkin"></span>
                    <a href="{if Phpfox::getParam('core.force_https_secure_pages')}https://{else}http://{/if}maps.google.com/maps?daddr={$aForms.location_latlng.latitude},{$aForms.location_latlng.longitude}" target="_blank">{$aForms.location_name}</a>
                </span>
            </div>
        {/if}
        <div class="item-detail-feedcomment">
		{module name='feed.comment'}
        </div>
	</div>
</div>
<script type="text/javascript">
	var bChangePhoto = true;
	var aPhotos = {$sPhotos};
	var oPhotoTagParams =  {l}{$sPhotoJsContent}{r};
	$Behavior.tagPhoto = function()
    {l} 
        $Core.photo_tag.init(oPhotoTagParams);
        $("#page_photo_view input.v_middle" ).focus(function() {l}
            $(this).parent('.table_right').addClass('focus');
            $(this).parents('.table').siblings('.cancel_tagging').addClass('focus');
        {r});
        $("#page_photo_view input.v_middle" ).focusout(function() {l}
            $(this).parent('.table_right').removeClass('focus');
            $(this).parents('.table').siblings('.cancel_tagging').removeClass('focus');
        {r});
    {r};
	$Behavior.removeImgareaselectBox = function()
        {l}
        {literal}
        if ($('body#page_photo_view').length == 0 || ($('body#page_photo_view').length > 0 && bChangePhoto == true)) {
            bChangePhoto = false;
            $('.imgareaselect-outer').hide();
            $('.imgareaselect-selection').each(function() {
                $(this).parent().hide();
            });
        }
        {/literal}
	{r};
</script>

{if $bLoadCheckin}
<script type="text/javascript">
    var bCheckinInit = false;
    $Behavior.prepareInit = function()
    {l}
    $Core.Feed.sIPInfoDbKey = '';
    $Core.Feed.sGoogleKey = '{param var="core.google_api_key"}';

    {if isset($aVisitorLocation)}
    $Core.Feed.setVisitorLocation({$aVisitorLocation.latitude}, {$aVisitorLocation.longitude} );
    {else}

    {/if}

    $Core.Feed.googleReady('{param var="core.google_api_key"}');
    {r}
</script>
{/if}