<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="table-responsive panel panel-default groups-manage-categories">
    <div class="panel-heading">
        <div class="panel-title">
            {if $bSubCategory}
            <a href="{url link='admincp.app' id='PHPfox_Groups'}">
            {/if}
            {_p('manage_categories')}
            {if $bSubCategory}
            </a> » {$sParentCategory}
            {/if}
        </div>
    </div>
    <table class="table table-admin" id="_sort" data-sort-url="{if $bSubCategory}{url link='admincp.groups.category.order' type='sub'}{else}{url link='groups.admincp.category.order' type='main'}{/if}">
        <tr>
            <th class="w20"></th>
            <th class="w20"></th>
            <th>{_p('Name')}</th>
            {if !$bSubCategory}
            <th class="w140 text-center">{_p var='sub_categories'}</th>
            <th class="w160 text-center">{_p var='image'}</th>
            {/if}
            <th class="w60 t_center">{_p var='Active'}</th>
        </tr>
        {foreach from=$aCategories key=iKey item=aCategory}
        <tr class="{if is_int($iKey/2)} tr{else}{/if}" data-sort-id="{if $bSubCategory}{$aCategory.category_id}{else}{$aCategory.type_id}{/if}">
            <td class="t_center">
                <i class="fa fa-sort"></i>
            </td>
            <td class="t_center">
                <a href="#" class="js_drop_down_link" title="Manage"></a>
                <div class="link_menu">
                    <ul>
                        <li><a href="{if $bSubCategory}{url link='admincp.groups.add-category' sub=$aCategory.category_id}{else}{url link='admincp.groups.add-category' category_id=$aCategory.type_id}{/if}">{_p var='Edit'}</a></li>
                        {if isset($aCategory.categories) && ($iTotalSub = count($aCategory.categories))}
                        <li><a href="{url link='admincp.app' id='PHPfox_Groups' val[sub]={$aCategory.type_id}">{_p var='Manage Sub-Categories (!<< total >>!)' total=$iTotalSub}</a></li>
                        {/if}
                        <li><a href="#" onclick="tb_show('', $.ajaxBox('groups.deleteCategory', 'height=400&amp;width=600&amp;category_id={if $bSubCategory}{$aCategory.category_id}&amp;is_sub=true{else}{$aCategory.type_id}{/if}')); return false;">{_p var='Delete'}</a></li>
                    </ul>
                </div>
            </td>
            <td>
                {_p var=$aCategory.name}
            </td>
            {if !$bSubCategory}
            <td class="text-center">
                {if count($aCategory.categories)}
                <a href="{url link='admincp.app' id='PHPfox_Groups' val[sub]=$aCategory.type_id}">
                {/if}
                    {$aCategory.categories|count}
                {if count($aCategory.categories)}
                </a>
                {/if}
            </td>
            <td class="text-center">
                {if !empty($aCategory.image_path)}
                <a href="{img server_id=$aCategory.image_server_id path='core.path_actual' file=$aCategory.image_path return_url=true}" class="thickbox">
                    {img server_id=$aCategory.image_server_id path='core.path_actual' file=$aCategory.image_path max_width='50' max_height='50'}
                </a>
                {else}
                    {img path='core.path_actual' file='PF.Site/Apps/core-groups/assets/img/default-category/default_category.png' max_width='50' max_height='50'}
                {/if}
            </td>
            {/if}
            <td>
                <div class="js_item_is_active"{if !$aCategory.is_active} style="display:none;"{/if}>
                    <a href="#?call=groups.updateActivity&amp;id={if $bSubCategory}{$aCategory.category_id}{else}{$aCategory.type_id}{/if}&amp;active=0&amp;sub={if $bSubCategory}1{else}0{/if}" class="js_item_active_link" title="{_p var='Deactivate'}"></a>
                </div>
                <div class="js_item_is_not_active"{if $aCategory.is_active} style="display:none;"{/if}>
                    <a href="#?call=groups.updateActivity&amp;id={if $bSubCategory}{$aCategory.category_id}{else}{$aCategory.type_id}{/if}&amp;active=1&amp;sub={if $bSubCategory}1{else}0{/if}" class="js_item_active_link" title="{_p var='Activate'}"></a>
                </div>
            </td>
        </tr>
    {/foreach}
    </table>
</div>