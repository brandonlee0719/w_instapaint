<?php 

 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<script type="text/javascript" src="{param var='core.path_actual'}PF.Site/Apps/core-forums/assets/admin.js"></script>
<div class="panel-group">
    <div class="panel panel-default">
        <div class="panel-heading"><div class="panel-title">{_p var='manage_permissions'}</div></div>
        <div class="panel-body">
            <label for="user_group">{_p var='user_group'}:</label>
            <div class="form-group">
                <select class="form-control" name="user_group_id" onchange="$('#js_display_perms').slideUp(); $('#js_form_user_group_id').val(this.value); $(this).ajaxCall('forum.loadPermissions', 'forum_id={$iForumId}');">
                    <option value="">{_p var='select'}:</option>
                {foreach from=$aUserGroups item=aUserGroup}
                    <option value="{$aUserGroup.user_group_id}">{$aUserGroup.title|clean}</option>
                {/foreach}
                </select>
                <div class="extra_info">
                    {_p var='select_a_user_group_to_assign_special_permissions_for_this_specific_forum'}
                </div>
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <div class="panel panel-default" id="js_display_perms" style="display:none;">
        <form method="post" action="#" onsubmit="$(this).ajaxCall('forum.savePerms'); return false;">
            <div><input type="hidden" name="val[forum_id]" value="{$iForumId}" /></div>
            <div><input type="hidden" name="val[user_group_id]" value="" id="js_form_user_group_id" /></div>
            <div class="panel-heading">
                <div class="panel-title">
                {_p var='forum_permissions'} - <span id="js_form_perm_group"></span>
                </div>
            </div>
            <div class="panel-body">
                <div id="js_display_list_perms"></div>
            </div>
            <div id="js_save_perms" class="panel-footer" style="display:none;">
                <input name="save" type="submit" value="{_p var='save'}" class="btn btn-primary" />
            </div>
        </form>
    </div>
</div>