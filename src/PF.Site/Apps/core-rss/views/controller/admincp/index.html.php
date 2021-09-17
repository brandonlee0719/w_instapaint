<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{if !count($aFeeds)}
<div class="alert alert-empty">
    {_p var='no_feeds_found'}
</div>
{else}
<div class="panel panel-default">
    <div class="table-responsive flex-sortable">
        <table class="table table-bordered" id="_sort" data-sort-url="{url link='rss.admincp.feed.order'}">
            <thead>
                <tr>
                    <th class="w30"></th>
                    <th class="w30"></th>
                    <th>{_p var='title'}</th>
                    <th class="t_center w140">{_p var='subscribers'}</th>
                    <th class="t_center w100">{_p var='site_wide'}</th>
                    <th class="t_center w100">{_p var='active'}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$aFeeds key=iKey item=aFeed}
                <tr class="{if is_int($iKey/2)} tr{else}{/if}" data-sort-id="{$aFeed.feed_id}">
                    <td class="t_center w30">
                        <i class="fa fa-sort"></i>
                    </td>
                    <td class="t_center w30">
                        <a href="#" class="js_drop_down_link" title="Manage"></a>
                        <div class="link_menu">
                            <ul>
                                <li><a href="{url link='admincp.rss.add' id=$aFeed.feed_id}">{_p var='edit_feed'}</a></li>
                                <li><a href="{url link='admincp.rss' delete=$aFeed.feed_id}" data-message="{_p var='are_you_sure' phpfox_squote=true}" class="sJsConfirm">{_p var='delete_feed'}</a></li>
                            </ul>
                        </div>
                    </td>
                    <td class="td-flex">{_p var=$aFeed.title_var}</td>
                    <td class="t_center w140">
                    {if $aFeed.total_subscribed > 0}<a href="{url link='admincp.rss.log' id=$aFeed.feed_id}">{/if}{$aFeed.total_subscribed}{if $aFeed.total_subscribed > 0}</a>{/if}
                    </td>
                    <td class="t_center w100">
                        <div class="js_item_is_active" style="{if !$aFeed.is_site_wide}display:none;{/if}">
                            <a href="#?call=rss.updateSiteWide&amp;id={$aFeed.feed_id}&amp;active=0" class="js_item_active_link" title="{_p var='disable'}"></a>
                        </div>
                        <div class="js_item_is_not_active" style="{if $aFeed.is_site_wide}display:none;{/if}">
                            <a href="#?call=rss.updateSiteWide&amp;id={$aFeed.feed_id}&amp;active=1" class="js_item_active_link" title="{_p var='enable'}"></a>
                        </div>
                    </td>
                    <td class="t_center w100">
                        <div class="js_item_is_active" style="{if !$aFeed.is_active}display:none;{/if}">
                            <a href="#?call=rss.updateFeedActivity&amp;id={$aFeed.feed_id}&amp;active=0" class="js_item_active_link" title="{_p var='deactivate'}"></a>
                        </div>
                        <div class="js_item_is_not_active" style="{if $aFeed.is_active}display:none;{/if}">
                            <a href="#?call=rss.updateFeedActivity&amp;id={$aFeed.feed_id}&amp;active=1" class="js_item_active_link" title="{_p var='activate'}"></a>
                        </div>
                    </td>
                </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
</div>
{/if}