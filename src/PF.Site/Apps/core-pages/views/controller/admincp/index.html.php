<?php 
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="table-responsive panel panel-default pages-manage-categories">
    <div class="panel-heading">
        <div class="panel-title">
            {if $bSubCategory}
            <a href="{url link='admincp.app' id='Core_Pages'}">
            {/if}
            {_p('manage_categories')}
            {if $bSubCategory}
            </a> » {$sParentCategory}
            {/if}
        </div>
    </div>
    <table class="table table-admin" id="js_drag_drop" cellpadding="0" cellspacing="0" data-app="core_pages" data-action-type="init" data-action="init_drag" data-table="#js_drag_drop" data-ajax="{if $bSubCategory}pages.categorySubOrdering{else}pages.categoryOrdering{/if}">
        <thead>
        <tr class="nodrop">
            <th class="w40"></th>
            <th class="w20"></th>
            <th>{_p var='name'}</th>
            {if !$bSubCategory}
            <th class="w140 text-center">{_p var='sub_categories'}</th>
            <th class="w160 text-center">{_p var='image'}</th>
            {/if}
            <th class="t_center w60">{_p var='active'}</th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$aCategories key=iKey item=aCategory}
        <tr class="checkRow">
            <td class="drag_handle text-center">
                <input type="hidden" name="val[ordering][{if $bSubCategory}{$aCategory.category_id}{else}{$aCategory.type_id}{/if}]" value="{$aCategory.ordering}" />
            </td>
            <td class="t_center">
                <a href="#" class="js_drop_down_link" title="{_p var='Manage'}"></a>
                <div class="link_menu">
                    <ul class="dropdown-menu">
                        <li><a href="{if $bSubCategory}{url link='admincp.pages.add' sub=$aCategory.category_id}{else}{url link='admincp.pages.add' category_id=$aCategory.type_id}{/if}">{_p var='edit'}</a></li>
                        {if isset($aCategory.categories) && ($iTotalSub = count($aCategory.categories))}
                        <li><a href="{url link='admincp.pages' sub={$aCategory.type_id}">{_p var='manage_sub_categories_total' total=$iTotalSub}</a></li>
                        {/if}
                        <li><a href="#" onclick="tb_show('', $.ajaxBox('pages.deleteCategory', 'height=400&amp;width=600&amp;category_id={if $bSubCategory}{$aCategory.category_id}&amp;is_sub=true{else}{$aCategory.type_id}{/if}')); return false;">{_p var='Delete'}</a></li>
                    </ul>
                </div>
            </td>
            <td>
                {if Phpfox::isPhrase($this->_aVars['aCategory']['name'])}
                {_p var=$aCategory.name}
                {else}
                {$aCategory.name|convert}
                {/if}
            </td>
            {if !$bSubCategory}
            <td class="text-center">
                {if count($aCategory.categories)}
                <a href="{url link='admincp.pages'}?sub={$aCategory.type_id}">
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
                    {img path='core.path_actual' file='PF.Site/Apps/core-pages/assets/img/default-category/default_category.png' max_width='50' max_height='50'}
                {/if}
            </td>
            {/if}
            <td>
                <div class="js_item_is_active"{if !$aCategory.is_active} style="display:none;"{/if}>
                    <a href="#?call=pages.updateActivity&amp;id={if $bSubCategory}{$aCategory.category_id}{else}{$aCategory.type_id}{/if}&amp;active=0&amp;sub={if $bSubCategory}1{else}0{/if}" class="js_item_active_link" title="{_p var='deactivate'}"></a>
                </div>
                <div class="js_item_is_not_active"{if $aCategory.is_active} style="display:none;"{/if}>
                    <a href="#?call=pages.updateActivity&amp;id={if $bSubCategory}{$aCategory.category_id}{else}{$aCategory.type_id}{/if}&amp;active=1&amp;sub={if $bSubCategory}1{else}0{/if}" class="js_item_active_link" title="{_p var='activate'}"></a>
                </div>
            </td>
        </tr>
        {/foreach}
        </tbody>
    </table>
</div>