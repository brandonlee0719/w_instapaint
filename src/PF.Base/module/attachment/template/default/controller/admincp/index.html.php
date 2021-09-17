<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: index.html.php 1298 2009-12-05 16:19:23Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="panel panel-default table-responsive">
    <table class="table table-admin">
        <thead>
            <tr>
                <th class="w30"></th>
                <th>{_p var='extension'}</th>
                <th>{_p var='mime_type'}</th>
                <th class="t_center">{_p var='image'}</th>
                <th class="t_center" style="width:60px;">{_p var='active'}</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$aRows key=iKey item=aRow}
            <tr>
                <td class="t_center">
                    <a href="#" class="js_drop_down_link" title="Manage"></a>
                    <div class="link_menu">
                        <ul class="dropdown-menu">
                            <li><a href="{url link='admincp.attachment.add' id=$aRow.extension}" class="popup">{_p var='edit'}</a></li>
                            <li><a href="{url link='admincp.attachment' delete=$aRow.extension}" data-message="{_p var='are_you_sure' phpfox_squote=true}" class="sJsConfirm">{_p var='delete'}</a></li>
                        </ul>
                    </div>
                </td>
                <td>{$aRow.extension}</td>
                <td>{$aRow.mime_type}</td>
                <td class="t_center">{if $aRow.is_image}{_p var='yes'}{else}{_p var='no'}{/if}</td>
                <td class="on_off">
                    <div class="js_item_is_active"{if !$aRow.is_active} style="display:none;"{/if}>
                        <a href="#?call=attachment.updateActivity&amp;id={$aRow.extension}&amp;active=0" class="js_item_active_link" title="{_p var='deactivate'}"></a>
                    </div>
                    <div class="js_item_is_not_active"{if $aRow.is_active} style="display:none;"{/if}>
                        <a href="#?call=attachment.updateActivity&amp;id={$aRow.extension}&amp;active=1" class="js_item_active_link" title="{_p var='activate'}"></a>
                    </div>
                </td>

            </tr>
            {/foreach}
        </tbody>
    </table>
</div>