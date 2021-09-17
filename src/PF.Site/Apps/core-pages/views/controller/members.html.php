<?php
    defined('PHPFOX') or exit('NO DICE!');
?>
<div class="pages-block-members">
    <div class="item-page-search-header">
        <div class="item-page-search-member input-group">
            <input class="form-control" type="search" placeholder="{_p var='search_member'}"
                   data-app="core_pages" data-action-type="keyup" data-action="search_member"
                   data-result-container=".search-member-result" data-container=".pages-member-container"
                   data-page-id="{$iPageId}"
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
        <a data-toggle="tab" href="#all" data-app="core_pages" data-action-type="click"
           data-action="change_tab" data-tab="all" data-container=".pages-member-container"
           data-page-id="{$iPageId}" data-result-container=".search-member-result"
        >
            {_p var='all_members'} <span class="member-count" id="all-members-count">({$iTotalMembers})</span>
        </a>
    </li>
    {if isset($iTotalAdmins) && $bCanViewAdmins}
    <li>
        <a data-toggle="tab" href="#admin" data-app="core_pages" data-action-type="click"
           data-action="change_tab" data-tab="admin" data-container=".pages-member-container"
           data-page-id="{$iPageId}" data-result-container=".search-member-result"
        >
            {_p var='page_admins'} <span class="member-count" id="admin-members-count">({$iTotalAdmins})</span>
        </a>
    </li>
    {/if}
</ul>
</div>
<div class="tab-content">
    <div class="tab-content pages-member-container">
        {module name='pages.search-member' tab='all' page_id=$iPageId container='.pages-member-container'}
    </div>
</div>

    <div class="search-member-result hide pages-member-container"></div>
</div>

{if $bIsAdmin && $iTotalMembers}
{moderation}
{/if}