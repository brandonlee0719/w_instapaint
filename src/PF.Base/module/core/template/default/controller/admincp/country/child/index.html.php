<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: index.html.php 1305 2009-12-08 02:51:17Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="table-responsive">
    <table class="table" id="js_drag_drop" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th class="w30"></th>
                <th class="w30"></th>
                <th>{_p var='name'}</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$aChildren name=childs item=aChild}
            <tr class="checkRow{if is_int($phpfox.iteration.childs/2)} tr{else}{/if}">
                <td class="drag_handle"><input type="hidden" name="val[ordering][{$aChild.child_id}]" value="{$aChild.ordering}" /></td>
                <td class="t_center">
                    <a href="#" class="js_drop_down_link" title="{_p var='manage'}"></a>
                    <div class="link_menu">
                        <ul class="dropdown-menu">
                            <li><a href="{url link='admincp.core.country.child.add' id={$aChild.child_id}" class="popup">{_p var='edit'}</a></li>
                            <li><a href="#" onclick="$(this).parents('.link_menu:first').hide(); tb_show('{_p var='translate' phpfox_squote=true}', $.ajaxBox('core.admincp.countryChildTranslate', 'height=410&amp;width=600&child_id={$aChild.child_id}')); return false;">{_p var='translate'}</a></li>
                            <li><a href="{url link='admincp.core.country.child' id=$aChild.country_iso delete={$aChild.child_id}" class="sJsConfirm" data-message="{_p var='are_you_sure' phpfox_squote=true}">{_p var='delete'}</a></li>
                        </ul>
                    </div>
                </td>
                <td>{$aChild.name}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>
<div class="table_clear">
	<input type="button" value="Delete All" class="button" onclick="$Core.jsConfirm({left_curly}message:'{_p var='are_you_sure' phpfox_squote=true}'{right_curly}, function(){left_curly} window.location.href = '{url link='admincp.core.country.child' id=$sParentId deleteall=true}'; {right_curly},function(){left_curly}{right_curly}); return false;" />
</div>