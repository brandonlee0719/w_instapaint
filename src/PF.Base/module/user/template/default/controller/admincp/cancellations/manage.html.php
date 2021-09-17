<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="table-responsive">
    <table class="table table-admin" id="js_drag_drop">
        <thead>
            <tr>
                <th class="w30"></th> <!-- Change order -->
                <th class="w30"></th>
                <th>{_p var='cancellation_reason'}</th>
                <th>{_p var='total'}</th>
                <th class="text-center w60">{_p var='active'}</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$aReasons item=aReason key=iKey}
            <tr>
                <td class="drag_handle"><input type="hidden" name="val[ordering][{$aReason.delete_id}]" value="{$aReason.ordering}" /></td>
                <td>
                    <a class="js_drop_down_link" role="button"></a>
                    <div class="link_menu">
                        <ul class="dropdown-menu">
                            <li><a href="{url link='admincp.user.cancellations.add' id={$aReason.delete_id}">{_p var='edit'}</a></li>
                            <li><a href="{url link='admincp.user.cancellations.manage' delete={$aReason.delete_id}" class="sJsConfirm" data-message="{_p var='are_you_sure'}">{_p var='delete'}</a></li>
                        </ul>
                    </div>
                </td>
                <td>{_p var=$aReason.phrase_var}</td>
                <td>
                    <a href="{url link='admincp.user.cancellations.feedback' option_id={$aReason.phrase_var}">{_p var=$aReason.total}</a>
                </td>
                <td class="on_off">
                    <div class="js_item_is_active"{if !$aReason.is_active} style="display:none;"{/if}>
                         <a href="#?call=core.updateCancellationsActivity&amp;id={$aReason.delete_id}&amp;active=0" class="js_item_active_link" title="{_p var='deactivate'}"></a>
                    </div>
                    <div class="js_item_is_not_active"{if $aReason.is_active} style="display:none;"{/if}>
                         <a href="#?call=core.updateCancellationsActivity&amp;id={$aReason.delete_id}&amp;active=1" class="js_item_active_link" title="{_p var='activate'}"></a>
                    </div>
                </td>
            </tr>

        {foreachelse}
        <tr>
            <td colspan="4">
                <div class="extra_info">
                    {_p var='there_are_no_options_available'}
                    <ul>
                        <li><a href="{url link='admincp.user.cancellations.add'}">{_p var='click_here_to_add'}</a></li>
                    </ul>
                </div>
            </td>
        </tr>
        {/foreach}
        </tbody>
    </table>
</div>