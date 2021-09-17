<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: index.html.php 1572 2010-05-06 12:37:24Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="table-responsive">
    <table class="table table-admin" id="js_drag_drop">
        <thead>
            <tr class="nodrop">
                <th class="w30"></th>
                <th class="w30"></th>
                <th class="w50">{_p var='iso'}</th>
                <th>{_p var='name'}</th>
                <th class="w100">{_p var='states_provinces'}</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$aCountries name=countries item=aCountry}
            <tr>
                <td class="drag_handle"><input type="hidden" name="val[ordering][{$aCountry.country_iso}]" value="{$aCountry.ordering}" /></td>
                <td class="t_center">
                    <a href="#" class="js_drop_down_link" title="{_p var='manage'}"></a>
                    <div class="link_menu">
                        <ul class="dropdown-menu">
                            <li><a class="popup" href="{url link='admincp.core.country.add' id={$aCountry.country_iso}">{_p var='edit'}</a></li>
                            <li><a href="{url link='admincp.core.country.child.add' iso={$aCountry.country_iso}" class="popup">{_p var='add_state_province'}</a></li>
                            {if $aCountry.total_children > 0}
                            <li><a href="{url link='admincp.core.country.child' id={$aCountry.country_iso}">{_p var='manage_states_provinces'}</a></li>
                            <li><a href="{url link='admincp.core.country' export={$aCountry.country_iso}">{_p var='export'}</a></li>
                            {/if}
                            <li><a href="#" onclick="$(this).parents('.link_menu:first').hide(); tb_show('{_p var='translate' phpfox_squote=true}', $.ajaxBox('core.admincp.countryTranslate', 'height=410&amp;width=600&country_iso={$aCountry.country_iso}')); return false;">{_p var='translate'}</a></li>
                            <li><a href="{url link='admincp.core.country' delete={$aCountry.country_iso}" class="sJsConfirm">{_p var='delete'}</a></li>
                        </ul>
                    </div>
                </td>
                <td class="t_center">{$aCountry.country_iso}</td>
                <td>{$aCountry.name}</td>
                <td class="t_center">{if $aCountry.total_children > 0}<a href="{url link='admincp.core.country.child' id={$aCountry.country_iso}">{/if}{$aCountry.total_children}{if $aCountry.total_children > 0}</a>{/if}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>