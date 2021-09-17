<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: controller.html.php 64 2009-01-19 15:05:54Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if count($aPackages)}
<div class="panel panel-default table-responsive">
    <table class="table table-admin" id="js_drag_drop">
        <thead>
            <tr>
                <th></th>
                <th class="w40"></th>
                <th>{_p var='title'}</th>
                <th class="t_center w120">{_p var='subscriptions'}</th>
                <th class="t_center w60">{_p var='active'}</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$aPackages key=iKey item=aPackage}
            <tr>
                <td class="drag_handle"><input type="hidden" name="val[ordering][{$aPackage.package_id}]" value="{$aPackage.ordering}" /></td>
                <td class="t_center">
                    <a href="#" class="js_drop_down_link" title="{_p var='manage'}"></a>
                    <div class="link_menu">
                        <ul class="dropdown-menu">
                            <li><a href="{url link='admincp.subscribe.add' id={$aPackage.package_id}">{_p var='edit_package'}</a></li>
                            <li><a href="{url link='admincp.subscribe' delete={$aPackage.package_id}" class="sJsConfirm" data-message="{_p var='are_you_sure' phpfox_squote=true}">{_p var='delete_package'}</a></li>
                            {if $aPackage.total_active > 0}
                            <li><a href="{url link='admincp.subscribe.list' package=$aPackage.package_id status='completed'}">{_p var='view_active_subscriptions'}</a></li>
                            <li><a href="{url link='admincp.user.browse' group=$aPackage.user_group_id}">{_p var='view_active_users'}</a></li>
                            {/if}
                        </ul>
                    </div>
                </td>
                <td>{_p var=$aPackage.title}</td>
                <td class="t_center">{if $aPackage.total_active > 0}<a href="{url link='admincp.subscribe.list' package=$aPackage.package_id status='completed'}">{/if}{$aPackage.total_active}{if $aPackage.total_active > 0}</a>{/if}</td>
                <td class="on_off">
                    <div class="js_item_is_active"{if !$aPackage.is_active} style="display:none;"{/if}>
                        <a href="#?call=subscribe.updateActivity&amp;package_id={$aPackage.package_id}&amp;active=0" class="js_item_active_link" title="{_p var='deactivate'}"></a>
                    </div>
                    <div class="js_item_is_not_active"{if $aPackage.is_active} style="display:none;"{/if}>
                        <a href="#?call=subscribe.updateActivity&amp;package_id={$aPackage.package_id}&amp;active=1" class="js_item_active_link" title="{_p var='activate'}"></a>
                    </div>
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>
{else}
<div class="alert alert-empty">
    <h4>{_p var='no_packages_have_been_added'}</h4><br/>
    <a class="btn btn-info" href="{url link='admincp.subscribe.add'}">{_p var='create_a_new_package'}</a>
</div>
{/if}