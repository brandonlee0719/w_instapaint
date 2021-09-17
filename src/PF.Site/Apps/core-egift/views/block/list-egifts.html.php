<?php
?>
<!--IF AJAX_PAGING-->
{if !$bIsPaging}
<div id="js_core_egift_list_egifts">
    {if count($aCategories) > 1}
    <div class="table">
        <form method="post" class="form-inline" id="js_core_egift_form_list_egift" action="" onsubmit="$(this).ajaxCall('egift.changeCategories','is_user_birthday={$bIsBirthday}'); return false;">
            <div class="form-group">
                <label for="select_category">{_p var='category'}</label>
                <select name="category" id="egift_select_category" class="form-control" onchange="$('#js_core_egift_form_list_egift').submit()">
                    {foreach from=$aCategories item=aCategory key=iKey}
                        <option value="{$aCategory.category_id}" {if $iCategoryId == $aCategory.category_id}selected{/if}>{_p var=$aCategory.phrase}</option>
                    {/foreach}
                </select>
            </div>
        </form>
    </div>
    {/if}
    <ul class="js_core_egift_list_items egift-container">
{/if}
<!--END AJAX_PAGING-->
        {foreach from=$aEgifts key=sName name=row item=aGift}
        <li class="js_egift_item egift-item" data-egift="{$aGift.egift_id}">
            <div class="item-outer">
                <div class='item-media'> 
                    <span style="background-image: url({img id='js_egift_item_image_'.$aGift.egift_id server_id=$aGift.server_id path='egift.url_egift' file=$aGift.file_path suffix='_120' max_width=120 max_height=120 return_url=true});"></span>
                    <!-- <span class="js_hover_info">{$aGift.title}</span> --></div>
                <div class="item-inner">
                    <div class="item-title"><div>{$aGift.title}</div></div>
                    <div class="item-price">
                        {if $aGift.price == '0.00'}
                            {_p var='free'}
                        {else}
                            {$aGift.currency_id|currency_symbol}{$aGift.price|number_format:2}
                        {/if}
                    </div>
                    
                </div>
            </div>
        </li>
        {/foreach}
        {pager}
<!--IF AJAX_PAGING-->
{if !$bIsPaging}
    </ul>
</div>

{literal}
<script>
    $Behavior.core_egifts_onclickselectegift = function()
    {
        $('.js_egift_item').click(function () {
            $('#js_core_egift_id').val($(this).data('egift'));
            js_box_remove(this);

            // Add preview
            var selected = $(this).clone();
            selected.find('.item-price').remove();
            selected.find('.item-inner').append('<div class="item-option"><a href="javascript:void(0)" onclick="core_egift_clear_preview()"><i class="ico ico-close-circle" aria-hidden="true"></i></a></div>');
            $('#js_core_egift_preview').html(selected.html());

            // Hide tooltips
            $('#js_global_tooltip_display').hide();
            //when selected egift item
            $('.activity_feed_form .activity_feed_form_holder').addClass('has-egift');
        })
    };
    $Core.loadInit();
</script>
{/literal}
{/if}
<!--END AJAX_PAGING-->
