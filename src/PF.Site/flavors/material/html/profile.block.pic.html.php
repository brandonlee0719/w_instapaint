<?php
    defined('PHPFOX') or exit('NO DICE!');
?>
<style type="text/css">
    .profiles_banner_bg .cover img.cover_photo
        {l}
        position: relative;
        left: 0;
        top: {$iConverPhotoPosition}px;
    {r}
</style>
<div class="profiles_banner {if isset($aCoverPhoto.server_id)}has_cover{/if}">
    {if !empty($aCoverPhoto.destination) || !empty($sCoverDefaultUrl)}
	<div class="profiles_banner_bg">
		<div class="cover_bg"></div>
        <a href="{permalink module='photo' id=$aCoverPhoto.photo_id title=$aCoverPhoto.title}">
            <div class="cover" id="cover_bg_container">
                {if !empty($aCoverPhoto.destination)}
                    {img server_id=$aCoverPhoto.server_id path='photo.url_photo' file=$aCoverPhoto.destination suffix='_1024' class="visible-lg cover_photo"}
                    <span style="background-image: url({img server_id=$aCoverPhoto.server_id path='photo.url_photo' file=$aCoverPhoto.destination suffix='_1024' return_url=true})" class="hidden-lg"></span>
                {elseif !empty($sCoverDefaultUrl)}
                    <span style="background-image: url({$sCoverDefaultUrl})"></span>
                {/if}
            </div>
        </a>
    </div>
    {/if}

    {if Phpfox::getUserParam('profile.can_change_cover_photo') && Phpfox::getUserId() == $aUser.user_id}
	<div class="dropdown change-cover-block">
		<a role="button" data-toggle="dropdown" class=" btn btn-primary btn-gradient" id="js_change_cover_photo">
			<span class="ico ico-camera"></span>
		</a>

		<ul class="dropdown-menu">
			{if empty($aUser.cover_photo)}
			<li role="presentation">
				<a role="button" id="js_change_cover_photo" onclick="$Core.box('profile.logo', 500); return false;">
					{_p var='add_a_cover'}
				</a>
			</li>
			{/if}
			{if !empty($aUser.cover_photo)}
				<li>
					<a role="button" id="js_change_cover_photo" onclick="$Core.box('profile.logo', 500); return false;">
						{_p var='change_cover'}
					</a>
				</li>
				{if !empty($aUser.cover_photo)}
				<li class="cover_section_menu_item " role="presentation" class="visible-lg">
					<a role="button" onclick="repositionCoverPhoto('user',1); return false;">{_p var='reposition'}</a></li>
				<li class="cover_section_menu_item " role="presentation">
					<a role="button" onclick="$('#cover_section_menu_drop').hide(); $.ajaxCall('user.removeLogo'); return false;">{_p var='remove_cover_photo'}</a></li>
				{/if}
			{/if}
		</ul>
	</div>
    {/if}

	<div class="profile-info-block">
		<div class="profile-image">
            <div class="profile_image_holder">
                {if Phpfox::isModule('photo') && $aProfileImage}
                <a href="<?php echo \Phpfox::permalink('photo', $this->_aVars['aProfileImage']['photo_id'], $this->_aVars['aProfileImage']['title']) ?>">
                    {$sProfileImage}
                </a>
                {else}
                {$sProfileImage}
                {/if}
				{if Phpfox::getUserId() == $aUser.user_id}
				{literal}
				<script>
					function changingProfilePhoto() {
						if ($('.profile_image_holder').find('i.fa.fa-spin.fa-circle-o-notch').length > 0) {
							$('.profile_image_holder').find('a').show();
							$('.profile_image_holder').find('i.fa.fa-spin.fa-circle-o-notch').remove();
						}
						else {
							$('.profile_image_holder').find('a').hide();
							$('.profile_image_holder').append('<i class="fa fa-circle-o-notch fa-spin"></i>');
						}
					};
				</script>
				{/literal}
				<form action="#">
					<label class="btn-primary btn-gradient" onclick="$Core.ProfilePhoto.update({if $sPhotoUrl}'{$sPhotoUrl}'{else}false{/if})"><span class="ico ico-camera"></span></label>
				</form>
				{/if}
            </div>
		</div>

		<div class="profile-info">
			<div class="profile-extra-info">
				<h1 {if Phpfox::getParam('user.display_user_online_status')}class="has-status-online"{/if}>
				<a href="{if isset($aUser.link) && !empty($aUser.link)}{url link=$aUser.link}{else}{url link=$aUser.user_name}{/if}" title="{$aUser.full_name|clean} {if Phpfox::getUserParam('profile.display_membership_info')} &middot; {_p var=$aUser.title}{/if}">
						{$aUser.full_name|clean}
				</a>
				{if Phpfox::getParam('user.display_user_online_status')}
					{if $aUser.is_online}
					<span class="user_is_online" title="{_p var='online'}"><i class="fa fa-circle js_hover_title"></i></span>
					{else}
					<span class="user_is_offline" title="{_p var='offline'}"><i class="fa fa-circle js_hover_title"></i></span>
					{/if}
				{/if}

				</h1>

				<div class="profile-info-detail">
					{if (!empty($aUser.gender_name))}
						{$aUser.gender_name}<b>.</b>
					{/if}
					{if User_Service_Privacy_Privacy::instance()->hasAccess('' . $aUser.user_id . '', 'profile.view_location') && (!empty($aUser.city_location) || !empty($aUser.country_child_id) || !empty($aUser.location))}
						<span>
						    {_p var='lives_in'}
                            {if !empty($aUser.city_location)}&nbsp;{$aUser.city_location}{/if}
						    {if !empty($aUser.city_location) && (!empty($aUser.country_child_id) || !empty($aUser.location))},{/if}
						    {if !empty($aUser.country_child_id)}
                                &nbsp;{$aUser.country_child_id|location_child},
                            {/if}
                            {if !empty($aUser.location)}
                                &nbsp;{$aUser.location}
                            {/if}<b>.</b>
						</span>
					{/if}

					{if isset($aUser.birthdate_display) && is_array($aUser.birthdate_display) && count($aUser.birthdate_display)}
						<span>
						{foreach from=$aUser.birthdate_display key=sAgeType item=sBirthDisplay}
						{if $aUser.dob_setting == '2'}
						{_p var='age_years_old' age=$sBirthDisplay}
						{else}
						{_p var='born_on_birthday' birthday=$sBirthDisplay}
						{/if}
						{/foreach}
						</span>
					{/if}

					{if Phpfox::getParam('user.enable_relationship_status') && isset($sRelationship) && $sRelationship != ''}<span>{$sRelationship}</span>{/if}
					{if isset($aUser.category_name)}<span>{$aUser.category_name|convert}</span>{/if}
				</div>
			</div>

			<div class="profile-actions">
				{if Phpfox::getUserId() == $aUser.user_id}
                <a class="btn btn-default btn-icon btn-round" role="link" href="{url link='user.profile'}">
                    <span class="ico ico-pencilline-o mr-1"></span>
                    {_p var='edit_profile'}
                </a>
				{/if}

				{if Phpfox::getUserId() != $aUser.user_id}
				{if (isset($aUser.is_friend_request) && $aUser.is_friend_request == 2)}
				<div class="dropdown pending-request">
					<a class="btn btn-default btn-round" data-toggle="dropdown">
						<span class="ico ico-clock-o mr-1"></span>
						{_p var='pending_friend_request'}
						<span class="ico ico-caret-down ml-1"></span>
					</a>
					<ul class="dropdown-menu">
						<li class="item-delete">
							<a href="javascript:void(0)" onclick="$.ajaxCall('friend.removePendingRequest', 'id={$aUser.is_friend_request_id}','GET');">
								<span class="ico ico-ban mr-1"></span>
								{_p var='cancel_request'}
							</a>
						</li>
					</ul>
				</div>
				{/if}
				
				<div class="profile-action-block profile-viewer-actions dropdown">
					{if Phpfox::isUser() && Phpfox::isModule('friend') && !$aUser.is_friend && $aUser.is_friend_request !== 2}
                        {if !$aUser.is_friend && $aUser.is_friend_request === 3}
                        <a class="btn btn-primary btn-icon btn-gradient btn-round add_as_friend_button" href="#" onclick="return $Core.addAsFriend('{$aUser.user_id}');" title="{_p var='add_to_friends'}">
                            <span class="ico ico-user2-check-o"></span>
                            <span class="">{_p var='confirm_friend_request'}</span>
                        </a>
                        {elseif Phpfox::getUserParam('friend.can_add_friends')}
                        <a class="btn btn-primary btn-icon btn-gradient btn-round add_as_friend_button" href="#" onclick="return $Core.addAsFriend('{$aUser.user_id}');" title="{_p var='add_to_friends'}">
                            <span class="ico ico-user1-plus-o"></span>
                            <span class="">{_p var='add_to_friends'}</span>
                        </a>
                        {/if}
					{/if}


					{if Phpfox::isModule('mail') && User_Service_Privacy_Privacy::instance()->hasAccess('' . $aUser.user_id . '', 'mail.send_message')}
					<a class="btn btn-default btn-icon btn-round" href="#" onclick="$Core.composeMessage({left_curly}user_id: {$aUser.user_id}{right_curly}); return false;">
						<span class="ico ico-comment-o"></span>
						<span class="">{_p var='message'}</span>
					</a>
					{/if}

					{plugin call='profile.template_block_menu_more'}

                    {if (Phpfox::getUserBy('profile_page_id') <= 0) && (
                    (Phpfox::getUserParam('user.can_block_other_members') && isset($aUser.user_group_id) && Phpfox::getUserGroupParam('' . $aUser.user_group_id . '', 'user.can_be_blocked_by_others'))
                    || (Phpfox::getUserParam('core.can_gift_points'))
                    || (Phpfox::isModule('friend') && Phpfox::getUserParam('friend.link_to_remove_friend_on_profile') && isset($aUser.is_friend) && $aUser.is_friend === true)
                    || ($bCanPoke && User_Service_Privacy_Privacy::instance()->hasAccess('' . $aUser.user_id . '', 'poke.can_send_poke'))
                    || (Phpfox::isUser() && $aUser.user_id != Phpfox::getUserId())
                    || (!empty($bShowRssFeedForUser))
                    )}
                    <div class="dropup btn-group">
                        <ul class="dropdown-menu dropdown-menu-right">
                            {if $bCanPoke && User_Service_Privacy_Privacy::instance()->hasAccess('' . $aUser.user_id . '', 'poke.can_send_poke')}
                            <li>
                                <a class="inlinePopup" href="#" id="section_poke" onclick="$Core.box('poke.poke', 400, 'user_id={$aUser.user_id}'); return false;">
                                    <i class="ico ico-smile-o"></i>
                                    <span class="" >{_p var='poke' full_name=''}</span>
                                </a>
                            </li>
                            {/if}

                            {if Phpfox::getUserParam('user.can_block_other_members') && isset($aUser.user_group_id) && Phpfox::getUserGroupParam('' . $aUser.user_group_id . '', 'user.can_be_blocked_by_others')}
                            <li>
                                <a href="#?call=user.block&amp;height=120&amp;width=400&amp;user_id={$aUser.user_id}" class="inlinePopup js_block_this_user" title="{if $bIsBlocked}{_p var='unblock_this_user'}{else}{_p var='block_this_user'}{/if}"><span class="ico ico-ban mr-1"></span>{if $bIsBlocked}{_p var='unblock_this_user'}{else}{_p var='block_this_user'}{/if}</a>
                            </li>
                            {/if}

                            {if Phpfox::getUserParam('core.can_gift_points')}
                            <li>
                                <a href="#?call=core.showGiftPoints&amp;height=120&amp;width=400&amp;user_id={$aUser.user_id}" class="inlinePopup js_gift_points" title="{_p var='gift_points'}">
                                    <span class="ico ico-gift-o mr-1"></span>
                                    {_p var='gift_points'}
                                </a>
                            </li>
                            {/if}
                            {if Phpfox::isUser() && $aUser.user_id != Phpfox::getUserId()}
                            <li>
                                <a href="#?call=report.add&amp;height=220&amp;width=400&amp;type=user&amp;id={$aUser.user_id}" class="inlinePopup" title="{_p var='report_this_user'}">
                                    <span class="ico ico-warning-o mr-1"></span>
                                    {_p var='report_this_user'}</a>
                            </li>
                            {/if}
                            {if isset($bShowRssFeedForUser)}
                                <li>
                                    <a href="{url link=''$aUser.user_name'.rss'}" class="no_ajax_link">
                                        <span class="ico ico-rss-o mr-1"></span>
                                        {_p var='subscribe_via_rss'}
                                    </a>
                                </li>
                            {/if}
                            {if Phpfox::isModule('friend') && Phpfox::getUserParam('friend.link_to_remove_friend_on_profile') && isset($aUser.is_friend) && $aUser.is_friend === true}
                            <li class="item-delete">
                                <a href="#" onclick="$Core.jsConfirm({l}{r}, function(){l}$.ajaxCall('friend.delete', 'friend_user_id={$aUser.user_id}&reload=1');{r}, function(){l}{r}); return false;">
                                    <span class="ico ico-close-circle-o mr-1"></span>
                                    {_p var='remove_friend'}
                                </a>
                            </li>
                            {/if}
                            {plugin call='profile.template_block_menu'}
                        </ul>
                    </div>
					{/if}

				</div>
				{if Phpfox::getUserParam('user.can_feature')}
				<div class="btn-group dropup btn-gear">
					<a class="btn" title="{_p var='options'}" data-toggle="dropdown">
						<span class="ico ico-gear-o"></span>
					</a>
					<ul class="dropdown-menu dropdown-menu-right">
						{if Phpfox::getUserParam('user.can_feature')}
						<li {if !isset($aUser.is_featured) || (isset($aUser.is_featured) && !$aUser.is_featured)} style="display:none;" {/if} class="user_unfeature_member">
						<a href="#" title="{_p var='un_feature_this_member'}" onclick="$(this).parent().hide(); $(this).parents('.dropdown-menu').find('.user_feature_member:first').show(); $.ajaxCall('user.feature', 'user_id={$aUser.user_id}&amp;feature=0&amp;type=1&reload=1'); return false;"><span class="ico ico-diamond-o mr-1"></span>{_p var='unfeature'}</a>
						</li>
						<li {if isset($aUser.is_featured) && $aUser.is_featured} style="display:none;" {/if} class="user_feature_member">
						<a href="#" title="{_p var='feature_this_member'}" onclick="$(this).parent().hide(); $(this).parents('.dropdown-menu').find('.user_unfeature_member:first').show(); $.ajaxCall('user.feature', 'user_id={$aUser.user_id}&amp;feature=1&amp;type=1&reload=1'); return false;"><span class="ico ico-diamond-o mr-1"></span>{_p var='feature'}</a>
						</li>
						{/if}
					</ul>
				</div>
				{/if}
				{/if}
			</div>
		</div>
	</div>
