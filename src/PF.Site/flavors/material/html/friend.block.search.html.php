<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{if !$bSearch}
    <script type="text/javascript">
      {literal}
      $Ready(function() {
        $Core.searchFriend.init('#js_find_friend', {
        {/literal}
          sPrivacyInputName: '{$sPrivacyInputName}',
          sSearchByValue: '',
          sFriendModuleId: '{$sFriendModuleId}',
          sFriendItemId: '{$sFriendItemId}'
        {literal}
        });
      });
      {/literal}
    </script>

    <div id="js_friend_loader_info"></div>
    <div id="js_friend_loader">
    {if $sFriendType != 'mail'}
        {if !$bInForm}
        <form method="post" onsubmit="$Core.searchFriend.search();return false;">
        {/if}
            <input type="text" class="js_is_enter v_middle default_value" name="find" placeholder="{_p var='search_by_email_full_name_or_user_name'}" id="js_find_friend" autocomplete="off" size="30" />
        {if !$bInForm}
        </form>
        {/if}
    {else}
        <input type="text" class="js_is_enter v_middle default_value" name="find" value="{_p var='search_by_email_full_name_or_user_name'}" id="js_find_friend" autocomplete="off" size="30" />
        <input type="button" value="{_p var='find'}" onclick="$Core.searchFriend.search();return false;" class="button v_middle" />
    {/if}
<div id="js_friend_search_content">
{/if}
    <div class="label_flow friend-search-invite-container">
        <div class="item-container">
        {foreach from=$aFriends name=friend item=aFriend}
        <article class="search-friend {if isset($aFriend.is_active)}friend_search_holder_active{/if}" id="search-friend-{$aFriend.user_id}"
                 data-id="{$aFriend.user_id}" data-can-message="{!empty($aFriend.canMessageUser)}"
                 {if !isset($aFriend.is_active)}
                 onclick="$Core.searchFriend.selectFriend(this)"
                 {/if}
        >
        <div class="item-outer">
            <div class="moderation_row">
                <label class="item-checkbox">
                    <input type="checkbox" class="js_global_item_moderate" name="friend[]" value="{$aFriend.user_id}"
                           id="js_friends_checkbox_{$aFriend.user_id}" value="{$aFriend.user_id}"
                           {if (isset($aFriend.canMessageUser) && $aFriend.canMessageUser == false) || isset($aFriend.is_active)}
                                disabled
                           {else}
                                onclick="$Core.searchFriend.addFriendToSelectList(this, '{$aFriend.user_id}');"
                           {/if}
                    />
                    <i class="ico ico-square-o"></i>
                </label>
            </div>
            <div class="user_rows">
                <div class="user_rows_image">
                    {img user=$aFriend suffix='_50_square' max_width=32 max_height=32 no_link=true style="vertical-align:middle;"}
                </div>
                <div class="user_rows_inner">
                    <div class="item-user">{$aFriend.full_name|clean}</div>
                    {if isset($aFriend.is_active)}
                        <span>({$aFriend.is_active})</span>
                    {/if}
                    {if isset($aFriend.canMessageUser) && $aFriend.canMessageUser == false}
                        {_p var='cannot_select_this_user'}
                    {/if}
                </div>
            </div>
        </div>
        </article>
        {foreachelse}
        <div class="extra_info">
        {if $sFriendType == 'mail'}
            {_p var='sorry_no_members_found'}
        {else}
            {_p var='sorry_no_friends_were_found'}
        {/if}
        </div>
        {/foreach}
    </div>
    </div>
{if !$bSearch}
</div>
<div class="selected-friends-content">
    <ul id="selected_friends_list">
        <li id="selected_friend_template" class="js_hover_title hide">
            <div class="img-wrapper">
                <div>
                    <span class="ico ico-close"></span>
                </div>
            </div>
            <span class="js_hover_info"></span>
        </li>
        <li id="selected_friend_view_more" class="hide">
            <span class="ico ico-dottedmore"></span>
        </li>
    </ul>
    <a role="button" id="deselect_all_friends" class="hide">{_p var='deselect_all'} (<span></span>)</a>
</div>
	{if !$bIsForShare && $sPrivacyInputName != 'invite'}
	<div class="main_break t_right">
		<input type="button" name="submit" value="{_p var='use_selected'}" onclick="$Core.searchFriend.selectSearchFriends()" class="btn btn-primary" />&nbsp;
        <input type="button" name="cancel" value="{_p var='cancel'}" onclick="$Core.searchFriend.cancelSearchFriends()" class="btn btn-default" />
	</div>
	{/if}
</div>
{/if}