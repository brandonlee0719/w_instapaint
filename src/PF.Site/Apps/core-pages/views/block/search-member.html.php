<!-- tab all members-->
{if $sTab == 'all'}
    {foreach from=$aMembers item=aUser}
    <article class="pages-member" id="pages-member-{$aUser.user_id}">
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
                    <a role="button" data-app="core_pages" data-action-type="click" data-action="remove_member"
                       data-message="{_p var='are_you_sure_you_want_to_delete_this_user_from_the_page'}"
                       data-page-id="{$iPageId}" data-user-id="{$aUser.user_id}"
                    >
                        <i class="fa fa-trash"></i> {_p var='delete'}
                    </a>
                </li>
            </ul>
        </div>
        {/if}
    </article>
    {/foreach}
<!-- tab admin -->
{elseif $sTab == 'admin'}
    {foreach from=$aMembers item=aUser}
    <article class="pages-member" id="pages-member-{$aUser.user_id}">
        {template file='user.block.rows'}

        {if $bIsOwner && $aUser.user_id != Phpfox::getUserId()}
        <div class="dropdown item-bar-action">
            <a role="button" data-toggle="dropdown" class="btn btn-sm s-4" aria-expanded="true">
                <span class="ico ico-gear-o"></span>
            </a>

            <ul class="dropdown-menu dropdown-menu-right">
                <li>
                    <a role="button" data-app="core_pages" data-action-type="click" data-action="remove_admin"
                       data-message="{_p var='are_you_sure_you_want_to_remove_admin_role_from_this_user'}"
                       data-page-id="{$iPageId}" data-user-id="{$aUser.user_id}"
                    >
                        <i class="fa fa-trash"></i> {_p var='remove_admin'}
                    </a>
                </li>
                <li>
                    <a role="button" data-app="core_pages" data-action-type="click" data-action="remove_member"
                       data-message="{_p var='are_you_sure_you_want_to_delete_this_user_from_the_page'}"
                       data-page-id="{$iPageId}" data-user-id="{$aUser.user_id}"
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
{if $sSearch && !count($aMembers)}
<div class="container">
    <div class="alert alert-info">
    {_p var='there_is_no_members_found'}
    </div>
</div>
{/if}
<!-- not show pagination when search member -->
{if !$sSearch}
{pager}
{/if}