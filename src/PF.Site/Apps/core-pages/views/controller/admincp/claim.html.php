<?php
defined ('PHPFOX') or die('No dice!');
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">{_p var='claims'}</div>
    </div>
    {if count($aClaims)}
    <table class="table table-admin">
        <thead>
        <tr>
            <th class="w20"></th>
            <th>{_p var='claimed_by'}</th>
            <th>{_p var='page_owner'}</th>
            <th>{_p var='page'}</th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$aClaims key=iKey item=aClaim}
        <tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}" id="claim_{$aClaim.claim_id}">
            <td class="text-center">
                <a href="#" class="js_drop_down_link" title="{_p var='manage'}"></a>
                <div class="link_menu">
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#" onclick="$Core.Pages.Claim.approve({$aClaim.claim_id}); return false;">
                                {_p var='grant'}
                            </a>
                            <a href="#" onclick="$Core.Pages.Claim.deny({$aClaim.claim_id}); return false;">
                                {_p var='deny'}
                            </a>
                        </li>
                    </ul>
                </div>
            </td>
            <td>{$aClaim|user}</td>
            <td>{$aClaim|user:'curruser_'}</td>
            <td><a href="{$aClaim.url}">{$aClaim.title}</a></td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    {else}
    <div class="panel-body">
        <div class="alert alert-info">
            {_p var='there_are_no_claims_at_the_moment_dot'}
        </div>
    </div>
    {/if}
</div>