</div>

<div class="profiles-menu set_to_fixed" data-class="profile_menu_is_fixed">
	<ul>
		<li class="profile-image-holder hidden">
                {if Phpfox::isModule('photo') && $aProfileImage}
                <a href="<?php echo \Phpfox::permalink('photo', $this->_aVars['aProfileImage']['photo_id'], $this->_aVars['aProfileImage']['title']) ?>">
                    {$sProfileImage}
                </a>
                {else}
                	{$sProfileImage}
                {/if}
		</li>
		<li>
			<a href="{url link=$aUser.user_name}">
				<span class="ico ico-user-circle-o"></span>
				{_p var='profile'}
			</a>
		</li>
		<li>
			<a href="{url link=''$aUser.user_name'.info'}">
				<span class="ico ico-user1-text-o"></span>
				{_p var='info'}
			</a>
		</li>
        {if $aProfileLinks}
		<li class="dropdown dropdown-overflow">
			<a role="button" data-toggle="dropdown" class="explore">
				<span class="ico ico-caret-down"></span>
			</a>
			<ul class="dropdown-menu dropdown-menu-limit dropdown-menu-right">
                <li class="visible-xs">
					<a href="{url link=''$aUser.user_name'.friend'}">
						<span>
							<span class="ico ico-user1-two-o"></span>
							{_p var='friends'}{if $aUser.total_friend > 0}
						</span>
						<span class="badge_number">{$aUser.total_friend}</span>{/if}
					</a>
				</li>
				{foreach from=$aProfileLinks item=aProfileLink}
					<li class="">
						<a href="{url link=$aProfileLink.url}" class="ajax_link {if isset($aProfileLink.is_selected)} active{/if}">
							<span>
                                {if !empty($aProfileLink.icon_class)}
                                <span class="{$aProfileLink.icon_class} mr-1"></span>
                                {else}
								<span class="ico ico-box-o mr-1"></span>
                                {/if}
								{$aProfileLink.phrase}{if isset($aProfileLink.total)}
							</span>
							<span class="badge_number">{$aProfileLink.total|number_format}</span>{/if}
						</a>
					</li>
				{/foreach}
			</ul>
		</li>
		{/if}
	</ul>
    {template file='core.block.actions-buttons'}
</div>

<div class="js_cache_check_on_content_block" style="display:none;"></div>
<div class="js_cache_profile_id" style="display:none;">{$aUser.user_id}</div>
<div class="js_cache_profile_user_name" style="display:none;">{if isset($aUser.user_name)}{$aUser.user_name}{/if}</div>