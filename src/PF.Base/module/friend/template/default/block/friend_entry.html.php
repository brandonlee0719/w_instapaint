<article class="user-item friend_row_holder js_selector_class_{$aUser.friend_id}" data-uid="{$aUser.friend_id}" id="js_friend_{$aUser.friend_id}">
    <div class="pages_item user_item">
        {img user=$aUser suffix='_200_square' max_width=200 max_height=200}
        <div class="pages_info">
            <div>
                {$aUser|user}
                {module name='user.friendship' friend_user_id=$aUser.user_id type='icon' extra_info=true}
                {module name='user.info' friend_user_id=$aUser.user_id number_of_info=2}
            </div>
        </div>
    </div>
    <input type="hidden" name="friend_id[]" value="{$aUser.friend_user_id}" class="js_friend_actual_user_id" />
    <div class="item-option friend_action">
        <div class="dropdown">
            <a class="js_friend_sort_handler"><i class="fa fa-arrows"></i></a>
            <a class="btn dropdown-toggle" type="button" id="dropdown-{$aUser.friend_id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <i class="fa fa-cog"></i>
            </a>
            <ul class="friend_action_drop_down dropdown-menu dropdown-menu-right dropdown-menu-checkmark" aria-labelledby="dropdown-{$aUser.friend_id}" data-dropdown-type="friend_action">
                <li class="friend_action_edit_list {if empty($aUser.lists)}hide{/if}">
                <a href="#" onclick="$(this).closest('ul').find('.add_to_list:not(.friend_action_edit_list)').toggleClass('hidden'); event.cancelBubble = true; if (event.stopPropagation) event.stopPropagation();return false;">{_p var='edit_lists'}</a></li>
                <li class="divider {if empty($aUser.lists)}hide{/if}"></li>
                {foreach from=$aUser.lists name=lists item=aList}
                <li class="add_to_list hidden"><a href="#" rel="{$aList.list_id}|{$aUser.friend_user_id}"{if $aList.is_active} class="active {if $aList.list_id == $iList}selected{/if}"{/if}><span></span>{$aList.name|clean}</a></li>
                {/foreach}
                <li class="add_to_list divider hidden"></li>
                <li class="item_delete">
                    <a href="#" class="friend_action_remove" rel="{$aUser.friend_id}">{_p var='remove_this_friend'}</a>
                </li>
            </ul>
        </div>
    </div>
</article>