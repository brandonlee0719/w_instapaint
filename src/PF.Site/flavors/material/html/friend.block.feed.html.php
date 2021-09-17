<?php  
    defined('PHPFOX') or exit('NO DICE!');
?>

    <div class="mutual-friends">
        {module name='user.friendship' friend_user_id=$aUserFriendFeed.user_id type='icon' extra_info=true}
    </div>
<div class="gender-location">
{if isset($aUserFriendFeed.gender_name)}
    <div class="item-gender">{$aUserFriendFeed.gender_name}</div>
{/if}


{if User_Service_Privacy_Privacy::instance()->hasAccess('' . $aUserFriendFeed.user_id . '', 'profile.view_location') && (!empty($aUserFriendFeed.city_location) || !empty($aUserFriendFeed.country_child_id) || !empty($aUserFriendFeed.location))}
    <div class="item-location">
        <span>{_p var='lives_in'}</span>
        {if !empty($aUserFriendFeed.city_location)}{$aUserFriendFeed.city_location}{/if}
        {if !empty($aUserFriendFeed.city_location) && (!empty($aUserFriendFeed.country_child_id) || !empty($aUserFriendFeed.location))},{/if}
        {if !empty($aUserFriendFeed.country_child_id)}&nbsp;{$aUserFriendFeed.country_child_id|location_child}{/if} {if !empty($aUserFriendFeed.location)}{$aUserFriendFeed.location}{/if}
    </div>
{/if}
</div>