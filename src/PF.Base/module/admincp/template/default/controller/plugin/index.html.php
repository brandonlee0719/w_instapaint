<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
{if count($aPlugins)}
<form method="post" action="{url link='admincp.plugin'}" class="form">
    <div class="table-responsive">
        <table class="table table-admin">
            <thead>
                <tr>
                    <th>{_p var='name'}</th>
                    <th class="w60">{_p var='active'}</th>
                    <th class="w60">{_p var='actions'}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$aPlugins key=iKey item=aPlugin}
                <tr>
                    <td>{$aPlugin.title}</td>
                    <td class="t_center">
                        <div><input type="hidden" name="val[{$aPlugin.plugin_id}][id]" value="1" /></div>
                        <div><input type="checkbox" name="val[{$aPlugin.plugin_id}][is_active]" value="1" {if $aPlugin.is_active}checked="checked" {/if}/></div>
                    </td>
                    <td>
                        <select name="action" class="goJump w140">
                            <option value="">{_p var='select'}</option>
                            <option value="{url link='admincp.plugin.add' id=$aPlugin.plugin_id}">{_p var='edit'}</option>
                            <option value="{url link='admincp.plugin' delete=$aPlugin.plugin_id}" style="color:red;">{_p var='delete'}</option>
                        </select>
                    </td>
                </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
	<div class="form-group">
		<input type="submit" value="{_p var='update'}" class="btn btn-primary" />
	</div>
</form>
{else}
<div class="alert alert-empty">
    <p class="text-danger">{_p var='deprecated_page_will_be_removed_in_470'}</p>
	<p>{_p var='no_plugins_have_been_added'}</p>
</div>
{/if}