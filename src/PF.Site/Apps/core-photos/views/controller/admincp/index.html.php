<?php
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
                <a href="{url link='admincp.app' id='Core_Photos'}">
                    {_p('Categories')}
                </a>
            </div>
        </div>
        <div class="table-responsive flex-sortable">
            <table class="table table-bordered" id="_sort" data-sort-url="{url link='photo.admincp.category.order'}">
                <thead>
                    <tr>
                        <th class="w40"></th>
                        <th class="w60"></th>
                        <th>{_p('Name')}</th>
                        {if !$bSubCategory}
                        <th class="t_center w140">{_p var='sub_categories'}</th>
                        {/if}
                        <th class="w140">{_p var='total_photos'}</th>
                        <th class="w80">{_p var='Active'}</th>
                    </tr>
                </thead>
                <tbody>
                {foreach from=$aCategories key=iKey item=aCategory}
                    <tr class="{if is_int($iKey/2)} tr{else}{/if}" data-sort-id="{$aCategory.category_id}">
                        <td class="t_center w40">
                            <i class="fa fa-sort"></i>
                        </td>
                        <td class="t_center w60">
                            <a href="javascript:void(0)" class="js_drop_down_link" title="Manage"></a>
                            <div class="link_menu">
                                <ul>
                                    <li><a class="popup" href="{url link='admincp.photo.add' edit=$aCategory.category_id}">{_p('Edit')}</a></li>
                                    <li>
                                        <a class="popup" href="{url link='admincp.photo.delete-category' delete=$aCategory.category_id}">{_p('Delete')}</a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                        <td class="td-flex">
                            {$aCategory.name}
                        </td>
                        {if !$bSubCategory}
                        <td class="t_center w140">
                            {if isset($aCategory.sub) && ($iTotalSub = count($aCategory.sub))}
                                <a href="{url link='admincp.app' id='Core_Photos' val[sub]={$aCategory.category_id}" class="">{$iTotalSub}</a>
                            {else}
                                0
                            {/if}
                        </td>
                        {/if}
                        <td class="w140 t_center">{if $aCategory.used > 0}<a href="{$aCategory.url}" id="js_category_link{$aCategory.category_id}">{$aCategory.used}</a>{else}0{/if}</td>
                        <td class="w80 on_off">
                            <div class="js_item_is_active"{if !$aCategory.is_active} style="display:none;"{/if}>
                                <a href="#?call=photo.toggleActiveCategory&amp;id={$aCategory.category_id}&amp;active=0" class="js_item_active_link" title="{_p var='Deactivate'}"></a>
                            </div>
                            <div class="js_item_is_not_active"{if $aCategory.is_active} style="display:none;"{/if}>
                                <a href="#?call=photo.toggleActiveCategory&amp;id={$aCategory.category_id}&amp;active=1" class="js_item_active_link" title="{_p var='Activate'}"></a>
                            </div>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </div>
{/if}