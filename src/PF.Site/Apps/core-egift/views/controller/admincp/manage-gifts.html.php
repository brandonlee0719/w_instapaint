<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<form method="get" class="form panel panel-default" id="js_core_egift_form_manage_egift">
    <div class="panel-body">
        <div class="form-group">
            <label for="select_category">{_p var='choose_category'}:</label>
            <select name="category" id="egift_select_category" class="form-control" onchange="$('#js_core_egift_form_manage_egift').submit()">
                <option value="">{_p var='all'}</option>
                {foreach from=$aCategories item=aCategory key=iKey}
                <option value="{$aCategory.category_id}" {value type='select' id='category' default=$aCategory.category_id}>{_p var=$aCategory.phrase}</option>
                {/foreach}
            </select>
        </div>
    </div>
</form>

{if count($aEgifts)}
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">{_p var='manage_egifts'}</div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered" data-sort-url="{url link='egift.admincp.category.order-egifts'}" id="{if $iCategoryId}_sort{/if}">
            <thead>
                <tr>
                    {if $iCategoryId}
                    <th class="w30"></th>
                    {/if}
                    <th class="w30"></th>
                    <th>{_p var='category'}</th>
                    <th>{_p var='egift_name'}</th>
                    <th class="text-center">{_p var='image'}</th>
                    <th>{_p var='price'}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$aEgifts key=iKey item=aGift}
                    <tr class="tr" data-sort-id="{$aGift.egift_id}">
                        {if $iCategoryId}
                        <td class="t_center">
                            <i class="fa fa-sort"></i>
                        </td>
                        {/if}
                        <td>
                            <a href="javascript:void(0)" class="js_drop_down_link" title="{_p var='manage'}"></a>
                            <div class="link_menu">
                                <ul>
                                    <li><a class="popup" href="{url link='admincp.egift.add-gift' edit=$aGift.egift_id}">{_p var='edit'}</a></li>
                                    <li>
                                        <a href="{url link='admincp.egift.manage-gifts' delete=$aGift.egift_id}" class="sJsConfirm" data-message="{_p var='are_you_sure_you_want_to_delete_this_egift_permanently'}">{_p var='delete'}</a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                        <td>
                            <a href="{url link='admincp.egift.manage-gifts' category=$aGift.category_id}">{$aGift.category_name}</a>
                        </td>
                        <td>
                            {$aGift.title}
                        </td>
                        <td class="t_center">
                            {img id='js_photo_view_image' server_id=$aGift.server_id thickbox=true path='egift.url_egift' file=$aGift.file_path suffix='_75_square' max_width=120 max_height=120 title=$aGift.title time_stamp=true}
                        </td>
                        <td class="t_center w100">
                            {foreach from=$aGift.currency key=sCurrency item=iPrice}
                            <p>{$sCurrency}: {$iPrice}</p>
                            {/foreach}
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
         {pager}
    </div>
</div>
{else}
<div class="alert alert-empty">
    {_p var='no_gifts_have_been_added'}
</div>
{/if}
{literal}
<script>
    $Behavior.core_egifts_init_managegift = function () {
        $('.apps_menu > ul > li:nth-child(3) a').addClass('active');
    }
</script>
{/literal}