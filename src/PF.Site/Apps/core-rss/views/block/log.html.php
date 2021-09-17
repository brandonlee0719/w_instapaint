<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{if count($aLogs)}
{if isset($bRssIsAdminCp)}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='feed_readers_aggregators_and_web_browsers'}
            </div>
        </div>
        <div class="go_left" style="width:40%;">
            <div class="panel-body">
                <table class="table table-bordered" cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{_p var='reader'}</th>
                            <th class="t_center">{_p var='subscribers'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$aLogs name=logs key=iKey item=aLog}
                            <tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                                <td>{$aLog.user_agent|parse}</td>
                                <td class="t_center">{$aLog.total_agent_count}</td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
        <div class="panel-body t_center" style="margin-left:40%;">
            <img src="http://chart.apis.google.com/chart?chs=472x236&amp;chd=t:{$sCounts}&amp;cht=p&amp;chl={$sNames}&amp;chco=195B85" alt="" />
        </div>
    </div>

    {if count($aUsers)}
    <div class="panel panel-default">
        <div class="main_break"></div>
        <div class="main_break"></div>
        <div class="panel-heading">
            <div class="panel-title">
                {_p var='subscribers'}
            </div>
        </div>
        <table class="table table-bordered" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th class="w180">{_p var='ip_address'}</th>
                    <th>{_p var='reader'}</th>
                    <th>{_p var='date'}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$aUsers name=users key=iKey item=aLog}
                    <tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                        <td class="w180">{$aLog.ip_address|parse}</td>
                        <td>{$aLog.user_agent|parse}</td>
                        <td>{$aLog.time_stamp|date}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
        {pager}
    </div>
    {/if}

{else}
    <div class="t_center">
        <img src="http://chart.apis.google.com/chart?chs=472x236&amp;chd=t:{$sCounts}&amp;cht=p&amp;chl={$sNames}&amp;chco=195B85" alt="" />
    </div>
    <h3>{_p var='feed_readers_aggregators_and_web_browsers'}</h3>
    {if PHPFOX_IS_AJAX}
    <div class="label_flow" style="height:150px;">
    {/if}
    {foreach from=$aLogs name=logs item=aLog}
        <div class="{if is_int($phpfox.iteration.logs/2)}row1{else}row2{/if}{if $phpfox.iteration.logs == 1} row_first{/if}">
            <div class="go_right">
                {$aLog.total_agent_count}
            </div>
            {$aLog.user_agent|parse}
        </div>
    {/foreach}
    {if PHPFOX_IS_AJAX}
    </div>
    {/if}
{/if}
{else}
<div class="extra_info">
	{_p var='no_subscribers_found'}
</div>
{/if}
