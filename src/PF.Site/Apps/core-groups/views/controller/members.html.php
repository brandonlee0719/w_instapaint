<?php
    defined('PHPFOX') or exit('NO DICE!');
?>
<div class="groups-block-members">
    <div class="item-group-search-header">
        <div class="item-group-search-member input-group">
            <input class="form-control" type="search" placeholder="{_p var='search_member'}"
                   data-app="core_groups" data-action-type="keyup" data-action="search_member"
                   data-result-container=".search-member-result" data-container=".groups-member-container"
                   data-group-id="{$iGroupId}"
            />
            <span class="input-group-btn" aria-hidden="true">
                <button class="btn " type="submit">
                     <i class="fa fa-search"></i>
                </button>
            </span>
        </div>
    </div>
    <div class="page_section_menu page_section_menu_header">
        <ul class="nav nav-tabs nav-justified">
            <li class="active">
                <a data-toggle="tab" href="#all" data-app="core_groups" data-action-type="click"
                   data-action="change_tab" data-tab="all" data-container=".groups-member-container"
                   data-group-id="{$iGroupId}" data-result-container=".search-member-result"
                >
                    {_p var='all_members'} <span class="member-count" id="all-members-count">({$iTotalMembers})</span>
                </a>
            </li>
            {if $bIsAdmin}
            <li>
                <a data-toggle="tab" href="#pending" data-app="core_groups" data-action-type="click"
                   data-action="change_tab" data-tab="pending" data-container=".groups-member-container"
                   data-group-id="{$iGroupId}" data-result-container=".search-member-result"
                >
                    {_p var='pending_requests'} <span class="member-count" id="pending-members-count">({$iTotalPendings})</span>
                </a>
            </li>
            {/if}
            {if isset($iTotalAdmins) && $bCanViewAdmins}
            <li>
                <a data-toggle="tab" href="#admin" data-app="core_groups" data-action-type="click"
                   data-action="change_tab" data-tab="admin" data-container=".groups-member-container"
                   data-group-id="{$iGroupId}" data-result-container=".search-member-result"
                >
                    {_p var='group_admins'} <span class="member-count" id="admin-members-count">({$iTotalAdmins})</span>
                </a>
            </li>
            {/if}
        </ul>
    </div>

    <div class="tab-content groups-member-container">
        {module name='groups.search-member' tab='all' group_id=$iGroupId container='.groups-member-container'}
    </div>

    <div class="search-member-result groups-member-container hide"></div>
    <div class="groups-searching hide">
        <i class="fa fa-spinner fa-spin"></i>
    </div>
</div>

{if $bIsAdmin && $iTotalMembers}
{moderation}
{/if}