<?php

defined('PHPFOX') or exit('NO DICE!');

?>
<div class="pf-front-site-statistics">
    {if $bShowOnlineMember}
    <div class="online-members">
        <span class="ico ico-user-man"></span>
        <div class="online-members-text">
            <div>{$iTotalOnlineMember|short_number}</div>
            <span class="member-label">{_p var='online_user_s'}</span>
        </div>
    </div>
    {/if}

    {if $bShowTodayStats && $bShowAllTimeStats && (!empty($aTodayStats) || !empty($aAllTimeStats))}
    <div class="page_section_menu">
        <ul class="nav nav-tabs">
            {if !empty($aTodayStats)}
            <li class="active">
                <a data-cmd="core.tab_item" href="#fe_today_stats">{_p var='today_normal'}</a>
            </li>
            {/if}
            <li {if empty($aTodayStats)}class="active"{/if}>
                <a data-cmd="core.tab_item" href="#fe_all_time_stats">{_p var='all_time_site_stats'}</a>
            </li>
        </ul>
    </div>
    <div class="tab-content clearfix">
        {if !empty($aTodayStats)}
        <div class="tab-pane active" id="fe_today_stats">
            {foreach from=$aTodayStats item=aStat}
            <div class="stat-info">
                <div class="stat-label">
                    {$aStat.phrase}:
                </div>
                <div class="stat-value">
                    {if isset($aStat.link)}<a href="{$aStat.link}">{/if}{$aStat.value|short_number}{if isset($aStat.link)}</a>{/if}
                </div>
            </div>
            {/foreach}
        </div>
        {/if}
        <div class="tab-pane {if empty($aTodayStats)}active{/if}" id="fe_all_time_stats">
            {foreach from=$aAllTimeStats item=aStat}
            <div class="stat-info">
                <div class="stat-label">
                    {$aStat.phrase}:
                </div>
                <div class="stat-value">
                    {if isset($aStat.link)}<a href="{$aStat.link}">{/if}{$aStat.value|short_number}{if isset($aStat.link)}</a>{/if}
                </div>
            </div>
            {/foreach}
        </div>
    </div>

    {elseif $bShowTodayStats}
    <div>{_p var='today_normal'}</div>
    {foreach from=$aTodayStats item=aStat}
    <div class="stat-info">
        <div class="stat-label">
            {$aStat.phrase}:
        </div>
        <div class="stat-value">
            {if isset($aStat.link)}<a href="{$aStat.link}">{/if}{$aStat.value|short_number}{if isset($aStat.link)}</a>{/if}
        </div>
    </div>
    {/foreach}
    {elseif $bShowAllTimeStats}
    <div>{_p var='all_time_site_stats'}</div>
    {foreach from=$aAllTimeStats item=aStat}
    <div class="stat-info">
        <div class="stat-label">
            {$aStat.phrase}:
        </div>
        <div class="stat-value">
            {if isset($aStat.link)}<a href="{$aStat.link}">{/if}{$aStat.value|short_number}{if isset($aStat.link)}</a>{/if}
        </div>
    </div>
    {/foreach}
    {/if}
</div>
