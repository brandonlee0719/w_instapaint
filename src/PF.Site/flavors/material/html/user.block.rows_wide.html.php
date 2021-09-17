<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="item-outer">
    <div class="item-inner">
        <div class="item-media">
            {img user=$aUser suffix='_200_square' max_width=200 max_height=200}
        </div>
        <div class="user-info">
            <div class="user-title">
                {$aUser|user}
            </div>
            {module name='user.friendship' friend_user_id=$aUser.user_id type='icon' extra_info=true no_button=true mutual_list=true}
            {module name='user.info' friend_user_id=$aUser.user_id number_of_info=2}

            {if Phpfox::getUserParam('user.can_feature')}
            <div class="dropdown admin-actions">
                <a href="" data-toggle="dropdown" class="btn btn-sm s-4">
                    <span class="ico ico-gear-o"></span>
                </a>
    
                <ul class="dropdown-menu dropdown-menu-right">
                    <li {if !isset($aUser.is_featured) || (isset($aUser.is_featured) && !$aUser.is_featured)} style="display:none;" {/if} class="user_unfeature_member">
                    <a href="#" title="{_p var='un_feature_this_member'}" onclick="$(this).parent().hide(); $(this).parents('.dropdown-menu').find('.user_feature_member:first').show(); $.ajaxCall('user.feature', 'user_id={$aUser.user_id}&amp;feature=0&amp;type=1&reload=1'); return false;"><span class="ico ico-diamond-o mr-1"></span>{_p var='unfeature'}</a>
                    </li>
                    <li {if isset($aUser.is_featured) && $aUser.is_featured} style="display:none;" {/if} class="user_feature_member">
                    <a href="#" title="{_p var='feature_this_member'}" onclick="$(this).parent().hide(); $(this).parents('.dropdown-menu').find('.user_unfeature_member:first').show(); $.ajaxCall('user.feature', 'user_id={$aUser.user_id}&amp;feature=1&amp;type=1&reload=1'); return false;"><span class="ico ico-diamond-o mr-1"></span>{_p var='feature'}</a>
                    </li>
                </ul>
            </div>
            {/if}
        </div>


        {if Phpfox::isUser() && $aUser.user_id != Phpfox::getUserId()}
        <div class="dropup friend-actions">
            <ul class="dropdown-menu dropdown-center">
                {if Phpfox::isModule('mail') && User_Service_Privacy_Privacy::instance()->hasAccess('' . $aUser.user_id . '', 'mail.send_message')}
                <li>
                    <a href="#" onclick="$Core.composeMessage({left_curly}user_id: {$aUser.user_id}{right_curly}); return false;">
                        <span class="mr-1 ico ico-pencilline-o"></span>
                        {_p var='message'}
                    </a>
                </li>
                {/if}

                <li>
                    <a href="#?call=report.add&amp;height=220&amp;width=400&amp;type=user&amp;id={$aUser.user_id}" class="inlinePopup" title="{_p var='report_this_user'}">
                    <span class="ico ico-warning-o mr-1"></span>
                    {_p var='report_this_user'}</a>
                </li>
                {if Phpfox::isModule('friend') && isset($is_friend) && $is_friend === true}
                <li class="item-delete">
                    <a href="#" onclick="$Core.jsConfirm({l}{r}, function(){l}$.ajaxCall('friend.delete', 'friend_user_id={$aUser.user_id}&reload=1');{r}, function(){l}{r}); return false;">
                        <span class="mr-1 ico ico-user2-del-o"></span>
                        {_p var='remove_friend'}
                    </a>
                </li>
                {elseif Phpfox::isModule('friend') && !empty($is_friend) && !empty($request_id)}
                <li class="item-delete">
                    <a href="{url link='friend.pending' id=$request_id}" class="sJsConfirm">
                        <span class="mr-1 ico ico-user2-del-o"></span>
                        {_p var='cancel_request'}
                    </a>
                </li>
                {/if}
            </ul>
        </div>
        {/if}
        
    </div>
    {if isset($aUser.is_featured) && $aUser.is_featured}
    <div class="item-featured" title="{_p var='featured'}">
        <span class="ico ico-diamond"></span>
    </div>
    {/if}
</div>