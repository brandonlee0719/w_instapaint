<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">{_p var='convert_old_groups'}</div>
    </div>
    <div class="panel-body">
        {if $iConvertedUserId}
        <div class="alert alert-info">
            {_p var="Groups are converting. Remaining groups now is:"} {$iNumberGroups}
        </div>
        {else}
            {if $iNumberGroups}
            <div class="well well-lg">
                <h2>
                    {_p var="you_have_number_old_groups_from_pages", number=$iNumberGroups}.
                    <br/>
                    {_p var="Do you want to convert to new groups system?"}:
                </h2>
                <div class="well-sm">
                    {if $iNumberGroups < 500}
                    <a class="btn-success btn" href="{url link='admincp.groups.convert', convert=true}">{_p var="Yes, convert directly"}</a>
                    {/if}
                    <a class="btn-danger btn" href="{url link='admincp.groups.convert', convert=true  cron=true}">{_p var="Yes, convert via cron job"}</a>
                </div>
            </div>
            <div class="well well-sm">
                {_p var="Learn more about this"} <a target="_blank" href="https://docs.phpfox.com/display/FOX4MAN/Enabling+and+Managing+the+Groups+App">{_p var="Visit here"}</a>
            </div>
            {/if}
        {/if}

        {if !$iNumberGroups}
        <div class="alert alert-info">
            {_p var="There is no old group to convert"}.
        </div>
        {/if}
    </div>
</div>