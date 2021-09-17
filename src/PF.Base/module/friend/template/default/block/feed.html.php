<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package  		Module_Friend
 * @version 		$Id: top.html.php 1135 2009-10-05 12:59:10Z Miguel_Espinoza $
 */
 
defined('PHPFOX') or exit('NO DICE!');
?>

{if isset($aUserFriendFeed.gender_name)}
<div class="extra_info">
    <span class="fw-600 txt-time-color">{_p var='gender'}:</span>
    <span>{$aUserFriendFeed.gender_name}</span>
</div>
{/if}

{if Phpfox::getService('user.privacy')->hasAccess('' . $aUserFriendFeed.user_id . '', 'profile.view_location') && (!empty($aUserFriendFeed.city_location) || !empty($aUserFriendFeed.country_child_id) || !empty($aUserFriendFeed.location))}
<div class="extra_info">
    <span class="fw-600 txt-time-color">{_p var='location'}:</span>
    <span>
        {if !empty($aUserFriendFeed.city_location)}{$aUserFriendFeed.city_location}{/if}
        {if !empty($aUserFriendFeed.city_location) && (!empty($aUserFriendFeed.country_child_id) || !empty($aUserFriendFeed.location))},{/if}
        {if !empty($aUserFriendFeed.country_child_id)}&nbsp;{$aUserFriendFeed.country_child_id|location_child}{/if} {if !empty($aUserFriendFeed.location)}{$aUserFriendFeed.location}{/if}
    </span>
</div>
{/if}

{module name='user.friendship' friend_user_id=$aUserFriendFeed.user_id type='icon' extra_info=true}