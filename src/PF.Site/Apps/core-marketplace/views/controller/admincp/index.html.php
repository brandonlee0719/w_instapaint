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
            <a href="{url link='admincp.app' id='Core_Marketplace'}">{_p var='categories'}</a>
            {if isset($aParentCategory.category_id)}
            <span>: {softPhrase var=$aParentCategory.name|convert}</span>
            {/if}
        </div>
    </div>
    <div class="table-responsive flex-sortable">
        <table class="table table-bordered" id="_sort" data-sort-url="{url link='marketplace.admincp.category.order'}">
            <tr>
                <th class="w40"></th>
                <th class="w40"></th>
                <th>{_p var='name'}</th>
                {if !$bSubCategory}
                <th class="t_center w140">{_p var='sub_categories'}</th>
                {/if}
                <th class="t_center w140">{_p var='total_listings'}</th>
                <th class="t_center w140">{_p var='Active'}</th>
            </tr>
            {foreach from=$aCategories key=iKey item=aCategory}
            <tr class="tr" data-sort-id="{$aCategory.category_id}">
                <td class="t_center w40">
                    <i class="fa fa-sort"></i>
                </td>
                <td class="t_center w60">
                    <a href="#" class="js_drop_down_link" title="{_p var='Manage'}"></a>
                    <div class="link_menu">
                        <ul>
                            <li><a href="{url link='admincp.marketplace.add' id=$aCategory.category_id}">{_p var='edit'}</a></li>
                            <li><a href="{url link='admincp.marketplace.delete' delete=$aCategory.category_id}" class="popup">{_p var='delete'}</a></li>
                        </ul>
                    </div>
                </td>
                <td class="td-flex">
                    {softPhrase var=$aCategory.name}
                </td>
                {if !$bSubCategory}
                <td class="t_center w140">
                    {if $aCategory.total_sub}
                        <a href="{url link='admincp.app' id='Core_Marketplace' val[sub]={$aCategory.category_id}" class="">{$aCategory.total_sub}</a>
                    {else}
                        0
                    {/if}
                </td>
                {/if}
                <td class="t_center w140">{if $aCategory.used > 0}<a href="{$aCategory.link}" id="js_category_link{$aCategory.category_id}">{$aCategory.used}</a>{else}0{/if}</td>

                <td class="t_center w140">
                    <div class="js_item_is_active"{if !$aCategory.is_active} style="display:none;"{/if}>
                    <a href="#?call=marketplace.toggleActiveCategory&amp;id={$aCategory.category_id}&amp;active=0" class="js_item_active_link" title="{_p var='Deactivate'}"></a>
                    </div>
                    <div class="js_item_is_not_active"{if $aCategory.is_active} style="display:none;"{/if}>
                    <a href="#?call=marketplace.toggleActiveCategory&amp;id={$aCategory.category_id}&amp;active=1" class="js_item_active_link" title="{_p var='Activate'}"></a>
                    </div>
                </td>
            </tr>
            {/foreach}
        </table>
    </div>
</div>
{/if}