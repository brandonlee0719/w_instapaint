<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<form method="post" action="{url link='admincp.user.group.delete'}">
    <input type="hidden" name="val[delete_id]" value="{$aGroup.user_group_id}" />
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">{_p var='are_you_sure_you_want_to_delete_the_user_group_title' title=$aGroup.title|clean}</div>
        </div>
        <div class="panel-body">
            <div class="alert alert-warning">
                {_p var='b_notice_b_this_cannot_be_undone'}
            </div>
            <div class="form-group">
                <label for="val_user_group_id" class="control-label">
                    {_p var='b_yes_b_i_am_sure_move_any_members_that_belong_to_the_user_group_title_to' title=$aGroup.title|clean}:
                </label>
                <select name="val[user_group_id]" class="form-control" id="val_user_group_id">
                    {foreach from=$aGroups item=aUserGroup}
                    {if $aUserGroup.user_group_id != $aGroup.user_group_id}
                    <option value="{$aUserGroup.user_group_id}"{if $aUserGroup.user_group_id == 2} selected="selected"{/if}>{$aUserGroup.title|clean}</option>
                    {/if}
                    {/foreach}
                </select>
            </div>
            <div class="form-group">
                <button type="submit"class="btn btn-danger">{_p var='delete_this_user_group'}</button>
                <a class="btn btn-link" href="{url link='admincp.user.group'}">{_p var='no_thanks_get_me_out_of_here'}</a>
            </div>
        </div>
    </div>
</form>
