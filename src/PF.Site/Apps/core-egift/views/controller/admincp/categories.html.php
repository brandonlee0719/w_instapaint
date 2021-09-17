<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if count($aCategories)}
<div class="panel panel-default">
    <div class="table-responsive flex-sortable">
        <table class="table table-bordered" id="_sort" data-sort-url="{url link='egift.admincp.category.order'}">
            <thead>
                <tr>
                    <th class="w30"></th>
                    <th class="w30"></th>
                    <th>{_p var='Name'}</th>
                    <th class="t_center w100">{_p var='total_egifts'}</th>
                    <th class="w180">{_p var='since'}</th>
                    <th class="w180">{_p var='until'}</th>
                    <th class="t_center w140">{_p var='use_schedule'}</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$aCategories key=iKey item=aCategory}
                <tr class="tr" data-sort-id="{$aCategory.category_id}">
                    <td class="t_center w30">
                        <i class="fa fa-sort"></i>
                    </td>
                    <td class="t_center w30">
                        <a class="js_drop_down_link" title="{_p var='manage'}"></a>
                        <div class="link_menu">
                            <ul class="dropdown-menu">
                                <li><a class="popup" href="{url link='admincp.egift.add-category' edit=$aCategory.category_id}">{_p var='edit'}</a></li>
                                <li>
                                    <a class="popup" href="{url link='admincp.egift.delete-category' delete=$aCategory.category_id}">{_p var='delete'}</a>
                                </li>
                            </ul>
                        </div>
                    </td>
                    <td class="td-flex">
                        {$aCategory.name}
                    </td>
                    <td class="w100">
                        {if $aCategory.item_count}
                            <a href="{url link='admincp.egift.manage-gifts'}?category={$aCategory.category_id}">{$aCategory.item_count}</a>
                        {else}
                            0
                        {/if}
                    </td>
                    <td class="w180">
                        {if $aCategory.time_start > 0}
                            {$aCategory.time_start|date:'core.global_update_time'}
                        {else}
                            {_p var='none'}
                        {/if}
                    </td>
                    <td class="w180">
                        {if $aCategory.time_end > 0}
                            {$aCategory.time_end|date:'core.global_update_time'}
                        {else}
                            {_p var='none'}
                        {/if}
                    </td>
                    <td class="t_center w140">
                        <input type="checkbox"  disabled="disabled" {if $aCategory.time_start > 0}checked="checked"{/if}>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>
{else}
<div class="alert alert-empty">
    {_p var='no_categories_have_been_added'}
</div>
{/if}
