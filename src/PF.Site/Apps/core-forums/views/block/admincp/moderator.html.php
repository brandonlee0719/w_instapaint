<?php
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div id="js_forum_add_moderator">
	<form method="post" action="#" onsubmit="$(this).ajaxCall('forum.updateModerator'); return false;">
        <input type="hidden" name="val[user_id]" id="js_actual_user_id" value="" />
        <div class="panel-group">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">
                        {_p var='manage_moderators'}
                    </div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="moderators" clas="mb-2">
                            <span>
                                <a href="#" onclick="$Core.browseUsers({literal}{{/literal}input : 'users&bIsAdminCp=true&bOnlyUser=true'{literal}}{/literal}); return false;">{_p var='moderators'}</a>:
                            </span>
                        </label>
                        {if !count($aUsers)}
                        <div><input type="text" class="form-control" name="default_users" id="js_default_users" value="" size="30" onclick="$Core.browseUsers({literal}{{/literal}input : 'users&bIsAdminCp=true&bOnlyUser=true'{literal}}{/literal}); return false;" class="w_95" /></div>
                        {/if}
                        <div class="label_flow input_clone" style="{if !count($aUsers)}height:30px; display:none;{else}height:90px;{/if}" class="form-control" id="js_selected_users">
                        {if count($aUsers) && is_array($aUsers)}
                            {foreach from=$aUsers name=users item=aUser}
                                <div class="js_cached_user_name row1 js_cached_user_id_{$aUser.user_id}{if $phpfox.iteration.users == 1} row_first{/if}" id="js_user_id_{$aUser.user_id}"><span style="display:none;">{$aUser.user_id}</span><input type="hidden" name="val[users][]" value="{$aUser.user_id}" /><a href="#" onclick="$Core.jsConfirm({left_curly}message:'{_p var='are_you_sure' phpfox_squote=true}'{right_curly}, function(){left_curly} $('.js_cached_user_id_{$aUser.user_id}').remove(); $.ajaxCall('forum.removeModerator', 'id={$aUser.moderator_id}'); {right_curly}, function(){left_curly}{right_curly}); return false;">{img theme='misc/delete.gif' class='delete_hover' style='vertical-align:middle;'}</a> {$aUser|user:'':' onclick="return plugin_userLinkClick(this);"'}</div>
                            {/foreach}
                        {/if}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="forums">
                            {_p var='forums'}:
                        </label>
                        <select name="val[forum]" id="js_forum_list_drop" class="form-control">
                            {$sForumDropDown}
                        </select>
                    </div>
                </div>
            </div>
        </div>
		<div id="js_perm_holder" class="panel-group">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title" id="js_perm_title">
                    {_p var='global_moderator_permissions'}
                    </div>
                </div>
                <div class="panel-body">
                    {foreach from=$aPerms key=sVar item=aPerm}
                    <div class="form-group">
                        <label>{$aPerm.phrase}:</label>
                        <div class="item_is_active_holder">
                            <span class="js_item_active item_is_active">
                                <input class='form-control' id="js_true_{$sVar}" type="radio" name="val[param][{$sVar}]" value="1" {if $aPerm.value}checked="checked" {/if}/> {_p var='yes'}
                            </span>
                            <span class="js_item_active item_is_not_active">
                                <input class='form-control' type="radio" name="val[param][{$sVar}]" value="0" {if !$aPerm.value}checked="checked" {/if}/> {_p var='no'}
                            </span>
                        </div>
                    </div>
                    {/foreach}
                </div>
		    </div>
        </div>
		<div class="panel-footer">
			<span id="js_update_mod"></span><input type="submit" value="{_p var='save'}" class="btn btn-primary" /> <input type="button" value="{_p var='manage_forums'}" class="btn btn-default" onclick="window.location.href = '#'; $('#js_forum_edit_content').hide(); $('#js_form_actual_content').show();" /> <input type="button" value="{_p var='cancel'}" class="btn btn-default" onclick="window.location.href = '#'; $('#js_forum_edit_content').hide(); $('#js_form_actual_content').show();" />
		</div>				
	</form>
</div>