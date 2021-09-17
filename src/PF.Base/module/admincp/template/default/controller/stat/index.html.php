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
<div class="table-responsive">
    <table class="table table-admin" id="js_drag_drop">
        <thead>
            <tr>
                <th class="w20"></th>
                <th>{_p var='title'}</th>
                <th class="t_center" style="width:60px;">{_p var='active'}</th>
                <th class="w100"></th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$aStats key=iKey item=aStat}
            <tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                <td class="drag_handle"><input type="hidden" name="val[ordering][{$aStat.stat_id}]" value="{$aStat.ordering}" /></td>
                <td>{_p var=$aStat.phrase_var}</td>
                <td class="on_off">
                    <div class="js_item_is_active"{if !$aStat.is_active} style="display:none;"{/if}>
                        <a href="#?call=core.updateStatActivity&amp;id={$aStat.stat_id}&amp;active=0" class="js_item_active_link" title="{_p var='deactivate'}"></a>
                    </div>
                    <div class="js_item_is_not_active"{if $aStat.is_active} style="display:none;"{/if}>
                        <a href="#?call=core.updateStatActivity&amp;id={$aStat.stat_id}&amp;active=1" class="js_item_active_link" title="{_p var='activate'}"></a>
                    </div>
                </td>
                <td class="t_center">
                    <a href="{url link='admincp.stat.add' id={$aStat.stat_id}">{_p var='edit'}</a>
                    &middot;
                    <a href="{url link='admincp.stat' delete={$aStat.stat_id}" data-message="{_p var='are_you_sure' phpfox_squote=true}" class="sJsConfirm">{_p var='delete'}</a>
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>