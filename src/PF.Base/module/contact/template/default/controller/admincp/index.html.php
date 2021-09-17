<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Miguel Espinoza
 * @package  		Module_Contact
 * @version 		$Id: index.html.php 1802 2010-09-08 12:52:12Z Miguel_Espinoza $
 */

defined('PHPFOX') or exit('NO DICE!');

?>

<form method="post" action="{url link='admincp.contact'}" id="admincp_contact_form_add" class="form">
    <input type="hidden" name="action" value="{if isset($aForms)}edit{else}add{/if}"/>
    {if isset($aForms)}
    <input type="hidden" name="iEdit" value="{$aForms.category_id}">
    {/if}

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">{if isset($aForms)}{_p('Edit category')}{else}{_p var='add_a_new_category'}{/if}</div>
        </div>
        <div class="panel-body">
            {field_language phrase='title' label='name' format='val[name_' field='name'}
        </div>
        <div class="panel-footer">
            <button class="btn btn-primary" type="submit">{if isset($aForms)}{_p var='update'}{else}{_p var='add'}{/if}</button>
        </div>
    </div>
</form>

<form method="post" id="admincp_contact_form_edit" action="{url link='admincp.contact'}">
    <div class="table-responsive">
        <table class="table table-admin" id="js_drag_drop">
            <thead>
                <tr>
                    <th class="w30"></th>
                    <th class="w20">{_p var='order'}</th>
                    <th class="w20"><input type="checkbox" name="val[id]" value="" id="js_check_box_all" class="main_checkbox" /></th>
                    <th>{_p var='category'}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$aCategories key=iKey item=aCategory}
                <tr>
                    <td class="t_center">
                        <a href="#" class="js_drop_down_link" title="Manage"></a>
                        <div class="link_menu">
                            <ul class="dropdown-menu">
                                <li><a href="{url link='admincp.contact' edit=$aCategory.category_id}">{_p var='Edit'}</a></li>
                                <li><a href="{url link='admincp.contact' delete=1}&id%5B%5D={$aCategory.category_id}" class="sJsConfirm">{_p var='Delete'}</a></li>
                            </ul>
                        </div>
                    </td>
                    <td class="drag_handle"><input type="hidden" name="val[ordering][{$aCategory.category_id}]" value="{$aCategory.ordering}" /></td>
                    <td class="t_center"><input type="checkbox" name="id[]" class="checkbox" value="{$aCategory.category_id}" id="js_id_row" /></td>
                    <td>{_p var=$aCategory.title}</td>
                </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
	<div class="table_bottom">
		<input type="submit" name="delete" value="{_p var='delete_selected'}" class="sJsConfirm delete btn btn-default sJsCheckBoxButton disabled" disabled="true" />
	</div>
</form>