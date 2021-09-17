<tr>
	<td class="drag_handle w30">
		<input type="hidden" name="val[{$aMenu.menu_id}][ordering]" value="{$aMenu.ordering}" size="3" class="t_center" />
	</td>
    <td class="w30">
        <a class="js_drop_down_link"></a>
        <div class="link_menu">
            <ul class="dropdown-menu text-left">
                <li><a href="{url link='admincp.menu.add.' id=$aMenu.menu_id}" class="popup">{_p var='edit'}</a></li>
                <li><a href="{url link='admincp.menu.' delete=$aMenu.menu_id}" class="sJsConfirm">{_p var='delete'}</a></li>
            </ul>
        </div>
    </td>
	<td class="w200">{$aMenu.name}</td>
	<td>{$aMenu.url_value}</td>
    <td class="on_off w30">
        <div class="js_item_is_active {if !$aMenu.is_active}hide{/if}">
            <a href="#?call=admincp.updateMenuActivity&amp;id={$aMenu.menu_id}&amp;active=0" class="js_item_active_link" title="{_p var='deactivate'}"></a>
        </div>
        <div class="js_item_is_not_active {if $aMenu.is_active}hide{/if}">
            <a href="#?call=admincp.updateMenuActivity&amp;id={$aMenu.menu_id}&amp;active=1" class="js_item_active_link" title="{_p var='activate'}"></a>
        </div>
    </td>
</tr>