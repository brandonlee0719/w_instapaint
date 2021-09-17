<!-- tab all members-->
{if $sTab == 'all'}
    {foreach from=$aMembers item=aUser}
    <article class="groups-member" id="groups-member-{$aUser.user_id}">
        {template file='user.block.rows'}

        {if $bIsAdmin}
        <div class="moderation_row">
            <label class="item-checkbox">
                <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aUser.user_id}" />
                <i class="ico ico-square-o"></i>
            </label>
        </div>
        <div class="dropdown item-bar-action">
            <a role="button" data-toggle="dropdown" class="btn btn-sm s-4" aria-expanded="true">
                <span class="ico ico-gear-o"></span>
            </a>

            <ul class="dropdown-menu dropdown-menu-right">
                <li>
                    <a role="button" data-app="core_groups" data-action-type="click" data-action="remove_member"
                       data-message="{_p var='are_you_sure_you_want_to_delete_this_user_from_the_group'}"
                       data-group-id="{$iGroupId}" data-user-id="{$aUser.user_id}"
                    >
                        <i class="fa fa-trash"></i> {_p var='delete'}
                    </a>
                </li>
            </ul>
        </div>
        {/if}
    </article>
    {/foreach}
<!-- tab pending members -->
{elseif $sTab == 'pending'}
    {if !count($aMembers)}
    <div class="container">
        <div class="alert alert-info">
            {_p var='there_is_no_pending_request'}
        </div>
    </div>
    {/if}
    {foreach from=$aMembers item=aUser}
    <article class="groups-member" id="groups-member-{$aUser.user_id}">
        {template file='user.block.rows'}

        <div class="dropdown item-bar-action">
            <a role="button" data-toggle="dropdown" class="btn btn-sm s-4" aria-expanded="true">
                <span class="ico ico-gear-o"></span>
            </a>

            <ul class="dropdown-menu dropdown-menu-right">
                <li>
                    <a role="button" onclick="$.ajaxCall('groups.approvePendingRequest', 'sign_up={$aUser.signup_id}&user_id={$aUser.user_id}')">
                        <i class="fa fa-check-square-o"></i> {_p var='approve'}
                    </a>
                </li>
                <li>
                    <a role="button" data-app="core_groups" data-action-type="click" data-action="remove_pending"
                       data-message="{_p var='are_you_sure_you_want_to_delete_this_user_from_the_group'}"
                       data-signup-id="{$aUser.signup_id}" data-user-id="{$aUser.user_id}"
                    >
                        <i class="fa fa-trash"></i> {_p var='delete'}
                    </a>
                </li>
            </ul>
        </div>
    </article>
    {/foreach}
<!-- tab admin -->
{elseif $sTab == 'admin'}
    {foreach from=$aMembers item=aUser}
    <article class="groups-member" id="groups-member-{$aUser.user_id}">
        {template file='user.block.rows'}

        {if $bIsOwner && $aUser.user_id != Phpfox::getUserId()}
        <div class="dropdown item-bar-action">
            <a role="button" data-toggle="dropdown" class="btn btn-sm s-4" aria-expanded="true">
                <span class="ico ico-gear-o"></span>
            </a>

            <ul class="dropdown-menu dropdown-menu-right">
                <li>
                    <a role="button" data-app="core_groups" data-action-type="click" data-action="remove_admin"
                       data-message="{_p var='are_you_sure_you_want_to_remove_admin_role_from_this_user'}"
                       data-group-id="{$iGroupId}" data-user-id="{$aUser.user_id}"
                    >
                        <i class="fa fa-trash"></i> {_p var='remove_admin'}
                    </a>
                </li>
                <li>
                    <a role="button" data-app="core_groups" data-action-type="click" data-action="remove_member"
                       data-message="{_p var='are_you_sure_you_want_to_delete_this_user_from_the_group'}"
                       data-group-id="{$iGroupId}" data-user-id="{$aUser.user_id}"
                    >
                        <i class="fa fa-trash"></i> {_p var='delete'}
                    </a>
                </li>
            </ul>
        </div>
        {/if}
    </article>
    {/foreach}
{/if}
<!-- not show pagination when search member -->
{if $sSearch && !count($aMembers)}
<div class="container">
    <div class="alert alert-info">
        {_p var='there_is_no_members_found'}
    </div>
</div>
{/if}

{if !$sSearch}
{pager}
{/if}