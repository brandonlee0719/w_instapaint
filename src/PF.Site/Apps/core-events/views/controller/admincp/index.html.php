<?php
/**
 * [PHPFOX_HEADER]
 *
 */

defined('PHPFOX') or exit('NO DICE!');
?>
{if !count($aCategories)}
    <div class="alert alert-danger">
        {_p var='no_categories_found'}
    </div>
{else}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                <a href="{url link='admincp.app' id='Core_Events'}">
                    {_p var='categories'}
                </a>
            </div>
        </div>
        <div class="table-responsive flex-sortable">
            <table class="table table-bordered" id="_sort" data-sort-url="{url link='event.admincp.category.order'}">
                <thead>
                    <tr>
                        <th class="w30"></th>
                        <th class="w30"></th>
                        <th>{_p var='name'}</th>
                        {if !$bSubCategory}
                            <th class="t_center w140">{_p var='sub_categories'}</th>
                        {/if}
                        <th class="t_center w140">{_p var='total_events'}</th>
                        <th class="t_center" style="width:60px;">{_p var='Active'}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$aCategories key=iKey item=aCategory}
                    <tr class="checkRow{if is_int($iKey/2)} tr{else}{/if}" data-sort-id="{$aCategory.category_id}">
                        <td class="t_center">
                            <i class="fa fa-sort"></i>
                        </td>
                        <td class="t_center">
                            <a href="#" class="js_drop_down_link" title="{_p var='Manage'}"></a>
                            <div class="link_menu">
                                <ul>
                                    <li><a href="{url link='admincp.event.add' id=$aCategory.category_id}">{_p var='edit'}</a></li>

                                    <li><a href="{url link='admincp.event.delete' delete=$aCategory.category_id}" class="popup">{_p var='delete'}</a></li>
                                </ul>
                            </div>
                        </td>
                        <td class="td-flex">
                            {softPhrase var=$aCategory.name}
                        </td>
                        {if !$bSubCategory}
                            <td class="t_center w140">
                                {if isset($aCategory.total_sub) && $aCategory.total_sub > 0}
                                    <a href="{url link='admincp.app' id='Core_Events' val[sub]={$aCategory.category_id}" class="">{$aCategory.total_sub}</a>
                                {else}
                                    0
                                {/if}
                            </td>
                        {/if}
                        <td class="t_center">
                            <a href="{$aCategory.link}">{$aCategory.used}</a>
                        </td>
                        <td class="t_center">
                            <div class="js_item_is_active"{if !$aCategory.is_active} style="display:none;"{/if}>
                            <a href="#?call=event.toggleActiveCategory&amp;id={$aCategory.category_id}&amp;active=0" class="js_item_active_link" title="{_p var='Deactivate'}"></a>
                            </div>
                            <div class="js_item_is_not_active"{if $aCategory.is_active} style="display:none;"{/if}>
                            <a href="#?call=event.toggleActiveCategory&amp;id={$aCategory.category_id}&amp;active=1" class="js_item_active_link" title="{_p var='Activate'}"></a>
                            </div>
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    </div>
{/if}