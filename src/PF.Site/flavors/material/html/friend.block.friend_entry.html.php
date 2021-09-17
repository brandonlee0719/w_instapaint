<div class="item-outer">
    <div class="item-inner">
        <div class="item-media">
            {img user=$aUser suffix='_200_square' max_width=200 max_height=200}
        </div>
        <div class="user-info">
            <div class="user-title">
                {$aUser|user}
            </div>
            {module name='user.friendship' friend_user_id=$aUser.user_id type='icon' extra_info=true}
            {module name='user.info' friend_user_id=$aUser.user_id number_of_info=2}
        </div>
        
        <input type="hidden" name="friend_id[]" value="{$aUser.friend_user_id}" class="js_friend_actual_user_id" />

        <div class="dropup friend-actions">

            <a class="js_friend_sort_handler"><i class="fa fa-arrows"></i></a>

            <a class="btn btn-default btn-sm btn-round" type="button" id="dropdown-{$aUser.friend_id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <span class="mr-1 ico ico-check"></span>
                {_p var='friend'} <span class="ml-1 ico ico-caret-down"></span>
            </a>

            <ul class="dropdown-menu dropdown-center" aria-labelledby="dropdown-{$aUser.friend_id}" data-dropdown-type="friend_action">
                <li class="friend_action_edit_list {if empty($aUser.lists)}hide{/if}">
                    <a href="#" onclick="$(this).closest('ul').find('.add_to_list:not(.friend_action_edit_list)').toggleClass('hidden'); event.cancelBubble = true; if (event.stopPropagation) event.stopPropagation();return false;"><span class="ico ico-pencilline-o mr-1"></span>{_p var='edit_lists'}</a>
                </li>
                
                <li class="divider {if empty($aUser.lists)}hide{/if}"></li>
                
                {foreach from=$aUser.lists name=lists item=aList}
                <li class="add_to_list hidden"><a href="#" rel="{$aList.list_id}|{$aUser.friend_user_id}"{if $aList.is_active} class="active {if $aList.list_id == $iList}selected{/if}"{/if}><span></span>{$aList.name|clean}</a></li>
                {/foreach}
                
                <li class="add_to_list divider hidden"></li>

                <li class="item_delete">
                    <a href="#" class="friend_action_remove" rel="{$aUser.friend_id}">
                        <span class="ico ico-trash-alt-o mr-1"></span>
                        {_p var='remove_this_friend'}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>