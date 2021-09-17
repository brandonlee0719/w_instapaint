<?php 
/**
 * [PHPFOX_HEADER]
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox
 * @version 		$Id: index.html.php 1344 2009-12-21 19:50:14Z Raymond_Benc $
 */
 
defined('PHPFOX') or exit('NO DICE!');
?>
{if !isset($sModuleId)}
{assign var=sModuleId value='core'}
{/if}
<form>
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="form-group">
                <label>{_p var='apps'}</label>
                <select class="form-control" onchange="$('.components').removeClass('in');$('#id_components_'+$(this).val()).addClass('in')">
                {foreach from=$aComponents key=sModule item=aRows}
                <option {if $sModuleId == $sModule}selected{/if} value="{$sModule}">{$sModule|translate:'module'}</option>
                {/foreach}
                </select>
            </div>
        </div>
    </div>
</form>
{foreach from=$aComponents key=sModule item=aRows}
<div class="panel collapse {if $sModule== $sModuleId}in{/if} panel-default components" id="id_components_{$sModule}">
    <div class="panel-heading">
        <div class="panel-title">{$sModule|translate:'module'}</div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th class="w30"></th>
                <th>{_p var='component'}</th>
                <th width="20%">{_p var='connection'}</th>
                <th width="20%" class="t_center">{_p var='controller'}</th>
                <th class="w60">{_p var='active'}</th>
            </tr>
            </thead>
            <tbody>
            {foreach from=$aRows key=iKey item=aRow}
            <tr>
                <td class="">
                    <a href="#" class="js_drop_down_link" title="{_p var='manage'}"></a>
                    <div class="link_menu">
                        <ul class="dropdown-menu">
                            <li><a href="{url link='admincp.component.add' id=$aRow.component_id}">{_p var='edit'}</a></li>
                            <li><a href="{url link='admincp.component' delete=$aRow.component_id}" data-message="{_p var='are_you_sure' phpfox_squote=true}" class="sJsConfirm">{_p var='delete'}</a></li>
                        </ul>
                    </div>
                </td>
                <td>{$aRow.component}</td>
                <td>{if empty($aRow.m_connection)}N/A{else}{$aRow.m_connection}{/if}</td>
                <td class="t_center">
                    {if $aRow.is_controller}
                    {_p var='yes'}
                    {else}
                    {_p var='no'}
                    {/if}
                </td>
                <td class="t_center">
                    <div class="js_item_is_active {if !$aRow.is_active}hide{/if}">
                        <a href="#?call=admincp.componentFeedActivity&amp;id={$aRow.component_id}&amp;active=0" class="js_item_active_link" title="{_p var='deactivate'}"></a>
                    </div>
                    <div class="js_item_is_not_active {if $aRow.is_active}hide{/if}">
                        <a href="#?call=admincp.componentFeedActivity&amp;id={$aRow.component_id}&amp;active=1" class="js_item_active_link" title="{_p var='activate'}"></a>
                    </div>
                </td>

            </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>
{/foreach}