<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if isset($vendorCreated)}
	<i class="fa fa-spin fa-circle-o-notch"></i>
	{literal}
		<script>
			$Ready(function() {
				$Behavior.addDraggableToBoxes();
				$('.admin_action_menu .popup').trigger('click');
			});
		</script>
	{/literal}
{else}

	<div class="admincp_apps_holder">
        {if isset($warning) && $warning}
        <section class="apps">
            <div class="text-danger text-center">{$warning}</div>
        </section>
        {/if}
        <section class="apps">
			<div class="table-responsive admincp_apps_installed">
                <input type="text" onkeyup="$Core.searchTable(this, 'list_apps', 'app_column_index');" placeholder="Search for app names..." class="form-control">
                <table class="table table-admin" id="list_apps">
                    <thead>
                        <tr>
                            <th class="w30"></th>
                            <th class="w30"></th>
                            <th id="app_column_index" class="sortable" onclick="$Core.sortTable(this, 'list_apps');">
                                {_p var="name"}
                            </th>
                            <th class="w120 sortable" onclick="$Core.sortTable(this, 'list_apps');">{_p var="version"}</th>
                            <th class="w120 sortable" onclick="$Core.sortTable(this, 'list_apps');">{_p var="latest"}</th>
                            <th class="sortable" onclick="$Core.sortTable(this, 'list_apps');">{_p var="Author"}</th>
                            <th class="w80 text-center">{_p var="Active"}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$apps item=app}
                        <tr>
                            <td><a href="{url link='admincp.app' id=$app.id}">{$app.icon}</a></td>
                            <td>
                                <a class="js_drop_down_link" role="button"></a>
                                <div class="link_menu">
                                    <ul class="dropdown-menu">
                                        {if !$app.is_module && $bIsTechie}
                                        <li><a href="{url link='admincp.app' id=$app.id verify=1 home=1}">{_p var="re-validation"}</a></li>
                                        {/if}
                                        {if !$app.is_core}
                                        <li><a href="{url link='admincp.app' id=$app.id uninstall='yes'}" data-message="{_p var='are_you_sure' phpfox_squote=true}" class="sJsConfirm">{_p var='uninstall'}</a></li>
                                        {/if}
                                        {if !$app.is_module && $bIsTechie}
                                        <li><a href="{url link='admincp.app' id=$app.id export=1}">{_p var="Export"}</a></li>
                                        {/if}
                                    </ul>
                                </div>
                            </td>
                            <td>
                                {if $app.is_active}<a href="{url link='admincp.app' id=$app.id}">{/if}
                                    {$app.name|clean}
                                {if $app.is_active}</a>{/if}
                            </td>
                            <td>
                                {if $app.is_phpfox_default}
                                {_p var='core'}
                                {else}
                                {$app.version}
                                {/if}
                            </td>
                            <td>
                                {if $app.is_phpfox_default}
                                {_p var='core'}
                                {elseif !empty($app.latest_version)}
                                {$app.latest_version}
                                {/if}
                                {if $app.have_new_version}
                                <br />
                                <a href="{$app.have_new_version}">
                                    {_p var='upgrade_now'}
                                </a>
                                {/if}
                            </td>
                            <td>
                                {if !empty($app.publisher_url)}
                                <a href="{$app.publisher_url}" target="_blank">
                                    {/if}
                                    {$app.publisher}
                                    {if !empty($app.publisher_url)}
                                </a>
                                {/if}
                            </td>
                            <td class="on_off">
                                {if $app.allow_disable}
                                <div class="js_item_is_active {if !$app.is_active}hide{/if}">
                                    <a href="#?call=admincp.updateModuleActivity&amp;id={$app.id}&amp;active=0" class="js_item_active_link" title="{_p var='deactivate'}"></a>
                                </div>
                                <div class="js_item_is_not_active {if $app.is_active}hide{/if}">
                                     <a href="#?call=admincp.updateModuleActivity&amp;id={$app.id}&amp;active=1" class="js_item_active_link" title="{_p var='activate'}"></a>
                                </div>
                                {/if}
                            </td>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>
			</div>
		</section>
		<section class="preview">
			<h1>{_p var='featured_apps'}</h1>
			<div class="phpfox_store_featured" data-type="apps" data-parent="{url link='admincp.store' load='apps'}">
			</div>
		</section>
	</div>

{/if}