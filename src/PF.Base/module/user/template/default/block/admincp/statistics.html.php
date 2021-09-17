<?php 
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="table-responsive">
    <div class="btn-group" style="width: 100%;">
        <a href="javascript:void(0)" id="btn_total_item" class="btn btn-primary" style="width: 50%">
            {_p var='total_items'}
        </a>
        <a href="javascript:void(0)" id="btn_total_activity" class="btn btn-default" style="width: 50%">
            {_p var='activity_points'}
        </a>
    </div>
    <div id="js_statistic_total_item">
        <table class="table table-admin">
            <tbody>
            <tr>
                <td class="w140">{_p('user_group')}</td>
                <td class="w140">
                    {if ($aUser.status_id == 1)}
                        <div class="js_verify_email_{$aUser.user_id}">{_p var='pending_email_verification'}</div>
                    {/if}
                    {if Phpfox::getParam('user.approve_users') && $aUser.view_id == '1'}
                        <span id="js_user_pending_group_{$aUser.user_id}">{_p var='pending_approval'}</span>
                    {elseif $aUser.view_id == '2'}
                        {_p var='not_approved'}
                    {else}
                        {$aUser.title|convert}
                    {/if}
                </td>
            </tr>
            {foreach from=$aStats name=stat key=iKey item=aStat}
                <tr>
                    <td class="w140">{$aStat.name}</td>
                    <td class="w140">{$aStat.total}</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    <div id="js_statistic_total_activity" style="display: none;">
        <table class="table table-admin">
            {foreach from=$aActivityPoints key=sActivityKeyName item=aActivityPoint}
                {foreach from=$aActivityPoint key=sActivityPhrase item=iActivityCount}
                    <tr>
                        <td class="w140">{$sActivityPhrase}</td>
                        <td class="w140">{$iActivityCount}</td>
                    </tr>
                {/foreach}
            {/foreach}
        </table>
    </div>
</div>

{literal}
    <script type="text/javascript">
        var divItem = $('#js_statistic_total_item'),
            divAct = $('#js_statistic_total_activity');
        $('#btn_total_item').on('click',function(){
            $(this).removeClass('btn-default').addClass('btn-primary');
            $('#btn_total_activity').removeClass('btn-primary').addClass('btn-default');
            divItem.show();
            divAct.hide();
        });
        $('#btn_total_activity').on('click',function(){
            $(this).removeClass('btn-default').addClass('btn-primary');
            $('#btn_total_item').removeClass('btn-primary').addClass('btn-default');
            divItem.hide();
            divAct.show();
        });
    </script>
{/literal}