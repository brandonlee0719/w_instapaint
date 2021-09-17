<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            <a href="{url link='admincp.app' id='PHPfox_Videos'}">{_p('manage_categories')}</a>
            {if isset($sParentCategory)}
                » {$sParentCategory}
            {/if}
        </div>
    </div>
    <table id="_sort" data-sort-url="{url link='v.admincp.category.order'}" class="table table-admin">
        <thead>
        <tr>
            <th style="width:20px"></th>
            <th style="width:20px"></th>
            <th>{_p('Name')}</th>
            {if !$bSubCategory}
            <th class="text-center w120">{_p var='sub_categories'}</th>
            {/if}
            <th class="text-center w120">{_p var='total_videos'}</th>
            <th class="text-center" style="width:60px;">{_p var='Active'}</th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$aCategories key=iKey item=aCategory}
        <tr class="tr" data-sort-id="{$aCategory.category_id}">
            <td class="t_center">
                <i class="fa fa-sort"></i>
            </td>
            <td class="text-center">
                <a class="js_drop_down_link" title="Manage"></a>
                <div class="link_menu">
                    <ul>
                        <li><a class="popup" href="{url link='admincp.v.add-category' edit=$aCategory.category_id}">{_p('Edit')}</a></li>
                        {if isset($aCategory.sub) && ($iTotalSub = count($aCategory.sub))}
                        <li><a href="{url link='admincp.app' id='PHPfox_Videos' val[sub]={$aCategory.category_id}">{_p('manage_sub_categories')} <span class="badge" style="display: initial;">{$iTotalSub}</span></a></li>
                        {/if}
                        <li>
                            <a class="popup" href="{url link='admincp.v.delete-category' delete=$aCategory.category_id}">{_p('Delete')}</a>
                        </li>
                    </ul>
                </div>
            </td>
            <td>
                {$aCategory.name}
            </td>
            {if !$bSubCategory}
            <td class="text-center">
                {if $aCategory.sub|count}
                <a href="{url link='admincp.app' id='PHPfox_Videos' val[sub]=$aCategory.category_id}">
                {/if}
                    {$aCategory.sub|count}
                {if $aCategory.sub|count}
                </a>
                {/if}
            </td>
            {/if}
            <td class="text-center" style="text-align: center">{if $aCategory.used > 0}<a href="{$aCategory.url}" id="js_category_link{$aCategory.category_id}">{$aCategory.used}</a>{else}0{/if}</td>
            <td class="text-center on_off">
                <div class="js_item_is_active"{if !$aCategory.is_active} style="display:none;"{/if}>
                    <a href="#?call=v.updateActivity&amp;id={$aCategory.category_id}&amp;active=0" class="js_item_active_link" title="{_p var='Deactivate'}"></a>
                </div>
                <div class="js_item_is_not_active"{if $aCategory.is_active} style="display:none;"{/if}>
                    <a href="#?call=v.updateActivity&amp;id={$aCategory.category_id}&amp;active=1" class="js_item_active_link" title="{_p var='Activate'}"></a>
                </div>
            </td>
        </tr>
        {/foreach}
        </tbody>
    </table>
</div>
