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
            <a href="{url link='admincp.app' id='Core_Blogs'}">{_p var='categories'}</a>
        </div>
    </div>
    <div class="table-responsive flex-sortable">
        <table class="table table-bordered" id="_sort" data-sort-url="{url link='blog.admincp.category.order'}">
            <thead>
                <tr>
                    <th class="w40"></th>
                    <th class="w60"></th>
                    <th>{_p('Name')}</th>
                    {if !$bSubCategory}
                        <th class="t_center w140">{_p var='sub_categories'}</th>
                    {/if}
                    <th class="t_center w140">{_p var='total_blogs'}</th>
                    <th class="t_center w80" style="width:60px;">{_p var='Active'}</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$aCategories key=iKey item=aCategory}
                <tr class="tr" data-sort-id="{$aCategory.category_id}">
                    <td class="t_center w40">
                        <i class="fa fa-sort"></i>
                    </td>
                    <td class="t_center w60">
                        <a href="javascript:void(0)" class="js_drop_down_link" title="{_p var='Manage'}"></a>
                        <div class="link_menu">
                            <ul>
                                <li><a class="popup" href="{url link='admincp.blog.add' edit=$aCategory.category_id}">{_p('Edit')}</a></li>
                                <li>
                                    <a class="popup" href="{url link='admincp.blog.delete-category' delete=$aCategory.category_id}">{_p('Delete')}</a>
                                </li>
                            </ul>
                        </div>
                    </td>
                    <td class="td-flex">
                        {softPhrase var=$aCategory.name|convert}
                    </td>
                    {if !$bSubCategory}
                    <td class="t_center w140">
                        {if isset($aCategory.sub) && ($iTotalSub = count($aCategory.sub))}
                            <a href="{url link='admincp.app' id='Core_Blogs' val[sub]={$aCategory.category_id}" class="">{$iTotalSub}</a>
                        {else}
                            0
                        {/if}
                    </td>
                    {/if}
                    <td class="t_center w140">{if $aCategory.used > 0}<a href="{$aCategory.url}" id="js_category_link{$aCategory.category_id}">{$aCategory.used}</a>{else}0{/if}</td>
                    <td class="on_off w80">
                        <div class="js_item_is_active"{if !$aCategory.is_active} style="display:none;"{/if}>
                            <a href="#?call=blog.toggleActiveCategory&amp;id={$aCategory.category_id}&amp;active=0" class="js_item_active_link" title="{_p var='Deactivate'}"></a>
                        </div>
                        <div class="js_item_is_not_active"{if $aCategory.is_active} style="display:none;"{/if}>
                            <a href="#?call=blog.toggleActiveCategory&amp;id={$aCategory.category_id}&amp;active=1" class="js_item_active_link" title="{_p var='Activate'}"></a>
                        </div>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>
{/if}
