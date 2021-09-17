<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="group-block-action block">
    <div class="item-inner">
        <div class="item-info-main">
            <div class="item-image">
                {img user=$aPageUser suffix='_50_square'}
            </div>
            <div class="item-info">
                <div class="item-title"><a href="{$aPage.link}">{$aPage.full_name}</a></div>
                <div class="item-member-count">
                    {if $aPage.total_like == 1}
                    {_p var='1_member'}
                    {else}
                    {_p var='total_members' total=$aPage.total_like}
                    {/if}
                </div>
            </div>
        </div>
        {if !empty($aPage.is_liked) && !Phpfox::getUserBy('profile_page_id')}
        <div class="item-btn-join dropdown">
            <button class="btn btn-primary btn-round dropdown-toggle btn-sm btn-icon" type="button" data-toggle="dropdown"><span class="ico ico-check"></span>{_p var='joined'}
                <span class="caret"></span></button>
            <ul class="dropdown-menu">
                <li><a role="button" onclick="$.ajaxCall('like.delete', 'type_id=groups&item_id={$aPage.page_id}&reload=true'); return false;"><span class="ico ico-close"></span>{_p var='unjoin'}</a></li>
            </ul>
        </div>
        {else}
        <a role="button" class="btn btn-round btn-icon btn-default btn-sm groups_like_join" onclick="$.ajaxCall('like.add', 'type_id=groups&item_id={$aPage.page_id}&reload=true'); return false;">
            <span class="ico ico-plus"></span>{_p var='join'}
        </a>
        {/if}
    </div>
    <ul class="item-group-action">
        {if $aPage.is_admin}
        <li><a href="{url link='groups.add' id=$aPage.page_id}"><i class="ico ico-pencil"></i>{_p('Edit group')}</a></li>
        {/if}
        {if Phpfox::isModule('report') && ($aPage.user_id != Phpfox::getUserId())}
        <li>
            <a href="#?call=report.add&amp;height=210&amp;width=400&amp;type=groups&amp;id={$aPage.page_id}" class="inlinePopup" title="{_p var='Report this Group'}"><i class="ico ico-flag-alt-o"></i>{_p var='report'}</a>
        </li>
        {/if}
    </ul>
</div>